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
    $heat_ids = [];
    foreach ($heats as $heat) {
        $heat_ids[] = $heat->id();
    }
    $json = [
        "current_heat" => $current_heat_num,
        "total_heats" => Heat::count(),
        "heats_to_show" => $heat_ids,
    ];
} else {
    $total_heats = $json['total_heats'];
    $current_heat_num = $json['current_heat'];
}

if ($current_heat_num == null && $total_heats != 0) {
    echo '<header class="container-fluid bg-dark text-white py-3"><div class="container text-center">';
    echo '<h1 id="head_h1">Results</h1>';
    echo '</div></header>';
    $hide_racers_with_no_results = true;
    echo '<div class="container mt-2">';
    include_once("includes/results_table.php");
    echo '</div>';
} else {

echo '<header class="container-fluid bg-dark text-white py-3"><div class="container text-center">';
if($current_heat_num != null) {
    $header_text = 'Running Heat ' . $current_heat_num;
} else {
    $header_text = 'No Heats';
}

echo '<h1 id="head_h1"><span class="current_heat"></span>'.$header_text.'</h1>';
echo '</div></header>';
?>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-hover table-bordered text-muted" id="heats">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Lane 1</th>
                    <th scope="col">Lane 2</th>
                    <th scope="col">Lane 3</th>
                    <th scope="col">Lane 4</th>
                    <th scope="col">Lane 5</th>
                    <th scope="col">Lane 6</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $heats = Heat::all();
                $total = Heat::count();
                foreach($heats as $heat) {
                    $racers = $heat->cars();
                    $class = '';
                    if ($current_heat_num == $heat->id()) {
                        $class = ' class="bg-info text-dark"';
                    }
                    echo '<tr id="heat'.$heat->id().'"' . $class . '>';
                    echo '<th scope="row">' . $heat->id() . '</th>';
                    foreach($racers as $racer) {
                        echo '<td data-racer="racer-' . $racer->id() . '">#' . $racer->id() . ": " . $racer->name() . '</td>';
                    }
                    if (count($racers) < 6) {
                        $diff = 6 - count($racers);
                        for($i = 0; $i < $diff; $i++) {
                            echo '<td>&nbsp;</td>';
                        }
                    }
                    echo '</tr>';
                }
                if ($total == 0) {
                    echo '<tr><td colspan="7"><div class="alert alert-info"><strong>No heats generated yet.</strong></div></td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
}
include_once("includes/footer.php");
