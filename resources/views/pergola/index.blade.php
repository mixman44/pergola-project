@extends('layouts.app')

@section('content')

    <div class="pergola-editor">
        <aside class="pg-sidebar">
            <div>
                <p class="pg-sidebar-sub">Configurateur</p>
                <h1 class="pg-sidebar-title">Pergola</h1>
            </div>

            {{-- Blocs de contrôles --}}
            <div class="pg-sidebar-body">
                <x-pergola.model-selector />
                <x-pergola.mode-selector />
                <x-pergola.sliders />
                <x-pergola.image-background />
                <x-pergola.pergola-image />
            </div>

            {{-- Bouton Générer image en bas --}}
            <div class="pg-sidebar-footer">
                <x-pergola.buttons />
            </div>
         </aside>

        {{--ZONE CANVAS DROITE --}}
        <main class="pg-main">
            <x-pergola.canvas />
        </main>
    </div>



    @vite('resources/js/pergola/pergola-editor.js')

@endsection
