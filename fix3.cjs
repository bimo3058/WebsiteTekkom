const fs = require('fs');
let c = fs.readFileSync('Modules/EOffice/resources/views/manajemen-praktikum/asprak/tugas-pengumpulan.blade.php', 'utf8');
c = c.replace(/\{\{\s*\$pr->user\?->name \?\? '.*?'\s*\}\}/g, '{!! $pr->user?->name ?? \'&ndash;\' !!}');
c = c.replace(/\{\{\s*\$pr->user\?->student\?->student_number \?\? '.*?'\s*\}\}/g, '{!! $pr->user?->student?->student_number ?? \'&ndash;\' !!}');
c = c.replace(/\{\{\s*\$displayNilai \?: '.*?'\s*\}\}/g, '{!! $displayNilai ?: \'&ndash;\' !!}');
c = c.replace(/\{\{\s*\$displayNilai \?\: '.*?'\s*\}\}/g, '{!! $displayNilai ?: \'&ndash;\' !!}');
fs.writeFileSync('Modules/EOffice/resources/views/manajemen-praktikum/asprak/tugas-pengumpulan.blade.php', c, 'utf8');
console.log('done3');
