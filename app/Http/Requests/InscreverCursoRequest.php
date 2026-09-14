<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\SituacaoCurso;
use App\Models\Curso;
use App\Models\Matricula;
use Illuminate\Foundation\Http\FormRequest;

class InscreverCursoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $curso = $this->route('curso');

        if (! $curso instanceof Curso || $curso->situacao !== SituacaoCurso::Publicado) {
            abort(404);
        }

        return $this->user()?->can('create', [Matricula::class, $curso]) === true;
    }

    public function rules(): array
    {
        return [];
    }
}
