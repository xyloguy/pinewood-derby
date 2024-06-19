<?php
include_once(dirname(__FILE__) . "/../functions.php");

class Heat
{
    private $id = null;
    private $racers;

    public function __construct($id=null, $racers=null)
    {
        if ($id != null && $racers == null) {
            $heat = static::get($id);
            if ($heat != null) {
                $this->id = $heat->id();
                $this->racers = $heat->racers();
                return $this;
            }
        } elseif ($id == null && $racers != null) {
            $this->racers = $racers;
            return static::create($racers);
        } elseif($id != null && $racers != null) {
            $this->id = $id;
            $this->racers = $racers;
            return $this;
        }
        return null;
    }

    public function id() {
        return $this->id;
    }

    public function racers() {
        return $this->racers;
    }

    public function setRacers($racers) {
        if (!empty(trim($racers))) {
            $this->racers = trim($racers);
        }
    }

    public static function get($id) {
        $id = intval(db()->real_escape_string($id));
        $sql = db()->prepare("SELECT * FROM `heats` WHERE `id` = ?");
        $sql->bind_param("i", $id);
        $sql->execute();
        $result = $sql->get_result();
        if ($row = $result->fetch_array()) {
            return new Heat($row[0], $row[1]);
        }
        return null;
    }

    public static function all() {
        $results = db()->query("SELECT * FROM `heats` ORDER BY `id`");
        $heats = [];
        while($row = $results->fetch_array()) {
            $heats[] = new Heat($row[0], $row[1]);
        }
        return $heats;
    }

    public function cars() {
        $racer_ids = explode(',', $this->racers);
        $racers = [];
        foreach ($racer_ids as $racer_id) {
            $racers[] = Racer::get($racer_id);
        }
        return $racers;
    }

    public function results() {
        return Result::all('heatid', $this->id());
    }

    public static function current_heat() {
        $result = db()->query("SELECT h.id FROM heats h LEFT JOIN results r ON h.id = r.heatid WHERE r.heatid IS NULL ORDER BY h.id ASC LIMIT 1");
        if($row = $result->fetch_assoc()) {
            return $row['id'];
        }
        return null;
    }

    public static function delete($id) {
        $id = intval(db()->real_escape_string($id));
        $sql = db()->prepare("DELETE FROM `heats` WHERE `id` = ?");
        $sql->bind_param("i", $id);
        $sql->execute();
        Result::delete_by_heat($id);
    }


    public static function update($id, $racers) {
        if ($id != null && $racers != null) {
            $id = intval(db()->real_escape_string($id));
            $racers = db()->real_escape_string($racers);
            $sql = db()->prepare("UPDATE `heats` SET `racers` = ? WHERE `id` = ?");
            $sql->bind_param("si",$racers, $id);
            if ($sql->execute()) {
                return Heat::get($id);
            }
        }
        return null;
    }

    public static function create($racers) {
        if ($racers != null) {
            $racers = db()->real_escape_string($racers);
            $sql = db()->prepare("INSERT INTO `heats` (`racers`) VALUES (?)");
            $sql->bind_param("s", $racers);
            if ($sql->execute()) {
                $id = db()->insert_id;
                return Heat::get($id);
            }
        }
        return null;
    }

    public function save() {
        if (is_null($this->id)) {
            return static::create($this->racers);
        }
        return static::update($this->id, $this->racers);
    }
}
