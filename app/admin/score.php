<?php include_once("includes/header.php");
if (clean_post('addresult') || clean_post('updateresult')) {
    if (
        !empty($_POST['heatid'])
        && !empty($_POST['points'])
        && !empty($_POST['results'])
    ) {
        $heatid = db()->real_escape_string($_POST['heatid']);
        $points = [];
        foreach ($_POST['points'] as $point) {
            $points[] = db()->real_escape_string($point);
        }
        $results = [];
        foreach ($_POST['results'] as $result) {
            $r = db()->real_escape_string($result);
            if (empty($r)) {
                $results[] = null;
            } else {
                $results[] = $r;
            }
        }
        $heat = Heat::get($heatid);
        if (!is_null($heat)) {
            $racers_ids = explode(',', $heat->racers());
            for ($i = 0; $i < min(count($racers_ids), count($points), count($results)); $i++) {
                $res = new Result([$results[$i], $heatid, $racers_ids[$i], $points[$i]]);
                $res->save();
            }
        }
    }
}


if (clean_post('clearresult')) {
    if (!empty($_POST['heatid']) && !empty(trim($_POST['heatid']))) {
        $heatid = db()->real_escape_string($_POST['heatid']);
        Result::delete_by_heat($heatid);
    }
}

if (clean_post('deleteheat')) {
    if (!empty($_POST['heatid']) && !empty(trim($_POST['heatid']))) {
        $heatid = db()->real_escape_string($_POST['heatid']);
        Heat::delete($heatid);
    }
}

$current_heat = Heat::current_heat(1);
$current_heat_num = null;
if (count($current_heat)) {
    $current_heat_num = $current_heat[0]->id();
    $i = 'row-heat-' . $current_heat[0]->id();
    echo '<script>';
    echo 'if (!window.location.hash.includes("'.$i.'")) {';
    echo 'window.location = location.pathname + "#'.$i.'";';
    echo '}';
    echo '</script>';
}
?>
<main>
    <!-- Index Racers -->
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered text-center text-muted" id="results">
                    <thead class="thead-dark table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Lane 1</th>
                        <th scope="col">Lane 2</th>
                        <th scope="col">Lane 3</th>
                        <th scope="col">Lane 4</th>
                        <th scope="col">Lane 5</th>
                        <th scope="col">Lane 6</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $heats = Heat::all();
                    $total = count($heats);
                    $options = ['1st', '2nd', '3rd', '4th', '5th', '6th'];
                    foreach($heats as $heat) {
                        $racers = $heat->cars();
                        echo '<tr' . (($current_heat_num == $heat->id()) ? ' class="table-active text-dark"' : '') . ' id="row-heat-' . $heat->id() . '">';
                        echo '<form method="post" action="score.php">';
                        echo '<th scope="row" class="align-middle">';
                        echo '<input type="hidden" name="checksum[]" value="' . get_checksum('addresult') . '">';
                        echo '<input type="hidden" name="checksum[]" value="' . get_checksum('updateresult') . '">';
                        echo '<input type="hidden" name="checksum[]" value="' . get_checksum('clearresult') . '">';
                        echo '<input type="hidden" name="checksum[]" value="' . get_checksum('deleteheat') . '">';
                        echo '<input type="hidden" name="heatid" value="' . $heat->id() . '">';
                        echo  $heat->id();
                        echo'</th>';
                        $total_racers = count($racers);
                        foreach($racers as $racer) {
                            echo '<td class="click-area align-middle">';
                            echo '<div class="form-group text-center pt-2 pb-2">';
                            $form_field_id = 'racer-' . $racer->id() . '-heat-' . $heat->id();
                            $result = Result::get_by_heat_and_racer($heat->id(), $racer->id());
                            $result_id = null;
                            $result_points = null;
                            if (!is_null($result)){
                                $result_id = $result->id();
                                $result_points = $result->points();
                            }
                            echo '<input type="hidden" name="results[]" value="';
                            echo (!is_null($result_id) ? $result_id : '');
                            echo '">';
                            echo '<label style="display:block;" for="' . $form_field_id .'"><h3>' . $racer->name() . '</h3><h1>#' . $racer->id() . '</h1>';
                            echo '<select id="' . $form_field_id . '" class="custom-select custom-select-md form-select form-control" name="points[]">';
                            echo '<option value="0"></option>';
                            for($current_index=0, $current_points=$total_racers; $current_index < $total_racers; $current_index++, $current_points--) {
                                $selected = (!is_null($result_points) && $result_points === $current_points) ? ' selected' : '';
                                echo '<option value="' . $current_points . '"' . $selected . '>' . $options[$current_index] . '</option>';
                            }
                            echo '</select></label>';
                            echo '</div>';
                            echo '</td>';
                            $current_points--;
                        }
                        if ($total_racers < 6) {
                            $diff = 6 - $total_racers;
                            for($i = 0; $i < $diff; $i++) {
                                echo '<td>&nbsp;</td>';
                            }
                        }
                        echo '<td class="align-middle">';
                        echo '<div class="form-group">';
                        echo '<nobr>';
                        if (count($heat->results())) {
                            echo '<button type="submit" name="updateresult" aria-label="Update" class="btn btn-success m-1"><i class="bi-check-lg" aria-hidden="true"></i></button>';
                            echo '<button type="submit" name="clearresult" aria-label="Delete" class="btn btn-danger m-1" onclick="return confirm(\'You want to clear results for heat #' . $heat->id() . '\')"><i class="bi-arrow-counterclockwise" aria-hidden="true"></i></button>';

                        } else {
                            echo '<button type="submit" name="addresult" aria-label="Add" class="btn btn-primary m-1"><i class="bi-plus-lg" aria-hidden="true"></i></button>';
                            echo '<button type="submit" name="deleteheat" aria-label="Delete" class="btn btn-danger m-1" onclick="return confirm(\'You want to DELETE heat #' . $heat->id() . '\')"><i class="bi-trash-fill" aria-hidden="true"></i></button>';

                        }
                        echo '</nobr>';
                        echo '</div>';
                        echo '</td>';
                        echo '</form>';
                        echo '</tr>';
                    }
                    if ($total == 0) {
                        echo '<tr><td colspan="8"><div class="alert alert-info"><strong>No heats generated yet.</strong></div></td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php include_once("includes/footer.php");
