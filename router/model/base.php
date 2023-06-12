<?php
  class DB {
    public static $pdo = false;

    public static function mq($sql, $arr = []){
      if(!self::$pdo){
        self::$pdo = new PDO("mysql:host=localhost;charset=utf8;dbname=21jeonnam", "root", "", [
          19 => 2,
          3 => 2
        ]);
      }
      
      $q = self::$pdo->prepare($sql);
      $q->execute(is_array($arr) ? $arr : [$arr]);

      return $q;
    }

    public static function all($sql = ""){
      $table = get_called_class();
      return self::mq("SELECT * FROM $table $sql")->fetchAll();
    }

    public static function find($sql, $arr = []){
      $table = get_called_class();
      return self::mq("SELECT * FROM $table WHERE $sql", $arr)->fetch();
    }

    public static function findAll($sql, $arr = []){
      $table = get_called_class();
      return self::mq("SELECT * FROM $table WHERE $sql", $arr)->fetchAll();
    }

    public static function insert($data){
      $table = get_called_class();

      $sql = implode(" = ?, ", array_keys($data))." = ?";

      self::mq("INSERT INTO $table SET $sql", array_values($data));

      return self::$pdo->lastinsertid();
    }
  }

  class breads extends DB {}
  class deliveries extends DB {}
  class delivery_items extends DB {}
  class distances extends DB {}
  class grades extends DB {}
  class likes extends DB {}
  class locations extends DB {}
  class replies extends DB {}
  class reservations extends DB {}
  class reviews extends DB {}
  class stores extends DB {}
  class users extends DB {}
?>