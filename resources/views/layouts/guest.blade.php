<x-app-layout>
    <x-slot name="head">
        {{ $head ?? '' }}
    </x-slot>

    @include('partials.navbar')

    <main {{ $attributes->merge(['class' => '']) }}>
        @include('partials.messages')
        
        {{ $slot }}
    </main>

    @include('partials.footer')

    <x-slot name="scripts">
        {{ $scripts ?? '' }}
        <script src="{{ asset('assets/js/burger.js') }}"></script>
    </x-slot>
</x-app-layout>
