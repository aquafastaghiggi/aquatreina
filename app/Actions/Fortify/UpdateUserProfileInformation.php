<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function update(Usuario $user, array $input): void
    {
        Validator::make($input, [
            'nome' => ['required', 'string', 'max:160'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('usuarios')->ignore($user->id),
            ],
        ])->validateWithBag('updateProfileInformation');

        $user->forceFill([
            'nome' => $input['nome'],
            'email' => $input['email'],
        ])->save();
    }
}
