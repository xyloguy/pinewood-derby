<?php
include_once("includes/header.php");
include_once("includes/ping.php");

if (!isset($heats)) {
    $heats = Heat::current_heat(3);
}
if (!isset($json)) {
    $total_heats = count(Heat::all());
    $current_heat_num = null;
    if (count($heats)) {
        $current_heat_num = $heats[0]->id();
    }
} else {
    $total_heats = $json['total_heats'];
    $current_heat_num = $json['current_heat'];
}

$heat_index = 0;
$bg_map = [
    [
        ' bg-danger text-white mt-3',
        ' bg-dark text-white mt-3',
        ' bg-success text-white mt-3',
        ' bg-primary text-white mt-3',
        ' bg-warning mt-3',
        ' bg-info text-white mt-3',
    ],
    [
        ' border-danger',
        ' border-dark',
        ' border-success',
        ' border-primary',
        ' border-warning',
        ' border-info',
    ],
    [
        ' border-danger',
        ' border-dark',
        ' border-success',
        ' border-primary',
        ' border-warning',
        ' border-info',
    ]
];

foreach ($heats as $heat) {
    if ($heat_index == 0) {
        echo '<header class="container-fluid bg-dark text-white py-3"><div class="container text-center">';
            echo '<h1 id="head_h1"><a href="/heats.php">Current Heat ' . $heat->id() . '</a></h1>';
        echo '</div></header>';
    } else {
        echo '<header class="container-fluid bg-dark text-white py-1"><div class="container text-center">';
            echo '<h3 id="head_h1"><a href="/heats.php">On Deck</a></h3>';
        echo '</div></header>';
    }
    echo '<div class="container-fluid">';
        echo '<div class="row">';
            $divs = 0;
            $cars = $heat->cars();
            $total_cars = count($cars);
            $remaining_cars = 6 - $total_cars;
            $half = intdiv($remaining_cars, 2);
            for($i = 0; $i < $half; $i++) {
                echo '<div class="m-2 col-sm p-sm-2"></div>';
                $divs++;
            }
            $lane = 1;
            foreach ($cars as $car) {
                $class = $bg_map[$heat_index][$lane - 1];
                $style = ($lane == 1) ? ' style="font-size:1.3rem !important;"' : '';
                echo '<div class="text-center border rounded m-2 col-sm p-sm-2'.$class.'"'.$style.'>';
                echo '<h2>'.$car->name().'</h2>';
                echo '<h1>#'.$car->id().'</h1>';
                echo '<h4>Lane ' . $lane . '</h4>';
                echo '</div>';
                $lane++;
                $divs++;
            }
            while ($divs < 6){
                echo '<div class="m-2 col-sm p-sm-2"></div>';
                $divs++;
            }
        echo '</div>';
    echo '</div>';
    $heat_index++;
}

if (is_null($current_heat_num)) {
    echo '<header class="container-fluid bg-dark text-white py-3 sticky-top">';
        echo '<div class="container text-center">';
        if ($total_heats == 0) {
            echo '<h1 id="head_h1"><a href="/admin/heats.php">No Heats Found</a></h1>';
        } else {
            echo '<h1 id="head_h1">Results</h1>';
        }
        echo '</div>';
    echo '</header>';
    if ($total_heats != 0) {
        $hide_racers_with_no_results = true;
        echo '<div class="container mt-2">';
        include_once("includes/results_table.php");
        echo '</div>';
    }
}

include_once("includes/footer.php");
