
const fs = require('fs');
let c = fs.readFileSync('Modules/EOffice/resources/views/manajemen-praktikum/asprak/tugas-pengumpulan.blade.php', 'utf8');
c = c.replace(/\{\{\s*\\\->user\?->name \?\? '.*?'\s*\}\}/g, '{!! \->user?->name ?? \'&ndash;\' !!}');
c = c.replace(/\{\{\s*\\\->user\?->student\?->student_number \?\? '.*?'\s*\}\}/g, '{!! \->user?->student?->student_number ?? \'&ndash;\' !!}');
c = c.replace(/\{\{\s*\\\ \?\: '.*?'\s*\}\}/g, '{!! \ ?: \'&ndash;\' !!}');
fs.writeFileSync('Modules/EOffice/resources/views/manajemen-praktikum/asprak/tugas-pengumpulan.blade.php', c, 'utf8');
console.log('done2');

