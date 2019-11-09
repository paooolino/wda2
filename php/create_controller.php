<?php
include("functions.php");

/**
 *  @param string $name The controller name.
 */

$code = file_get_contents(__DIR__ . '/files_defaults/app/src/Controller/Sample.php');
$code = str_replace('{{classname}}', $_POST["name"], $code);
$result = create_file( __DIR__ . '/../../app/src/Controller', $_POST["name"] . '.php', $code);

$result = [
  "result" => $result
];
echo json_encode($result);
