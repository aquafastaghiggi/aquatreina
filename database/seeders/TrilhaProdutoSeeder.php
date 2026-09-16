<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\NivelCurso;
use App\Enums\SituacaoCurso;
use App\Models\Categoria;
use App\Models\Curso;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\Permission\Models\Role;

class TrilhaProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $responsavel = Usuario::role('admin')->first();

        if ($responsavel === null) {
            throw new RuntimeException('Nenhum usuário com papel admin encontrado. Rode UsuarioAdminSeeder antes.');
        }

        Role::findOrCreate('admin', 'web');

        $categoria = Categoria::query()->updateOrCreate(
            ['slug' => config('treina.categoria_trilhas_produto_slug')],
            ['nome' => 'Aprenda na Prática', 'posicao' => 0],
        );

        foreach ($this->produtos() as $posicao => $produto) {
            $curso = Curso::query()->updateOrCreate(
                ['slug' => Str::slug($produto['titulo'])],
                [
                    'categoria_id' => $categoria->id,
                    'responsavel_id' => $responsavel->id,
                    'titulo' => $produto['titulo'],
                    'subtitulo' => $produto['subtitulo'],
                    'descricao' => $produto['descricao'],
                    'nivel' => NivelCurso::Basico,
                    'posicao' => $posicao,
                ],
            );

            // Situação nasce rascunho de propósito: sem foto real do produto nem
            // vídeo, o marketing completa via construtor de currículo e publica
            // quando o conteúdo real estiver pronto (RN-09).
            if ($curso->situacao === null) {
                $curso->situacao = SituacaoCurso::Rascunho;
                $curso->save();
            }
        }
    }

    /** @return list<array{titulo: string, subtitulo: string, descricao: string}> */
    private function produtos(): array
    {
        return [
            [
                'titulo' => 'Poder O2',
                'subtitulo' => 'Tira manchas com o poder do oxigênio ativo',
                'descricao' => $this->roteiro(
                    problema: 'Manchas difíceis em tecidos brancos e coloridos que o sabão comum não resolve.',
                    diferenciais: 'Ação com oxigênio ativo, seguro para cores, sem cloro.',
                    uso: 'Dilua conforme a embalagem e aplique direto na mancha antes de lavar, ou adicione à máquina de lavar.',
                    ideias: 'Grave o antes e depois de uma peça manchada — tênis branco, camiseta, toalha de cozinha.',
                ),
            ],
            [
                'titulo' => 'Lavagem Rápida',
                'subtitulo' => 'Roupa limpa e perfumada em menos tempo',
                'descricao' => $this->roteiro(
                    problema: 'Falta de tempo para ciclos longos de lavagem no dia a dia corrido.',
                    diferenciais: 'Fórmula concentrada que age rápido sem perder poder de limpeza.',
                    uso: 'Use na quantidade indicada em ciclos curtos da máquina de lavar.',
                    ideias: 'Mostre uma rotina corrida (trabalho, filhos, academia) e como o produto encaixa nela.',
                ),
            ],
            [
                'titulo' => 'Multiuso',
                'subtitulo' => 'Uma solução para várias superfícies da casa',
                'descricao' => $this->roteiro(
                    problema: 'Precisar de vários produtos diferentes para limpar a casa toda.',
                    diferenciais: 'Limpa e desinfeta várias superfícies com um só produto.',
                    uso: 'Borrife diretamente na superfície e limpe com um pano — sem enxágue na maioria dos casos.',
                    ideias: 'Sequência de limpeza de uma cozinha ou banheiro inteiro usando só esse produto.',
                ),
            ],
            [
                'titulo' => 'Desengordurante',
                'subtitulo' => 'Corta a gordura pesada da cozinha',
                'descricao' => $this->roteiro(
                    problema: 'Gordura acumulada em fogão, coifa e superfícies da cozinha.',
                    diferenciais: 'Ação rápida em gordura pesada, sem esforço excessivo.',
                    uso: 'Aplique sobre a superfície engordurada, deixe agir alguns minutos e remova com um pano.',
                    ideias: 'Antes e depois de um fogão ou coifa bem engordurados.',
                ),
            ],
            [
                'titulo' => 'Lava Roupas',
                'subtitulo' => 'Limpeza profunda para o dia a dia',
                'descricao' => $this->roteiro(
                    problema: 'Roupas do dia a dia (uniforme, escola, academia) que precisam de limpeza confiável.',
                    diferenciais: 'Boa relação entre poder de limpeza e economia no uso diário.',
                    uso: 'Siga a dosagem indicada na embalagem conforme o tipo e a quantidade de roupa.',
                    ideias: 'Mostre a rotina de lavagem de uma família ou de roupas de trabalho pesadas.',
                ),
            ],
            [
                'titulo' => 'Amaciantes',
                'subtitulo' => 'Maciez e perfume que duram',
                'descricao' => $this->roteiro(
                    problema: 'Roupa áspera ou sem perfume duradouro depois de lavada.',
                    diferenciais: 'Perfume de longa duração e toque macio nas roupas.',
                    uso: 'Adicione no compartimento do amaciante durante o ciclo de lavagem.',
                    ideias: 'Compare o toque da roupa antes e depois, e destaque o perfume que fica no tecido.',
                ),
            ],
        ];
    }

    private function roteiro(string $problema, string $diferenciais, string $uso, string $ideias): string
    {
        return implode("\n\n", [
            "<p><strong>Qual problema o produto ajuda a resolver:</strong> {$problema}</p>",
            "<p><strong>Principais diferenciais:</strong> {$diferenciais}</p>",
            "<p><strong>Como utilizar corretamente:</strong> {$uso}</p>",
            "<p><strong>Ideias para criar conteúdo:</strong> {$ideias}</p>",
        ]);
    }
}
