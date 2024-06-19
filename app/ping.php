<?php
include_once("includes/functions.php");
$json = [
    "current_heat" => intval(Heat::current_heat() ? Heat::current_heat() : 0),
    "total_heats" => count(Heat::all()),
];
echo json_encode($json);