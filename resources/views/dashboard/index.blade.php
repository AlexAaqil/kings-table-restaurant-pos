<x-authenticated-layout class="Dashboard">
    <x-slot name="head">
        <title>Dashboard</title>
    </x-slot>

    @if($user->user_level_label === 'super admin' || $user->user_level_label === 'admin')
        @include('dashboard.admin')
    @endif

    @if($user->user_level_label === 'cashier')
        @include('dashboard.user')
    @endif
</x-authenticated-layout>
