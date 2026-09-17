const fs = require('fs');
let c = fs.readFileSync('Modules/EOffice/resources/views/manajemen-praktikum/asprak/tugas-pengumpulan.blade.php', 'utf8');
c = c.replace(/\?\?:/g, '&#128172;:');
c = c.replace(/&middot;/g, '&ndash;');
fs.writeFileSync('Modules/EOffice/resources/views/manajemen-praktikum/asprak/tugas-pengumpulan.blade.php', c, 'utf8');
console.log('done5');
