<?php include_once("includes/header.php");
if (clean_post('addgroup')){
    if(
        !empty($_POST['name']) && !empty(trim($_POST['name']))
    ){
        $name = db()->real_escape_string($_POST['name']);
        Group::create($name);

        echo '<div class="alert alert-success autoclose fade show" data-dismiss="alert">';
        echo $name . ' added successfully!';
        echo '</div>';
    }
}

if (clean_post('updategroup')) {
    if ( !empty($_POST['id'])
        && !empty($_POST['name']) && !empty(trim($_POST['name']))
    ) {
        $id = db()->real_escape_string($_POST['id']);
        $name = db()->real_escape_string($_POST['name']);
        Group::update($id, $name);
    }
}

if (clean_post('deletegroup')) {
    if (!empty($_POST['id']) && !empty(trim($_POST['id']))) {
        $id = db()->real_escape_string($_POST['id']);
        if (Racer::count($id) == 0) {
            Group::delete($id);
        } else {
            echo '<div class="alert alert-warning autoclose fade show" data-dismiss="alert">';
            echo 'Can not delete a group when there are racers associated with it.';
            echo '</div>';
        }
    }
}

if (clean_post('resetgroup')) {
    if (!empty($_POST['confirm'])) {
        if (strtolower($_POST['confirm']) == "reset") {
            if (count(Racer::all()) == 0) {
                db()->query("TRUNCATE `groups`");
                db()->query("ALTER TABLE `groups` AUTO_INCREMENT = 1");
            } else {
                echo '<div class="alert alert-warning autoclose fade show" data-dismiss="alert">';
                echo 'Can not reset groups when there are racers.';
                echo '</div>';
            }
        }
    }
}
?>
    <div class="row">
        <div class="col-md-6 mb-md-5">
            <h2>Add Group</h2>
            <form action="groups.php" method="post">
                <input type="hidden" name="checksum" value="<?= get_checksum('addgroup') ?>">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" class="form-control">
                </div>
                <button type="submit" name="addgroup" class="btn btn-primary"><i class="bi-plus-lg"></i> Add Group</button>
            </form>
        </div>

        <div class="col-md-6  mb-md-5">
            <h2>Reset Groups</h2>
            <form method="post" action="groups.php">
                <input type="hidden" name="checksum" value="<?= get_checksum('resetgroup') ?>">

                <div class="form-group">
                    <label for="confirm">&nbsp;</label>
                    <input id="confirm" type="text" name="confirm" class="form-control" aria-describedby="confirm-help">
                    <small id="confirm-help" class="form-text text-muted">Enter "reset" groups. You can only do this if there are no racers.</small>
                </div>
                <button type="submit" name="resetgroup" class="btn btn-outline-danger"<?= Racer::count() ? ' disabled="disabled"' : '' ?>><i class="bi-x-lg"></i> Reset Data</button>
            </form>
        </div>
    </div>



    <!-- Index Racers -->
    <div class="row">
        <div class="col-md-12 mt-lg-5">
            <h2>Groups</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Racers</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $groups = Group::all();
                    $total = 0;
                    foreach($groups as $group) {
                        $total++;
                        $racers = Racer::all($group->id());
                        $racers_in_group = count($racers);
                        echo '<tr>';
                        echo '<form method="post" action="groups.php">';
                        echo '<th scope="row">';
                            echo '<input type="hidden" name="checksum[]" value="' . get_checksum('updategroup') . '">';
                            echo '<input type="hidden" name="checksum[]" value="' . get_checksum('deletegroup') . '">';
                            echo '<input type="hidden" name="id" value="' . $group->id() . '">';
                            echo  $group->id();
                        echo'</th>';
                        echo '<td class="form-group"><input type="text" name="name" value="' . $group->name() .'" class="form-control"></td>';
                        echo '<td>' . $racers_in_group . '</td>';
                        echo '<td style="max-width: 94px">';
                        echo '<button type="submit" name="updategroup" aria-label="Update" class="btn btn-success mr-2"><i class="bi-floppy-fill" aria-hidden="true"></i></button>';
                        if ($racers_in_group == 0) {
                            echo '<button type="submit" name="deletegroup" aria-label="Delete" class="btn btn-danger mr-2" onclick="return confirm(\'You want to delete ' . $group->name() . '\')"><i class="bi-trash-fill" aria-hidden="true"></i></button>';
                        }
                        echo '</td>';
                        echo '</form>';
                        echo '</tr>';
                    }
                    if ($total == 0) {
                        echo '<tr><td colspan="4"><div class="alert alert-info"><strong>No groups add yet</strong></div></td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php include_once("includes/footer.php");
