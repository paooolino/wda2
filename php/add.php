<?php
include("functions.php");

/**
 *  @param string $dir the directory in which to save the file
 *  @param string $file The file name to save.
 */

$dir = __DIR__ . '/../../' . $_POST["dir"];
$file = $_POST["file"];

$content = "<?php\r\n";
$result = create_file($dir, $file, $content);

$result = [
  "result" => $result
];
echo json_encode($result);
