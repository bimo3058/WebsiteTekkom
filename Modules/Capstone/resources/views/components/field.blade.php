@props(['name','label','type'=>'text','placeholder'=>'','required'=>false,'options'=>[]])
@php
    $autoPlaceholder = $placeholder ?: match(true) {
        $type === 'select' || $type === 'combobox' => 'Pilih '.$label,
        $type === 'search' => 'Cari...',
        $type === 'email' => 'nama@contoh.id',
        $type === 'date' => 'cth., 2026-11-11',
        $type === 'time' => 'cth., 09:00',
        $type === 'number' => 'cth., 0',
        $type === 'textarea' => 'Masukkan '.$label.'...',
        default => 'Masukkan '.$label,
    };
@endphp
<div class="space-y-2 {{ $type === 'textarea' ? 'col-span-full' : '' }}">
    <label for="field-{{ $name }}" class="text-sm font-medium leading-none text-on-surface">{{ $label }}@if($required)<span class="ml-0.5 text-danger">*</span>@endif</label>
    @if($type==='textarea')<textarea id="field-{{ $name }}" name="{{ $name }}" x-model="form.{{ $name }}" placeholder="{{ $autoPlaceholder }}" @required($required) class="border-outline placeholder:text-on-surface/50 text-on-surface min-h-24 w-full rounded-radius border bg-surface px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-primary focus-visible:ring-2 focus-visible:ring-primary/20" {{ $attributes }}></textarea>
    @elseif($type==='select')<x-capstone::select-menu id="field-{{ $name }}" :name="$name" x-model="form.{{ $name }}" :placeholder="$autoPlaceholder" :options="$options" :required="$required" {{ $attributes }}>{{ $slot }}</x-capstone::select-menu>
    @elseif($type==='combobox')<x-capstone::combobox id="field-{{ $name }}" :name="$name" x-model="form.{{ $name }}" :placeholder="$autoPlaceholder" :options="$options" :required="$required" {{ $attributes }}>{{ $slot }}</x-capstone::combobox>
    @elseif($type==='checkbox')<div><input id="field-{{ $name }}" name="{{ $name }}" type="checkbox" role="switch" x-model="form.{{ $name }}" class="h-4 w-8 accent-primary" {{ $attributes }}></div>
    @else<input id="field-{{ $name }}" :name="$name" :type="$type" x-model="form.{{ $name }}" :placeholder="$autoPlaceholder" :required="$required" {{ $attributes->merge(['class' => 'file:text-on-surface placeholder:text-on-surface/50 selection:bg-primary selection:text-on-primary border-outline h-9 w-full min-w-0 rounded-radius border bg-surface px-3 py-1 text-sm text-on-surface shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-primary focus-visible:ring-2 focus-visible:ring-primary/20']) }} />@endif
    <p class="text-danger text-sm" x-show="errors['{{ $name }}']" x-text="errors['{{ $name }}']?.[0]"></p>
</div>
