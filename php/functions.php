<?php
define("ROOT", __DIR__ . "/../../");

function create_file($dir, $filename, $code) {
  if (!is_dir($dir))
    mkdir($dir, 0777, true);  
  
  $file = $dir . '/' . $filename;

  if (!file_exists($file))
    file_put_contents($file, $code);
}
  
function checkEssentials($essentials=null) {
  if ($essentials == null) {
    $essentials = [
      "composer.json",
      "index.php",
      "settings.php",
      "app/dependencies.php",
      "app/routes.php",
      "app/middleware.php"
    ];
  }
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

function initialize_template() {
  checkEssentials([
    "templates/default/css/style.css",
    "templates/default/js/script.js",
    "templates/default/partials/header.php",
    "templates/default/partials/footer.php",
    "templates/default/home.php"
  ]);
}

function populate_template($tpl, $data) {
  foreach ($data as $k => $v) {
    $tpl = str_replace("{{".$k."}}", $v, $tpl);
  }
  return $tpl;
}

// estrae le dipendenze da dependencies.php
// per il container definito nel contenuto del file sorgente passato
function extract_deps($content) {
  $result = [];
  
  // trova il nome del container
  $namespace = trim(find_between("namespace", $content, ";"));
  $className = trim(find_between("class", $content, "{"));
  $container = $namespace . "\\" . $className;
  
  $dependencies = file_get_contents(ROOT . "app/dependencies.php");
  $deps_line = find_between("return new $container(", $dependencies, ");");
  if ($deps_line == "")
    return [];
  
  $deps_arr = explode(",", $deps_line);
  
  $result = array_map(function($item) {
    return [
      "name" => trim(str_replace('$c->', "", $item))
    ];
  }, $deps_arr);
  return $result;
}

// estrae le dipendenze passate nel costruttore
function extract_needed_deps($content) {
  $deps = find_between("public function __construct(", $content, ")");
  $deps_arr = explode(',', $deps);
  return $deps_arr;
}

function find_between($start, $s, $end) {
  $found = "";
  
  $parts = explode($start, $s);
  if (isset($parts[1])) $found = explode($end, $parts[1])[0];

  return $found;
}

function list_dir_content($dir) {
  if (file_exists($dir)) {
    $arr = scandir($dir);
    $arr = array_values(array_filter($arr, function($item) {
      if ($item == "." || $item == "..")
        return false;
      return true;
    }));
    return $arr;
  }
  return [];
}

function get_controllers_from_file($file) {
  $content = php_strip_whitespace($file);
  
  $matches = [];
  $pattern = "/WebApp\\\\Controller\\\\(.*?)'/";
  $controllers = preg_match_all(
    $pattern,
    $content,
    $matches
  );
  
  return $matches[1];
}
