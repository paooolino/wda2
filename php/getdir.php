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

$result = [
  "new" => false,
  "content" => $arr
];
echo json_encode($result);