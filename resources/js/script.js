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
    Stamp.loadProduct();
  },

  loadCode(){
    $.getJSON("/resources/json/code.json")
      .done(res => {
        Stamp.code = res;
      });
  },

  loadProduct(){
    $.getJSON("/resources/json/product.json")
      .done(res => {
        Stamp.product = res;

        Stamp.settingRoulette();
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

  async openFile(target){
    [ Stamp.fileSystem ] = await window.showOpenFilePicker();

    $(`.upload_file.${target}`).val(Stamp.fileSystem.name);
  },

  async addStamp(){
    if(!Stamp.fileSystem?.getFile()) return alert("먼저 스탬프 카드를 선택해주세요.");

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
      let chkAllow = false;
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

          chkAllow = true;
          break;
        };
      }

      if(chkAllow){
        canvas.toBlob((data) => {
          Stamp.saveFile(data);
  
          alert("적립이 완료되었습니다.");
          Modal.close();
        })
      }else{
        alert("적립이 불가능합니다.");
      }
    }  
  },

  async saveFile(data){
    const writeAble = await Stamp.fileSystem.createWritable();

    await writeAble.write(data);
    await writeAble.close();

    Stamp.fileSystem = null;
  },

  settingRoulette(){
    const canvas = $('#roulette')[0];
    const ctx = canvas.getContext("2d");
    const colors = ["#fb6", "#fff"];
    const radian = Math.PI/180;

    ctx.translate(300, 300);
    ctx.strokeStyle = "#666";
    ctx.textAlign = "center";
    ctx.font = "bold 18px sans";

    for(let i = 0; i < 10; i++){
      ctx.beginPath();
        ctx.fillStyle = colors[i%2];
        ctx.moveTo(0,0)
        ctx.arc(0, 0, 299, radian * -108, radian * -72);
      ctx.closePath();
      ctx.fill();
      ctx.stroke();

      ctx.fillStyle = "#000";
      ctx.fillText(Stamp.product[i], 0, -250, 120);

      ctx.rotate(radian * 36)
    }
  },

  removeCount(){  
    return new Promise(async (res, rej) => {
      const file = await Stamp.fileSystem.getFile();
      const img = new Image();
      const fr = new FileReader();
    
      const canvas = $("#stamp")[0];
      const ctx = canvas.getContext("2d");
  
      fr.readAsDataURL(file);
  
      fr.onload = () => {
        img.src = fr.result;
      }
  
      img.onload = async () => {
        let chkAllow = false;
        ctx.drawImage(img, 0, 0);
  
        for (let i = 0; i < 8; i++) {
          const color = await ctx.getImageData(163 + (15 * i), 271, 1, 1);
  
          if(JSON.stringify([...color.data]) == '[0,128,0,255]'){
            ctx.beginPath();
              ctx.fillStyle = "red";
              ctx.arc(163 + (15 * i), 271, 3, 0, Math.PI *2);
            ctx.closePath();
            ctx.fill();
  
            chkAllow = true;
            break;
          }
        }

        if(chkAllow){
          canvas.toBlob(function(data){
            Stamp.saveFile(data);

            res();
          });
        }else{
          rej();
        }
      }
    })
  },

  rotateRoulette(){
    if(!Stamp.fileSystem?.getFile()) return alert("먼저 스탬프 카드를 선택해주세요.");

    Stamp.removeCount()
      .then(() => {
        const deg = Math.random() * 359;
        const product = Stamp.product[Math.floor((deg + 18)/36)];

        $("canvas#roulette").css({
          "transition" : "4s",
          "transform" : `rotate(-${deg + 3600}deg)`
        })

        setTimeout(() => {
          alert(`축하드립니다. '${product}'에 당첨되었습니다.`);

          $("canvas#roulette").css({
            "transition" : "0s",
            "transform" : `rotate(-0deg)`
          })
        }, 4000);
      })
      .catch(() => {
        alert("참여 횟수가 모자라 참여할 수 없습니다.");
      })
    
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