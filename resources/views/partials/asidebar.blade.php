<aside class="w-64 bg-emerald-700 border-r border-slate-200 flex flex-col space-y-8">
    <div class="bg-emerald-500 p-6 h-32 flex items-center">
        <div>
            <img src="{{ asset('images/logo-ifpr.png') }}"
            <h1 class="font-bold text-xl leading-none text-white">SIGEP</h1>
            <p class="text-[10px] text-emerald-950 uppercase">Sistema de Gestao da Etica Publica</p>
        </div>
    </div>

    <nav class="flex-1 space-y-2 text-sm font-medium">
        <a href="{{ route('dashboard') }}" class="flex items-center p-3 {{ request()->routeIs('dashboard') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Dashboard</a>

        @if($usuario === 'admin')

            <div class="px-3">
                <button type="button" class="flex items-center justify-between w-full p-3 text-white hover:bg-emerald-900 rounded-lg transition-colors"
                        data-dropdown-toggle="processos-dropdown"
                        aria-expanded="false" aria-controls="processos-dropdown">
                    <span>Processos</span>
                    <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="processos-dropdown" class="hidden mt-1 ml-4 space-y-1 border-l-2 border-emerald-400 pl-3" role="menu">
                    <a href="{{ route('processos.create') }}" class="block px-3 py-2 text-sm text-white hover:bg-emerald-900 rounded-md" role="menuitem">Criar Processo</a>
                    <a href="{{ route('processos.devolvidos') }}" class="block px-3 py-2 text-sm text-white hover:bg-emerald-900 rounded-md" role="menuitem">Devolvidos</a>
                    <a href="{{ route('processos.publicos') }}" class="block px-3 py-2 text-sm text-white hover:bg-emerald-900 rounded-md" role="menuitem">Processos Públicos</a>
                    <a href="{{ route('processos.arquivados') }}" class="block px-3 py-2 text-sm text-white hover:bg-emerald-900 rounded-md" role="menuitem">Arquivados</a>
                </div>
            </div>

            <a href="{{ route('usuarios.list') }}" class="flex items-center p-3 {{ request()->routeIs('usuarios.*') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Usuarios</a>
            <a href="{{ route('resultados') }}" class="flex items-center p-3 {{ request()->routeIs('resultados') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Resultados</a>

            <div class="px-3">
                <button type="button" class="flex items-center justify-between w-full p-3 text-white hover:bg-emerald-900 rounded-lg transition-colors"
                        data-dropdown-toggle="votacoes-dropdown-admin"
                        aria-expanded="false" aria-controls="votacoes-dropdown-admin">
                    <span>Votações</span>
                    <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="votacoes-dropdown-admin" class="hidden mt-1 ml-4 space-y-1 border-l-2 border-emerald-400 pl-3" role="menu">
                    <a href="{{ route('votacoes.abertas') }}" class="block px-3 py-2 text-sm text-white hover:bg-emerald-900 rounded-md" role="menuitem">Votações Abertas</a>
                </div>
            </div>
        @elseif($usuario === 'membro')

            <div class="px-3">
                <button type="button" class="flex items-center justify-between w-full p-3 text-white hover:bg-emerald-900 rounded-lg transition-colors"
                        data-dropdown-toggle="processos-dropdown-membro"
                        aria-expanded="false" aria-controls="processos-dropdown-membro">
                    <span>Processos</span>
                    <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="processos-dropdown-membro" class="hidden mt-1 ml-4 space-y-1 border-l-2 border-emerald-400 pl-3" role="menu">
                    <a href="{{ route('processos.meus') }}" class="block px-3 py-2 text-sm text-white hover:bg-emerald-900 rounded-md" role="menuitem">Meus Processos</a>
                    <a href="{{ route('processos.arquivados') }}" class="block px-3 py-2 text-sm text-white hover:bg-emerald-900 rounded-md" role="menuitem">Arquivados</a>
                    <a href="{{ route('processos.publicos') }}" class="block px-3 py-2 text-sm text-white hover:bg-emerald-900 rounded-md" role="menuitem">Processos Públicos</a>
                </div>
            </div>

            <div class="px-3">
                <button type="button" class="flex items-center justify-between w-full p-3 text-white hover:bg-emerald-900 rounded-lg transition-colors"
                        data-dropdown-toggle="votacoes-dropdown-membro"
                        aria-expanded="false" aria-controls="votacoes-dropdown-membro">
                    <span>Votações</span>
                    <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="votacoes-dropdown-membro" class="hidden mt-1 ml-4 space-y-1 border-l-2 border-emerald-400 pl-3" role="menu">
                    <a href="{{ route('votacoes.disponiveis') }}" class="block px-3 py-2 text-sm text-white hover:bg-emerald-900 rounded-md" role="menuitem">Votações Disponíveis</a>
                    <a href="#" class="block px-3 py-2 text-sm text-white hover:bg-emerald-900 rounded-md" role="menuitem">Minerva</a>
                </div>
            </div>

        @endif

<a href="{{ route('perfil') }}" class="flex items-center p-3 {{ request()->routeIs('perfil') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Perfil</a>

        <hr class="mx-3 border-emerald-400">
        <a href="{{ route('configuracoes') }}" class="flex items-center p-3 {{ request()->routeIs('configuracoes') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Configuracoes</a>
    </nav>
</aside>
@push('scripts')
<script>
    (function () {
        var toggles = document.querySelectorAll('[data-dropdown-toggle]');

        if (!toggles.length) return;

        function fechar(btn, dropdown, icon) {
            dropdown.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
            if (icon) icon.style.transform = 'rotate(0deg)';
        }

        toggles.forEach(function (btn) {
            var dropdown = document.getElementById(btn.getAttribute('data-dropdown-toggle'));
            var icon = btn.querySelector('svg');

            if (!dropdown) return;

            btn.addEventListener('click', function () {
                var isOpen = !dropdown.classList.contains('hidden');
                dropdown.classList.toggle('hidden');
                btn.setAttribute('aria-expanded', !isOpen);
                if (icon) icon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
            });

            document.addEventListener('click', function (e) {
                if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                    fechar(btn, dropdown, icon);
                }
            });
        });
    })();
</script>
@endpush
