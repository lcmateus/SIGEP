<?php

use App\Models\UsuarioAdministrador;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

function adminDeUpload(): UsuarioAdministrador
{
    return UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);
}

it('retorna erro amigavel em JSON quando o upload excede o limite do servidor', function () {
    $request = Request::create('/documentos', 'POST', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $response = app(ExceptionHandler::class)->render($request, new PostTooLargeException);

    expect($response->getStatusCode())->toBe(413);
    expect(json_decode($response->getContent(), true))->toMatchArray(['ok' => false]);
    expect(json_decode($response->getContent(), true)['erro'])->toContain('excede o limite');
});

it('redireciona com erro amigavel quando o POST excede o limite (HTML)', function () {
    $request = Request::create('/processos', 'POST', [], [], [], [
        'HTTP_REFERER' => '/processos/criar',
    ]);
    $request->setLaravelSession($this->app['session']->driver());
    $this->app->instance('request', $request);

    $response = app(ExceptionHandler::class)->render($request, new PostTooLargeException);

    expect($response->getStatusCode())->toBe(302);
    expect($response->headers->get('Location'))->toContain('/processos/criar');
    expect(session()->has('errors'))->toBeTrue();
    expect(session('errors')->getBag('default')->first('arquivo'))->toContain('excede o limite');
});

it('impede upload acima do limite configuravel da aplicacao', function () {
    $admin = adminDeUpload();

    $arquivo = UploadedFile::fake()->create(
        'grande.pdf',
        config('sigep.upload_max_kb') + 1,
        'application/pdf',
    );

    $this->actingAs($admin)
        ->post(route('processos.store'), [
            'numero_sei' => '00000000000099',
            'arquivo' => $arquivo,
            'tipo' => 'pdf_sei',
        ])
        ->assertSessionHasErrors('arquivo');
});
