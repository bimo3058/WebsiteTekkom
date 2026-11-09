<x-app-layout>
    <x-sidebar :user="auth()->user()">
        @include('superadmin.users._status-page', ['statusPage' => 'online'])
    </x-sidebar>
</x-app-layout>
