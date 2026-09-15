<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Enums\SituacaoUsuario;
use App\Models\TextoLegal;
use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): Usuario
    {
        Validator::make($input, [
            'nome' => ['required', 'string', 'max:160'],
            'email' => [
                'required',
                'string',
                'email',
                'max:190',
                Rule::unique(Usuario::class),
            ],
            'telefone' => ['nullable', 'string', 'max:20'],
            'empresa' => ['nullable', 'string', 'max:160'],
            'cargo' => ['nullable', 'string', 'max:120'],
            'aceite_termos' => ['accepted'],
            'website' => ['nullable', 'max:0'],
            'password' => $this->passwordRules(),
        ])->validate();

        $usuario = Usuario::create([
            'nome' => $input['nome'],
            'email' => $input['email'],
            'password' => $input['password'],
            'telefone' => $input['telefone'] ?? null,
            'empresa' => $input['empresa'] ?? null,
            'cargo' => $input['cargo'] ?? null,
            'situacao' => SituacaoUsuario::Pendente,
            'termos_aceitos_em' => now(),
            'termos_versao_aceita' => TextoLegal::vigente('termos')?->versao,
            'termos_ip' => request()->ip(),
        ]);

        $usuario->assignRole('aluno');

        return $usuario;
    }
}
