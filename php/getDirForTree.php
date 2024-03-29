<?php
/**
 *  @param string $dir The directory name to scan.
 */

$dir = $_GET["id"];
if ($dir == '#')
  $dir = 'templates/default';

$path = __DIR__ . '/../../' . $dir;

$arr = [];
if (file_exists($path)) {
  $arr = scandir($path);
  $arr = array_values(array_filter($arr, function($item) {
    if ($item == "." || $item == "..")
      return false;
    return true;
  }));
  $arr = array_map(function($item) use($path, $dir) {
    $is_dir = is_dir($path . "/" . $item);
    return [
      "text" => $item,
      "children" => $is_dir ? true : false,
      "type" => $is_dir ? "dir" : "file",
      "icon" => $is_dir ? "far fa-folder color-folder" : "far fa-file color-file",
      "id" => $dir . "/" . $item
    ];
  }, $arr);
  // ordina in modo da avere per prime le directories
  usort($arr, function($a, $b) {
    if ($a["children"] != $b["children"]) {
      return $a["children"] === true ? -1 : 1;
    }
    if ($a["children"] == $b["children"]) {
      return strcmp("" . $a["text"], "" . $b["text"]);
    }
    return 0;
  });
}

echo json_encode($arr);