<?php
include("functions.php");

/**
 *  @param string $dir the directory in which to save the file
 *  @param string $file The file name to save.
 */

$file = __DIR__ . '/../../' . $_POST["file"];

$result = unlink($file);

$result = [
  "result" => $result
];
echo json_encode($result);
