<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps  = false;
    protected $fillable = ['user_id', 'module', 'action', 'subject_type', 'subject_id', 'description', 'created_at'];
    protected $casts    = ['created_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function getModuleLabelAttribute(): string
    {
        return match($this->module) {
            'auth'                => 'Auth', 
            'bank_soal'           => 'Bank Soal',
            'capstone'            => 'Capstone',
            'eoffice'             => 'E-Office',
            'manajemen_mahasiswa' => 'Manajemen Mahasiswa',
            'user_management'     => 'User Management',
            default               => ucfirst($this->module),
        };
    }

    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'CREATE' => 'bg-green-500/20 text-green-300 border-green-500/30',
            'UPDATE' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
            'DELETE' => 'bg-red-500/20 text-red-300 border-red-500/30',
            'VIEW'   => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
            'LOGIN'  => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
            default  => 'bg-slate-500/20 text-slate-300 border-slate-500/30',
        };
    }
}