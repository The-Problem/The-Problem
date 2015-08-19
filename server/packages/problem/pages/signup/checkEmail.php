<?php
header('Content-type: application/json');

$result = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);
$returnArray = (
	"result"=>$result
);

echo json_encode($returnArray);