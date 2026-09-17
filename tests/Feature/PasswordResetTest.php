<?php

use App\Mail\CodigoRecuperacao;
use App\Mail\SenhaAlterada;
use App\Models\RecuperacaoSenha;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

function criarAdminParaRecuperacao(): UsuarioAdministrador
{
    return UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario Geral',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);
}

function codigoDoUltimoEmail(): string
{
    $codigo = '';

    Mail::assertSent(CodigoRecuperacao::class, function (CodigoRecuperacao $mail) use (&$codigo) {
        $codigo = $mail->codigo;

        return true;
    });

    return $codigo;
}

it('exibe o formulario de recuperacao pedindo apenas o siape', function () {
    $this->get('/recuperar-senha')
        ->assertOk()
        ->assertSee('SIAPE')
        ->assertDontSee('E-mail');
});

it('envia codigo de 6 caracteres para o e-mail do siape existente', function () {
    Mail::fake();

    criarAdminParaRecuperacao();

    $this->post('/recuperar-senha', ['siape' => '9999999'])
        ->assertRedirect('/recuperar-senha/codigo');

    $codigo = codigoDoUltimoEmail();

    expect(strlen($codigo))->toBe(6);

    $this->assertDatabaseHas('recuperacao_senha', [
        'siape' => '9999999',
        'email' => 'secretario@sigep.test',
        'usado_em' => null,
    ]);

    $this->assertTrue($this->app['session']->has('recuperacao.siape'));
    expect($this->app['session']->get('recuperacao.siape'))->toBe('9999999');
});

it('nao revela se o siape nao existe', function () {
    Mail::fake();

    $this->post('/recuperar-senha', ['siape' => '0000000'])
        ->assertRedirect('/recuperar-senha/codigo');

    Mail::assertNothingSent();

    $this->assertDatabaseCount('recuperacao_senha', 0);
    expect($this->app['session']->has('recuperacao.siape'))->toBeFalse();
});

it('redefine a senha com codigo correto e invalida o fluxo', function () {
    Mail::fake();

    $admin = criarAdminParaRecuperacao();

    $this->post('/recuperar-senha', ['siape' => '9999999']);

    $codigo = codigoDoUltimoEmail();

    $this->post('/recuperar-senha/codigo', ['codigo' => $codigo])
        ->assertRedirect('/recuperar-senha/redefinir');

    $this->post('/recuperar-senha/redefinir', [
        'password' => 'NovaSenha#2026',
        'password_confirmation' => 'NovaSenha#2026',
    ])->assertRedirect('/');

    expect($admin->fresh()->password)
        ->not->toBe($admin->password)
        ->and(password_verify('NovaSenha#2026', $admin->fresh()->password))
        ->toBeTrue();

    $this->assertDatabaseCount('recuperacao_senha', 0);
    expect($this->app['session']->has('recuperacao.siape'))->toBeFalse();

    Mail::assertSent(SenhaAlterada::class);
});

it('rejeita codigo incorreto e incrementa as tentativas', function () {
    Mail::fake();

    criarAdminParaRecuperacao();

    $this->post('/recuperar-senha', ['siape' => '9999999']);

    $this->post('/recuperar-senha/codigo', ['codigo' => 'AAAAAA'])
        ->assertSessionHasErrors('codigo');

    expect(RecuperacaoSenha::query()->first()->tentativas)->toBe(1);
    expect($this->app['session']->has('recuperacao.autorizado'))->toBeFalse();
});

it('invalida o fluxo ao atingir o limite de tentativas', function () {
    Mail::fake();

    criarAdminParaRecuperacao();

    $this->post('/recuperar-senha', ['siape' => '9999999']);

    foreach (range(1, 4) as $i) {
        $this->post('/recuperar-senha/codigo', ['codigo' => 'BBBBBB'])
            ->assertSessionHasErrors('codigo');
    }

    expect(RecuperacaoSenha::query()->first()->tentativas)->toBe(4);

    $this->post('/recuperar-senha/codigo', ['codigo' => 'BBBBBB'])
        ->assertRedirect('/recuperar-senha');

    expect(RecuperacaoSenha::query()->first()->tentativas)->toBe(5);
    expect($this->app['session']->has('recuperacao.siape'))->toBeFalse();
});

it('invalida o fluxo quando o codigo esta expirado', function () {
    Mail::fake();

    criarAdminParaRecuperacao();

    $this->post('/recuperar-senha', ['siape' => '9999999']);

    RecuperacaoSenha::query()->update(['expira_em' => now()->subMinute()]);

    $this->post('/recuperar-senha/codigo', ['codigo' => 'CCCCCC'])
        ->assertRedirect('/recuperar-senha');

    expect($this->app['session']->has('recuperacao.siape'))->toBeFalse();
});

it('bloqueia o acesso direto as telas de codigo e redefinicao sem fluxo ativo', function () {
    $this->get('/recuperar-senha/codigo')->assertRedirect('/recuperar-senha');
    $this->get('/recuperar-senha/redefinir')->assertRedirect('/recuperar-senha');
    $this->post('/recuperar-senha/redefinir', [
        'password' => 'NovaSenha#2026',
        'password_confirmation' => 'NovaSenha#2026',
    ])->assertRedirect('/recuperar-senha');
});

it('redefine a senha de um membro ativo', function () {
    Mail::fake();

    $admin = criarAdminParaRecuperacao();

    UsuarioMembro::query()->create([
        'siape' => '1000001',
        'nome' => 'Membro Teste',
        'email' => 'membro1@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
        'data_ativacao' => now(),
        'ativado_por' => $admin->siape,
    ]);

    $membro = UsuarioMembro::query()->where('siape', '1000001')->first();

    $this->post('/recuperar-senha', ['siape' => '1000001']);

    $codigo = codigoDoUltimoEmail();

    $this->post('/recuperar-senha/codigo', ['codigo' => $codigo])
        ->assertRedirect('/recuperar-senha/redefinir');

    $this->post('/recuperar-senha/redefinir', [
        'password' => 'NovaSenha#2026',
        'password_confirmation' => 'NovaSenha#2026',
    ])->assertRedirect('/');

    expect(password_verify('NovaSenha#2026', $membro->fresh()->password))->toBeTrue();
});