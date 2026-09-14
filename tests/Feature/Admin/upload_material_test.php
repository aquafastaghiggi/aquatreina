<?php

declare(strict_types=1);

use App\Filament\Pages\ConstrutorCurriculo;
use App\Models\Aula;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Usuario;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('admin', 'web');
    $this->admin = Usuario::factory()->create();
    $this->admin->assignRole('admin');
    $this->actingAs($this->admin);
    $this->curso = Curso::factory()->for($this->admin, 'responsavel')->create();
    $modulo = Modulo::factory()->for($this->curso)->create();
    $this->aula = Aula::factory()->for($modulo)->create();
});

it('upload rejeita arquivo acima de 20 MB', function (): void {
    Storage::fake('materiais');

    Livewire::test(ConstrutorCurriculo::class, ['registro' => (string) $this->curso->id])
        ->call('selecionarAula', $this->aula->id)
        ->set('tituloMaterial', 'Manual grande')
        ->set('arquivoMaterial', UploadedFile::fake()->create('manual.pdf', 20481, 'application/pdf'))
        ->call('enviarMaterial')
        ->assertHasErrors(['arquivoMaterial']);
});

it('upload rejeita MIME não permitido', function (): void {
    Storage::fake('materiais');

    Livewire::test(ConstrutorCurriculo::class, ['registro' => (string) $this->curso->id])
        ->call('selecionarAula', $this->aula->id)
        ->set('tituloMaterial', 'Executável')
        ->set('arquivoMaterial', UploadedFile::fake()->create('programa.exe', 10, 'application/x-msdownload'))
        ->call('enviarMaterial')
        ->assertHasErrors(['arquivoMaterial' => 'mimes']);
});

it('upload permitido fica no disco privado de materiais', function (): void {
    Storage::fake('materiais');

    Livewire::test(ConstrutorCurriculo::class, ['registro' => (string) $this->curso->id])
        ->call('selecionarAula', $this->aula->id)
        ->set('tituloMaterial', 'Manual técnico')
        ->set('arquivoMaterial', UploadedFile::fake()->create('manual.pdf', 100, 'application/pdf'))
        ->call('enviarMaterial')
        ->assertHasNoErrors();

    $material = $this->aula->materiais()->firstOrFail();
    Storage::disk('materiais')->assertExists($material->caminho);
    expect($material->disco)->toBe('materiais');
});
