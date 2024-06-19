<?php include_once("includes/header.php");
$heats = Heat::all();

$racers_with_rank = [];
if(count($heats) > 0){
    $racers = Racer::all();
    foreach ($racers as $racer) {
        $rank = strval($racer->ranking_value());
        if (!str_contains($rank, '.')) {
            $rank .= '.';
        }
        $rank .= intval(($racer->count_results() / count($racer->heats())) * 1000);
        $rank .= str_pad($racer->count_results(), 2, "0", STR_PAD_LEFT);
        $rank .= str_pad($racer->id(), 2, "0", STR_PAD_LEFT);
        $racers_with_rank[$rank] = $racer;
    }
    krsort($racers_with_rank, SORT_NUMERIC);
}
?>

    <!-- Index Racers -->
    <div class="row">
        <div class="col-md-12">
            <h2>Results</h2>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">Place</th>
                        <th scope="col">Car #</th>
                        <th scope="col">Name</th>
                        <th scope="col">Group</th>
                        <th scope="col">Rating</th>
                        <th scope="col">Heats</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $place = 1;
                    foreach($racers_with_rank as $rank => $racer) {
                    ?>
                        <tr>
                            <th scope="row"><?= $place ?></th>
                            <td><?= $racer->id() ?></td>
                            <td><?= $racer->name() ?></td>
                            <td><?= $racer->group()->name() ?></td>
                            <td><?= $rank ?></td>
                            <td><?= $racer->count_results() ?>/<?= count($racer->heats()) ?></td>
                        </tr>
                    <?php
                        $place++;
                    }
                    if (count($racers_with_rank) == 0) {
                        echo '<tr><td colspan="6"><div class="alert alert-info"><strong>No results have been entered yet.</strong></div></td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php include_once("includes/footer.php");
