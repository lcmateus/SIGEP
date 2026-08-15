<?php

use App\Enums\DocumentoTipo;
use App\Models\Documento;
use App\Models\Etapa;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('DocumentoController CRUD', function () {
    test('index retorna documentos da etapa', function () {
        $admin = User::factory()->admin()->create();
        $etapa = Etapa::factory()->create();
        Documento::factory()->count(2)->create(['etapa_id' => $etapa->id]);

        $response = $this->actingAs($admin)
            ->get(route('documentos.index', $etapa));

        $response->assertStatus(200);
        expect($response->json())->toHaveCount(2);
    });

    test('show retorna documento', function () {
        $admin = User::factory()->admin()->create();
        $documento = Documento::factory()->create();

        $response = $this->actingAs($admin)
            ->get(route('documentos.show', $documento));

        $response->assertStatus(200);
        expect($response->json('id'))->toBe($documento->id);
    });

    test('store faz upload e cria documento', function () {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $etapa = Etapa::factory()->create();

        $response = $this->actingAs($admin)
            ->post(route('documentos.store', $etapa), [
                'tipo' => DocumentoTipo::Relatorio->value,
                'anexo' => UploadedFile::fake()->create('relatorio.pdf', 100, 'application/pdf'),
            ]);

        $response->assertRedirect();
        $documento = $etapa->documentos()->first();
        expect($documento)->not->toBeNull()
            ->and($documento->tipo)->toBe(DocumentoTipo::Relatorio)
            ->and(Storage::disk('public')->exists($documento->anexo))->toBeTrue();
    });

    test('store rejeita tipo invalido', function () {
        $admin = User::factory()->admin()->create();
        $etapa = Etapa::factory()->create();

        $response = $this->actingAs($admin)
            ->post(route('documentos.store', $etapa), [
                'tipo' => 'tipo_inexistente',
                'anexo' => UploadedFile::fake()->create('doc.pdf'),
            ]);

        $response->assertSessionHasErrors('tipo');
    });

    test('destroy remove documento e arquivo', function () {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $etapa = Etapa::factory()->create();
        $documento = Documento::factory()->create([
            'etapa_id' => $etapa->id,
            'anexo' => 'documentos/teste.pdf',
        ]);
        Storage::disk('public')->put('documentos/teste.pdf', 'conteudo');

        $response = $this->actingAs($admin)
            ->delete(route('documentos.destroy', $documento));

        $response->assertRedirect();
        expect(Documento::find($documento->id))->toBeNull()
            ->and(Storage::disk('public')->exists('documentos/teste.pdf'))->toBeFalse();
    });
});
