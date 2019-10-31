<?php
/**
 *  @param string $file The file name to load.
 */

$file = $_POST["file"];

$content = file_get_contents(__DIR__ . "/../../" . $file);
$new = false;

if (!$file) {
  $content = file_get_contents(__DIR__ . "/../files_defaults/" . $file);
  $new = true;
} 

$result = [
  "new" => $new,
  "content" => $content
];
echo json_encode($result);