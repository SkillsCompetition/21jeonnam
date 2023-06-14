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

  function view($loc, $data = []){
    extract($data);

    require ROOT."/view/header.php";
    require ROOT."/view/$loc.php";
    require ROOT."/view/footer.php";
  }
?>