<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$t = Modules\EOffice\Models\Tugas::first();
$req = Illuminate\Http\Request::create("/", "POST", ["deadline" => "2026-09-17T11:46", "deadline_acc" => "2026-09-18T11:46"]);
$data = $req->only(["deadline", "deadline_acc"]);
$merged = [...$data, "file_path" => null];
print_r($merged);
$t->update($merged);
var_dump($t->refresh()->deadline);

