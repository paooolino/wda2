<?php
  include("functions.php");
  
?>
<!doctype html>
<html>
<head>
  <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="css/silver.css">
  <link rel="stylesheet" href="css/pill-nav.css">
  <link rel="stylesheet" href="css/style.css">
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
      <div class="pill-nav topbuttons_d">
        <a data-dir="app/Controller">Controllers</a>
        <a data-dir="app/Model">Models</a>
        <a data-dir="app">Services</a>
        <a data-dir="app/Middleware">Middlewares</a>
      </div>
    </div>
    <div class="grid bodycontent">
      <div class="col sidebar">
        <!--
        <ul class="listmenu">
          <li>HOME</li>
          <li>LOGIN</li>
          <li>LOGIN_POST</li>
          <li>DASHBOARD</li>
          <li>MESSAGE</li>
        </ul>
        <p><button>add</button></p>
        -->
      </div>
      <div class="col bigger">
        <div id="editor"></div>
      </div>
    </div>
  </div>
  
  <script src="lib/jquery/jquery-3.4.1.min.js"></script>
  <script src="lib/ace/ace.js" type="text/javascript" charset="utf-8"></script>
  <script src="js/script.js"></script>
</body>
</html>