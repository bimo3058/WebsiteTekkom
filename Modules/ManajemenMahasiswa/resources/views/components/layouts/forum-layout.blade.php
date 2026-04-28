@php
    $user = auth()->user();
    $roles = $user ? $user->roles->pluck('name') : collect();
    
    if ($roles->intersect(['superadmin', 'admin', 'dosen_koordinator', 'pengurus_himpunan', 'gpm', 'admin_kemahasiswaan'])->isNotEmpty()) {
        $layout = 'manajemenmahasiswa::layouts.admin';
    } elseif ($roles->intersect(['dosen', 'dosen_pembimbing', 'dosen_penguji', 'pic_kegiatan', 'koordinator_skripsi'])->isNotEmpty()) {
        $layout = 'manajemenmahasiswa::layouts.dosen';
    } else {
        $layout = 'manajemenmahasiswa::layouts.mahasiswa';
    }
@endphp

<x-dynamic-component :component="$layout">
    {{ $slot }}

    {{-- Level Up Confetti & Toast --}}
    @if(session('level_up'))
        @php $levelUp = session('level_up'); @endphp
        <style>
            .level-up-overlay {
                position: fixed;
                inset: 0;
                z-index: 99999;
                pointer-events: none;
            }
            .confetti-piece {
                position: absolute;
                width: 10px;
                height: 10px;
                top: -10px;
                animation: confetti-fall linear forwards;
            }
            @keyframes confetti-fall {
                0%   { transform: translateY(0) rotate(0deg) scale(1); opacity: 1; }
                70%  { opacity: 1; }
                100% { transform: translateY(100vh) rotate(720deg) scale(0.3); opacity: 0; }
            }
            .level-up-toast {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) scale(0.5);
                z-index: 100000;
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
                color: #fff;
                padding: 32px 48px;
                border-radius: 20px;
                text-align: center;
                box-shadow: 0 25px 60px rgba(99, 102, 241, 0.4), 0 0 80px rgba(139, 92, 246, 0.2);
                animation: level-up-appear 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
                pointer-events: auto;
            }
            @keyframes level-up-appear {
                0%   { transform: translate(-50%, -50%) scale(0.5); opacity: 0; }
                100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
            }
            .level-up-toast .level-icon {
                font-size: 56px;
                display: block;
                margin-bottom: 8px;
                animation: level-icon-bounce 0.6s ease 0.3s both;
            }
            @keyframes level-icon-bounce {
                0%   { transform: scale(0); }
                50%  { transform: scale(1.3); }
                100% { transform: scale(1); }
            }
            .level-up-toast .level-text {
                font-size: 28px;
                font-weight: 800;
                letter-spacing: -0.5px;
                margin-bottom: 4px;
            }
            .level-up-toast .level-tier {
                font-size: 16px;
                font-weight: 600;
                opacity: 0.9;
            }
            .level-up-toast .level-sub {
                font-size: 13px;
                opacity: 0.7;
                margin-top: 8px;
            }
        </style>

        <div class="level-up-overlay" id="confettiOverlay"></div>
        <div class="level-up-toast" id="levelUpToast">
            <span class="level-icon">{!! $levelUp['tier_icon'] !!}</span>
            <div class="level-text">LEVEL UP!</div>
            <div class="level-tier">Level {{ $levelUp['new_level'] }} — {{ $levelUp['tier_name'] }}</div>
            <div class="level-sub">Selamat! Anda naik dari Level {{ $levelUp['old_level'] }}</div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('confettiOverlay');
            const colors = ['#ff6b6b', '#feca57', '#48dbfb', '#ff9ff3', '#54a0ff', '#5f27cd', '#01a3a4', '#f368e0', '#ff9f43', '#00d2d3'];
            const shapes = ['circle', 'square', 'triangle'];

            // Create 120 confetti pieces
            for (let i = 0; i < 120; i++) {
                const piece = document.createElement('div');
                piece.className = 'confetti-piece';
                const color = colors[Math.floor(Math.random() * colors.length)];
                const shape = shapes[Math.floor(Math.random() * shapes.length)];
                const size = Math.random() * 8 + 6;
                const left = Math.random() * 100;
                const delay = Math.random() * 1.5;
                const duration = Math.random() * 2 + 2;

                piece.style.left = left + '%';
                piece.style.width = size + 'px';
                piece.style.height = size + 'px';
                piece.style.backgroundColor = color;
                piece.style.animationDuration = duration + 's';
                piece.style.animationDelay = delay + 's';

                if (shape === 'circle') {
                    piece.style.borderRadius = '50%';
                } else if (shape === 'triangle') {
                    piece.style.width = '0';
                    piece.style.height = '0';
                    piece.style.backgroundColor = 'transparent';
                    piece.style.borderLeft = (size/2) + 'px solid transparent';
                    piece.style.borderRight = (size/2) + 'px solid transparent';
                    piece.style.borderBottom = size + 'px solid ' + color;
                }

                overlay.appendChild(piece);
            }

            // Auto-dismiss after 4.5 seconds
            setTimeout(() => {
                const toast = document.getElementById('levelUpToast');
                if (toast) {
                    toast.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    toast.style.opacity = '0';
                    toast.style.transform = 'translate(-50%, -50%) scale(0.8)';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 4500);

            setTimeout(() => {
                if (overlay) overlay.remove();
            }, 5000);
        });
        </script>
    @endif
</x-dynamic-component>
