<?php
/**
 *  @param string $dir The directory name to scan.
 */

$dir = $_POST["dir"];

$path = __DIR__ . '/../../' . $dir;

$arr = scandir($path);
$arr = array_values(array_filter($arr, function($item) {
  if ($item == "." || $item == "..")
    return false;
  return true;
}));
$result = [
  "content" => $arr
];
echo json_encode($result);