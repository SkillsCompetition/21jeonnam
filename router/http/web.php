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

?>