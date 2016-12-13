<?php
include_once("./template/header.html");
if (file_exists('config.ini')) {
  header("Location: login.php");
}
include_once("/template/index.html");
include_once("./template/footer.html");