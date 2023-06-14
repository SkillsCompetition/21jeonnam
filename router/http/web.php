<?php

  get("/", function(){
    view("index");
  });

  get("/sub", function(){
    view("sub");
  });

  get("/stamp", function(){
    view("stamp");
  });

  middleware("notUser", function(){

    get("/login", function(){
      view("login");
    });
  
    post("/login", function(){
      err(emp_vali(P), "모든 값을 입력해주세요.");
  
      $find = users::find("id = ? && pw = ?", array_values(P));
      err(!$find, "아이디 혹은 비밀번호를 다시 확인해주세요.");
  
      $_SESSION["user_101"] = $find;
  
      move("로그인이 완료되었습니다.", "/");
    });

  });

  middleware("user", function(){

    get("/logout", function(){
      session_destroy();

      move("로그아웃이 완료되었습니다.", "/");
    });

    get("/mypage", function(){
      $type = USER["type"];

      if($type == "normal"){
        $orders = DB::mq(
         "SELECT 
            D.*, 
            U.name AS rider_name, 
            S.name AS store_name,
            U.location_id,
            S.user_id
          FROM deliveries AS D
          LEFT JOIN users AS U 
          ON D.driver_id = U.id
          LEFT JOIN stores AS S 
          ON D.store_id = S.id
          WHERE D.orderer_id = ?", USER["id"])->fetchAll();

        $orders = array_map(function($v){
          $list = DB::mq(
           "SELECT 
              B.name, 
              D.price, 
              D.cnt 
            FROM delivery_items AS D
            LEFT JOIN breads AS B 
            ON D.bread_id = B.id
            WHERE D.delivery_id = ?", $v["id"])->fetchAll();
                        
          $v["breads"] = $list;
          return $v;
        }, $orders);

        $reservations = DB::mq(
         "SELECT
            S.name,
            R.*
          FROM reservations AS R
          LEFT JOIN stores AS S 
          ON S.id = R.store_id
          WHERE R.user_id = ?", USER["id"])->fetchAll();
      }

      view("mypage_$type", get_defined_vars());
    });

    post("/review", function(){
      $data = P;

      if($_FILES["image"]){
        $img = $_FILES["image"];
        $type = explode("/", $img["type"]);

        $ext = end($type);

        $name = uniqid().".$ext";
        move_uploaded_file($img["tmp_name"], ROOT."/image/review/$name");

        $data["image"] = "/image/review/$name";
      }

      reviews::insert($data);
      move("리뷰가 등록되었습니다.");
    });

    post("/score", function(){
      grades::insert(P);
      move("평점이 등록되었습니다.");
    });

  });

?>