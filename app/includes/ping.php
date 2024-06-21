<?php
include_once("functions.php");
$heats = Heat::current_heat(3);

$current_heat_num = null;
if (count($heats)) {
    $current_heat_num = $heats[0]->id();
}

$heat_ids = [];
foreach ($heats as $heat) {
    $heat_ids[] = $heat->id();
}

$json = [
    "current_heat" => $current_heat_num,
    "total_heats" => Heat::count(),
    "heats_to_show" => $heat_ids,
];

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    echo json_encode($json);
}
