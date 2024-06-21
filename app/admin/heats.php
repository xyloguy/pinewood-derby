<?php include_once("includes/header.php");
if (clean_post('generateheats')){
    if(
        !empty($_POST['racers'])
        && !empty($_POST['lanes'])
        && !empty($_POST['rounds'])
    ){
        if (count($_POST['racers']) <= 1) {
            echo '<div class="alert alert-warning autoclose fade show" data-dismiss="alert">';
            echo "At least 2 racers needed to generate heats!";
            echo "</div>";
        } elseif ($_POST['lanes'] <= 1 || $_POST['lanes'] >6) {
            echo '<div class="alert alert-warning autoclose fade show" data-dismiss="alert">';
            echo "The value of lanes is out of range.";
            echo "</div>";
        } elseif ($_POST['rounds'] < 1 || $_POST['rounds'] > 10) {
            echo '<div class="alert alert-warning autoclose fade show" data-dismiss="alert">';
            echo "The value for rounds is out of range.";
            echo "</div>";
        } else {
            $g = new ChaoticChart($_POST['lanes'], $_POST['racers'], $_POST['rounds']);
            $g->generate();
            $total = 0;
            foreach($g->getChart() as $heat) {
                $lanes = [];
                foreach($heat as $car) {
                    $lanes[] = str_replace("car_", "", $car);
                }
                Heat::create(implode(",", $lanes));
                $total++;
            }
            echo '<div class="alert alert-success autoclose fade show" data-dismiss="alert">';
            echo $total . ' heats added successfully!';
            echo '</div>';
        }
    }
}

if (clean_post('createheat')) {
    if (!empty($_POST['racers'])) {
        $racers = [];
        foreach($_POST['racers'] as $racer) {
            if (!empty($racer=trim($racer))) {
                if (!in_array($racer, $racers)) {
                    $racers[] = trim($racer);
                }
            }
        }
        if (count($racers) <= 1) {
            echo '<div class="alert alert-warning autoclose fade show" data-dismiss="alert">';
            echo "At least 2 racers needed to create a heat!";
            echo "</div>";
        } else {
            $racer_string = implode(',', $racers);
            Heat::create($racer_string);
        }
    }
}

if (clean_post('resetheats')){
    if (!empty($_POST['confirm'])) {
        $confirm = strtolower($_POST['confirm']);
        if ($confirm == "reset") {
            db()->query("TRUNCATE `heats`");
            db()->query("ALTER TABLE `heats` AUTO_INCREMENT = 1");

            db()->query("TRUNCATE `results`");
            db()->query("ALTER TABLE `results` AUTO_INCREMENT = 1");
        }
    }
}

if (clean_post('deleteheats')) {
    if (!empty($_POST['id']) && !empty(trim($_POST['id']))) {
        $id = db()->real_escape_string($_POST['id']);
        Heat::delete($id);
    }
}

