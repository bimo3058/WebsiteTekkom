@props(['title'=>null, 'description'=>null])
<section {{ $attributes->class(['bg-card text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-sm']) }}>
@if($title || $description)<div class="grid auto-rows-min items-start gap-2 px-6">@if($title)<h2 class="leading-none font-semibold">{{ $title }}</h2>@endif @if($description)<p class="text-muted-foreground text-sm">{{ $description }}</p>@endif</div>@endif
{{ $slot }}
</section>
