@props(['name','label','type'=>'text','placeholder'=>'','required'=>false,'options'=>[]])
<div class="space-y-2 {{ $type === 'textarea' ? 'col-span-full' : '' }}">
    <label for="field-{{ $name }}" class="text-sm font-medium leading-none">{{ $label }}@if($required)<span class="ml-0.5 text-red-500">*</span>@endif</label>
    @if($type==='textarea')<textarea id="field-{{ $name }}" name="{{ $name }}" x-model="form.{{ $name }}" placeholder="{{ $placeholder }}" @required($required) class="border-input placeholder:text-muted-foreground min-h-24 w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ $attributes }}></textarea>
    @elseif($type==='select')<select id="field-{{ $name }}" name="{{ $name }}" x-model="form.{{ $name }}" @required($required) class="border-input h-9 w-full rounded-md border bg-transparent px-3 text-sm shadow-xs" {{ $attributes }}>@foreach($options as $key=>$value)<option value="{{ $key }}">{{ $value }}</option>@endforeach{{ $slot }}</select>
    @elseif($type==='checkbox')<div><input id="field-{{ $name }}" name="{{ $name }}" type="checkbox" role="switch" x-model="form.{{ $name }}" class="h-4 w-8 accent-primary" {{ $attributes }}></div>
    @else<x-capstone::input id="field-{{ $name }}" :name="$name" :type="$type" x-model="form.{{ $name }}" :placeholder="$placeholder" :required="$required" {{ $attributes }} />@endif
    <p class="text-destructive text-sm" x-show="errors['{{ $name }}']" x-text="errors['{{ $name }}']?.[0]"></p>
</div>
