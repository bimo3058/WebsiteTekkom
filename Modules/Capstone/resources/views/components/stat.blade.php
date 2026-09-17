@props(['title','value','icon','variant'=>'default'])
<div class="rounded-xl border bg-card text-card-foreground shadow-sm p-5 {{ $variant === 'primary' ? 'border-primary/20 bg-primary/5' : ($variant === 'warning' ? 'border-yellow-200 bg-yellow-50' : '') }}">
    <div class="flex items-start justify-between"><div class="space-y-1"><p class="text-sm text-muted-foreground">{{ $title }}</p><p class="text-2xl font-bold tracking-tight" x-text="{{ $value }}"></p></div><div class="rounded-lg p-3 {{ $variant === 'primary' ? 'bg-primary/10 text-primary' : ($variant === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-muted text-muted-foreground') }}"><x-capstone::icon :name="$icon" class="h-5 w-5" /></div></div>
</div>
