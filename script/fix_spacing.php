
<?php
$f = "Modules/EOffice/resources/views/manajemen-praktikum/dosen/modul.blade.php";
$c = file_get_contents($f);
$c = preg_replace("/<div\s+style=\"font-size:\s*14px;\s*color:\s*\#374151;\s*margin-bottom:\s*20px;.*?\">[\s\S]*?\{\{\s*trim\(\\$modul->deskripsi\)\s*\}\}\s*<\/div>/im", "<div style=\"font-size: 14px; color: #374151; margin-bottom: 20px; line-height: 1.5; white-space: pre-wrap; text-align: left;\">{{ trim(\$modul->deskripsi) }}</div>", $c);
file_put_contents($f, $c);

