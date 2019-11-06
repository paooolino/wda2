<?php
  include("php/functions.php");
  checkEssentials();
?>
<!doctype html>
<html>
<head>
  <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="css/silver.css">
  <link rel="stylesheet" href="css/pill-nav.css">
  <link rel="stylesheet" href="css/style.css">
  <script src="https://kit.fontawesome.com/e6666a068f.js" crossorigin="anonymous"></script>
</head>
<body>
  <div id="container">
    <div id="header">
      <div class="pill-nav editor_mode">
        <a id="button_logic" title="App logic"><i class="fas fa-project-diagram"></i></a>
        <a id="button_template" title="App template"><i class="far fa-file-code"></i></a>
      </div>
      <div class="pill-nav">
        <a title="Launch app" target="_blank" href="../index.php"><i class="fas fa-external-link-alt"></i></a>
      </div>
      <div class="pill-nav topbuttons_f">
        <a data-file="app/routes.php">routes.php</a>
        <a data-file="app/dependencies.php">dependencies.php</a>
        <a data-file="settings.php">settings.php</a>
        <a data-file="app/middleware.php">middleware.php</a>
        <a data-file="composer.json">composer.json</a>
        <a data-file="index.php">index.php</a>
      </div>
    </div>
    <div id="bodycontent">
      <div id="sidebar" class="col sidebar">
        <div id="sidebar_header">
          <button data-dir="app/src/Controller" title="Controllers"><i class="fas fa-gamepad"></i></button>
          <button data-dir="app/src/Model" title="Models"><i class="fas fa-database"></i></button>
          <button data-dir="app/src" title="Services"><i class="fas fa-hand-holding"></i></button>
          <button data-dir="app/src/Middleware" title="Middlewares"><i class="fas fa-bullseye"></i></button>
          <div class="close"></div>
        </div>
        <div id="listmenu_container">
          <ul id="listmenu">
          </ul>
          <ul id="listmenu_template">
          </ul>
        </div>
        <p id="add_input"><button class="add">add</button><input class="add_input"></p>
      </div>
      <div id="editor_area" class="col bigger">
        <div class="editor_bar">&nbsp;</div>
        <div id="editor"></div>
      </div>
    </div>
  </div>
  <div id="loading_layer">
    <div class="loading_bg"></div>
    <p><i class="fas fa-sync-alt fa-spin"></i> Loading...</p>
  </div>
  <script src="lib/jquery/jquery-3.4.1.min.js"></script>
  <script src="lib/ace/ace.js" type="text/javascript" charset="utf-8"></script>
  <script src="js/script.js"></script>
</body>
</html>