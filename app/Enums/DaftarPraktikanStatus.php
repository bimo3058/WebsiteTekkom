<?php

namespace App\Enums;

enum DaftarPraktikanStatus: string
{
    case Terdaftar = 'terdaftar';
    case Lulus = 'lulus';
    case Gagal = 'gagal';
}
