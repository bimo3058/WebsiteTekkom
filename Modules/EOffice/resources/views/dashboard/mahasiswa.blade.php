<x-banksoal::layouts.master>
    <div class="flex justify-between items-center">
        <h1>Hello Mahasiswa</h1>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>

    <p>Module: {!! config('eoffice.name') !!}</p>
</x-banksoal::layouts.master>