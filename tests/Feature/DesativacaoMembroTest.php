<?php

use App\Mail\NovoPresidente;
use App\Mail\ProcessoDesignado;
use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use App\Models\Voto;
use App\Services\VotacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

function adminParaExclusao(): UsuarioAdministrador
{
    return UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);
}

function membroAtivoParaExclusao(string $siape, bool $presidente = false): UsuarioMembro
{
    return UsuarioMembro::query()->create([
        'siape' => $siape,
        'nome' => "Membro {$siape}",
        'email' => "membro{$siape}@sigep.test",
        'password' => bcrypt('SenhaForte#1234'),
        'data_ativacao' => now(),
        'ativado_por' => '9999999',
        'is_presidente' => $presidente,
    ]);
}

function processoParaExclusao(string $numeroSei, string $relatorSiape): Processo
{
    $processo = Processo::query()->create([
        'numero_sei' => $numeroSei,
        'data_admissao' => now()->toDateString(),
        'id_administrador' => '9999999',
        'id_relator' => $relatorSiape,
    ]);

    Etapa::query()->create([
        'numero_sei_processo' => $processo->numero_sei,
        'ordem' => 1,
        'tipo' => Etapa::TIPO_JUIZO,
        'status' => Etapa::STATUS_EM_ELABORACAO,
    ]);

    return $processo;
}

it('exclui o membro e redistribui as relatorias ativas para outro membro', function () {
    $admin = adminParaExclusao();
    $m1 = membroAtivoParaExclusao('1000001');
    membroAtivoParaExclusao('1000002');

    $p1 = processoParaExclusao('00000000000001', '1000001');
    $p2 = processoParaExclusao('00000000000002', '1000001');

    $this->actingAs($admin)
        ->delete(route('usuarios.destroy', $m1))
        ->assertRedirect();

    $this->assertDatabaseMissing('usuario_membro', ['siape' => '1000001']);
    expect($p1->fresh()->id_relator)->toBe('1000002');
    expect($p2->fresh()->id_relator)->toBe('1000002');
});

it('nao redistribui processos arquivados ou finalizados', function () {
    $admin = adminParaExclusao();
    $m1 = membroAtivoParaExclusao('1000001');
    membroAtivoParaExclusao('1000002');

    $arquivado = Processo::query()->create([
        'numero_sei' => '00000000000009',
        'data_admissao' => now()->toDateString(),
        'id_administrador' => '9999999',
        'id_relator' => '1000001',
    ]);
    Etapa::query()->create([
        'numero_sei_processo' => $arquivado->numero_sei,
        'ordem' => 1,
        'tipo' => Etapa::TIPO_JUIZO,
        'status' => Etapa::STATUS_ARQUIVADO,
    ]);

    $this->actingAs($admin)
        ->delete(route('usuarios.destroy', $m1));

    $this->assertDatabaseMissing('usuario_membro', ['siape' => '1000001']);
    expect($arquivado->fresh()->id_relator)->toBeNull();
});

it('exclui o presidente e redesigna as rodadas abertas para outro membro', function () {
    $admin = adminParaExclusao();
    $presidente = membroAtivoParaExclusao('1000001');
    $outro = membroAtivoParaExclusao('1000002');

    $processo = processoParaExclusao('00000000000005', '1000002');

    $rodada = RodadaVotacao::query()->create([
        'data_abertura' => now(),
        'data_encerramento' => now()->addWeek(),
        'resultado' => null,
        'id_etapa' => $processo->etapas->first()->id,
        'id_presidente' => '1000001',
    ]);

    $this->actingAs($admin)
        ->delete(route('usuarios.destroy', $presidente))
        ->assertRedirect();

    $this->assertDatabaseMissing('usuario_membro', ['siape' => '1000001']);
    expect($rodada->fresh()->id_presidente)->toBe($outro->siape);
});

it('bloqueia excluir o ultimo membro ativo', function () {
    $admin = adminParaExclusao();
    $unico = membroAtivoParaExclusao('1000001');

    $this->actingAs($admin)
        ->delete(route('usuarios.destroy', $unico))
        ->assertRedirect()
        ->assertSessionHas('error');

    $this->assertDatabaseHas('usuario_membro', ['siape' => '1000001']);
    expect($unico->fresh()->data_ativacao)->not->toBeNull();
});

it('preserva os votos do membro excluido de forma anonima', function () {
    $admin = adminParaExclusao();
    $m1 = membroAtivoParaExclusao('1000001');
    membroAtivoParaExclusao('1000002');

    $processo = processoParaExclusao('00000000000003', '1000001');

    $rodada = RodadaVotacao::query()->create([
        'data_abertura' => now(),
        'data_encerramento' => now()->addWeek(),
        'resultado' => null,
        'id_etapa' => $processo->etapas->first()->id,
    ]);

    Voto::query()->create([
        'opcao' => 'aprova',
        'is_minerva' => false,
        'id_membro' => '1000001',
        'id_rodada' => $rodada->id,
    ]);

    $service = app(VotacaoService::class);
    expect($service->contagemVotos($rodada)['aprova'])->toBe(1);

    $this->actingAs($admin)
        ->delete(route('usuarios.destroy', $m1));

    $this->assertDatabaseHas('voto', ['opcao' => 'aprova', 'id_membro' => null]);
    expect($service->contagemVotos($rodada))->toBe([
        'aprova' => 0,
        'desaprova' => 0,
        'abstencoes' => 0,
    ]);
    expect($service->totalMembrosAtivos())->toBe(1);
});

it('permite excluir um membro pendente de aprovacao', function () {
    $admin = adminParaExclusao();
    $pendente = UsuarioMembro::query()->create([
        'siape' => '1000003',
        'nome' => 'Membro Pendente',
        'email' => 'pendente@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);

    $this->actingAs($admin)
        ->delete(route('usuarios.destroy', $pendente))
        ->assertRedirect();

    $this->assertDatabaseMissing('usuario_membro', ['siape' => '1000003']);
});

it('envia e-mail ao novo relator e ao novo presidente da rodada', function () {
    Mail::fake();

    $admin = adminParaExclusao();
    $presidente = membroAtivoParaExclusao('1000001', true);
    $outro = membroAtivoParaExclusao('1000002');

    $processo = processoParaExclusao('00000000000004', '1000001');

    RodadaVotacao::query()->create([
        'data_abertura' => now(),
        'data_encerramento' => now()->addWeek(),
        'resultado' => null,
        'id_etapa' => $processo->etapas->first()->id,
        'id_presidente' => '1000001',
    ]);

    $this->actingAs($admin)
        ->delete(route('usuarios.destroy', $presidente))
        ->assertRedirect();

    Mail::assertSent(NovoPresidente::class, function (NovoPresidente $mail) use ($outro) {
        return $mail->nome === $outro->nome;
    });

    Mail::assertSent(ProcessoDesignado::class, function (ProcessoDesignado $mail) use ($outro) {
        return $mail->nome === $outro->nome;
    });
});