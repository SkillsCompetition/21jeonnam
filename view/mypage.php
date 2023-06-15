<div class="emp_box" style="height: 100px;"></div>

<div class="content">

  <div class="mypage_section">
    <div class="wrap">
      <div class="title">
        <h1>마이페이지</h1>
      </div>

      <div class="mypage">

      </div>
    </div>
  </div>

</div>

<script>

  function change(){
    $.get("/mypage/<?= $type ?>")
      .then(res => {
        $(".mypage").html(res);
      })
  }

  change();

  setInterval(() => change(), 1000);

</script>