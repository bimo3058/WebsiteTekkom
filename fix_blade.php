<?php
$file = __DIR__ . '/Modules/EOffice/resources/views/manajemen-praktikum/admin/pendaftaran-asprak.blade.php';
$content = file_get_contents($file);
$content = str_replace('$pendaftarans', '$pendaftaran', $content);
file_put_contents($file, $content);
echo "Replaced successfully.\n";
