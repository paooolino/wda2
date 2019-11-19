<?php
include("functions.php");
/**
 *  @param string $file The file name to load.
 */

$file = $_POST["file"];

$content = file_get_contents(__DIR__ . "/../../" . $file);

$notices = [];
$deps = [];

// per il file routes, verifica che i controller definiti esistano
if ($file == "app/routes.php") {
  $missing_controllers = [];
  $controllers = get_controllers_from_file(__DIR__ . "/../../" . $file);
  $missing_controllers = array_values(array_filter($controllers, function($item) {
    $ctrl = __DIR__ . '/../../app/src/Controller/' . $item . '.php';
    if (!file_exists($ctrl))
      return true;
    return false;
  }));
  
  $notices = array_map(function($item) {
    return [
      "type" => "missing_controller",
      "name" => $item
    ];
  }, $missing_controllers);
}

// per il file dependencies, verifica che siano presenti le definizioni delle
// classi edistenti
if ($file == "app/dependencies.php") {
  $dependencies_php = file_get_contents(__DIR__ . "/../../app/dependencies.php");
  $controllers = list_dir_content(__DIR__ . "/../../app/src/Controller");
  foreach ($controllers as $c) {
    $classname = 'WebApp\\Controller\\' . str_replace('.php', '', $c);
    $find = '$container[\'' . $classname . '\'] = ';
    $code = file_get_contents(__DIR__ . '/files_defaults/app/dependencies_container.tpl');
    $code = str_replace("{{classname}}", $classname, $code);
    if (strpos($dependencies_php, $find) === false) {
      $notices[] = [
        "type" => "missing_controller_deps",
        "name" => $classname,
        "code" => $code
      ];
    }
  }
}

if (stristr($file, "app/src/Controller/") !== false) {
  $deps = extract_deps($content);
  $needed_deps = extract_needed_deps($content);
  if (count($deps) == 0) {
    $notices[] = [
      "type" => "missing_deps_entry",
      "needed_deps" => $needed_deps
    ];
  }
}



$result = [
  "content" => $content,
  "notices" => $notices,
  "deps" => $deps
];
echo json_encode($result);