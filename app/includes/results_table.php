<?php
include_once("functions.php");
if (!isset($hide_racers_with_no_results)) {
    $hide_racers_with_no_results = false;
}
$racers_with_rank = Result::racer_rankings($hide_racers_with_no_results);
$total_groups = Group::count() > 1;
?>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark table-dark">
                <tr>
                    <th scope="col">Place</th>
                    <th scope="col">Car #</th>
                    <th scope="col">Name</th>
                    <?= $total_groups ? '<th scope="col">Group</th>' : '' ?>
                    <th scope="col">Ranking</th>
                    <?= $hide_racers_with_no_results ? '' : '<th scope="col">Heats</th>' ?>
                </tr>
                </thead>
                <tbody>
                <?php
                $index = 1;
                $place = 0;
                $previous_place = 0;
                foreach($racers_with_rank as $rank => $racers) {
                    $place++;
                    if($previous_place != $place) {
                        $place = $index;
                    } else {
                        $previous_place = $place;
                    }
                    foreach($racers as $racer) {
                        $bg = [" bg-primary", " bg-danger", " bg-warning"];
                        $class = (count($bg) >= $place) ? $bg[$place-1] : '';
                        $display_rank = round($rank * 100, 0);
                        $display_place = $place;
                        if ($rank == '0.0') {
                            $display_rank = 'n/a';
                            $class = '';
                            $display_place = '&nbsp;';
                        }
                    ?>
                        <tr id="racer-result-<?= $index ?>"  class="lead<?= $class ?>">
                            <th scope="row"><?= $display_place ?></th>
                            <td><?= $racer->id() ?></td>
                            <td><?= $racer->name() ?></td>
                            <?= $total_groups ? '<td>' . $racer->group()->name() . '</td>' : '' ?>
                            <td><?= $display_rank ?></td>
                            <?= $hide_racers_with_no_results ? '' : '<td>' . $racer->count_results() . '/' . count($racer->heats()) . '</td>' ?>
                        </tr>

                        <?php
                        $index++;
                    }
                }

                if (count($racers_with_rank) == 0) {
                    $colspan = 6;
                    if ($hide_racers_with_no_results) {
                        $colspan--;
                    }
                    if (!$total_groups) {
                        $colspan--;
                    }
                    echo '<tr><td colspan="' . $colspan . '"><div class="alert alert-info"><strong>No racers have been created.</strong></div></td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>