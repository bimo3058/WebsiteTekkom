<div {{ $attributes->class('dosen-page-wrap') }}>
    <div class="dosen-page-box">
        <div class="dosen-page-header">
            {{ $header }}
        </div>
        <div class="dosen-page-body">
            {{ $slot }}
        </div>
    </div>
</div>
