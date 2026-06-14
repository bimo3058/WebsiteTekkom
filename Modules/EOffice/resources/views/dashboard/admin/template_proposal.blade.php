<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Template Proposal — E-Office SIPERKOM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <style>
        * { font-family: 'Inter Tight', sans-serif; }
        .sikape-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; }
        .sikape-btn-primary { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 20px; background:#0B266E; color:#fff; border-radius:8px; font-size:14px; font-weight:600; border:none; cursor:pointer; transition:all .15s; text-decoration:none; }
        .sikape-btn-primary:hover { background:#1a3a8f; }

        /* A4 WYSIWYG Styling */
        .a4-editor-wrapper {
            width: 100%;
            overflow-x: auto;
            background: #f3f4f6;
            padding: 24px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #proposal-editor {
            width: 21cm;
            min-height: 29.7cm;
            padding: 4cm 3cm 3cm 4cm; /* Top Right Bottom Left */
            background-color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #d1d5db !important;
            border-radius: 0 !important;
            font-family: 'Times New Roman', Times, serif; /* Sesuai standar tulisan ilmiah */
            font-size: 12pt;
            line-height: 1.5;
        }
        @media (max-width: 21cm) {
            .a4-editor-wrapper { align-items: flex-start; }
        }
        .ql-toolbar { width: 100%; max-width: 21cm; background: #f9fafb; border-color: #d1d5db !important; border-radius: 4px 4px 0 0 !important; }
        .ql-container { border-color: #d1d5db !important; border-radius: 0 0 4px 4px !important; }
    </style>
</head>
<body class="bg-[#F6F8FA] text-[#0D0D12] antialiased">
<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Edit Template Dokumen KP</h1>
            <p class="text-sm text-gray-500 mt-1">Sesuaikan format standar untuk pembuatan dokumen Kerja Praktik (Proposal).</p>
        </div>
        <a href="{{ route('eoffice.dashboard') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="sikape-card overflow-hidden">
        <form action="{{ route('eoffice.admin.template_proposal.store') }}" method="POST" id="template-form">
            @csrf
            <input type="hidden" name="content" id="content">
            
            <div class="p-6 border-b border-gray-200 flex items-center justify-between bg-white">
                <div class="text-sm text-gray-600">
                    Format Halaman: <strong>A4</strong> | Margin: <strong>Atas 4cm, Kiri 4cm, Kanan 3cm, Bawah 3cm</strong>
                </div>
                <button type="submit" class="sikape-btn-primary" onclick="return saveContent()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Simpan Template
                </button>
            </div>

            <div class="a4-editor-wrapper">
                <div id="proposal-editor" style="background:#fff;">{!! $templateContent !!}</div>
            </div>
        </form>
    </div>
</div>

<script>
    var quill = new Quill('#proposal-editor', {
        theme: 'snow',
        placeholder: 'Ketik format standar proposal di sini...',
        modules: {
            toolbar: [
                [{ heading: [1, 2, 3, 4, 5, 6, false] }],
                [{ 'font': [] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        }
    });

    // Sesuaikan margin toolbar
    document.querySelector('.ql-toolbar').style.margin = '0 auto';

    function saveContent() {
        document.getElementById('content').value = quill.root.innerHTML;
        return true;
    }
</script>
</body>
</html>
