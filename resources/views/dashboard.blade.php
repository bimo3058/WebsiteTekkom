<x-app-layout>
    <div class="p-6 bg-slate-50 min-h-screen">

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-slate-800">Dashboard</h1>
            <p class="text-slate-500 text-sm mt-0.5">Selamat datang, pilih modul untuk melanjutkan.</p>
        </div>

        {{-- Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($cards as $card)
                <a href="{{ route($card['route']) }}"
                   class="group bg-white border border-slate-200 rounded-xl p-5 hover:shadow-md hover:border-blue-200 transition-all flex flex-col gap-4">

                    <div @class([
                        'w-10 h-10 rounded-xl flex items-center justify-center',
                        'bg-blue-50'   => $card['color'] === 'blue',
                        'bg-purple-50' => $card['color'] === 'purple',
                        'bg-green-50'  => $card['color'] === 'green',
                        'bg-orange-50' => $card['color'] === 'orange',
                    ])>
                        <span @class([
                            'material-symbols-outlined',
                            'text-blue-600'   => $card['color'] === 'blue',
                            'text-purple-600' => $card['color'] === 'purple',
                            'text-green-600'  => $card['color'] === 'green',
                            'text-orange-600' => $card['color'] === 'orange',
                        ]) style="font-size:20px">
                            {{ $card['icon'] }}
                        </span>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-800 group-hover:text-blue-700 transition-colors">
                            {{ $card['title'] }}
                        </h3>
                        <p class="text-slate-400 text-xs mt-0.5 leading-relaxed">
                            {{ $card['description'] }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-app-layout>