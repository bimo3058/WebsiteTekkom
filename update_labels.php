<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/Modules/EOffice/resources/views/manajemen-praktikum');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$replacements = 0;
foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    
    // Replace standalone 'Koor' with 'Koordinator'
    // Negative lookbehind and lookahead mencegah penggantian pada URL, class, variabel, dan file path
    $patternKoor = '/(?<![\$\-_.\/\\\\\'\"a-zA-Z])(Koor)(?![\-_.\/\\\\\'\"a-zA-Z])/i';
    
    // Replace standalone 'Asprak' with 'Asisten Praktikum'
    $patternAsprak = '/(?<![\$\-_.\/\\\\\'\"a-zA-Z])(Asprak)(?![\-_.\/\\\\\'\"a-zA-Z])/i';
    
    $newContent = preg_replace_callback($patternKoor, function($matches) {
        return $matches[1] === 'koor' ? 'koordinator' : ($matches[1] === 'KOOR' ? 'KOORDINATOR' : 'Koordinator');
    }, $content);
    
    $newContent = preg_replace_callback($patternAsprak, function($matches) {
        return $matches[1] === 'asprak' ? 'asisten praktikum' : ($matches[1] === 'ASPRAK' ? 'ASISTEN PRAKTIKUM' : 'Asisten Praktikum');
    }, $newContent);
    
    if ($content !== $newContent) {
        file_put_contents($path, $newContent);
        echo 'Berhasil merapikan tata bahasa di file: ' . $path . PHP_EOL;
        $replacements++;
    }
}
echo 'Total file UI yang dirapikan: ' . $replacements . PHP_EOL;
