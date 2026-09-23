<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\SituacaoUsuario;
use App\Models\Usuario;
use App\Servicos\Video\ExtratorIdVideo;
use App\Servicos\Video\LeitorMetadadosVideo;
use App\Servicos\Video\ProvedorVideo;
use App\Servicos\Video\YoutubeProvedor;
use Illuminate\Contracts\Cache\Factory as FabricaCache;
use Illuminate\Http\Client\Factory as ClienteHttp;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LeitorMetadadosVideo::class, fn ($app): LeitorMetadadosVideo => new LeitorMetadadosVideo(
            $app->make(ClienteHttp::class),
            $app->make(FabricaCache::class),
            config('services.youtube.api_key'),
        ));

        $this->app->singleton(YoutubeProvedor::class, fn ($app): YoutubeProvedor => new YoutubeProvedor(
            $app->make(ExtratorIdVideo::class),
            $app->make(LeitorMetadadosVideo::class),
            (string) config('app.url'),
        ));

        $this->app->bind(ProvedorVideo::class, YoutubeProvedor::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->isProduction() || config('seguranca.forcar_https')) {
            URL::forceScheme('https');
            URL::forceRootUrl((string) config('app.url'));
        }

        Password::defaults(fn (): Password => Password::min(8));

        Gate::define('acessar-admin', fn (Usuario $usuario): bool => $usuario->situacao === SituacaoUsuario::Ativo
            && $usuario->hasAnyRole(['admin', 'instrutor']));
    }
}
