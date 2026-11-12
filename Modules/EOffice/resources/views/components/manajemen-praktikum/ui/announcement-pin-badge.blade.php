@props(['announcement'])
@if($announcement->isPinned())
    <span class="mp-badge primary sm" title="Disematkan sampai {{ $announcement->pinned_until->format('d/m/Y, H:i') }} WIB"><x-eoffice::manajemen-praktikum.ui.icon name="pin" size="12" />Disematkan</span>
@endif
