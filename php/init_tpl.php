<?php
include("functions.php");

initialize_template();

$result = [
  "result" => "OK"
];
echo json_encode($result);
