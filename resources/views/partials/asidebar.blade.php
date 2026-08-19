<aside class="w-64 bg-emerald-700 border-r border-slate-200 flex flex-col space-y-8">
    <div class="bg-emerald-500 p-6 h-32 flex items-center">
        <div>
            <h1 class="font-bold text-xl leading-none text-white">SIGEP</h1>
            <p class="text-[10px] text-emerald-950 uppercase">Sistema de Gestao da Etica Publica</p>
        </div>
    </div>

    <nav class="flex-1 space-y-2 text-sm font-medium">
        <a href="{{ route('dashboard') }}" class="flex items-center p-3 {{ request()->routeIs('dashboard') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Dashboard</a>

        @if($usuario === 'admin')
            <a href="{{ route('processos.index') }}" class="flex items-center p-3 {{ request()->routeIs('processos.index') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Processos</a>
            <a href="{{ route('processos.create') }}" class="flex items-center p-3 {{ request()->routeIs('processos.create') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Criar votacao</a>
            <a href="{{ route('usuarios.list') }}" class="flex items-center p-3 {{ request()->routeIs('usuarios.*') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Usuarios</a>
            <a href="{{ route('resultados') }}" class="flex items-center p-3 {{ request()->routeIs('resultados') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Resultados</a>
        @elseif($usuario === 'membro')
            <a href="{{ route('processos.index') }}" class="flex items-center p-3 {{ request()->routeIs('processos.*') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Processos disponiveis</a>
            <a href="{{ route('perfil') }}" class="flex items-center p-3 {{ request()->routeIs('perfil') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Perfil</a>
        @endif

        <hr>
        <a href="{{ route('configuracoes') }}" class="flex items-center p-3 {{ request()->routeIs('configuracoes') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Configuracoes</a>
    </nav>
</aside>
