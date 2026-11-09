<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ManprakModalFooterTest extends TestCase
{
    public function test_padded_modal_footers_extend_to_modal_edges(): void
    {
        $root = dirname(__DIR__, 2).'/Modules/EOffice/resources/views/manajemen-praktikum/';
        foreach ([
            'asprak/modul' => 1, 'asprak/pengumuman' => 2,
            'mahasiswa/daftar-asprak' => 4, 'admin/matkul-praktikum' => 2,
            'admin/praktikum' => 2, 'admin/praktikum-detail' => 1,
            'koordinator/pengumuman' => 1, 'koordinator/pendaftaran-asprak' => 1,
            'dosen/pendaftaran-koor' => 1, 'admin/kelola-role' => 3,
        ] as $view => $count) {
            $source = file_get_contents($root.$view.'.blade.php');
            $this->assertSame($count, substr_count($source, 'mp-modal-footer-bleed'), $view);
        }
        $styles = file_get_contents($root.'partials/_shell-styles.blade.php');
        $this->assertStringContainsString('background:#FAFBFC !important;', $styles);
        $this->assertStringContainsString('margin-inline:calc(-1 * var(--mp-modal-inline-inset, 24px))', $styles);
        $this->assertStringContainsString('margin-bottom:calc(-1 * var(--mp-modal-bottom-inset, 24px))', $styles);
    }

    public function test_group_editor_has_one_full_width_footer_not_a_nested_footer(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2).'/Modules/EOffice/resources/views/manajemen-praktikum/koordinator/daftar-praktikan.blade.php');
        $this->assertSame(1, substr_count($source, 'class="mp-modal-footer'));
        $this->assertStringContainsString('mp-modal-footer mp-modal-footer-split', $source);
        $this->assertStringContainsString('class="mp-modal-actions"', $source);
    }
}
