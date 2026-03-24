<header class="bg-white border-b border-slate-200">
    <div class="flex items-center justify-between px-8 h-16 lg:h-20">
        <!-- Header Title -->
        <div>
            <h1 class="text-xl font-bold text-slate-800">
                @yield('header_title', 'Dashboard Mahasiswa')
            </h1>
        </div>
        
        <!-- Optional Right Content -->
        <div class="flex items-center gap-4">
            @yield('header_right')
        </div>
    </div>
</header>
