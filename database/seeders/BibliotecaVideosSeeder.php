<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Acoes\Curso\PublicarCurso;
use App\Enums\ProvedorVideo;
use App\Enums\SituacaoAula;
use App\Enums\SituacaoCurso;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Organiza a biblioteca de vídeos reais (gravados fora da plataforma) em
 * módulos e aulas para cada produto da trilha "Aprenda na Prática".
 *
 * Aulas com video_id nascem publicadas; aulas sem video_id (vídeo ainda não
 * subiu no YouTube) nascem em rascunho — o campo "descricao" guarda o nome do
 * arquivo de origem, pra identificar qual vídeo é qual na hora de completar.
 * Assim que um produto ganha ao menos uma aula com vídeo, o curso é publicado
 * automaticamente (mesma regra de PublicarCurso usada no admin).
 */
class BibliotecaVideosSeeder extends Seeder
{
    public function run(): void
    {
        $responsavel = Usuario::role('admin')->first();
        $publicar = app(PublicarCurso::class);

        foreach ($this->biblioteca() as $slugCurso => $modulos) {
            $curso = Curso::query()->where('slug', $slugCurso)->first();

            if ($curso === null) {
                throw new RuntimeException("Curso '{$slugCurso}' não encontrado. Rode TrilhaProdutoSeeder antes.");
            }

            foreach ($modulos as $posicaoModulo => $dadosModulo) {
                $modulo = Modulo::query()->updateOrCreate(
                    ['curso_id' => $curso->id, 'titulo' => $dadosModulo['titulo']],
                    ['posicao' => $posicaoModulo],
                );

                foreach ($dadosModulo['aulas'] as $posicaoAula => $aula) {
                    $dados = [
                        'titulo' => $aula['titulo'],
                        'descricao' => "Arquivo de origem: {$aula['arquivo']}",
                        'posicao' => $posicaoAula,
                    ];

                    if ($aula['video_id'] !== null) {
                        $dados['provedor'] = ProvedorVideo::Youtube;
                        $dados['video_id'] = $aula['video_id'];
                        $dados['situacao'] = SituacaoAula::Publicada;
                        $dados['publicada_em'] = now();
                    }

                    $modulo->aulas()->updateOrCreate(['slug' => Str::slug($aula['titulo'])], $dados);
                }
            }

            if ($curso->situacao === SituacaoCurso::Rascunho) {
                $temAulaComVideo = $curso->modulos()->with('aulas')->get()
                    ->flatMap->aulas
                    ->contains(fn ($aula) => $aula->video_id !== null);

                if ($temAulaComVideo) {
                    $publicar->executar($curso, $responsavel);
                }
            }
        }
    }

    /** @return array<string, list<array{titulo: string, aulas: list<array{titulo: string, arquivo: string, video_id: string|null}>}>> */
    private function biblioteca(): array
    {
        return [
            'poder-o2' => [
                [
                    'titulo' => 'Remoção de manchas',
                    'aulas' => [
                        ['titulo' => 'Manchas em blusa branca', 'arquivo' => '23 Poder O2 Blusa Branca.MOV', 'video_id' => 'JoJh4zjI6-E'],
                        ['titulo' => 'Manchas em tênis branco', 'arquivo' => 'Poder 02 tênis branco.MOV', 'video_id' => 'TQjlMFLM42I'],
                        ['titulo' => 'Mancha de molho', 'arquivo' => 'Reels poder O2 mancha de molho.MOV', 'video_id' => '3fNP5NfRq4A'],
                    ],
                ],
            ],
            'multiuso' => [
                [
                    'titulo' => 'Limpeza do dia a dia',
                    'aulas' => [
                        ['titulo' => 'Limpeza do banheiro', 'arquivo' => '26 Multiuso Banheiro.MOV', 'video_id' => null],
                        ['titulo' => 'Álcool e bicarbonato', 'arquivo' => 'Reels álcool e bicarbonato.MOV', 'video_id' => 'aL8TSm-RQ6U'],
                        ['titulo' => 'Mancha em uniforme', 'arquivo' => 'Reels Mancha Uniforme.MOV', 'video_id' => 'hTxNunR-I1U'],
                    ],
                ],
            ],
            'desengordurante' => [
                [
                    'titulo' => 'Cozinha sem gordura',
                    'aulas' => [
                        ['titulo' => 'Desengordurante no dia a dia', 'arquivo' => '22 Desengordurante versão sem link.MOV', 'video_id' => null],
                        ['titulo' => 'Desengordurante — versão alternativa 1', 'arquivo' => 'Desengordurante sem link.MOV', 'video_id' => 'OGqsbNiQa30'],
                        ['titulo' => 'Desengordurante — versão alternativa 2', 'arquivo' => 'Desengordurante versão sem click no link.MOV', 'video_id' => 'M30pGAv-XEQ'],
                        ['titulo' => 'Limpando a grelha do fogão', 'arquivo' => 'Reels Grelha Desengordurante.MOV', 'video_id' => '-adMSeMXoB0'],
                    ],
                ],
            ],
            'lava-roupas' => [
                [
                    'titulo' => 'Roupas limpas',
                    'aulas' => [
                        ['titulo' => 'Lava roupas e amaciante', 'arquivo' => 'Lava roupas Amaciante feed (1).MOV', 'video_id' => 'HR9_Zo8dNx4'],
                    ],
                ],
            ],
            'amaciantes' => [
                [
                    'titulo' => 'Roupas macias e perfumadas',
                    'aulas' => [
                        ['titulo' => 'Lava roupas e amaciante', 'arquivo' => 'Lava roupas Amaciante feed (1).MOV', 'video_id' => null],
                    ],
                ],
            ],
            'aromatizador-de-ambientes' => [
                [
                    'titulo' => 'Ambientes perfumados',
                    'aulas' => [
                        ['titulo' => 'Aromatizadores', 'arquivo' => '28 Aromatizadores.MOV', 'video_id' => null],
                        ['titulo' => 'Aromatizador — versão alternativa', 'arquivo' => 'Aromatizador versão sem link (1).MOV', 'video_id' => null],
                    ],
                ],
            ],
        ];
    }
}
