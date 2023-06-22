<?php
  $locations = locations::all();
?>

<div class="info_section">
  <h3>내정보</h3>
  <form action="/rider" method="POST" enctype="multipart/form-data">
    <div class="inputs">
      <div class="input_box">
        <label for="location">내 위치</label>
        <select name="location_id" id="location" onchange="change()">
          <?php foreach($locations as $v): ?>
          <option <?= USER["location_id"] == $v["id"] ? "selected" : "" ?> value="<?= $v["id"] ?>"><?= $v["name"] ?></option>
          <?php endforeach ?>
        </select>
      </div>
  
      <div class="input_box">
        <label for="transportation">내 교통수단</label>
        <select name="transportation" id="transportation" onchange="change()">
          <option <?= USER["transportation"] == "bike" ? "selected" : "" ?> value="bike">자전거</option>
          <option <?= USER["transportation"] == "motorcycle" ? "selected" : "" ?> value="motorcycle">오토바이</option>
        </select>
      </div>
    </div>
  </form>
</div>

<div class="delivery_list">
  <h3>배달 리스트 영역</h3>
  <table>
    <tr>
      <th>빵집 이름</th>
      <th>빵집 주소</th>
      <th>배달 주소</th>
      <th>도착 예정 시간</th>
      <th>빵 종류</th>
      <th>수량</th>
      <th>상태</th>
    </tr>
    
    <?php foreach($delivery_list as $v): ?>
      <?php 
        $rowspan = count($v["breads"]); 
        $menu = $v["breads"];

        unset($v["breads"]);
      ?>
      <?php foreach($menu as $k => $val): ?>
      <tr>
        <?php if($k == 0): ?>
        <td rowspan="<?= $rowspan ?>"><?= $v["store_name"] ?></td>
        <td rowspan="<?= $rowspan ?>"><?= users::location($v["owner_id"]) ?></td>
        <td rowspan="<?= $rowspan ?>"><?= users::location($v["orderer_id"]) ?></td>
        <td rowspan="<?= $rowspan ?>"><?= min_distance($v) ?></td>
        <td><?= $val["name"] ?> ( <?= number_format($val["price"]) ?>원 )</td>
        <td><?= $val["cnt"] ?>개</td>
        <td rowspan="<?= $rowspan ?>">
          <div class="btn_box jcc">
            <?php if($v["state"] == "accept"): ?>
              <a class="btn" href="/rider/taking/<?= $v["id"] ?>">수락</a>
            <?php elseif($v["state"] == "complete"): ?>
              완료한 배달
            <?php else: ?>
              <a class="btn" href="/rider/complete/<?= $v["id"] ?>">완료</a>
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



<script>

  function change(){
    const formdata = new FormData($(".info_section form")[0]);

    $.ajax({
      url : "/rider",
      method : "post",
      processData : false,
      contentType : false,
      data : formdata
    });
  }

</script>