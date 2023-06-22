<?php

  get("/", function(){
    view("index");
  });

  get("/sub", function(){
    view("sub");
  });

  get("/sub_template", function(){
    extract(G);

    $sql = @$keyword ? "WHERE store.name LIKE '%$keyword%'" : "";
    $search = DB::mq(
      "SELECT
        store.*,
        (SELECT AVG(grade.score) FROM grades AS grade WHERE store.id = grade.store_id) AS score,
        (SELECT COUNT(*) FROM reviews AS review WHERE store.id = review.store_id) AS review_count,
        NVL(SUM(item.cnt), 0) AS cnt
       FROM stores AS store
       LEFT JOIN breads AS bread
       ON bread.store_id = store.id
       LEFT JOIN delivery_items AS item
       ON item.bread_id = bread.id
       $sql
       GROUP BY store.id
       ORDER BY cnt DESC")->fetchAll();

    $popular = array_splice($search, 0, 5);

    require ROOT."/view/sub_ui.php";
  });

  get("/sale_event", function(){
    view("sale_event");
  });

  get("/sale_template", function(){
    $data = breads::findAll("sale != ? ORDER BY sale DESC", 0);

    require ROOT."/view/sale.php";
  });

  get("/stamp", function(){
    view("stamp");
  });

  middleware("notUser", function(){

    get("/login", function(){
      view("login");
    });
  
    post("/login", function(){
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

      view("mypage", get_defined_vars());
    });

    get("/mypage/$", function($type){
      if($type == "normal"){
        $orders = DB::mq(
         "SELECT 
            D.*, 
            S.name AS store_name
          FROM deliveries AS D
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
      }else if($type == "owner"){
        $orders = DB::mq(
          "SELECT 
             deli.*
           FROM deliveries AS deli
           LEFT JOIN stores AS store 
           ON deli.store_id = store.id
           WHERE store.user_id = ?", USER["id"])->fetchAll();

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
             store.name,
             reservation.*
           FROM reservations AS reservation
           LEFT JOIN stores AS store
           ON store.id = reservation.store_id
           WHERE store.user_id = ?", USER["id"])->fetchAll();

        $menus = DB::mq(
          "SELECT
            bread.*,
            NVL(SUM(deli.cnt), 0) AS cnt
           FROM stores AS store
           LEFT JOIN breads AS bread
           ON bread.store_id = store.id
           LEFT JOIN delivery_items AS deli
           ON deli.bread_id = bread.id
           WHERE store.user_id = ?
           GROUP BY bread.id", USER["id"])->fetchAll();
      }else{
        $delivery_list = DB::mq(
          "SELECT 
             D.*, 
             S.name AS store_name,
             S.user_id AS owner_id
           FROM deliveries AS D
           LEFT JOIN stores AS S 
           ON D.store_id = S.id
           WHERE D.state = ? || D.driver_id = ?
           ORDER BY D.order_at DESC", ["accept", USER["id"]])->fetchAll();
 
        $delivery_list = array_map(function($v){
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
        }, $delivery_list);
      }

      require ROOT."/view/mypage_$type.php";
    });

    get("/order/$", function($store_id){
      view("order", get_defined_vars());
    });

    get("/order_template/$", function($store_id){
      $store = stores::find("id = ?", $store_id);
      $menus = breads::findAll("store_id = ?", $store_id);

      $review_list = reviews::findAll("store_id = ?", $store_id);
      $maxPage = ceil(count($review_list)/2);

      $nowPage = @G["page"] > $maxPage ? ($maxPage == 0 ? 1 : $maxPage) : @G["page"]; 
      $review_list = array_slice($review_list, $nowPage - 1, 2);

      require ROOT."/view/order_ui.php";
    });

    get("/like/$", function($review_id){
      likes::insert([
        "review_id" => $review_id,
        "user_id" => USER["id"]
      ]);

      http_response_code(200);
      return true;
    });

  });

  middleware("normal", function(){

    post("/order_menu/$", function($store_id){
      $all_count = 0;
      $all_price = 0;

      $deli_id = deliveries::insert([
        "store_id" => $store_id,
        "orderer_id" => USER["id"]
      ]);

      foreach(P["menu"] as $k => $v){
        if($v["cnt"] == 0) continue;

        delivery_items::insert([
          "delivery_id" => $deli_id,
          "bread_id" => $k,
          "price" => $v["price"],
          "cnt" => $v["cnt"]
        ]);

        $all_price += $v["price"] * $v["cnt"];
        $all_count += $v["cnt"];
      }

      if($all_count == 0){
        DB::mq("DELETE FROM deliveries WHERE id = ?", $deli_id);

        move("주문할 상품을 한개 이상 선택해주세요.");
      }

      move("총 $all_count 개, $all_price 원이 주문되었습니다.", "/mypage");
    });
    
    post("/reservation", function(){
      extract(P);

      $reservation_at = "$reservation_date $reservation_time:00";

      reservations::insert([
        "store_id" => $store_id,
        "user_id" => USER["id"],
        "reservation_at" => $reservation_at
      ]);

      move("예약이 완료되었습니다.", "/mypage");
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

  middleware("owner", function(){

    get("/delivery/$/$", function($state, $delivery_id){
      DB::mq("UPDATE deliveries SET state = ? WHERE id = ?", [$state, $delivery_id]);

      move("주문 상태가 변경되었습니다.");
    });

    get("/reservation/$/$", function($state, $reservation_id){
      DB::mq("UPDATE reservations SET state = ? WHERE id = ?", [$state, $reservation_id]);

      move("예약 상태가 변경되었습니다.");
    });

    post("/sale", function(){
      extract(P);

      DB::mq("UPDATE breads SET sale = ? WHERE id = ?", [$sale, $bread_id]);

      move("할인율 변경이 완료되었습니다.");
    });

    post("/replie", function(){
      replies::insert(P);

      move("리뷰가 등록되었습니다.");
    });

  });

  middleware("rider", function(){

    post("/rider", function(){
      extract(P);

      $chk1 = true;
      $chk2 = true;

      if(USER["transportation"] != $transportation){
        $chk1 = DB::mq("UPDATE users SET transportation = ? WHERE id = ?", [$transportation, USER["id"]]);
  
        $_SESSION["user_101"]["transportation"] = $transportation;
      };

      if(USER["location_id"] != $location_id){
        $chk2 = DB::mq("UPDATE users SET transportation = ? WHERE id = ?", [$location_id, USER["id"]]);
  
        $_SESSION["user_101"]["location_id"] = $location_id;
      };

      echo $chk1 && $chk2;
    });

    get("/rider/$/$", function($state, $delivery_id){
      DB::mq("UPDATE deliveries SET state = ?, driver_id = ? WHERE id = ?", [$state, USER["id"], $delivery_id]);

      $msg = $state == "accept" ? "수락" : "완료 처리";

      move("배달이 ".$msg."되었습니다.");
    });

  });

?>