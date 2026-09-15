<nav aria-label="Jenis pendaftaran" style="display:flex;gap:10px;flex-wrap:wrap;">
    @foreach(['koor' => 'Koordinator', 'asprak' => 'Asisten Praktikum', 'praktikan' => 'Praktikan'] as $tab => $tabLabel)
        <a href="{{ route('eoffice.manprak.admin.pendaftaran-'.$tab.'.index') }}"
           class="mp-btn {{ $activeRegistrationTab === $tab ? 'primary' : 'secondary' }} md"
           @if($activeRegistrationTab === $tab) aria-current="page" @endif>
            <x-eoffice::manajemen-praktikum.ui.icon :name="$tab === 'praktikan' ? 'book' : 'file-check'" />
            {{ $tabLabel }}
        </a>
    @endforeach
</nav>
@if($errors->any())
    <div class="mp-flash mp-flash-error" role="alert" style="margin-top:12px;">
        <x-eoffice::manajemen-praktikum.ui.icon name="info" />
        <div>@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
    </div>
@endif
