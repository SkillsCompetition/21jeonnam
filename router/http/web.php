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

  });

?>