$count_groups = Group::count();
$count_racers = Racer::count();
if ($count_groups == 0) {
    echo '<div class="alert alert-warning show">';
    echo 'You must <a href="groups.php" class="alert-link">create a group</a> before you can generate heats!';
    echo '</div>';
} elseif ($count_racers <= 1) {
    echo '<div class="alert alert-warning show">';
    echo 'You must <a href="racers.php" class="alert-link">create racers</a> before you can generate heats!';
    echo '</div>';
}
?>
<div class="row">
    <div class="col-md-6 mb-md-5">
        <h2>Generate Heats</h2>
        <form action="heats.php" method="post">
            <input type="hidden" name="checksum" value="<?= get_checksum('generateheats') ?>">
            <div class="form-group">
                <?php
                $groups = Group::all();
                echo '<div class="btn-group btn-group-toggle form-group" data-toggle="buttons">';
                echo '<button class="btn btn-secondary border-dark" type="button" data-group="all">Select All</button>';
                $largest_group = 4;
                foreach ($groups as $group) {
                    $racers = $group->racers();
                    $c = count($racers);
                    if ($c == 0) { continue; }
                    if ($c > $largest_group) {
                        $largest_group = $c;
                    }
                    echo '<button class="btn btn-secondary border-dark" type="button" autocomplete="off" data-group="'.$group->name().'">'.$group->name().'</button>';
                }
                echo '</div>';
                ?>
                <div class="form-group">
                    <select multiple id="racers" name="racers[]" class="custom-select" size="<?= $largest_group + 1 ?>" aria-describedby="racers-help">
                        <?php
                        foreach($groups as $group){
                            $racers = $group->racers();
                            $c = count($racers);
                            if ($c == 0) { continue; }
                            echo '<optgroup label="'.$group->name().'">';
                            foreach ($racers as $racer) {
                                echo '<option value="car_' . $racer->id() . '">#' .$racer->id() . ": " . $racer->name() . '</option>';
                            }
                            echo '</optgroup>';
                        }
                        ?>
                    </select>
                    <small id="racers-help" class="form-text text-muted">0 racers selected.</small>

                </div>
                <div class="form-group">
                    <label for="rounds">Rounds:</label>
                    <select id="rounds" name="rounds" class="custom-select" aria-describedby="rounds-help">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                        <option>6</option>
                        <option>7</option>
                        <option>8</option>
                        <option>9</option>
                        <option>10</option>
                    </select>
                    <small id="rounds-help" class="form-text text-muted">The number of times each racer will race in each lane.</small>
                </div>
                <div class="form-group">
                    <label for="lanes">Lanes:</label>
                    <select id="lanes" name="lanes" class="custom-select" aria-describedby="lanes-help">
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                        <option selected>6</option>
                    </select>
                    <small id="lanes-help" class="form-text text-muted">The number of lanes on the track to use (if possible).</small>

                </div>
            </div>
            <div class="alert alert-secondary show" id="heat-info">0 heats will be generated. Each car will race 0 times.</div>
            <button type="submit" name="generateheats" class="btn btn-primary"<?= ($count_racers > 1) ? '' : ' disabled="disabled"' ?>><i class="bi-plus-lg"></i> Generate Heats</button>
        </form>
    </div>

    <div class="col-md-6  mb-md-5">
        <h2>Create Custom Heat</h2>
        <form method="post" action="heats.php" id="custom-heat">
            <p>Lane assignments will be squashed down in the order selected (ie, empty lanes are ignored). Duplicate racers will be ignored.</p>
            <input type="hidden" name="checksum" value="<?= get_checksum('createheat') ?>">
            <?php
            $racers = Racer::all();
            for($lane = 1; $lane <= 6; $lane++){
                echo '<div class="form-group">';
                echo '<label for="lane'.$lane.'">Lane '.$lane.':</label>';
                echo '<select id="lane'.$lane.'" class="custom-select" name="racers[]">';
                echo '<option value=""></option>';
                foreach($racers as $racer){
                    echo '<option value="'.$racer->id().'">#'.$racer->id().': '.$racer->name().' ('.$racer->group()->name().')</option>';
                }
                echo '</select>';
                echo '</div>';
            }
            ?>

            <button type="submit" name="createheat" class="btn btn-primary"<?= ($count_racers > 1) ? '' : ' disabled="disabled"' ?>><i class="bi-plus-lg"></i> Create Custom Heat</button>
        </form>
    </div>
</div>



<div class="row">
    <div class="col-md-6  mb-md-5">
        <h2>Reset Heats</h2>
        <form method="post" action="heats.php">
            <input type="hidden" name="checksum" value="<?= get_checksum('resetheats') ?>">

            <div class="form-group">
                <label for="confirm">&nbsp;</label>
                <input id="confirm" type="text" name="confirm" class="form-control" aria-describedby="confirm-help">
                <small id="confirm-help" class="form-text text-muted">Type "reset" and submit to clear heats and results.</small>
            </div>

            <button type="submit" name="resetheats" class="btn btn-outline-danger"><i class="bi-x-lg"></i> Reset Data</button>
        </form>
    </div>
</div>



<div class="row">
    <div class="col-md-12 mt-lg-5">
        <h2>Heats</h2>
        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="heats">
                <thead class="thead-dark">
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
                $total = Heat::count();
                foreach($heats as $heat) {
                    $racers = $heat->cars();
                    echo '<tr>';
                    echo '<form method="post" action="heats.php">';
                    echo '<th scope="row">';
                    echo '<input type="hidden" name="checksum[]" value="' . get_checksum('updateheats') . '">';
                    echo '<input type="hidden" name="checksum[]" value="' . get_checksum('deleteheats') . '">';
                    echo '<input type="hidden" name="id" value="' . $heat->id() . '">';
                    echo  $heat->id();
                    echo'</th>';
                    foreach($racers as $racer) {
                        echo '<td data-racer=" racer-' . $racer->id() . ' ">#' . $racer->id() . ": " . $racer->name() . '</td>';
                    }
                    if (count($racers) < 6) {
                        $diff = 6 - count($racers);
                        for($i = 0; $i < $diff; $i++) {
                            echo '<td>&nbsp;</td>';
                        }
                    }
                    echo '<td style="max-width: 94px">';
                    echo '<button type="submit" name="deleteheats" aria-label="Delete" class="btn btn-danger mr-2" onclick="return confirm(\'You want to delete ' . $heat->id() . '\')"><i class="bi-trash-fill" aria-hidden="true"></i></button>';
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
<?php include_once("includes/footer.php");
