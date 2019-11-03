<?php
include("functions.php");

/**
 *  @param string $dir the directory in which to save the file
 *  @param string $file The file name to save.
 */

$dir = __DIR__ . '/../../' . $_POST["dir"];
$file = $_POST["file"];

// normalize file name
$file = ucfirst($file);
if (stristr($file, ".php") === false) {
  $file .= '.php';
}
$file = str_replace(" ", "_", $file);

$content = "<?php\r\n";
$sample = __DIR__ . '/files_defaults/' . $_POST["dir"] . '/Sample.php';
if (file_exists($sample)) {
  $content = file_get_contents($sample);
  $content = populate_template($content, [
    "classname" => str_replace(".php", "", $file)
  ]);
}

$result = create_file($dir, $file, $content);

$result = [
  "result" => $result
];
echo json_encode($result);
