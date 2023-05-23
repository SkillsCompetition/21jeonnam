const dd = console.log;

const App = {

  init(){
    App.hook();
    
    if(location.pathname.includes("stemp")) Stamp.init();
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
  fileSystem : null,
  pos : [
    [20, 77],
    [123, 77],
    [226, 77],
    [329, 77],
    [20, 173],
    [123, 173],
    [226, 173],
    [329, 173]
  ],
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

      ctx.fillStyle = "#fff"
      ctx.font = "14px sans"
      ctx.fillText(name, 368, 20);

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
    const code = $("#couponCode").val();

    if(!Stamp.code.includes(code)) return alert("쿠폰 코드가 일치하지 않습니다.");

    Modal.open("addStamp");
  },

  async openFile(){
    [ Stamp.fileSystem ] = await window.showOpenFilePicker();

    $(".upload_file").val(Stamp.fileSystem.name);
  },

  async addStamp(){
    const file = await Stamp.fileSystem.getFile();
    const img = new Image();
    const fr = new FileReader();

    const canvas = $("#stamp")[0];
    const ctx = canvas.getContext("2d");

    if(!file.type.includes("image")) return alert("이미지만 선택 가능합니다.");

    fr.readAsDataURL(file);

    fr.onload = () => {
      img.src = fr.result;
    }

    img.onload = async () => {
      ctx.drawImage(img, 0, 0, 432, 288);

      for(let i = 0; i < 8; i++) {
        const [x, y] = Stamp.pos[i];
        const color = ctx.getImageData(x + 40, y + 50, 1, 1);

        if(JSON.stringify([...color.data]) == "[175,179,180,255]") {
          const stamp = $("#stamp_img")[0];

          ctx.drawImage(stamp, x, y, 83, 83);

          ctx.beginPath();
            ctx.fillStyle = "green";
            ctx.arc(163 + (15 * i), 271, 3, 0, Math.PI *2);
          ctx.closePath();
          ctx.fill();
          break;
        };
      }

      canvas.toBlob((data) => {
        Stamp.saveFile(data);
      })
    }  
  },

  async saveFile(data){
    const writeAble = await Stamp.fileSystem.createWritable();

    await writeAble.write(data);
    await writeAble.close();
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