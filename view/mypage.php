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

  const prevHtml = {};
  let template = true;

  $.ajaxSetup({ cache : false })

  function load(){
    $.ajax("/mypage/<?= USER['type'] ?>")
      .then(res => {
        const sectionList = $($(res).toArray().filter(v => v.className));
        const templates = $($(res).toArray().filter(v => ["TEMPLATE", "SCRIPT"].includes(v.tagName)));

        if(template){
          template = false;

          templates.each((i, el) => {
            $("body").append($(el).clone());
          })
        }

        sectionList.each((i, el) => {
          const type = el.className.split(" ").join(".");
          const html = $(el).html();

          if(!prevHtml[type]) $(".mypage").append($(el).clone());

          if(prevHtml[type] != html) $(`.mypage .${type}`).html(html);

          prevHtml[type] = html;
        });
      })
  }

  load();

  setInterval(() => load(), 200);
</script>