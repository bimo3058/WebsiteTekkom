<x-app-layout>
    <div class="min-h-screen p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 h-40">

            @foreach($cards as $card)
                <a href="{{ route($card['route']) }}"
                   @class([
                       'p-5 rounded-xl shadow-lg transition duration-200 hover:scale-105 hover:shadow-2xl flex flex-col justify-between',
                       'bg-blue-600 hover:bg-blue-700'     => $card['color'] === 'blue',
                       'bg-purple-600 hover:bg-purple-700' => $card['color'] === 'purple',
                       'bg-green-600 hover:bg-green-700'   => $card['color'] === 'green',
                       'bg-orange-600 hover:bg-orange-700' => $card['color'] === 'orange',
                   ])>

                    <span class="material-symbols-outlined text-3xl text-white">
                        {{ $card['icon'] }}
                    </span>

                    <div>
                        <h3 class="text-base font-bold text-white mb-1">
                            {{ $card['title'] }}
                        </h3>
                        <p class="text-white/80 text-xs">
                            {{ $card['description'] }}
                        </p>
                    </div>
                </a>
            @endforeach

        </div>
    </div>
</x-app-layout>