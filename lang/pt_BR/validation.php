<?php

declare(strict_types=1);

return [
    'accepted' => 'O campo :attribute deve ser aceito.',
    'confirmed' => 'A confirmação de :attribute não confere.',
    'email' => 'Informe um e-mail válido.',
    'max' => ['string' => 'O campo :attribute não pode ter mais de :max caracteres.'],
    'min' => ['string' => 'O campo :attribute deve ter pelo menos :min caracteres.'],
    'password' => [
        'letters' => 'A senha deve conter letras.',
        'mixed' => 'A senha deve conter letras maiúsculas e minúsculas.',
        'numbers' => 'A senha deve conter números.',
        'symbols' => 'A senha deve conter símbolos.',
        'uncompromised' => 'Esta senha apareceu em um vazamento de dados. Escolha outra.',
    ],
    'required' => 'O campo :attribute é obrigatório.',
    'string' => 'O campo :attribute deve ser um texto.',
    'unique' => 'Este :attribute já está em uso.',
    'attributes' => [
        'aceite_termos' => 'aceite dos termos',
        'email' => 'e-mail',
        'nome' => 'nome',
        'password' => 'senha',
    ],
];
