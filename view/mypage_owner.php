<?php

  $state = [
    "accept" => "수락한 주문",
    "reject" => "거절한 주문",
    "taking" => "배달 중",
    "complete" => "배달 완료",
  ];

  $state_reservation = [
    "reject" => "거절한 예약",
    "accept" => "승인한 예약"
  ]

?>

<div class="order_list">
  <h3>주문 조회</h3>
  <table>
    <tr>
      <th>주문자 이름</th>
      <th>배달 주소</th>
      <th>라이더 이름</th>
      <th>도착 예정 시간</th>
      <th>방 종류 및 가격</th>
      <th>수량</th>
      <th>주문 상태</th>
    </tr>
    <?php foreach($orders as $v): ?>
      <?php 
        $rowspan = count($v["breads"]); 
        $menu = $v["breads"];

        $json = json_encode($v);
      ?>
      <?php foreach($menu as $k => $val): ?>
      <tr>
        <?php if($k == 0): ?>
        <td rowspan="<?= $rowspan ?>"><?= users::name($v["orderer_id"]) ?></td>
        <td rowspan="<?= $rowspan ?>"><?= users::location($v["orderer_id"]) ?></td>
        <td rowspan="<?= $rowspan ?>"><?= users::name($v["driver_id"]) ?></td>
        <td rowspan="<?= $rowspan ?>"><?= min_distance($v) ?></td>
        <td><?= $val["name"] ?> ( <?= number_format($val["price"]) ?>원 )</td>
        <td><?= $val["cnt"] ?>개</td>
        <td rowspan="<?= $rowspan ?>">
          <?php if($v["state"] == "order"): ?>
            <div class="btn_box jcc">
              <a href="/delivery/accept/<?= $v["id"] ?>" class="btn" style="background-color: green;">수락</a>
              <a href="/delivery/reject/<?= $v["id"] ?>" class="btn" style="background-color: tomato;">거절</a>
            </div>
          <?php else: ?>
            <?= $state[$v["state"]] ?>
          <?php endif ?>
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
      <th>예약자 이름</th>
      <th>예약 일시</th>
      <th>예약 신청 일시</th>
      <th>상태</th>
    </tr>
    <?php foreach($reservations as $v): ?>
    <tr>
      <td><?= $v["name"] ?></td>
      <td><?= formatDate($v["reservation_at"]) ?></td>
      <td><?= formatDate($v["request_at"]) ?></td>
      <td>
        <?php if($v["state"] == "order"): ?>
          <div class="btn_box jcc">
            <a href="/reservation/accept/<?= $v["id"] ?>" class="btn" style="background-color: green;">수락</a>
            <a href="/reservation/reject/<?= $v["id"] ?>" class="btn" style="background-color: tomato;">거절</a>
          </div>
        <?php else: ?>
          <?= $state_reservation[$v["state"]] ?>
        <?php endif ?>
      </td>
    </tr>
    <?php endforeach ?>
  </table>
</div>

<div class="menu_list">
  <h3>메뉴 리스트</h3>
  <table>
    <tr>
      <th>이름</th>
      <th>가격</th>
      <th>할인율</th>
      <th>할인가</th>
      <th>총 팔린 개수</th>
      <th>할인</th>
    </tr>
    <?php foreach($menus as $v): ?>
    <?php 
      $sale_price = sale_price($v["price"], $v["sale"]);  
    ?>
    <tr>
      <td><?= $v["name"] ?></td>
      <td><?= number_format($v["price"]) ?>원</td>
      <td><?= $v["sale"] == 0 ? "" : $v["sale"]."%" ?></td>
      <td><?= $v["sale"] == 0 ? "" : number_format($sale_price)."원" ?></td>
      <td><?= number_format($v["cnt"])."개" ?></td>
      <td class="none">
        <div class="btn" onclick="formOpen(<?= $v['id'] ?>, <?= $v['sale'] ?>)">할인</div>
      </td>
    </tr>
    <?php endforeach ?>
  </table>
</div>

<template>

  <div class="sale_modal">
    <form action="/sale" method="POST">
      <div class="flex jcsb aic">
        <h3>할인</h3>
        
        <div class="close_btn">
          <i class="fa fa-close"></i>
        </div>
      </div>
      <hr>

      <input type="text" id="bread_id" name="bread_id" hidden>
      <div class="inputs">
        <div class="input_box">
          <label for="sale">할인율</label>
          <input type="number" name="sale" id="sale" oninput="changeInput(this)">
          <h3>%</h3>
        </div>

        <div class="btn_box full">
          <button class="btn">할인율 변경하기</button>
        </div>
      </div>
    </form>
  </div>

</template>

<script>

  function formOpen(id, nowSale){
    Modal.open("sale");

    $("#bread_id").val(id);
    $("#sale").val(nowSale)
  }

  function changeInput(target){
    const value = target.value.replace(/\D/g, "") * 1;
    const vali_value = value < 0 ? 0 : value > 99 ? 99 : value;

    target.value = vali_value;
  }

</script>

