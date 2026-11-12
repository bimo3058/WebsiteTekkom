@if($errors->any() || session('success') || session('error'))
<div class="mp-feedback-stack" data-feedback-pending aria-label="Hasil tindakan">
    @foreach([['error', $errors->first()], ['success', session('success')], ['error', session('error')]] as [$type, $message])
        @if($message)
        <div class="mp-flash mp-flash-{{ $type }} mp-floating-feedback" role="{{ $type === 'error' ? 'alert' : 'status' }}">
            <x-eoffice::manajemen-praktikum.ui.icon :name="$type === 'success' ? 'check' : 'alert-circle'" :size="16" />
            <span>{{ $message }}</span>
        </div>
        @endif
    @endforeach
</div>
@endif
