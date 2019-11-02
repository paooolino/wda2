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
    <div class="header">
      <div class="pill-nav topbuttons_f">
        <a data-file="app/routes.php">routes.php</a>
        <a data-file="app/dependencies.php">dependencies.php</a>
        <a data-file="settings.php">settings.php</a>
        <a data-file="app/middleware.php">middleware.php</a>
        <a data-file="composer.json">composer.json</a>
        <a data-file="index.php">index.php</a>
      </div>
      <!--<div class="pill-nav topbuttons_d">
        <a data-dir="app/Controller">Controllers</a>
        <a data-dir="app/Model">Models</a>
        <a data-dir="app">Services</a>
        <a data-dir="app/Middleware">Middlewares</a>
      </div>-->
    </div>
    <div class="grid bodycontent">
      <div class="col sidebar">
        <div class="sidebar_header">
          <button data-dir="app/Controller" title="Controllers"><i class="fas fa-gamepad"></i></button>
          <button data-dir="app/Model" title="Models"><i class="fas fa-database"></i></button>
          <button data-dir="app/src" title="Services"><i class="fas fa-hand-holding"></i></button>
          <button data-dir="app/Middleware" title="Middlewares"><i class="fas fa-bullseye"></i></button>
          <div class="close"></div>
        </div>
        <ul class="listmenu">
        </ul>
        <input class="add_input">
        <p><button class="add">add</button></p>
      </div>
      <div class="col bigger">
        <div class="editor_bar"></div>
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