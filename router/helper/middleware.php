<?php
  
  function notUser(){
    err(@$USER, "비회원만 이용 가능합니다.", "/");
  }

  function user(){
    err(!USER, "로그인한 회원만 이용 가능합니다.", "/");
  }

?>