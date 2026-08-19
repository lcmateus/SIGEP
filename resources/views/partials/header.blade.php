<header class="flex justify-between items-center p-8 mb-5 bg-white hover:shadow-sm transition-shadow">
    <h2 class="text-2xl font-bold text-emerald-900 uppercase">Dashboard</h2>
    <div class="flex items-center space-x-4">
        <button class="text-emerald-900 font-bold hover:underline">{{$usuario}}</button>
        <span class="text-slate-400">|</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-left text-red-600 font-bold hover:underline">Sair</button>
        </form>
    </div>
</header>
