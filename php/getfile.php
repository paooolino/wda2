<?php
include("functions.php");
/**
 *  @param string $file The file name to load.
 */

$file = $_POST["file"];

$content = file_get_contents(__DIR__ . "/../../" . $file);

$deps = extract_deps($content);

$missing_controllers = [];
if ($file == "app/routes.php") {
  $controllers = get_controllers_from_file(__DIR__ . "/../../" . $file);
  $missing_controllers = array_values(array_filter($controllers, function($item) {
    $ctrl = __DIR__ . '/../../app/src/Controller/' . $item . '.php';
    if (!file_exists($ctrl))
      return true;
    return false;
  }));
}

$notices = array_map(function($item) {
  return [
    "type" => "missing_controller",
    "name" => $item
  ];
}, $missing_controllers);

$result = [
  "content" => $content,
  "deps" => $deps,
  "notices" => $notices
];
echo json_encode($result);