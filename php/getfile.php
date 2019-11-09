<?php
include("functions.php");
/**
 *  @param string $file The file name to load.
 */

$file = $_POST["file"];

$content = file_get_contents(__DIR__ . "/../../" . $file);

$deps = extract_deps($content);

$controllers = [];
if ($file == "app/routes.php") {
  $controllers = get_controllers_from_file(__DIR__ . "/../../" . $file);
}

$notices = array_map(function($item) {
  return [
    "type" => "missing_controller",
    "name" => $item
  ];
}, $controllers);

$result = [
  "content" => $content,
  "deps" => $deps,
  "notices" => $notices
];
echo json_encode($result);