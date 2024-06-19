<?php
include_once("includes/header.php");
?>

<header class="container-fluid bg-dark text-white py-3">
    <div class="container text-center">
        <h1 id="head_h1"><a href="/admin">Current Heat</a></h1>
    </div>
</header>


<?php
$current_heat = intval(Heat::current_heat() ? Heat::current_heat() : 0);
$total_heats = count(Heat::all());

$i = 0;
$heats = [];
while($current_heat <= $total_heats and $i < 3) {
    $heat = Heat::get($current_heat + $i);
    if ($heat) {
        $heats[] = $heat;
    }
    $i++;
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
    echo '<div class="container-fluid">';
        echo '<div class="row">';
            $lane = 1;
            foreach ($heat->cars() as $car) {
                $class = $bg_map[$heat_index][$lane - 1];
                $style = ($lane == 1) ? ' style="font-size:1.3rem !important;"' : '';
                echo '<div class="text-center border rounded m-2 col-sm p-sm-2'.$class.'"'.$style.'>';
                echo '<h2>'.$car->name().'</h2>';
                echo '<h1>#'.$car->id().'</h1>';
                echo '<h4>Lane ' . $lane . '</h4>';
                echo '</div>';
                $lane++;
            }
        echo '</div>';
    echo '</div>';
    if ($heat_index == 0) {
?>
        <header class="container-fluid bg-dark text-white py-3 mt-3">
            <div class="container text-center">
                <h1 id="head_h1">On Deck Heat</h1>
            </div>
        </header>
<?php
    }
    $heat_index++;
}
?>

<?php
include_once("includes/footer.php");
?>