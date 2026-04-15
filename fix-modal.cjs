const fs = require('fs');
const file = 'Modules/BankSoal/resources/views/pages/bank-soal/Dosen/index.blade.php';
let content = fs.readFileSync(file, 'utf8');

// The modal text starts at "<!-- Tarik Soal Modal -->"
const modalStart = content.indexOf('<!-- Tarik Soal Modal -->');
if (modalStart > -1) {
    const modalContent = content.substring(modalStart);
    content = content.substring(0, modalStart);
    
    // find </x-banksoal::layouts.dosen-admin>
    const endTag = '</x-banksoal::layouts.dosen-admin>';
    content = content.replace(endTag, modalContent + '\n' + endTag);
    fs.writeFileSync(file, content);
    console.log('Fixed location of modal');
}
