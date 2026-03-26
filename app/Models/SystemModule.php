<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemModule extends Model
{
    protected $table = 'system_modules';

    protected $fillable = [
        'name', 'slug', 'icon', 'description',
        'is_active', 'is_maintenance', 'settings',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'is_maintenance' => 'boolean',
        'settings'       => 'array',
    ];

    public function setting(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    // Audit log table per modul
    public function auditLogTable(): string
    {
        return match($this->slug) {
            'bank-soal'            => 'bs_audit_logs',
            'manajemen-mahasiswa'  => 'mk_audit_logs',
            'capstone'             => 'capstone_audit_logs',
            'eoffice'              => 'eo_audit_logs',
            default                => 'audit_logs',
        };
    }
}