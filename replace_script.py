import os, re

path = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum'

def process_praktikum_dropdown(match):
    return '''@php
                $praktikumOptions = [];
                if(isset($praktikumList)) {
                    foreach($praktikumList as $p) {
                        $label = $p->nama;
                        if(isset($p->kode) && $p->kode) $label .= " [{$p->kode}]";
                        if(isset($p->semester)) $label .= " · {$p->semester} {$p->tahun_ajaran}";
                        $praktikumOptions[] = ['value' => (string)$p->id, 'label' => $label];
                    }
                }
            @endphp
            <x-eoffice::manajemen-praktikum.ui.select 
                name="praktikum_id"
                :options="$praktikumOptions"
                :selected="(string)request('praktikum_id', (isset($praktikum) ? $praktikum?->id : (isset($praktikumId) ? $praktikumId : '')))"
                placeholder="Pilih Praktikum..."
                onChange="$event.target.form.submit()"
                minWidth="240px"
            />'''

def process_status_dropdown(match):
    return '''<x-eoffice::manajemen-praktikum.ui.select 
                name="status"
                :options="[
                    ['value' => '', 'label' => 'Semua Status'],
                    ['value' => 'pending', 'label' => 'Menunggu'],
                    ['value' => 'approved', 'label' => 'Disetujui'],
                    ['value' => 'rejected', 'label' => 'Ditolak']
                ]"
                :selected="request('status', '')"
                placeholder="Semua Status"
                onChange="$event.target.form.submit()"
                minWidth="160px"
            />'''

def process_modul_dropdown(match):
    return '''@php
                $modulOptions = [];
                if(isset($modulList)) {
                    foreach($modulList as $m) {
                        $modulOptions[] = ['value' => (string)$m->id, 'label' => $m->judul];
                    }
                }
            @endphp
            <x-eoffice::manajemen-praktikum.ui.select 
                name="modul_id"
                :options="$modulOptions"
                :selected="(string)request('modul_id', (isset($modul) ? $modul?->id : ''))"
                placeholder="Pilih Modul..."
                onChange="$event.target.form.submit()"
                minWidth="200px"
            />'''

def process_sort_dropdown(match):
    return '''<x-eoffice::manajemen-praktikum.ui.select 
                name="sort"
                :options="[
                    ['value' => 'terbaru', 'label' => 'Terbaru'],
                    ['value' => 'terlama', 'label' => 'Terlama'],
                    ['value' => 'nama_asc', 'label' => 'Nama (A-Z)'],
                    ['value' => 'nama_desc', 'label' => 'Nama (Z-A)']
                ]"
                :selected="request('sort', 'terbaru')"
                placeholder="Urutkan..."
                onChange="$event.target.form.submit()"
                minWidth="140px"
            />'''

for root, dirs, files in os.walk(path):
    for file in files:
        if file.endswith('.blade.php') and '-edit' not in file and '-create' not in file:
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            
            new_content = content
            new_content = re.sub(r'<select name="praktikum_id"[^>]*>.*?</select>', process_praktikum_dropdown, new_content, flags=re.DOTALL)
            new_content = re.sub(r'<select name="status"[^>]*>.*?</select>', process_status_dropdown, new_content, flags=re.DOTALL)
            new_content = re.sub(r'<select name="modul_id"[^>]*>.*?</select>', process_modul_dropdown, new_content, flags=re.DOTALL)
            new_content = re.sub(r'<select name="sort"[^>]*>.*?</select>', process_sort_dropdown, new_content, flags=re.DOTALL)
            
            if new_content != content:
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                print('Updated dropdowns in', filepath.replace(path, ''))
