<?php
$blade = file_get_contents("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php");
// just compile it manually for testing by running regex for unclosed tags
$openForeach = substr_count($blade, "@foreach") + substr_count($blade, "@forelse");
$closeForeach = substr_count($blade, "@endforeach") + substr_count($blade, "@endforelse");
echo "Foreach: $openForeach / $closeForeach\n";

$openIf = substr_count($blade, "@if") - substr_count($blade, "@endif"); // Wait, @elseif doesnt count
echo "If (approx): " . substr_count($blade, "@if") . " / " . substr_count($blade, "@endif") . "\n";
?>
