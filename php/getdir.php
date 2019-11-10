<?php
/**
 *  @param string $dir The directory name to scan.
 */

$dir = $_POST["dir"];

$path = __DIR__ . '/../../' . $dir;
if (!file_exists($path)) {
  $result = [
    "new" => true,
    "content" => []
  ];
  echo json_encode($result);
  die();
}

$arr = scandir($path);
$arr = array_values(array_filter($arr, function($item) use($path) {
  if ($item == "." || $item == "..")
    return false;
  if (is_dir($path . '/' . $item))
    return false;
  return true;
}));
$result = [
  "new" => false,
  "content" => $arr
];
echo json_encode($result);