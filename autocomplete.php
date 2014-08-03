<?php

require "login.php";

$tag = $_POST["tag"];
$prereqs = $_POST["prereqs"];

$table = "tags";
if ($prereqs=="1") $table = "prereqs";

$STH=$DBH->prepare("SELECT name FROM :table WHERE LOWER(name) LIKE LOWER(:tag)");
$STH->setFetchMode(PDO::FETCH_ASSOC);
$STH->execute(array(":table" => $table, ":tag" => $tag));
$result = $STH->fetchAll();

echo $result[0]["name"];


?>