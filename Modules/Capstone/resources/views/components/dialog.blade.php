@props(['id', 'title'=>null, 'description'=>null, 'width'=>'max-w-lg'])
<dialog id="{{ $id }}" {{ $attributes->class(['fixed inset-0 m-auto w-[calc(100%-2rem)] max-h-[90vh] overflow-y-auto bg-background text-foreground gap-4 rounded-lg border p-6 shadow-lg', $width]) }} @click="if ($event.target === $event.currentTarget && ($event.offsetX < 0 || $event.offsetY < 0 || $event.offsetX > $event.currentTarget.clientWidth || $event.offsetY > $event.currentTarget.clientHeight)) $event.currentTarget.close()">
<button type="button" class="absolute top-4 right-4 opacity-70 hover:opacity-100" aria-label="Close" @click="$el.closest('dialog').close()"><x-capstone::icon name="X" /></button>
@if($title)<h2 class="text-lg leading-none font-semibold pr-6">{{ $title }}</h2>@endif
@if($description)<p class="text-muted-foreground mt-2 text-sm">{{ $description }}</p>@endif
{{ $slot }}
</dialog>
