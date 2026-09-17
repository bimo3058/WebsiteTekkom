const fs = require('fs');
let c = fs.readFileSync('Modules/EOffice/resources/views/manajemen-praktikum/asprak/tugas-pengumpulan.blade.php', 'utf8');
c = c.replace(/\?\? Mhs:/g, '&#128172; Mhs:');
fs.writeFileSync('Modules/EOffice/resources/views/manajemen-praktikum/asprak/tugas-pengumpulan.blade.php', c, 'utf8');
console.log('done4');
