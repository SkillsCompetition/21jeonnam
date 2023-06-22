<div class="sale_section">
  <div class="wrap">
    <div class="title">
      <h1>할인 이벤트</h1>
    </div>

    <div class="sale">
      <?php foreach($data as $v): ?>
      <div class="item col-flex" onclick="location.href='/order/<?= $v['store_id'] ?>?menu=<?= $v['id'] ?>'">
        <div class="store"><?= stores::data($v["store_id"], "name") ?></div>
        <img src="<?= $v["image"] ?>" alt="">
        <div class="text_box col-flex">
          <div class="top flex jcsb aife">
            <h3><?= $v["name"] ?></h3>
            <p><?= number_format($v["price"] * ((100 - $v["sale"])/100)) ?>원</p>
          </div>
          <hr>
          <div class="bottom flex jcsb">
            <p>원가 : <?= number_format($v["price"]) ?>원</p>
            <p>할인율 : <?= $v["sale"] ?>%</p>
          </div>
        </div>
      </div>
      <?php endforeach ?>
    </div>
  </div>
</div>