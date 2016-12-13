<?php

include_once('./template/header.html');

if (!empty($_POST)) {
  require('./classes/validation.php');
  $rules = array(
    'admin_name' => 'required|text',
    'admin_email' => 'required|email',
    'admin_pass' => 'required|password',
    'normal_name' => 'text',
    'normal_email' => 'email',
    'normal_pass' => 'password',
  );
  if (admin_validate($rules)) {
    require('./classes/db.php');
    $db = new Db();
    $db->connect();
    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];
    $admin_pass = $_POST['admin_pass'];
    $result = $db->getUser($admin_name);
    if (count($result) == 0) {
      $db->createUser($admin_name, $admin_email, $admin_pass, 1);
    }
    else {
      $error1 = "<div class='alert alert-danger text-center'>
        Az admin jogosultságú felhasználó már létezik.</div>";
    }
    if (!empty($_POST['normal_name']) && !empty($_POST['normal_email']) &&
      !empty($_POST['normal_pass'])) {
      $normal_name = $_POST['normal_name'];
      $normal_email = $_POST['normal_email'];
      $normal_pass = $_POST['normal_pass'];
      $result = $db->getUser($normal_name);
      if (count($result) == 0) {
        $res = $db->createUser($normal_name, $normal_email, $normal_pass, 0);
      }
      else {
        $error2 = "<div class='alert alert-danger text-center'>
        A normál jogosultságú felhasználó már létezik.</div>";
      }
    }

    if (!empty($error1)) {
      echo($error1);
      if (!empty($error2)) {
        echo($error2);
      }
    }
    else {
      echo($error1);
      header("Location: login.php");
    }
  }
}
include_once('./template/setup_admin.html');

include_once('./template/footer.html');

function admin_validate($rules) {
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