<?php

require "login.php";

$tag = $_POST["tag"] . "%";
$prereqs = $_POST["prereqs"];

if ($prereqs=="1") {
    $STH=$DBH->prepare("SELECT name FROM prereqs WHERE LOWER(name) LIKE LOWER(:tag)");
} else {
    $STH=$DBH->prepare("SELECT name FROM tags WHERE LOWER(name) LIKE LOWER(:tag)");
}

$STH->setFetchMode(PDO::FETCH_ASSOC);
$STH->execute(array(":tag" => $tag));
$result = $STH->fetchAll();

echo str_replace("_", " ", $result[0]["name"]);

?>