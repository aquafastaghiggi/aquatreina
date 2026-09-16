<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Configuracao;
use App\Models\TextoLegal;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Configuracoes extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Configurações';

    protected static ?string $slug = 'configuracoes';

    protected string $view = 'filament.pages.configuracoes';

    public int $percentualConclusao = 90;

    public int $intervaloPing = 10;

    public string $textoBoasVindas = '';

    public string $termos = '';

    public string $privacidade = '';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') === true;
    }

    public function mount(): void
    {
        $this->percentualConclusao = (int) Configuracao::valor('percentual_conclusao', 90);
        $this->intervaloPing = (int) Configuracao::valor('intervalo_ping', 10);
        $this->textoBoasVindas = (string) Configuracao::valor('texto_boas_vindas', '');
        $this->termos = (string) (TextoLegal::vigente('termos')?->conteudo ?? 'Termos de uso do Aquafast Treina.');
        $this->privacidade = (string) (TextoLegal::vigente('privacidade')?->conteudo ?? 'Política de privacidade do Aquafast Treina.');
    }

    public function salvar(): void
    {
        $dados = $this->validate([
            'percentualConclusao' => ['required', 'integer', 'min:1', 'max:100'],
            'intervaloPing' => ['required', 'integer', 'min:5', 'max:120'], 'textoBoasVindas' => ['nullable', 'string', 'max:1000'],
            'termos' => ['required', 'string'], 'privacidade' => ['required', 'string'],
        ]);

        Configuracao::definir('percentual_conclusao', $dados['percentualConclusao']);
        Configuracao::definir('intervalo_ping', $dados['intervaloPing']);
        Configuracao::definir('texto_boas_vindas', $dados['textoBoasVindas']);
        $this->versionar('termos', $dados['termos']);
        $this->versionar('privacidade', $dados['privacidade']);

        activity()->causedBy(auth()->user())->log('Configurações atualizadas');

        Notification::make()->title('Configurações salvas')->success()->send();
    }

    private function versionar(string $tipo, string $conteudo): void
    {
        $vigente = TextoLegal::vigente($tipo);
        if ($vigente?->conteudo === $conteudo) {
            return;
        }
        TextoLegal::query()->create(['tipo' => $tipo, 'versao' => ($vigente?->versao ?? 0) + 1, 'conteudo' => $conteudo, 'publicado_em' => now()]);
    }
}
