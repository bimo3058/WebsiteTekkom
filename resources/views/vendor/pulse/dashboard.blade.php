<x-pulse>
    {{-- Row 1: Server penuh --}}
    <livewire:pulse.servers cols="full" />

    {{-- Row 2: Usage + Queues + Cache --}}
    <livewire:pulse.usage cols="4" rows="2" />
    <livewire:pulse.queues cols="4" />
    <livewire:pulse.cache cols="4" />

    {{-- Row 3: Slow Queries --}}
    <livewire:pulse.slow-queries cols="full" />

    {{-- Row 4: Request Monitor --}}
    <livewire:pulse.request-monitor cols="full" />

    {{-- Row 5: Exceptions + Slow Requests + Slow Jobs --}}
    <livewire:pulse.exceptions cols="4" />
    <livewire:pulse.slow-requests cols="4" />
    <livewire:pulse.slow-jobs cols="4" />

    {{-- Row 6: Slow Outgoing --}}
    <livewire:pulse.slow-outgoing-requests cols="full" />
</x-pulse>