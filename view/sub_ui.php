<div class="popular_section">
    <div class="wrap">
      <div class="title">
        <h1>빵집 베스트5<span>인기있는 빵집들입니다!</span></h1>
      </div>

      <div class="popular">

        <?php foreach($popular as $k => $v): ?>
        <div class="item" onclick="location.href='/order/<?= $v['id'] ?>'">
          <div class="rank"><?= $k + 1 ?></div>
          <img src="<?= $v["image"] ?>" alt="#" title="#">
          <div class="text_box">
            <div class="flex aic jcsb">
              <h2><?= $v["name"] ?><span><?= users::location($v["user_id"]) ?></span></h2>

              <p><i class="fa fa-star"></i> <?= number_format(round($v["score"], 1), 1) ?>점</p>
            </div>
            <hr>
            <div class="bottom flex jcsb">
              <p><?= $v["connect"] ?></p>

              <p>리뷰 <?= number_format($v["review_count"]) ?>개</p>
            </div>
          </div>
        </div>
        <?php endforeach ?>

      </div>
    </div>
  </div>

  <div class="shoplist_section">
    <div class="wrap">
      <div class="title">
        <h1>대전 빵집<span>대전 빵집 목록을 확인하세요!</span></h1>
      </div>

      <div class="shoplist col-flex">
        <form class="search_box flex" action="/sub" method="GET">
          <input type="text" id="search" name="search" value="<?= @$keyword ?>" placeholder="검색어를 입력해주세요.">
          <button class="btn">검색</button>
        </form>

        <div class="container">
          <?php foreach($search as $v): ?>
            <div class="item" onclick="location.href='/order/<?= $v['id'] ?>'">
              <img src="<?= $v["image"] ?>" alt="#" title="#">
              <div class="text_box">
                <div class="flex aic jcsb">
                  <h2><?= $v["name"] ?><span><?= users::location($v["user_id"]) ?></span></h2>
  
                  <p><i class="fa fa-star"></i> <?= number_format(round($v["score"], 1), 1) ?>점</p>
                </div>
                <hr>
                <div class="bottom flex jcsb">
                  <p><?= $v["connect"] ?></p>

                  <p>리뷰 <?= number_format($v["review_count"]) ?>개</p>
                </div>
              </div>
            </div>
          <?php endforeach ?>
        </div>

      </div>
    </div>
  </div>