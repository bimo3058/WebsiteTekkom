<?php

namespace Modules\EOffice\Tests\Unit;

use Tests\TestCase;

class PracticumPresentationTest extends TestCase
{
    public function test_grade_breadcrumb_titles_and_assistant_announcement_actions_are_consistent(): void
    {
        foreach (['dosen', 'mahasiswa'] as $role) {
            $source = file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/'.$role.'/nilai.blade.php'));
            $this->assertStringContainsString(':pageTitle="\'Absensi & Nilai\'"', $source);
        }
        $source = file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/asprak/pengumuman.blade.php'));
        $this->assertMatchesRegularExpression('/@if\(\$isMine\)\s*<x-eoffice::manajemen-praktikum.ui.action-menu>/', $source);
        foreach (['openEditModal(', 'pengumuman.destroy', 'Edit Pengumuman', 'Hapus Pengumuman'] as $text) {
            $this->assertStringContainsString($text, $source);
        }
        $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source), TOKEN_PARSE));
    }

    public function test_student_recap_and_lecturer_announcement_use_shared_presentation(): void
    {
        $root = 'Modules/EOffice/resources/views/';
        $student = file_get_contents(base_path($root.'manajemen-praktikum/mahasiswa/nilai.blade.php'));
        foreach (['ui.grade-group-filter model="globalKelompok"', 'ui.grade-group-filter model="globalShift"', 'role="mahasiswa"', 'ui.nilai-csv-picker'] as $text) {
            $this->assertStringContainsString($text, $student);
        }
        $this->assertStringNotContainsString('belum tersedia.', $student);
        $announcement = file_get_contents(base_path($root.'manajemen-praktikum/dosen/praktikum-detail.blade.php'));
        $this->assertStringContainsString('manajemen-praktikum.dosen-header', $announcement);
        $this->assertStringContainsString('border-radius:14px; padding:15px 24px;', $announcement);
        $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($announcement), TOKEN_PARSE));
        $ta = file_get_contents(base_path($root.'components/manajemen-praktikum/ui/tugas-akhir-card.blade.php'));
        $this->assertStringContainsString("in_array(\$role,['koor','dosen','mahasiswa'])", $ta);
        $this->assertStringNotContainsString("fn(\$q) => \$q->where('user_id', auth()->id())", $ta);
    }

    public function test_final_grade_controls_and_publication_cancel_use_shared_components(): void
    {
        $root = 'Modules/EOffice/resources/views/';
        $final = file_get_contents(base_path($root.'components/manajemen-praktikum/ui/final-grades.blade.php'));
        $this->assertLessThan(strpos($final, 'x-model="predicate"'), strpos($final, 'Tambah Kolom'));
        $this->assertStringNotContainsString('Maksimal tiga kolom tambahan.', $final);
        $this->assertStringContainsString("['#FEFCE8','#FFF7ED','#EEF2FF'][columnIndex]", $final);
        foreach (['koordinator', 'dosen'] as $role) {
            $source = file_get_contents(base_path($root.'manajemen-praktikum/'.$role.'/nilai.blade.php'));
            $this->assertStringContainsString('ui.cancel-publication-confirmation', $source);
            $this->assertStringNotContainsString("confirm('Batalkan persetujuan nilai?')", $source);
            $this->assertStringContainsString('ui.grade-group-filter model="globalKelompok"', $source);
        }
        foreach (['components/manajemen-praktikum/ui/cancel-publication-confirmation', 'components/manajemen-praktikum/koor-header', 'manajemen-praktikum/dosen/pengumuman'] as $view) {
            $source = file_get_contents(base_path($root.$view.'.blade.php'));
            $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source), TOKEN_PARSE));
        }
        $header = file_get_contents(base_path($root.'components/manajemen-praktikum/koor-header.blade.php'));
        $this->assertStringContainsString('ui.action-menu', $header);
        $announcement = file_get_contents(base_path($root.'manajemen-praktikum/dosen/pengumuman.blade.php'));
        $this->assertStringContainsString('manajemen-praktikum.dosen-header', $announcement);
        $this->assertStringNotContainsString('Halaman ini bersifat', $announcement);
    }

    public function test_tab_divider_stays_above_toolbar_and_module_scores_use_integer_display(): void
    {
        foreach (['koor', 'asprak', 'dosen', 'mhs'] as $role) {
            $source = file_get_contents(base_path("Modules/EOffice/resources/views/components/manajemen-praktikum/{$role}-header.blade.php"));
            $this->assertStringContainsString('mp-praktikum-tabs-divider', $source);
            if ($role !== 'mhs') {
                $this->assertLessThan(strpos($source, '@if($slot->isNotEmpty())'), strpos($source, 'mp-praktikum-tabs-divider'));
            }
        }
        $input = view('eoffice::components.manajemen-praktikum.ui.grade-score-input', ['model' => 'r.score', 'integer' => true])->render();
        $this->assertStringContainsString('Number(r.score).toFixed(0)', $input);
        foreach (['koordinator', 'dosen', 'mahasiswa'] as $role) {
            $source = file_get_contents(base_path("Modules/EOffice/resources/views/manajemen-praktikum/{$role}/nilai.blade.php"));
            $this->assertStringContainsString("number_format(\$njMap['laporan'], 0, ',', '')", $source);
        }
        $recap = file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/tugas-akhir/rekap-inline.blade.php'));
        $this->assertStringContainsString('ui.grade-history', $recap);
        $this->assertStringContainsString(':entries="$grade?->riwayat ?? []"', $recap);
        $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($recap), TOKEN_PARSE));
    }

    public function test_student_and_lecturer_cards_and_grade_nesting_share_existing_patterns(): void
    {
        foreach(['mahasiswa','dosen'] as $role){
            foreach(['modul','tugas'] as $page){
                $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/'.$role.'/'.$page.'.blade.php'));
                $this->assertStringContainsString('mp-module-card-header',$source);
                $this->assertStringContainsString('font-size:13px; color:#666D80; font-weight:400; margin-bottom:4px;',$source);
            }
            $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/'.$role.'/nilai.blade.php'));
            $this->assertMatchesRegularExpression('/ui\.tugas-akhir-card[^>]*section="nilai"\s*\/>\s*<x-eoffice::manajemen-praktikum.ui.final-grades[^>]*embedded="true"\s*\/>\s*<\/div>\{\{-- \/outer card --\}\}/',$source);
            $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source),TOKEN_PARSE));
        }
        $studentGrades=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/mahasiswa/nilai.blade.php'));
        $this->assertStringNotContainsString('Menunggu persetujuan Koordinator',$studentGrades);
        $studentTasks=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/mahasiswa/tugas.blade.php'));
        $this->assertStringContainsString('Tenggat AC:',$studentTasks);
        $this->assertStringContainsString('Tenggat ACC:',$studentTasks);
        $this->assertStringNotContainsString('border-top:1px solid #DFE1E7; margin:16px',$studentTasks);
        foreach(['content','student-submission'] as $view){
            $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/tugas-akhir/'.$view.'.blade.php'));
            $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source),TOKEN_PARSE));
        }
    }

    public function test_bulk_popup_is_bounded_and_sidebar_only_highlights_current_role(): void
    {
        $popup=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/tugas-akhir/bulk-grades.blade.php'));
        foreach(['max-height:calc(100dvh - 32px)','max-height:104px;overflow-y:auto','data-ta-bulk-fields','flex-shrink:0'] as $text)$this->assertStringContainsString($text,$popup);
        $this->assertStringNotContainsString('@click="openBulk()"',$popup);
        $sidebar=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/partials/_sidebar.blade.php'));
        $this->assertStringContainsString("mp-role-toggle {{ \$sectionActive ? 'is-active' : '' }}",$sidebar);
        $this->assertStringContainsString("\$sectionActive &&",$sidebar);
        foreach(['manajemen-praktikum/tugas-akhir/bulk-grades','components/manajemen-praktikum/ui/final-grades','components/manajemen-praktikum/ui/ta-grade-head'] as $view){
            $source=file_get_contents(base_path('Modules/EOffice/resources/views/'.$view.'.blade.php'));
            $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source),TOKEN_PARSE));
        }
    }

    public function test_grade_empty_states_are_centered_inside_the_table(): void
    {
        foreach(['manajemen-praktikum/tugas-akhir/rekap-asprak','components/manajemen-praktikum/ui/final-grades'] as $view){
            $source=file_get_contents(base_path('Modules/EOffice/resources/views/'.$view.'.blade.php'));
            $this->assertMatchesRegularExpression('/<tr x-show="visibleRows.length===0"[^>]*><td :?colspan="[^"]+" style="[^"]*text-align:center[^\"]*">Data tidak ditemukan\.<\/td><\/tr>/', $source);
        }
        $styles=file_get_contents(base_path('Modules/EOffice/resources/views/components/manajemen-praktikum/ui/grade-accordion.blade.php'));
        foreach(['.mp-grade-row:hover td.mp-grade-predicate','cursor:cell;user-select:none','--selection-background:#C7D2FE'] as $text)$this->assertStringContainsString($text,$styles);
    }

    public function test_multi_role_sidebar_has_exactly_one_active_role_and_menu(): void
    {
        $sections=collect(['koor','asprak','mahasiswa'])->map(fn($role)=>['label'=>$role,'match'=>'manprak.'.$role,'groups'=>['Kelola'=>[['label'=>'Praktikum','href'=>'/test/'.$role,'match'=>$role.'.praktikum','icon'=>'']]]])->all();
        foreach(['koor','asprak','mahasiswa'] as $role){
            $html=view('eoffice::manajemen-praktikum.partials._sidebar',['sections'=>$sections,'currentRoute'=>'eoffice.manprak.'.$role.'.praktikum.show','multiRole'=>true,'iBack'=>'','iGear'=>'','iLogout'=>''])->render();
            $this->assertSame(1,substr_count($html,'class="mp-role-toggle is-active"'));
            $this->assertSame(1,substr_count($html,'aria-current="page"'));
            $this->assertStringContainsString('href="/test/'.$role.'" class="mp-nav-link is-active"',$html);
        }
    }

    public function test_updated_views_compile_without_changing_card_nesting(): void
    {
        $views=["Modules/EOffice/resources/views/manajemen-praktikum/tugas-akhir/editor.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/tugas-akhir/attachments.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/partials/_sidebar.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/mahasiswa/tugas.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/mahasiswa/pengumuman.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/mahasiswa/nilai.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/mahasiswa/modul.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/koordinator/pendaftaran-asprak.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/koordinator/modul-detail.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/dosen/tugas.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/dosen/praktikum-detail.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/dosen/pengumuman.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/dosen/nilai.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/dosen/modul.blade.php","Modules/EOffice/resources/views/components/manajemen-praktikum/ui/tugas-akhir-card.blade.php","Modules/EOffice/resources/views/components/manajemen-praktikum/ui/ta-select.blade.php","Modules/EOffice/resources/views/components/manajemen-praktikum/ui/select.blade.php","Modules/EOffice/resources/views/components/manajemen-praktikum/ui/pagination.blade.php","Modules/EOffice/resources/views/components/manajemen-praktikum/ui/module-history.blade.php","Modules/EOffice/resources/views/components/manajemen-praktikum/ui/grade-group-filter.blade.php","Modules/EOffice/resources/views/components/manajemen-praktikum/ui/client-pagination.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/asprak/absensi-show.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/asprak/tugas.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/asprak/tugas-edit.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/asprak/tugas-create.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/asprak/pengumuman.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/asprak/modul.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/asprak/modul-detail.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/asprak/absensi.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/admin/praktikum.blade.php","Modules/EOffice/resources/views/manajemen-praktikum/admin/matkul-praktikum.blade.php"];
        foreach ($views as $path) {
            $source=file_get_contents(base_path($path));
            $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source),TOKEN_PARSE),$path);
            $this->assertSame(substr_count($source,'<x-eoffice::manajemen-praktikum.ui.grade-accordion ')+substr_count($source,'<x-eoffice::manajemen-praktikum.ui.grade-accordion>'),substr_count($source,'</x-eoffice::manajemen-praktikum.ui.grade-accordion>'),$path);
        }
    }

    public function test_revision_information_has_distinct_labels_and_fallback_note(): void
    {
        $html=view('eoffice::manajemen-praktikum.tugas-akhir.revision-summary',['stage'=>['revisi_aktif'=>true,'revisi_deadline'=>'2026-10-31 20:21:00','revisi_metode'=>'rata_rata']])->render();
        foreach (['Ketentuan Revisi','Tenggat revisi','Metode penilaian','Rata-rata nilai awal dan revisi','Tanpa pengumpulan revisi, nilai awal tetap digunakan.'] as $label) $this->assertStringContainsString($label,$html);
    }

    public function test_attachment_caption_uses_primary_blue(): void
    {
        $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/tugas-akhir/attachments.blade.php'));
        $this->assertStringContainsString('background:#0B266E',$source);
        $this->assertStringNotContainsString('background:#293C79',$source);
    }

    public function test_submission_layout_and_revision_popup_match_shared_patterns(): void
    {
        $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/asprak/tugas-pengumpulan.blade.php'));
        $this->assertStringContainsString('<x-slot name="header">',$source);
        foreach (['Cari nama / NIM...','Semua Kelompok','Semua Shift',"color:hasChanges ? '#fff' : '#0B266E'"] as $text) $this->assertStringContainsString($text,$source);
        $popup=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/asprak/module-revision-modal.blade.php'));
        foreach (['top:50%;left:50%;transform:translate(-50%,-50%)','Berkas Saat Ini:','border:1px dashed #DFE1E7','Batas akumulasi maksimal lampiran adalah 3 berkas per tugas.'] as $text) $this->assertStringContainsString($text,$popup);
        $summary=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/tugas-akhir/rekap-inline.blade.php'));
        $this->assertStringNotContainsString('client-pagination :separator="true"',$summary);
    }

    public function test_assistant_recap_templates_compile_and_share_save_export_controls(): void
    {
        foreach(['manajemen-praktikum/tugas-akhir/rekap-asprak','components/manajemen-praktikum/ui/grade-history','components/manajemen-praktikum/ui/nilai-csv-picker'] as $path){
            $source=file_get_contents(base_path('Modules/EOffice/resources/views/'.$path.'.blade.php'));
            $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source),TOKEN_PARSE));
        }
        $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/asprak/absensi.blade.php'));
        foreach(['registerExtraSave','get isDirty()',':separator="true"','asprak.absensi-toolbar','data-absensi-manager'] as $text)$this->assertStringContainsString($text,$source);
        $toolbar=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/asprak/absensi-toolbar.blade.php'));
        foreach(['ui.nilai-csv-picker',"cursor:!isDirty || isSaving ? 'not-allowed'"] as $text)$this->assertStringContainsString($text,$toolbar);
        $history=file_get_contents(base_path('Modules/EOffice/resources/views/components/manajemen-praktikum/ui/grade-history.blade.php'));
        foreach(['mp-grade-history-cell:hover','opacity:0','name="history"'] as $text)$this->assertStringContainsString($text,$history);
    }

    public function test_repeated_accordions_never_print_css_as_visible_text(): void
    {
        $html=\Illuminate\Support\Facades\Blade::render('<div x-data="{expanded:true}"><x-eoffice::manajemen-praktikum.ui.grade-accordion>Modul</x-eoffice::manajemen-praktikum.ui.grade-accordion><x-eoffice::manajemen-praktikum.ui.grade-accordion>Tugas Akhir</x-eoffice::manajemen-praktikum.ui.grade-accordion></div>');
        $visible=strip_tags(preg_replace('~<style\b[^>]*>[\s\S]*?</style>~i','',$html));
        $this->assertStringNotContainsString('.mp-grade-card-header:hover',$visible);
        $this->assertStringNotContainsString('.mp-grade-row:hover',$visible);
        $this->assertSame(1,substr_count($html,'.mp-grade-card-header:hover'));
        $this->assertStringContainsString('.mp-grade-row:hover td.mp-grade-tint',$html);
    }

    public function test_revision_placeholders_and_history_use_shared_layout(): void
    {
        $enabled=\Illuminate\Support\Facades\Blade::render('<x-eoffice::manajemen-praktikum.ui.grade-score-input model="score" />');
        $unavailable=\Illuminate\Support\Facades\Blade::render('<x-eoffice::manajemen-praktikum.ui.grade-score-input :unavailable="true" label="Belum mengumpulkan revisi" />');
        foreach([$enabled,$unavailable] as $html)$this->assertStringContainsString('height:32px;line-height:20px;width:64px',$html);
        $this->assertStringContainsString('value="—"',$unavailable);
        $this->assertStringContainsString('disabled',$unavailable);
        $history=file_get_contents(base_path('Modules/EOffice/resources/views/components/manajemen-praktikum/ui/grade-history.blade.php'));
        $this->assertStringContainsString('Kolom Penilaian:',$history);
        $this->assertStringContainsString('grid-template-columns:30px minmax(0,1fr) 30px',$history);
        $popup=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/tugas-akhir/bulk-grades.blade.php'));
        foreach(['Isi Nilai Terpilih','mp-assessment-trigger','availableAssessmentOptions','Tambah Kolom Penilaian','No Dipilih','bulkTarget'] as $text)$this->assertStringContainsString($text,$popup);
        $this->assertStringNotContainsString('type="checkbox"',$popup);
        $recap=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/tugas-akhir/rekap-asprak.blade.php'));
        foreach(['beginSelection','finishSelection','@mouseup.window','assessmentOptions'] as $text)$this->assertStringContainsString($text,$recap);
    }

    public function test_task_borders_and_assistant_card_order_are_consistent(): void
    {
        foreach (['asprak','dosen','mahasiswa'] as $role) {
            $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/'.$role.'/tugas.blade.php'));
            $this->assertStringContainsString("\$loop->last ? '' : 'border-bottom:1px solid var(--c-border);'",$source);
            $this->assertStringContainsString('box-shadow:none; overflow: hidden;',$source);
            $this->assertStringContainsString("boxShadow:modulOpen ? 'inset 0 -1px 0 var(--c-border)' : 'none'",$source);
        }
        $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/asprak/absensi.blade.php'));
        $this->assertMatchesRegularExpression('/@endforelse\s*<x-eoffice::manajemen-praktikum.ui.tugas-akhir-card[^>]*\/>\s*<x-eoffice::manajemen-praktikum.ui.final-grades[^>]*embedded="true"/',$source);
        $dashboard=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/asprak/dashboard.blade.php'));
        $this->assertStringContainsString('font-weight:700; font-size:16px; color:#0D0D12; margin-bottom: 2px;',$dashboard);
        $this->assertStringContainsString('font-size:12px; color:#374151;',$dashboard);
    }
}

