<?php
  session_start();
  date_default_timezone_set('Asia/Seoul');

  define("USER", @$_SESSION["user_101"]);
  define("ROOT", $_SERVER["DOCUMENT_ROOT"]);
  define("G", $_GET);
  define("P", $_POST);
  define("U", explode("?", $_SERVER["REQUEST_URI"])[0]);

  function move($msg = false, $url = "back"){
    $msg = is_array($msg) ? implode("\\n", $msg) : $msg;
    $url = $url == "back" ? "history.back()" : "location.href='$url'";

    if($msg) echo "<script>alert('$msg')</script>";
    if($url) echo "<script>$url</script>";

    exit;
  }

  function dd(){
    echo "<pre>";
    var_dump(...func_get_args());
    echo "</pre>";
  }

  function err($err, $msg = false, $url = "back"){
    if($err){
      move($msg, $url);
    }
  }

  function emp_vali($arr){
    foreach($arr as $v){
      if(trim($v) == "") return true;
    }

    return false;
  }

  function formatDate($date){
    return date("Y년 m월 d일 Ah:i", strtotime($date));
  }

  function sale_price($price, $sale_per){
    return floor($price * ( (100 - $sale_per)/100 ));
  }

  function min_distance($data){
    if(!in_array($data["state"], ["taking", "complete"])) return;

    $loot = distances::all("ORDER BY distance");

    $store_loc = users::data(stores::data($data["store_id"], "user_id"), "location_id");
    $user_loc = users::data($data["orderer_id"], "location_id");

    $driver = users::find("id = ?", $data["driver_id"]);

    $driver_loc = $driver["location_id"];
    $driver_transportation = $driver["transportation"];

    $user_route = [
      "min" => INF
    ];
    $driver_route = [
      "min" => INF
    ];

    loc_find($loot, $store_loc, $driver_loc, $driver_route);
    loc_find($loot, $store_loc, $user_loc, $user_route);

    $distance = $driver_route["min"] + $user_route["min"];
    $speed = ["motorcycle" => 30, "bike" => 15][$driver_transportation];

    $time = strtotime($data["taking_at"]) + 3600 * ($distance/$speed);
    $date = date("Y년 m월 d일 Ah:i", $time);
    
    return $date;
  }

  function loc_find($loot, $now, $target, &$route, $distance = 0, $visit = [], $arrive = false){
    $min = $route["min"];

    if($min <= $distance) return;

    foreach($loot as $v){
      if($v["vertex1"] !== $now) continue;

      $nowPath = $v["vertex1"]."-".$v["vertex2"];
      if(in_array($nowPath, $visit)) return;

      $tmpVisit = [...$visit, $nowPath];
      $tmpDistance = $distance + $v["distance"];

      if($target == $now){
        $arrive = true;
      }

      if(!$arrive){
        loc_find($loot, $v["vertex2"], $target, $route, $tmpDistance, $tmpVisit, $arrive);
        continue;
      }

      $route["min"] = $distance;
      $route["path"] = $visit;
    }
  }

  function view($loc, $data = []){
    extract($data);

    require ROOT."/view/header.php";
    require ROOT."/view/$loc.php";
    require ROOT."/view/footer.php";
  }
?>