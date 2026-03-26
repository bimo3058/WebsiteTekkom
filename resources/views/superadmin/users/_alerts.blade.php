@if($errors->any())
<div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
    <div class="flex items-center gap-3 mb-2">
        <span class="material-symbols-outlined text-red-500">error</span>
        <h3 class="text-red-700 font-semibold text-sm">Terdapat kesalahan</h3>
    </div>
    <ul class="text-red-600 text-sm space-y-1 pl-6 list-disc">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6">
    <div class="flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-500">check_circle</span>
        <span class="text-emerald-700 text-sm">{{ session('success') }}</span>
    </div>
</div>
@endif