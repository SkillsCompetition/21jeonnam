<div class="double wrap">
  <div class="store_section">
    <div class="title">
      <h1>빵집 정보</h1>
    </div>

    <div class="store">
      <img src="<?= $store["image"] ?>" alt="">
      <div class="text_box">
        <h2><?= $store["name"] ?></h2>
        <hr>
        <div class="flex jcsb">
          <p><?= users::location($store["user_id"]) ?></p>
          <p><?= $store["connect"] ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="reservation_section col-flex">
    <div class="title">
      <h1>예약하기</h1>
    </div>

    <form action="/reservation" method="POST" class="reservation col-flex jcsb">
      <input type="text" name="store_id" value="<?= $store_id ?>" hidden>
      <div class="inputs">
        <div class="input_box">
          <label for="reservation_date">예약 날짜</label>
          <input type="date" name="reservation_date" id="reservation_date">
        </div>

        <div class="input_box">
          <label for="reservation_time">예약 시간</label>
          <input type="time" name="reservation_time" id="reservation_time">
        </div>

        <button class="btn">예약하기</button>
      </div>
    </form>
  </div>
</div>

<div class="orderMenu_list">
  <div class="wrap">
    <div class="title">
      <h1>주문</h1>
    </div>

    <form action="/order_menu/<?= $store["id"] ?>" method="POST" class="orderMenu col-flex">
      <div class="container">
        <?php foreach($menus as $v): ?>
        <?php
          $count = @G["menu"] == $v["id"] ? 1 : 0;  
        ?>
        <div class="item col-flex">
          <div class="img_box">
            <img src="<?= $v["image"] ?>" alt="">
          </div>
          <div class="text_box">
            <div class="flex jcsb aife">
              <h3 class="name"><?= $v["name"] ?></h3>
  
              <?php if($v["sale"] != 0): ?>
              <div class="sale_box flex aife">
                <small><?= number_format($v["price"]) ?>원</small>
                <h3 class="sale_per"><?= $v["sale"] ?>%</h3>
                <h3><?= number_format($v["price"] * ((100 - $v["sale"])/100)) ?>원</h3>
              </div>
              <?php else: ?>
                <h3><?= number_format($v["price"]) ?>원</h3>
              <?php endif ?>
            </div>
            <div class="btn_box">
              <div class="input_box">
                <label for="cnt">개수</label>
                <input type="number" name="menu[<?= $v["id"] ?>][price]" value="<?= $v["price"] * ((100 - $v["sale"])/100) ?>" hidden>
                <input type="text" name="menu[<?= $v["id"] ?>][cnt]" id="cnt" value="<?= $count ?>" readonly>
              </div>
              <div class="btn plus" onclick="count('menu[<?= $v['id'] ?>][cnt]', 1)"><i class="fa fa-plus"></i></div>
              <div class="btn minus" onclick="count('menu[<?= $v['id'] ?>][cnt]', -1)"><i class="fa fa-minus"></i></div>
            </div>
          </div>
        </div>
        <?php endforeach ?>
      </div>

      <button class="btn">주문하기</button>
    </form>
  </div>
</div>

<div class="review_section">
  <div class="wrap">
    <div class="title">
      <h1>리뷰</h1>
    </div>

    <div class="review col-flex aic">
      <div class="container">
        <?php foreach($review_list as $v): ?>
          <?php
            $replie = replies::find("review_id = ?", $v["id"]);
          ?>
          <div class="col-flex" style="gap: 1rem;">
            <div class="item">
              <?php if($v["image"]): ?>
                <img src="<?= $v["image"] ?>" alt="#">
              <?php else: ?>
                <div class="notFound_img">NOT FOUND</div>
              <?php endif ?>
              <div class="text_box col-flex jcsb">
                <div class="main col-flex">
                  <div class="top flex jcsb aic">
                    <div>
                      <h3 class="user"><?= users::name($v["user_id"]) ?>님</h3>
                      <small><?= $v['write_at'] ?></small>
                    </div>
        
                    <div class="like flex aic">
                      <?php if(likes::find("review_id = ? && user_id = ?", [$v["id"], USER["id"]])): ?>
                        <i class="fa fa-heart" style="color: tomato;"></i>
                      <?php else: ?>
                        <i class="fa fa-heart-o" onclick="like(<?= $v['id'] ?>)"></i>
                      <?php endif ?>
                      <p><?= count(likes::findAll("review_id = ?", $v["id"])) ?></p>
                    </div>
                  </div>
  
                  <h2><?= htmlspecialchars($v["title"]) ?></h2>
                  <p><?= htmlspecialchars($v["contents"]) ?></p>
                </div>
      
      
                <?php if($store["user_id"] == USER["id"] && !$replie): ?>
                <div class="btn" onclick="replieopen(<?= $v['id'] ?>)">답글 작성</div>
                <?php endif ?>
              </div>
            </div>
  
            <?php if($replie): ?>
              <div class="replie">
                <h3>사장님</h3>
                <p><?= htmlspecialchars($replie["contents"]) ?></p>
              </div>
            <?php endif ?>
          </div>
        <?php endforeach ?>
      </div>
      
      <div class="paging flex">
        <?php for($i = 1; $i <= $maxPage;$i++): ?>
        <div class="<?= $nowPage == $i ? "now" : "" ?>" onclick="page(<?= $i ?>)"><?= $i ?></div>
        <?php endfor?>
      </div>
    </div>
  </div>
</div>

<template>

  <div class="replie_modal">
    <form action="/replie" method="POST">
      <input type="text" name="review_id" hidden>
      <div class="flex jcsb aic">
        <h3>답글 작성하기</h3>
        
        <div class="close_btn">
          <i class="fa fa-close"></i>
        </div>
      </div>
      <hr>
      <div class="inputs">
        <div class="input_box">
          <div class="long">
            <label for="contents">내용</label>
          </div>
          <textarea name="contents" id="contents"></textarea>
        </div>

        <button class="btn">등록하기</button>
      </div>

    </form>
  </div>

</template>

<script>

  function page(i){
    nowPage = i;
    load();
  }

  function replieopen(id){
    Modal.open("replie");

    $(".modal input[name='review_id']").val(id);
  }

  function like(review_id){
    $.ajax({
      url : `/like/${review_id}`,
      method : "get"
    }).done(res => {
      alert("공감이 완료되었습니다.");
    });
  }

  function count(target, dir){
    let val = $(`input[name='${target}']`).val() * 1 + dir;

    val = val < 0 ? 0 : val;

    $(`input[name='${target}']`).val(val);
  }

</script>