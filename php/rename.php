<?php
include("functions.php");

/**
 *  @param string $file the complete path of the file to rename
 *  @param string $newname The new name
 */

$file = $_POST["file"];
$newname = $_POST["newname"];

$newfile = implode("/", array_slice(explode("/", $file), 0, -1)) . '/' . $newname;
$result = rename(__DIR__ . '/../../' . $file, __DIR__ . '/../../' . $newfile);

$result = [
  "result" => $result
];
echo json_encode($result);
