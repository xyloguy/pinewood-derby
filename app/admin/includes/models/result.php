<?php
include_once(dirname(__FILE__) . "/../functions.php");

class Result
{
    private $id = null;
    private $heatid;
    private $racerid;
    private $points;

    public function __construct($array) {
        $this->id = $array[0];
        $this->heatid = $array[1];
        $this->racerid = $array[2];
        $this->points = $array[3];
    }

    public function id() {
        return $this->id;
    }

    public function heatid() {
        return $this->heatid;
    }

    public function racerid() {
        return $this->racerid;
    }

    public function points() {
        return $this->points;
    }

    public static function get($id) {
        $id = intval(db()->real_escape_string($id));
        $sql = db()->prepare("SELECT * FROM `results` WHERE `id` = ?");
        $sql->bind_param("i", $id);
        $sql->execute();
        $result = $sql->get_result();
        if ($result) {
            $row = $result->fetch_array();
            return new Result($row);
        }
        return null;
    }

    public static function get_by_heat_and_racer($heatid, $racerid)
    {
        $heatid = intval(db()->real_escape_string($heatid));
        $racerid = intval(db()->real_escape_string($racerid));
        $sql = db()->prepare("SELECT * FROM `results` WHERE `heatid` = ? AND `racerid` = ?");
        $sql->bind_param("ii", $heatid, $racerid);
        $sql->execute();
        $result = $sql->get_result();
        if ($row = $result->fetch_array()) {
            return new Result($row);
        }
        return null;
    }

    public static function all($where=null, $whereval=null, $orderby=null) {
        if (is_null($orderby) || !in_array($orderby, ["id", "heatid", "racerid", "points"])) {
            $orderby = "id";
        }
        if (is_null($where) || !in_array($where, ["id", "heatid", "racerid", "points"])) {
            $where = null;
        }
        if (!is_null($where) && !is_null($whereval)) {
            $sql = db()->prepare("SELECT * FROM `results` WHERE `" . $where . "` = ? ORDER BY `" . $orderby . "`");
            $sql->bind_param("i", $whereval);
            $sql->execute();
            $results = $sql->get_result();
        } else {
            $results = db()->query("SELECT * FROM `results` ORDER BY `" . $orderby . "`");
        }
        $res = [];
        while($row = $results->fetch_array()) {
            $res[] = new Result($row);
        }
        return $res;
    }

    public function heat() {
        if ($this->heatid == null) {
            return null;
        }
        return Heat::get($this->heatid);
    }

    public function racer() {
        if ($this->racerid == null) {
            return null;
        }
        return Racer::get($this->racerid);
    }

    public static function racer_rankings($hide_racers_with_no_results = false) {
        $racers_with_rank = [];
        $racers = Racer::all();
        foreach ($racers as $racer) {
            if(count($racer->heats()) == 0 or $racer->count_results() == 0){
                if ($hide_racers_with_no_results) {
                    continue;
                }
                $key = '0.0';
                if(!array_key_exists($key, $racers_with_rank)){
                    $racers_with_rank[$key] = [];
                }
                $racers_with_rank[$key][] = $racer;
                continue;
            }

            $rank = strval(round($racer->ranking_value(), 2));
            if (strlen($rank) == 1) {
                $rank .= '.00';
            }
            while(strlen($rank) < 4) {
                $rank .= "0";
            }
            $rank .= str_pad(intval(($racer->count_results() / count($racer->heats())) * 10), 2, "0", STR_PAD_LEFT);
            $rank .= str_pad($racer->count_results(), 2, "0", STR_PAD_LEFT);
            //$rank = $rank;
            if(!array_key_exists($rank, $racers_with_rank)){
                $racers_with_rank[$rank] = [];
            }
            $racers_with_rank[$rank][] = $racer;
        }
        krsort($racers_with_rank, SORT_NUMERIC);
        return $racers_with_rank;
    }

    public function point_percentage() {
        $results = Result::all('heatid', $this->heatid());
        if (count($results) > 0) {
            return $this->points / count($results);
        }
        return 0;
    }

    public static function delete($id) {
        $id = intval(db()->real_escape_string($id));
        $sql = db()->prepare("DELETE FROM `results` WHERE `id` = ?");
        $sql->bind_param("i", $id);
        $sql->execute();
    }


    public static function delete_by_heat($heatid) {
        $heatid = intval(db()->real_escape_string($heatid));
        $sql = db()->prepare("DELETE FROM `results` WHERE `heatid` = ?");
        $sql->bind_param("i", $heatid);
        $sql->execute();
    }


    public static function update($id, $heatid, $racerid, $points) {
        if ($id != null && $heatid != null && $racerid != null && $points != null) {
            $id = intval(db()->real_escape_string($id));
            $heatid = db()->real_escape_string($heatid);
            $racerid = db()->real_escape_string($racerid);
            $points = db()->real_escape_string($points);
            $sql = db()->prepare("UPDATE `results` SET `heatid` = ?, `racerid` = ?, `points` = ? WHERE `id` = ?");
            $sql->bind_param("iiii",$heatid, $racerid, $points, $id);
            if ($sql->execute()) {
                return Result::get($id);
            }
        }
        return null;
    }

    public static function create($heatid, $racerid, $points) {
        if ($heatid != null && $racerid != null && $points != null) {
            $heatid = db()->real_escape_string($heatid);
            $racerid = db()->real_escape_string($racerid);
            $points = db()->real_escape_string($points);
            $sql = db()->prepare("INSERT INTO `results` (`heatid`, `racerid`, `points`) VALUES (?, ?, ?)");
            $sql->bind_param("iii", $heatid, $racerid, $points);
            if ($sql->execute()) {
                $id = db()->insert_id;
                return Result::get($id);
            }
        }
        return null;
    }

    public function save() {
        if (is_null($this->id)) {
            return static::create($this->heatid, $this->racerid, $this->points);
        }
        return static::update($this->id, $this->heatid, $this->racerid, $this->points);
    }
}
