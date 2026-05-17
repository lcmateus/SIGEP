@extends('layouts/main_layout')

@section('titulo', 'Exibir processos')

@section('content')

<div class = "flex min-h-screen">
    @include('partials.asidebar', ['usuario' => 'Admin']) 
    <main class="flex-1 bg-gray-200">

        @include('partials.header', ['titulo' => 'CRIAR VOTAÇÃO', 'usuario' => 'Admin'])

        <div class="h-1/4 bg-white border border-slate-200 rounded-xl shadow-sm p-6 m-8 flex flex-row hover:shadow-lg transition-shadow">
            <div class = "w-1/4 h- bg-red-300 border rounded-lg">
                
            </div>
            <div class = "flex flex-col ml-6">
                <label class="font-bold text-emerald-900 text-xl mb-4">Reforma do Auditório</label>
                <label class="text-slate-600 text-md font-bold leading-relaxed mb-6">Descrição da votação</label>
                <label class="text-md text-slate-400 mt-auto">Encerramento em: 28 de abril de 2026 às 23:59</label>
            </div>
            
            <div class = "flex flex-col justify-between items-end ml-auto">
                <span class="bg-green-600 px-5 rounded-md text-md font-semibold tracking-wide text-white">
                    Ativa
                </span>
                <a href="#" class="text-center bg-blue-600 hover:bg-blue-800 text-white text-sm font-bold px-5 py-2 rounded-md transition-colors shadow-sm">
                    Ver detalhes
                </a>
            </div>
        </div>
    </main>
</div>

@endsection