<aside class="w-64 bg-emerald-700 border-r border-slate-200 flex flex-col space-y-8">
    <div class = "bg-emerald-500 p-6 h-32 flex items-center">
        <div class="flex items-center space-x-3">
            <div class="grid grid-cols-3 gap-0.5 w-8">
                <div class="h-2 w-2 bg-red-600"></div><div class="h-2 w-2 bg-green-600"></div><div class="h-2 w-2 bg-green-600"></div>
                <div class="h-2 w-2 bg-green-600"></div><div class="h-2 w-2 bg-green-600"></div><div class="h-2 w-2 bg-green-600"></div>
            </div>
            <div>
                <h1 class="font-bold text-xl leading-none">SIGEP</h1>
                <p class="text-[10px] text-slate-500 uppercase">Gestão da Ética Pública</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 space-y-2 text-sm font-medium">
        <a href="{{ route('dashboard') }}" class="flex items-center p-3 {{ request()->routeIs('dashboard') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Dashboard</a>

        @if($usuario == 'admin')
        <a href="{{ route('processos.create') }}" class="flex items-center p-3 {{ request()->routeIs('processos.*') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Processos</a>
        <a href="{{ route('processos.create') }}" class="flex items-center p-3 {{ request()->routeIs('processos.create') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Criar votação</a>
        <a href="{{ route('usuarios.list') }}" class="flex items-center p-3 {{ request()->routeIs('usuarios.*') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Usuários</a>
        <a href="{{ route('resultados') }}" class="flex items-center p-3 {{ request()->routeIs('resultados') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Resultados</a>
        @elseif($usuario == 'usuario')
        <a href="{{ route('votacao') }}" class="flex items-center p-3 {{ request()->routeIs('votacao') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Processos disponíveis</a>
        <a href="{{ route('perfil') }}" class="flex items-center p-3 {{ request()->routeIs('perfil') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Perfil</a>
        @endif

        <hr>
        <a href="{{ route('configuracoes') }}" class="flex items-center p-3 {{ request()->routeIs('configuracoes') ? 'bg-emerald-200 text-emerald-700' : 'text-white hover:bg-emerald-900' }}">Configurações</a>
    </nav>
</aside>

