<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter Tight', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #1e293b;
        }
        .container { text-align: center; max-width: 380px; padding: 0 24px; }
        .label { font-size: 11px; font-weight: 600; letter-spacing: 2.5px; text-transform: uppercase; color: #94a3b8; margin-bottom: 20px; }
        .icon-wrap {
            width: 56px; height: 56px; border-radius: 16px;
            background: #fcebeb; border: 1px solid #f7c1c1;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px; position: relative; color: #a32d2d;
        }
        .icon-wrap svg { width: 24px; height: 24px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
        .icon-dot {
            position: absolute; top: -4px; right: -4px;
            width: 10px; height: 10px; background: #e24b4a;
            border-radius: 50%; border: 2px solid #f8fafc;
            animation: blink 2s ease-in-out infinite;
        }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        h1 { font-size: 20px; font-weight: 600; color: #1e293b; letter-spacing: -0.3px; margin-bottom: 8px; }
        p { font-size: 13px; color: #64748b; line-height: 1.65; margin-bottom: 28px; }
        .actions { display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 500;
            font-family: 'Inter Tight', sans-serif; text-decoration: none;
            cursor: pointer; transition: opacity 0.15s; border: none;
        }
        .btn:hover { opacity: 0.85; }
        .btn svg { width: 14px; height: 14px; flex-shrink: 0; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .btn-back { background: #ffffff; color: #475569; border: 0.5px solid #e2e8f0; }
        .btn-primary { background: #7c6bf8; color: #ffffff; }
        footer { position: fixed; bottom: 0; width: 100%; padding: 16px 24px; text-align: center; font-size: 11px; color: #94a3b8; }
        footer a { color: #7c6bf8; text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>
    <div class="container">
        <div class="label">403 Forbidden</div>
        <div class="icon-wrap">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
            <span class="icon-dot"></span>
        </div>
        <h1>Akses Ditolak</h1>
        <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <div class="actions">
            <a href="javascript:history.back()" class="btn btn-back">
                <svg viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                Kembali
            </a>
            <a href="/" class="btn btn-primary">
                <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Beranda
            </a>

        </div>
    </div>
    <footer>
        &copy; {{ date('Y') }} <a href="/">LuminHR</a>. All rights reserved.
    </footer>
</body>
</html>