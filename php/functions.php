<?php
define("ROOT", __DIR__ . "/../../");

function create_file($dir, $filename, $code) {
  if (!is_dir($dir))
    mkdir($dir, 0777, true);  
  
  $file = $dir . '/' . $filename;

  if (!file_exists($file))
    file_put_contents($file, $code);
}
  
function checkEssentials() {
  $essentials = [
    "composer.json",
    "index.php",
    "settings.php",
    "app/dependencies.php",
    "app/routes.php",
    "app/middleware.php"
  ];
  foreach ($essentials as $f) {
    $filename = ROOT . $f;

    if (!file_exists($filename)) {
      $dir = dirname($filename);
      if (!is_dir($dir))
        mkdir($dir, 0777, true);
      
      $code = file_get_contents(__DIR__ . "/files_defaults/" . $f);
      file_put_contents($filename, $code);
    }
  }
}