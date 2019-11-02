<?php
include("functions.php");

/**
 *  @param string $file The file path to save.
 *  @param string $value The file content to save.
 */

$file = __DIR__ . '/../../' . $_POST["file"];

$content = "<?php\r\n";
$result = file_put_contents($file, $_POST["value"]);

$result = [
  "result" => $result
];
echo json_encode($result);
