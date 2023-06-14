<?php
  $state = [
    "order" => "주문 대기",
    "accept" => "상품 준비 중",
    "reject" => "주문 거절",
    "taking" => "배달 중",
    "complete" => "배달 완료",
  ];

  $state_reservation = [
    "order" => "신청",
    "reject" => "거절",
    "accept" => "승인"
  ]
?>

<div class="emp_box" style="height: 100px;"></div>

<div class="content">

  <div class="mypage_section">
    <div class="wrap">
      <div class="title">
        <h1>마이페이지</h1>
      </div>

      <div class="mypage">

        <div class="order_list">
          <h3>주문 조회</h3>
          <table>
            <tr>
              <th>빵집 이름</th>
              <th>주문 일시</th>
              <th>빵 종류 및 가격</th>
              <th>수량</th>
              <th>라이더 이름</th>
              <th>도착 예정 시간</th>
              <th>주문상태</th>
              <th>기타</th>
            </tr>
            <?php foreach($orders as $v): ?>
              <?php 
                $rowspan = count($v["breads"]); 
                $menu = $v["breads"];

                unset($v["breads"]);

                $json = json_encode($v);
              ?>
              <?php foreach($menu as $k => $val): ?>
              <tr>
                <?php if($k == 0): ?>
                <td rowspan="<?= $rowspan ?>"><?= $v["store_name"] ?></td>
                <td rowspan="<?= $rowspan ?>"><?= formatDate($v["order_at"]) ?></td>
                <td><?= $val["name"] ?> ( <?= number_format($val["price"]) ?>원 )</td>
                <td><?= $val["cnt"] ?>개</td>
                <td rowspan="<?= $rowspan ?>"><?= $v["rider_name"] ?></td>
                <td rowspan="<?= $rowspan ?>"></td>
                <td rowspan="<?= $rowspan ?>"><?= $state[$v["state"]] ?></td>
                <td rowspan="<?= $rowspan ?>">
                  <div class="btn_box">
                    <?php if($v["state"] == "complete"): ?>
                      <div class="btn" data-json='<?= $json ?>' onclick="formopen(this, 'review')">리뷰</div>
                      <div class="btn" data-json='<?= $json ?>' onclick="formopen(this, 'score')">평점</div>
                    <?php endif ?>
                  </div>
                </td>
                <?php else: ?>
                <td><?= $val["name"] ?> ( <?= number_format($val["price"]) ?>원 )</td>
                <td><?= $val["cnt"] ?>개</td>
                <?php endif?>
              </tr>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </table>
        </div>

        <div class="reservation_list">
          <h3>예약 조회</h3>
          <table>
            <tr>
              <th>예약한 빵집</th>
              <th>예약 일시</th>
              <th>예약 신청 일시</th>
              <th>상태</th>
            </tr>
            <?php foreach($reservations as $v): ?>
            <tr>
              <td><?= $v["name"] ?></td>
              <td><?= formatDate($v["reservation_at"]) ?></td>
              <td><?= formatDate($v["request_at"]) ?></td>
              <td><?= $state_reservation[$v["state"]] ?></td>
            </tr>
            <?php endforeach ?>
          </table>
        </div>

      </div>
    </div>
  </div>

</div>

<template>

  <div class="review_modal">
    <form action="/review" method="POST" enctype="multipart/form-data">
      <div class="flex jcsb aic">
        <h3>리뷰</h3>

        <div class="close_btn">
          <i class="fa fa-close"></i>
        </div>
      </div>
      <hr>
      <input type="text" id="store_id" name="store_id" hidden>
      <input type="text" id="user_id" name="user_id" hidden>
  
      <div class="inputs">
  
        <div class="input_box">
          <label for="title">제목</label>
          <input type="text" id="title" name="title" required>
        </div>
  
        <div class="input_box">
          <label for="contents">내용</label>
          <textarea name="contents" id="contents" required>내용을 입력해주세요.</textarea>
        </div>
        
        <div class="input_box">
          <label for="image">사진 (선택)</label>
          <input type="file" id="image" name="image" onchange="imageChk(this)">
        </div>
  
        <div class="btn_box full">
          <button class="btn">리뷰 등록하기</button>
        </div>
      </div>
    </form>
  </div>

  <div class="score_modal">
    <form action="/score" method="POST">
      <div class="flex jcsb aic">
        <h3>평점</h3>
        
        <div class="close_btn">
          <i class="fa fa-close"></i>
        </div>
      </div>
      <hr>
      <input type="text" id="store_id" name="store_id" hidden>
      <input type="text" id="user_id" name="user_id" hidden>
      <input type="text" id="score" name="score" hidden>
  
      <div class="inputs">
  
        <div class="input_box">
          <label for="title">평점</label>
          <div class="star">
            <div class="emp">
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
            </div>

            <div class="fill">
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
            </div>
          </div>
        </div>
  
        <div class="btn_box full">
          <button class="btn">평점 등록하기</button>
        </div>
      </div>
    </form>
  </div>

</template>

<script>

  $(document)
    .on("mousemove", ".star .emp", score)

  function formopen(target, name){
    const data = $(target).data("json");

    Modal.open(name);

    $(".modal #store_id").val(data.store_id);
    $(".modal #user_id").val(data.orderer_id);
  }

  function imageChk(target){
    const file = target.files[0];

    if(!file.type.includes("image")){
      target.value = null;
      alert("이미지 파일만 등록 가능합니다.");
    }
  }

  function score(e){
    const { left } = $(e.target).offset();
    const x = e.pageX - left;
    const score = Math.abs(Math.ceil(x/19));

    $(".score_modal #score").val(score);
    $(".score_modal .star .fill").css({
      "width" : `${score * 19.5}px`
    })
  }

</script>