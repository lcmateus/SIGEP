<?php

use App\Models\Processo;
use App\Models\User;

test('visitante e redirecionado ao tentar acessar rotas protegidas', function () {
    foreach ([
        route('dashboard'),
        route('processos.index'),
        route('perfil'),
        route('resultados'),
        route('configuracoes'),
    ] as $url) {
        $this->get($url)->assertRedirect(route('home'));
    }
});

test('usuario pendente autenticado recebe forbidden em rotas protegidas', function () {
    $user = User::factory()->pendente()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertForbidden();
});

test('rotas administrativas exigem perfil admin', function () {
    $membro = User::factory()->create();

    foreach ([
        ['GET', route('usuarios.list')],
        ['GET', route('processos.create')],
        ['POST', route('processos.store')],
    ] as [$method, $url]) {
        $this->actingAs($membro)
            ->call($method, $url)
            ->assertForbidden();
    }
});

test('rotas de votacao exigem perfil membro', function () {
    $admin = User::factory()->admin()->create();
    $processo = Processo::query()->create([
        'titulo' => 'Processo aberto',
        'descricao' => 'Processo disponivel para votacao.',
        'status' => 'ativa',
    ]);

    $this->actingAs($admin)
        ->get(route('processos.votar', $processo))
        ->assertForbidden();

    $this->actingAs($admin)
        ->post(route('processos.votar.store', $processo), ['tipo' => 'favor'])
        ->assertForbidden();
});

test('admin e membro acessam rotas permitidas para seus perfis', function () {
    $admin = User::factory()->admin()->create();
    $membro = User::factory()->create();
    $processo = Processo::query()->create([
        'titulo' => 'Processo publico aos perfis ativos',
        'descricao' => 'Ambos os perfis ativos podem visualizar.',
        'status' => 'ativa',
    ]);

    $this->actingAs($admin)->get(route('usuarios.list'))->assertOk();
    $this->actingAs($admin)->get(route('processos.create'))->assertOk();
    $this->actingAs($admin)->get(route('processos.show', $processo))->assertOk();

    $this->actingAs($membro)->get(route('processos.index'))->assertOk();
    $this->actingAs($membro)->get(route('processos.show', $processo))->assertOk();
    $this->actingAs($membro)->get(route('processos.votar', $processo))->assertOk();
});
