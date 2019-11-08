<?php
/**
 *  @param string $dir The directory name to scan.
 */

$dir = $_POST["dir"];

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
      "name" => $item,
      "type" => $is_dir ? "dir" : "file",
      "data-file" => $dir . "/" . $item
    ];
  }, $arr);
  usort($arr, function($a, $b) {
    if ($a["type"] != $b["type"])
      return $a["type"] == "dir" ? -1 : 1;
    return 0;
  });
}

$result = [
  "content" => $arr
];
echo json_encode($result);