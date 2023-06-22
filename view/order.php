<div class="emp_box" style="height: 70px;"></div>

<div class="content">

</div>

<script>

const prevHtml = {};
let nowPage = 1;
let template = true;

$.ajaxSetup({ cache : false })

  function load(){
    $.ajax(`/order_template/<?= $store_id ?>?menu=<?= @G["menu"] ?>&page=${nowPage}`)
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

          if(!prevHtml[type]) $(".content").append($(el).clone());

          if(prevHtml[type] != html) $(`.content .${type}`).html(html);

          prevHtml[type] = html;
        });
      })
  }

  load();

  setInterval(() => load(), 200);

</script>

