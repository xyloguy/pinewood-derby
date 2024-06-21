<?php
include_once(dirname(__FILE__) . "/../functions.php");

class Group
{
    private $id = null;
    private $name;


    public function __construct($id=null, $name=null)
    {
        if(!is_null($id) && !is_null($name)) {
            $this->id = $id;
            $this->name = $name;
            return $this;
        }
        elseif (!is_null($id) && is_null($name)) {
            $group = static::get($id);
            if ($group != null) {
                $this->id = $group->id();
                $this->name = $group->name();
                return $this;
            }
        }
        elseif (is_null($id) && !is_null($name)) {
            $this->name = $name;
            return static::create($name);
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

    public static function get($id) {
        $id = intval(db()->real_escape_string($id));
        $sql = db()->prepare("SELECT * FROM `groups` WHERE `id` = ?");
        $sql->bind_param("i", $id);
        $sql->execute();
        $result = $sql->get_result();
        if ($result) {
            list($id, $name) = $result->fetch_array();
            return new Group($id, $name);
        }

        return null;
    }

    public static function all() {
        $results = db()->query("SELECT * FROM `groups` ORDER BY `id`");
        $groups = [];
        while($row = $results->fetch_assoc()) {
            $groups[] = new Group($row['id'], $row['name']);
        }
        return $groups;
    }

    public static function count() {
        if ($result = db()->query("SELECT COUNT(*) FROM `groups`")) {
            return $result->fetch_array()[0];
        }
        return 0;
    }

    public function racers() {
        if ($this->id == null) {
            return null;
        }
        return Racer::all($this->id);
    }

    public static function delete($id) {
        $id = db()->real_escape_string($id);
        $sql = db()->prepare("DELETE FROM `groups` WHERE `id` = ?");
        $sql->bind_param("i", $id);
        $sql->execute();
    }

    public static function update($id, $name) {
        if ($id != null && $name != null) {
            $id = db()->real_escape_string($id);
            $name = db()->real_escape_string($name);
            $sql = db()->prepare("UPDATE `groups` SET `name` = ? WHERE `id` = ?");
            $sql->bind_param("si", $name, $id);
            if ($sql->execute()) {
                return Group::get($id);
            }
        }
        return null;
    }

    public static function create($name) {
        if ($name != null) {
            $name = db()->real_escape_string($name);
            $sql = db()->prepare("INSERT INTO `groups` (`name`) VALUES (?)");
            $sql->bind_param("s", $name);
            if ($sql->execute()) {
                $id = db()->insert_id;
                return Group::get($id);
            }
        }
        return null;
    }

    public function save() {
        if (is_null($this->id)) {
            return static::create($this->name);
        }
        return static::update($this->id, $this->name);
    }
}
