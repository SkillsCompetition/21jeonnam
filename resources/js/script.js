const dd = console.log;

const App = {

  init(){
    App.hook();
  },

  hook(){
    $(document)
      .on("click", ".modal .close_btn", Modal.close)
  }

}

const Map = {

  init(){

  },

}

const Stamp = {
  code : [],

  init(){
    Stamp.loadCode();
  },

  loadCode(){
    $.getJSON("/resources/json/code.json")
      .done(res => {
        Stamp.code = res;
      });
  },

  issued(){
    const name = $("#username").val();

    if(!name) return alert("이름을 입력해주세요.");

    const canvas = $("#stamp")[0];
    const ctx = canvas.getContext("2d");

    const image = new Image();
    image.src = "/resources/img/stamp/stamp.png";

    image.onload = function(){
      ctx.drawImage(image, 0, 0, 432, 288);

      const data = canvas.toDataURL("image/jpeg");
      const a = document.createElement("a");

      a.href = data;
      a.download = "스탬프카드.jpg";

      a.click();
      a.remove();

      Modal.close();
    }
  },

  checkCode(){

  }

}

const Modal = {
  template : (target) => $($("template")[0].content).find(`.${target}_modal`).clone(),

  open(target){
    $("body").css("overflow", "hidden");

    $(".modal")
      .addClass("open")
      .html(Modal.template(target))
  },

  close(){
    $("body").css("overflow", "");

    $(".modal")
      .removeClass("open")
      .html("")
  }
}

$(() => App.init())