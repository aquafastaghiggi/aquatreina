<?php

declare(strict_types=1);

namespace App\Servicos\Importacao;

use App\Enums\OrigemMatricula;
use App\Enums\SituacaoMatricula;
use App\Enums\SituacaoUsuario;
use App\Models\Curso;
use App\Models\Organizacao;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

final class ImportadorUsuarios
{
    /** @return list<array{linha: int, dados: array<string, string>, erros: list<string>}> */
    public function previsualizar(string $caminho): array
    {
        $arquivo = fopen($caminho, 'rb');
        if ($arquivo === false) {
            throw new \RuntimeException('Não foi possível abrir o CSV.');
        }
        $cabecalho = array_map(fn ($item) => trim((string) $item), fgetcsv($arquivo) ?: []);
        $esperado = ['nome', 'email', 'telefone', 'empresa', 'cargo', 'organizacao', 'cursos'];
        if ($cabecalho !== $esperado) {
            fclose($arquivo);
            throw new \InvalidArgumentException('Cabeçalho inválido. Use o modelo disponível para download.');
        }

        $resultado = [];
        $numero = 1;
        while (($valores = fgetcsv($arquivo)) !== false) {
            $numero++;
            $valores = array_pad(array_map(fn ($item) => trim((string) $item), $valores), count($esperado), '');
            $dados = array_combine($esperado, array_slice($valores, 0, count($esperado)));
            if ($dados === false || count(array_filter($dados)) === 0) {
                continue;
            }
            $erros = $this->validar($dados);
            $resultado[] = ['linha' => $numero, 'dados' => $dados, 'erros' => $erros];
        }
        fclose($arquivo);

        return $resultado;
    }

    /** @return array{importados: int, falhas: list<array{linha: int, erros: list<string>}>} */
    public function importar(string $caminho): array
    {
        $importados = 0;
        $falhas = [];
        foreach ($this->previsualizar($caminho) as $linha) {
            if ($linha['erros'] !== []) {
                $falhas[] = ['linha' => $linha['linha'], 'erros' => $linha['erros']];

                continue;
            }
            try {
                $novo = false;
                $usuario = DB::transaction(function () use ($linha, &$novo): Usuario {
                    $dados = $linha['dados'];
                    $organizacao = $dados['organizacao'] === '' ? null : Organizacao::query()->where('nome', $dados['organizacao'])->first();
                    $usuario = Usuario::query()->where('email', mb_strtolower($dados['email']))->first();
                    if ($usuario === null) {
                        $novo = true;
                        $usuario = Usuario::query()->create([
                            'nome' => $dados['nome'], 'email' => mb_strtolower($dados['email']),
                            'telefone' => $dados['telefone'] ?: null, 'empresa' => $dados['empresa'] ?: null,
                            'cargo' => $dados['cargo'] ?: null, 'organizacao_id' => $organizacao?->id,
                            'situacao' => SituacaoUsuario::Ativo, 'password' => Str::password(24),
                        ]);
                        $usuario->assignRole('aluno');
                    }
                    $cursos = Curso::query()->whereIn('slug', $this->slugs($dados['cursos']))->get();
                    foreach ($cursos as $curso) {
                        $usuario->matriculas()->firstOrCreate(['curso_id' => $curso->id], [
                            'origem' => OrigemMatricula::Importacao, 'situacao' => SituacaoMatricula::Ativa,
                            'matriculado_em' => now(),
                        ]);
                    }

                    return $usuario;
                });
                if ($novo) {
                    Password::broker()->sendResetLink(['email' => $usuario->email]);
                }
                $importados++;
            } catch (\Throwable $erro) {
                report($erro);
                $falhas[] = ['linha' => $linha['linha'], 'erros' => ['Falha ao persistir a linha.']];
            }
        }

        return compact('importados', 'falhas');
    }

    /** @param array<string, string> $dados
     * @return list<string>
     */
    private function validar(array $dados): array
    {
        $validador = Validator::make($dados, [
            'nome' => ['required', 'string', 'max:160'], 'email' => ['required', 'email', 'max:190'],
            'telefone' => ['nullable', 'max:20'], 'empresa' => ['nullable', 'max:160'],
            'cargo' => ['nullable', 'max:120'], 'organizacao' => ['nullable', 'exists:organizacoes,nome'],
        ]);
        $erros = $validador->errors()->all();
        $informados = $this->slugs($dados['cursos']);
        if (Curso::query()->whereIn('slug', $informados)->count() !== count($informados)) {
            $erros[] = 'Um ou mais slugs de curso não existem.';
        }

        return $erros;
    }

    /** @return list<string> */
    private function slugs(string $valor): array
    {
        return array_values(array_unique(array_filter(array_map('trim', explode(';', $valor)))));
    }
}
