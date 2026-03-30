<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">

    <x-pulse::card-header
        name="Request Monitor"
        title="All incoming HTTP requests"
        details="last {{ $this->periodForHumans() }}">
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="w-5 h-5">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            </svg>
        </x-slot:icon>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand" wire:key="request-monitor-scroll">

        @if ($requests->isEmpty())
            <x-pulse::no-results />
        @else
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-200/75 dark:border-gray-700/75">
                        <th class="px-3 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 w-16">Verb</th>
                        <th class="px-3 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Path</th>
                        <th class="px-3 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 w-16 text-center">Status</th>
                        <th class="px-3 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 w-24 text-right">Duration</th>
                        <th class="px-3 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 w-28 hidden md:table-cell">User</th>
                        <th class="px-3 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 w-24 hidden lg:table-cell">IP</th>
                        <th class="px-3 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 w-28 text-right">Happened</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($requests as $req)
                    <tr class="group hover:bg-gray-50/80 dark:hover:bg-white/[0.02] transition-colors duration-150">

                        {{-- Method --}}
                        <td class="px-3 py-2.5">
                            @php
                                $methodColor = match($req->method) {
                                    'GET'    => 'bg-blue-50   text-blue-600   border border-blue-100   dark:bg-blue-500/15   dark:text-blue-300   dark:border-blue-500/25',
                                    'POST'   => 'bg-green-50  text-green-600  border border-green-100  dark:bg-green-500/15  dark:text-green-300  dark:border-green-500/25',
                                    'PUT'    => 'bg-yellow-50 text-yellow-600 border border-yellow-100 dark:bg-yellow-500/15 dark:text-yellow-300 dark:border-yellow-500/25',
                                    'PATCH'  => 'bg-orange-50 text-orange-600 border border-orange-100 dark:bg-orange-500/15 dark:text-orange-300 dark:border-orange-500/25',
                                    'DELETE' => 'bg-red-50    text-red-600    border border-red-100    dark:bg-red-500/15    dark:text-red-300    dark:border-red-500/25',
                                    default  => 'bg-slate-50  text-slate-500  border border-slate-200  dark:bg-slate-500/15  dark:text-slate-300  dark:border-slate-500/25',
                                };
                            @endphp
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $methodColor }}">
                                {{ $req->method }}
                            </span>
                        </td>

                        {{-- Path --}}
                        <td class="px-3 py-2.5 max-w-0">
                            <span class="text-xs font-mono text-gray-700 dark:text-gray-300 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors duration-150 truncate block max-w-[280px]">
                                {{ $req->path }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="px-3 py-2.5 text-center">
                            @php
                                $statusColor = match(true) {
                                    $req->status >= 500 => 'bg-rose-50 text-rose-600 ring-1 ring-inset ring-rose-200/80 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20',
                                    $req->status >= 400 => 'bg-orange-50 text-orange-500 ring-1 ring-inset ring-orange-200/80 dark:bg-orange-500/10 dark:text-orange-400 dark:ring-orange-500/20',
                                    $req->status >= 300 => 'bg-sky-50 text-sky-500 ring-1 ring-inset ring-sky-200/80 dark:bg-sky-500/10 dark:text-sky-400 dark:ring-sky-500/20',
                                    default             => 'bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-200/80 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
                                };
                            @endphp
                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[10px] font-black tabular-nums {{ $statusColor }}">
                                {{ $req->status }}
                            </span>
                        </td>

                        {{-- Duration --}}
                        <td class="px-3 py-2.5 text-right">
                            @php
                                $durationColor = match(true) {
                                    $req->duration >= 2000 => 'text-rose-500 dark:text-rose-400 font-bold',
                                    $req->duration >= 1000 => 'text-orange-500 dark:text-orange-400 font-semibold',
                                    $req->duration >= 500  => 'text-amber-500 dark:text-amber-400',
                                    default                => 'text-gray-400 dark:text-gray-500',
                                };
                            @endphp
                            <span class="text-xs font-mono tabular-nums {{ $durationColor }}">
                                {{ number_format($req->duration) }}ms
                            </span>
                        </td>

                        {{-- User --}}
                        <td class="px-3 py-2.5 hidden md:table-cell">
                            <span class="text-[11px] text-gray-500 dark:text-gray-400 truncate block max-w-[120px]" title="{{ $req->user }}">
                                {{ $req->user }}
                            </span>
                        </td>

                        {{-- IP --}}
                        <td class="px-3 py-2.5 hidden lg:table-cell">
                            <span class="text-[11px] font-mono text-gray-400 dark:text-gray-500 tabular-nums">
                                {{ $req->ip }}
                            </span>
                        </td>

                        {{-- Happened --}}
                        <td class="px-3 py-2.5 text-right">
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">
                                {{ $req->happened }}
                            </span>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </x-pulse::scroll>

</x-pulse::card>