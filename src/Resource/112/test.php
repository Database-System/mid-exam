<?php
$jsonFiles = glob('./*.json');
foreach ($jsonFiles as $file) {
    $jsondata = file_get_contents($file);
    $records = json_decode($jsondata, true);
    foreach ($records["items"] as $record) {
        echo $record["scr_selcode"].PHP_EOL.$record["sub_name"].PHP_EOL.$record["scr_credit"].PHP_EOL;
    }
}