<?php

use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function criarMembro(string $siape = '1000001', string $email = 'membro1@sigep.test', ?string $ativadoEm = null): UsuarioMembro
{
    return UsuarioMembro::query()->create([
        'siape' => $siape,
        'nome' => 'Membro Teste',
        'email' => $email,
        'password' => bcrypt('SenhaForte#1234'),
        'data_ativacao' => $ativadoEm,
        'ativado_por' => $ativadoEm ? '9999999' : null,
        'is_presidente' => false,
    ]);
}

function dadosCadastro(array $extra = []): array
{
    return array_merge([
        'siape' => '2000001',
        'nome' => 'Novo Usuario',
        'email' => 'novo@sigep.test',
        'password' => 'SenhaForte#1234',
        'password_confirmation' => 'SenhaForte#1234',
    ], $extra);
}

it('bloqueia cadastro de membro quando o siape ja pertence a um administrador', function () {
    UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);

    $this->post('/cadastro', dadosCadastro(['siape' => '9999999']))
        ->assertSessionHasErrors('siape');

    expect(UsuarioMembro::query()->count())->toBe(0);
});

it('bloqueia cadastro quando o siape ja pertence a um membro pendente de aprovacao', function () {
    UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);

    UsuarioMembro::query()->create([
        'siape' => '1000001',
        'nome' => 'Pendente',
        'email' => 'pendente@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
        'data_ativacao' => null,
    ]);

    $this->post('/cadastro', dadosCadastro(['siape' => '1000001']))
        ->assertSessionHasErrors('siape');
});

it('impede que o primeiro administrador use o siape de um membro existente', function () {
    UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);

    criarMembro('1000001', 'membro1@sigep.test', now());

    UsuarioAdministrador::query()->where('siape', '9999999')->delete();

    $this->post('/cadastro', dadosCadastro(['siape' => '1000001']))
        ->assertSessionHasErrors('siape');

    expect(UsuarioAdministrador::query()->count())->toBe(0);
});

it('bloqueia cadastro quando o e-mail ja pertence a um administrador (cross-table)', function () {
    UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);

    $this->post('/cadastro', dadosCadastro(['email' => 'secretario@sigep.test']))
        ->assertSessionHasErrors('email');
});

it('bloqueia cadastro quando o e-mail ja pertence a um membro em outra tabela', function () {
    UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);

    criarMembro('1000001', 'membro1@sigep.test', now());

    $this->post('/cadastro', dadosCadastro(['siape' => '2000001', 'email' => 'membro1@sigep.test']))
        ->assertSessionHasErrors('email');
});

it('permite cadastro quando siape e email sao novos', function () {
    UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);

    $this->post('/cadastro', dadosCadastro())
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertDatabaseHas('usuario_membro', ['siape' => '2000001', 'email' => 'novo@sigep.test']);
});

it('permite salvar o perfil sem alterar o proprio siape/email', function () {
    $admin = UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);

    $this->actingAs($admin)
        ->put('/perfil', ['nome' => 'Secretario', 'email' => 'secretario@sigep.test'])
        ->assertSessionHasNoErrors()
        ->assertRedirect();
});

it('bloqueia trocar o email para um email ja usado por outro usuario', function () {
    $admin = UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);

    criarMembro('1000001', 'membro1@sigep.test', now());

    $this->actingAs($admin)
        ->put('/perfil', ['nome' => 'Secretario', 'email' => 'membro1@sigep.test'])
        ->assertSessionHasErrors('email');
});

it('bloqueia editar membro para um siape ja usado em outra tabela', function () {
    $admin = UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);

    $membro = criarMembro('1000001', 'membro1@sigep.test', now());

    $this->actingAs($admin)
        ->put(route('usuarios.update', $membro), [
            'nome' => 'Membro Editado',
            'email' => 'membro2@sigep.test',
            'siape' => '9999999',
            'is_presidente' => false,
        ])
        ->assertSessionHasErrors('siape');
});