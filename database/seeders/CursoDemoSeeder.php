<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\NivelCurso;
use App\Enums\OrigemMatricula;
use App\Enums\ProvedorVideo;
use App\Enums\SituacaoAula;
use App\Enums\SituacaoCurso;
use App\Enums\SituacaoMatricula;
use App\Enums\SituacaoUsuario;
use App\Models\Categoria;
use App\Models\Curso;
use App\Models\Matricula;
use App\Models\ProgressoAula;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CursoDemoSeeder extends Seeder
{
    public function run(): void
    {
        $senhaDemo = env('DEMO_ALUNO_PASSWORD', env('ADMIN_PASSWORD'));

        if (! is_string($senhaDemo) || $senhaDemo === '') {
            $senhaDemo = Str::random(40);
        }

        $responsavel = Usuario::role('admin')->firstOrFail();
        $produtos = Categoria::query()->where('slug', 'produtos')->firstOrFail();
        $operacao = Categoria::query()->where('slug', 'operacao')->firstOrFail();

        $armazenamento = $this->criarCurso(
            $responsavel,
            $produtos,
            'Fundamentos dos produtos Aquafast',
            'fundamentos-produtos-demo',
            'Conheça aplicações, cuidados e boas práticas dos produtos.',
            0,
        );
        $atendimento = $this->criarCurso(
            $responsavel,
            $operacao,
            'Atendimento e operação segura',
            'atendimento-operacao-demo',
            'Orientações essenciais para atender e operar com segurança.',
            1,
        );

        $this->criarModuloComAulas($armazenamento, 'Conhecendo a linha', 0, [
            'Visão geral dos produtos', 'Aplicações mais comuns', 'Como orientar o cliente',
        ], true);
        $this->criarModuloComAulas($armazenamento, 'Cuidados essenciais', 1, [
            'Armazenamento correto', 'Transporte e manuseio', 'Dúvidas frequentes',
        ]);
        $this->criarModuloComAulas($atendimento, 'Rotina de atendimento', 0, [
            'Preparação do atendimento', 'Conferência operacional', 'Encerramento seguro',
        ], true);

        foreach (range(1, 5) as $numero) {
            $aluno = Usuario::query()->updateOrCreate(
                ['email' => "aluno.demo{$numero}@example.test"],
                [
                    'nome' => "Aluno demonstração {$numero}",
                    'password' => Hash::make($senhaDemo),
                    'situacao' => SituacaoUsuario::Ativo,
                    'email_verified_at' => now(),
                    'empresa' => 'Empresa demonstrativa',
                ],
            );
            $aluno->syncRoles(['aluno']);

            if ($numero <= 3) {
                $matricula = Matricula::query()->firstOrCreate(
                    ['usuario_id' => $aluno->id, 'curso_id' => $armazenamento->id],
                    ['origem' => OrigemMatricula::Admin, 'situacao' => SituacaoMatricula::Ativa, 'matriculado_em' => now()],
                );
                $this->criarProgressoDemo($matricula, $armazenamento, $numero);
            }
        }

        Cache::forget('catalogo:publicados');
    }

    private function criarProgressoDemo(Matricula $matricula, Curso $curso, int $perfil): void
    {
        $aulas = $curso->modulos()->with('aulas')->get()->flatMap->aulas->values();

        foreach ($aulas as $indice => $aula) {
            if ($perfil === 2 || ($perfil === 1 && $indice < 2)) {
                ProgressoAula::query()->updateOrCreate(
                    ['matricula_id' => $matricula->id, 'aula_id' => $aula->id],
                    [
                        'segundos_assistidos' => $aula->duracao_segundos,
                        'posicao_maxima' => $aula->duracao_segundos,
                        'primeira_visualizacao_em' => now()->subDays(3),
                        'concluido_em' => now()->subDays(2),
                    ],
                );

                continue;
            }

            if (($perfil === 1 && $indice === 2) || ($perfil === 3 && $indice === 0)) {
                ProgressoAula::query()->updateOrCreate(
                    ['matricula_id' => $matricula->id, 'aula_id' => $aula->id],
                    [
                        'segundos_assistidos' => 120,
                        'posicao_maxima' => 120,
                        'primeira_visualizacao_em' => now()->subDay(),
                        'concluido_em' => null,
                    ],
                );
            }
        }

        $matricula->update(match ($perfil) {
            1 => [
                'situacao' => SituacaoMatricula::Ativa,
                'percentual_progresso' => 33,
                'ultima_aula_id' => $aulas->get(2)?->id,
                'concluido_em' => null,
            ],
            2 => [
                'situacao' => SituacaoMatricula::Concluida,
                'percentual_progresso' => 100,
                'ultima_aula_id' => $aulas->last()?->id,
                'concluido_em' => now()->subDays(2),
            ],
            default => [
                'situacao' => SituacaoMatricula::Ativa,
                'percentual_progresso' => 0,
                'ultima_aula_id' => $aulas->first()?->id,
                'concluido_em' => null,
            ],
        });
    }

    private function criarCurso(
        Usuario $responsavel,
        Categoria $categoria,
        string $titulo,
        string $slug,
        string $subtitulo,
        int $posicao,
    ): Curso {
        return Curso::query()->updateOrCreate(['slug' => $slug], [
            'categoria_id' => $categoria->id,
            'responsavel_id' => $responsavel->id,
            'titulo' => $titulo,
            'subtitulo' => $subtitulo,
            'descricao' => '<p>Conteúdo demonstrativo para validar a experiência da plataforma.</p>',
            'nivel' => NivelCurso::Basico,
            'situacao' => SituacaoCurso::Publicado,
            'total_aulas' => $slug === 'fundamentos-produtos-demo' ? 6 : 3,
            'minutos_estimados' => $slug === 'fundamentos-produtos-demo' ? 30 : 15,
            'posicao' => $posicao,
            'publicado_em' => now(),
        ]);
    }

    /** @param list<string> $titulos */
    private function criarModuloComAulas(
        Curso $curso,
        string $titulo,
        int $posicao,
        array $titulos,
        bool $primeiraAmostra = false,
    ): void {
        $modulo = $curso->modulos()->updateOrCreate(['posicao' => $posicao], ['titulo' => $titulo]);

        foreach ($titulos as $posicaoAula => $tituloAula) {
            // IDs públicos são placeholders e devem ser trocados pelos vídeos do canal Aquafast.
            $modulo->aulas()->updateOrCreate(['slug' => Str::slug($tituloAula)], [
                'titulo' => $tituloAula,
                'provedor' => ProvedorVideo::Youtube,
                'video_id' => 'M7lc1UVf-VE',
                'duracao_segundos' => 300,
                'amostra_gratuita' => $primeiraAmostra && $posicaoAula === 0,
                'situacao' => SituacaoAula::Publicada,
                'posicao' => $posicaoAula,
                'publicada_em' => now(),
            ]);
        }
    }
}
