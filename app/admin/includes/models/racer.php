<?php
include_once(dirname(__FILE__) . "/../functions.php");

class Racer
{
    private $id = null;
    private $name;
    private $groupid;

    public function __construct($id=null, $name=null, $groupid=null)
    {
        if ($id != null && $name == null && $groupid = null) {
            $racer = static::get($id);
            if ($racer != null) {
                $this->id = $racer->id();
                $this->name = $racer->name();
                $this->groupid = $racer->groupid();
                return $this;
            }
        } elseif ($id == null && $name != null && $groupid != null) {
            $this->name = $name;
            $this->groupid = $groupid;
            return static::create($name, $groupid);
        } elseif($id != null && $name != null && $groupid != null) {
            $this->id = $id;
            $this->name = $name;
            $this->groupid = $groupid;
            return $this;
        }
        return null;
    }

    public function id() {
        return $this->id;
    }

    public function name() {
        return $this->name;
    }

    public function setName($name) {
        if (!empty(trim($name))) {
            $this->name = trim($name);
        }
    }

    public function groupid() {
        return $this->groupid;
    }

    public function setGroupid($groupid) {
        if (!empty($groupid)) {
            $this->groupid = intval($groupid);
        }
    }

    public static function get($id) {
        $id = intval(db()->real_escape_string($id));
        $sql = db()->prepare("SELECT * FROM `racers` WHERE `id` = ?");
        $sql->bind_param("i", $id);
        $sql->execute();
        $result = $sql->get_result();
        if ($result) {
            $row = $result->fetch_array();
            return new Racer($row[0], $row[1], $row[2]);
        }

        return null;
    }

    public static function all($groupid=null, $orderby=null) {
        if (is_null($orderby) || !in_array($orderby, array("id", "name", "groupid"))) {
            $orderby="id";
        }
        if ($groupid != null) {
            $sql = db()->prepare("SELECT * FROM `racers` WHERE `groupid` = ? ORDER BY `" . $orderby . "`");
            $sql->bind_param("i", $groupid);
            $sql->execute();
            $results = $sql->get_result();
        } else {
            $results = db()->query("SELECT * FROM `racers` ORDER BY `" . $orderby . "`");
        }
        $racers = [];
        while($row = $results->fetch_array()) {
            $racers[] = new Racer($row[0], $row[1], $row[2]);
        }
        return $racers;
    }

    public function group() {
        if ($this->groupid == null) {
            return null;
        }
        return new Group($this->groupid);
    }

    public function heats() {
        $heats = [];
        foreach (Heat::all() as $heat) {
            if (in_array($this->id(), explode(',', $heat->racers()))) {
                $heats[] = $heat;
            }
        }
        return $heats;
    }

    public function results() {
        return Result::all('racerid', $this->id());
    }

    public function count_results() {
        return count($this->results());
    }

    public function ranking_value() {
        $total = 0;
        $results = $this->results();
        $len = count($results);
        if ($len === 0) {
            return 0;
        }
        foreach ($results as $result) {
            $total += $result->point_percentage();
        }
        return $total/$len;
    }

    public static function delete($id) {
        $id = intval(db()->real_escape_string($id));
        $sql = db()->prepare("DELETE FROM `racers` WHERE `id` = ?");
        $sql->bind_param("i", $id);
        $sql->execute();
    }

    public static function update($id, $name, $groupid) {
        if ($id != null && $name != null && $groupid != null) {
            $id = intval(db()->real_escape_string($id));
            $name = db()->real_escape_string($name);
            $groupid = intval(db()->real_escape_string($groupid));
            $sql = db()->prepare("UPDATE `racers` SET `name` = ?, `groupid` = ? WHERE `id` = ?");
            $sql->bind_param("sii",$name, $groupid, $id);
            if ($sql->execute()) {
                return Racer::get($id);
            }
        }
        return null;
    }

    public static function create($name, $groupid) {
        if ($name != null && $groupid != null) {
            $name = db()->real_escape_string($name);
            $groupid = intval(db()->real_escape_string($groupid));
            $sql = db()->prepare("INSERT INTO `racers` (`name`, `groupid`) VALUES (?, ?)");
            $sql->bind_param("si", $name, $groupid);
            if ($sql->execute()) {
                $id = db()->insert_id;
                return Racer::get($id);
            }
        }
        return null;
    }

    public function save() {
        if (is_null($this->id)) {
            return static::create($this->name, $this->groupid);
        }
        return static::update($this->id, $this->name, $this->groupid);
    }
}
