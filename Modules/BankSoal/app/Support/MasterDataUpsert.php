<?php

namespace Modules\BankSoal\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Aturan penyimpanan data master.
 *
 * Identitas sebuah data master ditentukan oleh KESELURUHAN field form:
 * - semua field sama persis  -> dianggap satu data yang sama, baris lama dipakai
 *   lagi (dipulihkan bila sebelumnya di-soft delete), tidak ada baris baru;
 * - ada satu saja field berbeda -> dianggap data berbeda, baris baru ditulis.
 *
 * Baris yang sudah di-soft delete ikut diperiksa supaya data yang "disembunyikan"
 * tombol hapus tidak menghasilkan duplikat saat diisi ulang.
 */
class MasterDataUpsert
{
    /**
     * Rapikan nilai field sebelum dibandingkan (string di-trim).
     *
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    public static function normalize(array $values): array
    {
        $normalized = [];

        foreach ($values as $column => $value) {
            $normalized[$column] = is_string($value) ? trim($value) : $value;
        }

        return $normalized;
    }

    /**
     * Cari baris yang sama persis pada seluruh field identitas, termasuk baris
     * yang sudah di-soft delete.
     *
     * @template TModel of Model
     * @param  class-string<TModel>  $modelClass
     * @param  array<string, mixed>  $identity
     * @return TModel|null
     */
    public static function findIdentical(string $modelClass, array $identity): ?Model
    {
        $query = $modelClass::query();

        if (in_array(SoftDeletes::class, class_uses_recursive($modelClass), true)) {
            $query->withTrashed();
        }

        return $query->where(self::normalize($identity))->first();
    }

    /**
     * Ambil baris identik bila ada (sekaligus memulihkannya bila ter-soft delete),
     * atau tulis baris baru bila tidak ada data yang sama persis.
     *
     * @template TModel of Model
     * @param  class-string<TModel>  $modelClass
     * @param  array<string, mixed>  $identity    kolom => nilai (seluruh field form)
     * @param  array<string, mixed>  $attributes  nilai tambahan saat membuat baris baru
     * @return array{record: TModel, created: bool, restored: bool}
     */
    public static function firstOrCreateIdentical(string $modelClass, array $identity, array $attributes = []): array
    {
        $identity = self::normalize($identity);
        $existing = self::findIdentical($modelClass, $identity);

        if ($existing) {
            $restored = false;

            if (method_exists($existing, 'trashed') && $existing->trashed()) {
                $existing->restore();
                $restored = true;
            }

            return ['record' => $existing, 'created' => false, 'restored' => $restored];
        }

        /** @var TModel $created */
        $created = $modelClass::create(array_merge($identity, $attributes));

        return ['record' => $created, 'created' => true, 'restored' => false];
    }

    /**
     * Pesan standar saat data yang diisi ternyata sudah ada.
     */
    public static function existingMessage(string $label, bool $restored): string
    {
        if ($restored) {
            return "Data {$label} dengan isi yang sama sudah ada dan dipulihkan kembali. Menampilkan data tersebut.";
        }

        return "Data {$label} dengan isi yang sama sudah ada. Menampilkan data tersebut.";
    }
}
