<?php
  
  function notUser(){
    err(@$USER, "비회원만 이용 가능합니다.", "/");
  }

  function user(){
    err(!USER, "로그인한 회원만 이용 가능합니다.", "/");
  }

  function rider(){
    err(@USER["type"] != "rider", "라이더만 접근 가능합니다.");
  }

  function owner(){
    err(@USER["type"] != "owner", "사장님만 접근 가능합니다.");
  }

  function normal(){
    err(@USER["type"] != "normal", "고객만 접근 가능합니다.");
  }

?>