<div class="emp_box" style="height: 70px;"></div>

<div class="content">

</div>

<script>

const prevHtml = {};

$.ajaxSetup({ cache : false })

function load(){
  $.ajax("/sale_template")
    .then(res => {
      const sectionList = $($(res).toArray().filter(v => v.className));

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