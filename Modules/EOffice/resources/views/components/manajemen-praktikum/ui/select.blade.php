@props([
    'name',
    'label' => null,
    'hint' => null,
    'placeholder' => 'Pilih opsi...',
    'options' => [], // array of ['value' => '...', 'label' => '...']
    'selected' => '',
    'onChange' => null, // e.g., '$event.target.form.submit()'
    'minWidth' => '140px',
    'searchable' => false,
    'searchPlaceholder' => 'Cari opsi...',
])

@php
// Pastikan options berformat array asosiatif jika hanya array 1D atau collection
$formattedOptions = [];
foreach($options as $key => $opt) {
    if(is_array($opt) && isset($opt['value'])) {
        $formattedOptions[] = $opt;
    } elseif(is_object($opt)) {
        // jika dikirim objek (seperti dari map)
        $formattedOptions[] = ['value' => (string)($opt->id ?? $opt->value ?? $key), 'label' => $opt->nama ?? $opt->label ?? $opt];
    } else {
        // Jika format list ['value1', 'value2'] atau ['val' => 'Label']
        // Asumsikan key adalah value jika assoc, atau value adalah value jika list
        if (is_int($key)) {
            $formattedOptions[] = ['value' => $opt, 'label' => $opt];
        } else {
            $formattedOptions[] = ['value' => $key, 'label' => $opt];
        }
    }
}
@endphp

<div class="flex flex-col gap-1.5 w-full flex-1" style="min-width: {{ $minWidth }};">
    @if($label)
    <label class="text-[13px] font-medium text-[#666D80]">{{ $label }}</label>
    @endif
    
    <div x-data='{ 
            open: false, 
            selected: "{{ $selected }}", 
            options: @json($formattedOptions),
            search: "",
            get selectedLabel() {
                let opt = this.options.find(o => o.value == this.selected);
                return opt ? opt.label : "{{ $placeholder }}";
            },
            get filteredOptions() {
                if (!this.search) return this.options;
                return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
            }
        }' 
        class="relative w-full text-left" @click.away="open = false">
        
        <button type="button" @click="open = !open"
                class="w-full h-[38px] pl-3 pr-3 text-left text-[13px] border focus:outline-none font-medium flex items-center justify-between transition-colors rounded-[8px]"
                :class="open ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'">
            <span x-text="selectedLabel" class="truncate pr-2" :class="!selected && !options.find(o => o.value == selected) ? 'text-[#94A3B8]' : ''"></span>
            <svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0" 
                 :class="{'rotate-180': open, 'text-[#0B266E]': open, 'text-[#666D80]': !open}" 
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        </button>
        
        <div x-show="open" style="display:none;"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute z-50 w-full mt-1.5 bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-1.5 flex flex-col">
            
            @if($searchable)
            <div class="mb-1.5 flex-shrink-0">
                <input type="text" x-model="search" placeholder="{{ $searchPlaceholder }}" 
                       class="w-full text-[13px] px-2.5 py-1.5 border border-[#DFE1E7] rounded-[6px] focus:outline-none focus:border-[#0B266E] transition-colors bg-[#FAFAFA]"
                       @click.stop @keydown.enter.prevent>
            </div>
            @endif

            <div class="overflow-y-auto min-h-0 flex flex-col" style="max-height: 190px;">
                <template x-for="option in filteredOptions" :key="option.value">
                    <label class="flex items-center justify-between px-3 py-2.5 rounded-[6px] cursor-pointer text-[13px] transition-colors mb-0.5 last:mb-0"
                           :class="selected == option.value ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                        <input type="radio" :name="'{{ $name }}'" :value="option.value" x-model="selected" 
                               @change="open = false; {{ $onChange ?? '' }}" class="hidden">
                        <span x-text="option.label" class="truncate"></span>
                        <svg x-show="selected == option.value" class="w-4 h-4 flex-shrink-0 text-[#0B266E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </label>
                </template>
                <div x-show="filteredOptions.length === 0" class="px-3 py-4 text-center text-[#94A3B8] text-[12px]">
                    Tidak ada opsi.
                </div>
            </div>
        </div>
    </div>

    @if($hint)
    <p class="text-[11px] text-[#94A3B8] mt-1">{{ $hint }}</p>
    @endif
</div>
