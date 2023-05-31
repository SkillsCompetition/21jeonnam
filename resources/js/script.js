const dd = console.log;

const App = {

  init(){
    App.hook();
    
    if(location.pathname.includes("/")) Map.init();
    if(location.pathname.includes("stemp")) Stamp.init();
  },

  hook(){
    $(document)
      .on("click", ".modal .close_btn", Modal.close)
      .on("mousedown", ".modal .map_box", Map.moveMap.mousedown)
      .on("mousemove", ".modal .map_box", Map.moveMap.mousemove)
      .on("mouseup mouseleave", ".modal .map_box", Map.moveMap.mouseup)
  }

}

const Map = {
  pos : [0, 0],
  isWheel : true,
  zoom : 1,
  maxSize : 800,
  data : [
    {
      "x" : 2941,
      "y" : 2694,
      "store" : "성심당 본점"
    },
    {
      "x" : 1712,
      "y" : 1991,
      "store" : "콜마르브레드"
    },
    {
      "x" : 2347 ,
      "y" : 625,
      "store" : "아빠의 꿈"
    },
    {
      "x" : 3447,
      "y" : 2381,
      "store" : "파이한모금"
    },
    {
      "x" : 815 ,
      "y" : 3632,
      "store" : "르뺑99-1"
    }
  ],
  nowData : {
    "x" : 2941,
    "y" : 2694,
    "store" : "성심당 본점"
  },

  openMap(idx){
    Map.nowData = Map.data[idx];
    Map.zoom = 1;
    Map.maxSize = 800;

    Modal.open("map")
    $(".map_box")[0].addEventListener("wheel", Map.zoomMap, { passive : true })

    Map.newMap(0, 0).then(() => {
      Map.addMarker();
      Map.setContext();
    });
  },

  newMap(x, y){
    const canvas = $(`.map_modal #map${Map.zoom}`);
    const ctx = canvas[0].getContext("2d");

    canvas.css({
      "left" : `${x}px`,
      "top" : `${y}px`
    });

    ctx.clearRect(0, 0, canvas[0].width, canvas[0].height);

    const split = 2**(Map.zoom - 1);

    const promise = new Array(4**(Map.zoom - 1)).fill(0).map((v, i) => {
      return new Promise(res => {
        const x = 800 * (i%split);
        const y = 800 * Math.floor(i/split);
  
        $(`<img>`, { src : `/resources/img/map/${Map.zoom}/${i + 1}.jpg` })[0]
          .onload = (e) => {
            ctx.drawImage(e.target, x, y, 800, 800);
            res();
          }
      })
    });

    return Promise.all(promise);
  },

  zoomMap(e){
    if(!Map.isWheel) return;

    const { top, left } = $(`.map_box`).offset();
    const origin = $(`.map_modal #map${Map.zoom}`).offset();
    let [originX, originY] = [origin.left, origin.top];
    const dir = e.deltaY/-100;
    let [x, y] = [...Map.pos];

    if(Map.zoom + dir > 3 || Map.zoom + dir < 1) return;

    Map.isWheel = false;
    Map.zoom += dir;

    if (dir === -1) {
      const [posX, posY] = Map.pos;
      const limit = (Map.maxSize/2 - 600)/2;

      originX = posX > limit ? "left" : posX < (limit * -1) ? "right" : `${posX + (Map.maxSize/2)}px`;
      originY = posY > limit ? "top" : posY < (limit * -1) ? "bottom" : `${posY + (Map.maxSize/2)}px`;

      x = ( x + ( ( 400 - ( e.pageX - left ) ) * dir ) )/2;
      y = ( y + ( ( 400 - ( e.pageY - top ) ) * dir ) )/2;

      Map.maxSize /= 2
    } else {
      originX = `${e.pageX - originX}px`;
      originY = `${e.pageY - originY}px`;

      x = ( x + ( ( 400 - ( e.pageX - left ) ) * dir ) )*2;
      y = ( y + ( ( 400 - ( e.pageY - top ) ) * dir ) )*2;

      Map.maxSize *= 2
    }

    $(`.map_modal #map${Map.zoom - dir}`).css({
      "transform-origin" : `${originX} ${originY}`
    })

    setTimeout(() => {
      $(`.map_modal #map${Map.zoom - dir}`).css({
        "transition" : `.3s`,
        "transform" : `translate(calc(-50% + 400px), calc(-50% + 400px)) scale(${1 + (dir == -1 ? -.3 : 1)})`
      })
    }, 100)

    setTimeout(() => {
      $(`.map_modal canvas:not(#context)`).attr("hidden", true)
      $(`.map_modal #map${Map.zoom}`).attr("hidden", false);

      $(`.map_modal #map${Map.zoom - dir}`).css({
        "transition" : `0s`,
        "transform" : `translate(calc(-50% + 400px), calc(-50% + 400px))`
      })

      Map.isWheel = true;
    }, 400)

    Map.pos = Map.max([x, y]);

    Map.newMap(...Map.pos).then(() => {
      Map.addMarker();
      Map.setContext();
    })
  },

  addMarker(){
    const { x, y } = {...Map.nowData};

    const canvas = $(`.map_modal #map${Map.zoom}`);
    const ctx = canvas[0].getContext("2d");

    const marker = $("#marker_img")[0];

    const split = 2**(3 - Map.zoom);

    ctx.drawImage(marker, (x/2)/split - 15, (y/2)/split - 23, 21, 30);
  },

  setContext(){
    const [x, y] = Map.pos;
    const max = Map.maxSize/2 - 400;

    const canvas = $(`.map_modal #context`);
    const ctx = canvas[0].getContext("2d");

    ctx.drawImage($(`.map_modal #map${Map.zoom}`)[0], max - x, max - y, 800, 800, 0, 0, 800, 800);
  },

  moveMap : {
    mousedown(e){
      Map.startPos = [
        e.pageX,
        e.pageY
      ];

      Map.isMove = true;
    },

    mousemove(e){
      if(!Map.isMove) return;

      let movePos = [
        Map.pos[0] + e.pageX - Map.startPos[0],
        Map.pos[1] + e.pageY - Map.startPos[1]
      ];

      const [moveX, moveY] = Map.max(movePos);

      $(`.map_modal #map${Map.zoom}`).css({
        "left" : moveX,
        "top" : moveY
      })
    },

    mouseup(e){
      if(!Map.isMove) return;
      Map.isMove = false;

      let movePos = [
        Map.pos[0] + e.pageX - Map.startPos[0],
        Map.pos[1] + e.pageY - Map.startPos[1]
      ];

      Map.pos = Map.max(movePos);
      Map.setContext();
    }
  },

  max(arr){
    const max = Map.maxSize/2 - 400;
    return arr.map(v => {
      return Math.abs(v) > max ? max * Math.sign(v): v
    })
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