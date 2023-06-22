<!DOCTYPE html>
<html lang="zxx">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/resources/fontawesome/css/font-awesome.css">
  <link rel="stylesheet" href="/resources/css/style.css">
  <script src="/resources/js/jquery-3.6.0.min.js"></script>
  <script src="/resources/js/script.js"></script>
  <title>Document</title>
</head>
<body>

  <?php
    $user_type_list = [
      "owner" => "사장님",
      "rider" => "라이더",
      "normal" => "고객"
    ]
  ?>

  <header>
    <div class="wrap flex jcsb aic">
      <a href="#"><img src="/resources/img/logo.png" alt="#" title="#" class="logo"></a>

      <div class="menu_nav flex">
        <a href="/sub" class="depth1">대전 빵집</a>
        <a href="#" class="depth1">스탬프</a>
        <a href="/sale_event" class="depth1">할인 이벤트</a>
        <a href="/mypage" class="depth1">마이페이지</a>
      </div>

      <div class="box flex jcfe">

        <?php if(@USER):?>
          <p>
            <?= USER["name"] ?>
            (<?= $user_type_list[USER["type"]] ?>)
          </p>
          <a class="btn" href="/logout"><i class="fa fa-user-times"></i>로그아웃</a>
        <?php else: ?>
          <a class="btn" href="/login"><i class="fa fa-user"></i>로그인</a>
        <?php endif ?>
      </div>
    </div>
  </header>