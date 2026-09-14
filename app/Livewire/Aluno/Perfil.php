<?php

declare(strict_types=1);

namespace App\Livewire\Aluno;

use App\Models\Usuario;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Perfil extends Component
{
    use WithFileUploads;

    public string $nome = '';

    public string $telefone = '';

    public string $empresa = '';

    public string $cargo = '';

    public mixed $foto = null;

    public string $senhaAtual = '';

    public string $novaSenha = '';

    public string $novaSenha_confirmation = '';

    public function mount(): void
    {
        $usuario = $this->usuario;
        $this->nome = $usuario->nome;
        $this->telefone = (string) $usuario->telefone;
        $this->empresa = (string) $usuario->empresa;
        $this->cargo = (string) $usuario->cargo;
    }

    #[Computed]
    public function usuario(): Usuario
    {
        return Usuario::query()->with('organizacao')->findOrFail(auth()->id());
    }

    public function salvarPerfil(): void
    {
        $dados = $this->validate([
            'nome' => ['required', 'string', 'max:160'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'empresa' => ['nullable', 'string', 'max:160'],
            'cargo' => ['nullable', 'string', 'max:120'],
            'foto' => ['nullable', 'image', 'max:5120'],
        ]);
        $usuario = $this->usuario;

        if ($this->foto !== null) {
            Storage::disk('local')->delete((string) $usuario->avatar_caminho);
            $dados['avatar_caminho'] = $this->foto->store("avatares/{$usuario->id}", 'local');
        }

        unset($dados['foto']);
        $usuario->update($dados);
        $this->reset('foto');
        session()->flash('sucesso_perfil', 'Dados atualizados.');
    }

    public function trocarSenha(): void
    {
        $dados = $this->validate([
            'senhaAtual' => ['required', 'current_password:web'],
            'novaSenha' => ['required', 'confirmed', Password::defaults()],
        ]);
        $this->usuario->update(['password' => Hash::make($dados['novaSenha'])]);
        $this->reset('senhaAtual', 'novaSenha', 'novaSenha_confirmation');
        session()->flash('sucesso_senha', 'Senha atualizada.');
    }

    public function render(): View
    {
        return view('livewire.aluno.perfil')
            ->layout('components.layouts.aluno', ['titulo' => 'Perfil']);
    }
}
