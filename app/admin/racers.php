<?php include_once("includes/header.php");
echo '<main class="container">';
if (clean_post('addracer')){
    if(
        !empty($_POST['name']) && !empty(trim($_POST['name']))
        && !empty($_POST['groupid']) && !empty(trim($_POST['groupid']))
    ){
        $name = db()->real_escape_string($_POST['name']);
        $groupid = db()->real_escape_string($_POST['groupid']);
        $racer = Racer::create($name, $groupid);
        echo '<div class="alert alert-success autoclose fade show" data-dismiss="alert">';
        echo $racer->id() . ": " . $name . ' added successfully!';
        echo '</div>';
    }
}

if (clean_post('updateracer')) {
    if ( !empty($_POST['id'])
         && !empty($_POST['name']) && !empty(trim($_POST['name']))
         && !empty($_POST['groupid']) && !empty(trim($_POST['groupid']))
    ) {
        $id = db()->real_escape_string($_POST['id']);
        $name = db()->real_escape_string($_POST['name']);
        $groupid = db()->real_escape_string($_POST['groupid']);
        Racer::update($id, $name, $groupid);
    }
}

if (clean_post('deleteracer')) {
    if (!empty($_POST['id']) && !empty(trim($_POST['id']))) {
        $id = db()->real_escape_string($_POST['id']);
        $racer = Racer::get($id);
        if (count($racer->heats()) == 0) {
            Racer::delete($id);
        } else {
            echo '<div class="alert alert-danger autoclose fade show" data-dismiss="alert">';
            echo $racer->id() . ": " . $racer->name() . ' can\'t be deleted because they have heats.';
            echo '</div>';
        }

    }
}

if (clean_post('resetracers')) {
    if (!empty($_POST['confirm'])) {
        $confirm = strtolower($_POST['confirm']);
        if ($confirm == "reset") {
            db()->query("TRUNCATE `racers`");
            db()->query("ALTER TABLE `racers` AUTO_INCREMENT = 1");

            db()->query("TRUNCATE `heats`");
            db()->query("ALTER TABLE `heats` AUTO_INCREMENT = 1");

            db()->query("TRUNCATE `results`");
            db()->query("ALTER TABLE `results` AUTO_INCREMENT = 1");
        }
    }
}

if (($count_groups = Group::count()) == 0) {
    echo '<div class="alert alert-warning show">';
    echo 'You must <a href="groups.php" class="alert-link">create a group</a> before you can create racers!';
    echo '</div>';
}
?>
    <div class="row">
        <div class="col-md-6 mb-md-5">
            <h2>Add Racer</h2>
            <form action="racers.php" method="post">
                <input type="hidden" name="checksum" value="<?= get_checksum('addracer') ?>">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" class="form-control autofocus">
                </div>
                <div class="form-group">
                    <label for="group">Group:</label>
                    <select name="groupid" id="group" class="custom-select form-select form-control">
                        <?php
                        $groups = Group::all();
                        foreach($groups as $group) {
                            echo '<option value="' . $group->id() . '">' . $group->name() . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" name="addracer" class="btn btn-primary"<?= ($count_groups ? '' : 'disabled="disabled"') ?>><i class="bi-plus-lg"></i> Add Racer</button>
                </div>
            </form>
        </div>

        <div class="col-md-6  mb-md-5">
            <h2>Reset Race Data</h2>
            <form method="post" action="racers.php">
                <input type="hidden" name="checksum" value="<?= get_checksum('resetracers') ?>">
                <div class="form-group">
                    <label for="confirm">&nbsp;</label>
                    <input id="confirm" type="text" name="confirm" class="form-control" aria-describedby="confirm-help">
                    <small id="confirm-help" class="form-text text-muted">Enter "reset" and submit to reset all racers, heats, and results.</small>
                </div>
                <div class="form-group">
                    <button type="submit" name="resetracers" class="btn btn-outline-danger"><i class="bi-x-lg"></i> Reset Race Data</button>
                </div>
            </form>
        </div>
    </div>



    <!-- Index Racers -->
    <div class="row">
        <div class="col-md-12 mt-lg-5">
            <h2>Racers</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Group</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $racers = Racer::all();
                        $total = count($racers);
                        foreach($racers as $racer) {
                            echo '<tr>';
                                echo '<form method="post" action="racers.php">';
                                echo '<input type="hidden" name="checksum[]" value="' . get_checksum('updateracer') . '">';
                                echo '<input type="hidden" name="checksum[]" value="' . get_checksum('deleteracer') . '">';
                                echo '<input type="hidden" name="id" value="' . $racer->id() . '">';
                                echo '<th scope="row">' . $racer->id() . '</th>';
                                echo '<td class="form-group"><input type="text" name="name" value="' . $racer->name() .'" class="form-control"></td>';
                                echo '<td class="form-group"><select name="groupid" class="custom-select form-select form-control">';
                                foreach($groups as $group) {
                                    $selected = ($group->id() == $racer->groupid()) ? ' selected="selected"' : '';
                                    echo '<option value="' . $group->id(). '"' . $selected . '>' . $group->name() . '</option>';
                                }
                                echo '</select></td>';
                                echo '<td style="max-width: 94px">';
                                echo '<button type="submit" name="updateracer" aria-label="Update" class="btn btn-success mr-2"><i class="bi-floppy-fill" aria-hidden="true"></i></button>';
                                if (count($racer->heats()) == 0) {
                                    echo '<button type="submit" name="deleteracer" aria-label="Delete" class="btn btn-danger mr-2" onclick="return confirm(\'You want to delete ' . $racer->name() . '\')"><i class="bi-trash-fill" aria-hidden="true"></i></button>';
                                }
                                echo '</td>';
                                echo '</form>';
                            echo '</tr>';
                        }
                        if ($total == 0) {
                            echo '<tr><td colspan="4"><div class="alert alert-info"><strong>No racers add yet</strong></div></td></tr>';
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php include_once("includes/footer.php");
