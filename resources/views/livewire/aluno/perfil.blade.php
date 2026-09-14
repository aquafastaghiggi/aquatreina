<div>
    <div class="flex items-end justify-between gap-4"><div><p class="text-sm text-marca">Sua conta</p><h1 class="mt-1 text-3xl font-semibold">Perfil</h1></div><a href="{{ route('app.painel') }}" class="link">Minha área</a></div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <section class="rounded-lg border border-linha bg-superficie p-6">
            <h2 class="text-xl font-semibold">Dados profissionais</h2>
            @if (session('sucesso_perfil'))<div class="sucesso mt-4">{{ session('sucesso_perfil') }}</div>@endif
            <form wire:submit="salvarPerfil" class="mt-5 space-y-4">
                <label class="campo">Nome<input wire:model="nome" required></label>
                <label class="campo">Telefone<input wire:model="telefone"></label>
                <label class="campo">Empresa<input wire:model="empresa"></label>
                <label class="campo">Cargo<input wire:model="cargo"></label>
                <label class="campo">Foto<input type="file" wire:model="foto" accept="image/*"></label>
                @error('foto')<p class="erro">{{ $message }}</p>@enderror
                <button class="botao-primario" type="submit">Salvar dados</button>
            </form>
        </section>

        <div class="space-y-6">
            <section class="rounded-lg border border-linha bg-superficie p-6">
                <h2 class="text-xl font-semibold">Trocar senha</h2>
                @if (session('sucesso_senha'))<div class="sucesso mt-4">{{ session('sucesso_senha') }}</div>@endif
                <form wire:submit="trocarSenha" class="mt-5 space-y-4">
                    <label class="campo">Senha atual<input type="password" wire:model="senhaAtual" autocomplete="current-password"></label>
                    <label class="campo">Nova senha<input type="password" wire:model="novaSenha" autocomplete="new-password"></label>
                    <label class="campo">Confirmar nova senha<input type="password" wire:model="novaSenha_confirmation" autocomplete="new-password"></label>
                    <button class="botao-secundario" type="submit">Atualizar senha</button>
                </form>
            </section>

            <section class="rounded-lg border border-linha bg-superficie p-6">
                <h2 class="text-xl font-semibold">Dados pessoais guardados</h2>
                <dl class="mt-5 grid gap-3 text-sm">
                    <div><dt class="text-texto-3">E-mail</dt><dd>{{ $this->usuario->email }}</dd></div>
                    <div><dt class="text-texto-3">Situação da conta</dt><dd>{{ $this->usuario->situacao->rotulo() }}</dd></div>
                    <div><dt class="text-texto-3">Organização vinculada</dt><dd>{{ $this->usuario->organizacao?->nome ?? 'Nenhuma' }}</dd></div>
                    <div><dt class="text-texto-3">Termos aceitos em</dt><dd>{{ $this->usuario->termos_aceitos_em?->format('d/m/Y H:i') ?? 'Não registrado' }}</dd></div>
                    <div><dt class="text-texto-3">IP do aceite</dt><dd>{{ $this->usuario->termos_ip ?? 'Não registrado' }}</dd></div>
                    <div><dt class="text-texto-3">Último acesso</dt><dd>{{ $this->usuario->ultimo_acesso_em?->format('d/m/Y H:i') ?? 'Não registrado' }}</dd></div>
                    <div><dt class="text-texto-3">Matrículas</dt><dd>{{ $this->usuario->matriculas()->count() }}</dd></div>
                </dl>
                <a class="botao-secundario mt-5 inline-flex" href="{{ route('app.dados.exportar') }}">Exportar meus dados (JSON)</a>
            </section>
        </div>
    </div>
</div>
