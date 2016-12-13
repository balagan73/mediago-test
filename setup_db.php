<?php
include_once('./template/header.html');

if (!empty($_POST)) {
  require('./classes/validation.php');
  $rules = array(
    'address' => 'required|text',
    'username' => 'required|text',
    'password' => 'required|password',
    'dbname' => 'required|text',
  );
  if (validate($rules)) {
    if (!file_exists('config.ini')) {
      $data = "address: " . $_POST['address']. "\n";
      $data .= "username: " . $_POST['username']. "\n";
      $data .= "password: " . $_POST['password']. "\n";
      $data .= "dbname: " . $_POST['dbname'];
      file_put_contents('config.ini', $data);
    }
    else {
      echo "<div class='warning warning-danger'><h2>Már létezik config.ini fájl!</h2></div>";
    }

    require('./classes/db.php');
    $db = new Db();
    $db->connect();
    $db->createTable();

    header("Location: setup_admin.php");
  }
}

include_once('./template/setup_db.html');
include_once('./template/footer.html');

function validate($rules) {
  $validation = new Validation();
  if ($validation->validate($_POST, $rules) == TRUE) {
    return TRUE;
  }
  else {
    foreach ($validation->errors as $error) {
      echo("<div class='row'>");
      echo("<ul class='alert alert-danger text-center'>$error</ul>");
      echo("</div>");
    }
  }
}