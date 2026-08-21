<?php
$message = "Setuju veh-80 e3";
if (!preg_match('/^(SETUJU|TOLAK|SELESAI|COMPLETED)\s+([A-Z]+)-(\d+)(?:\s*([a-zA-Z])(?:\s*(\d+))?)?$/i', trim($message), $matches)) {
    echo "No match\n";
} else {
    print_r($matches);
    $optLetter  = isset($matches[4]) ? strtoupper($matches[4]) : '';
    echo "optLetter: $optLetter\n";
    $idx = ord($optLetter) - 65;
    echo "idx: $idx\n";
}
