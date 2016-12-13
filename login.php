<?php
include_once('./template/header.html');

if (!empty($_POST)) {
  require('./classes/validation.php');
  $rules = array(
    'username' => 'text',
    'password' => 'password',
    'reg_name' => 'text',
    'reg_email' => 'email',
    'reg_pass' => 'password',
  );
  if (login_validate($rules)) {
    require('./classes/db.php');
    $db = new Db();
    $db->connect();

    if (isset($_POST['Login'])) {
      echo "LOGIN";
      $username = $_POST['username'];
      $result = $db->getUser($username);
      if (count($result) == 0) {
        echo "<div class='alert alert-danger text-center'>
        Hibás felhasználónév vagy jelszó</div>";
      }
      else {
        $password = $_POST['password'];
        if (password_verify($password, $result[0]['hash'])) {
          $id = $result[0]['id'];
          $is_admin = $result[0]['is_admin'];
          $db->updateLogin($id);
          session_start();
          $_SESSION["login"] = time();
          $_SESSION["user"] = $username;
          $location = $is_admin ? "admin_page.php" : "normal_page.php";
          header("Location: $location");
         }
        else {
          echo "<div class='alert alert-danger text-center'>
            Hibás felhasználónév vagy jelszó</div>";
        }
      }
    }
    if (isset($_POST['Register'])) {
      $reg_name = $_POST['reg_name'];
      $reg_email = $_POST['reg_email'];
      $reg_pass = $_POST['reg_pass'];
      if (!empty($_POST['reg_name']) && !empty($_POST['reg_email']) &&
        !empty($_POST['reg_pass'])) {
        $result = $db->getUser($reg_name);
        if (count($result) == 0) {
          $db->createUser($reg_name, $reg_email, $reg_pass, 0);
          session_start();
          $_SESSION["login"] = time();
          $_SESSION["user"] = $reg_name;
          header("Location: normal_page.php");
        }
        else {
          $error = "<div class='alert alert-danger text-center'>
        A felhasználó már létezik.</div>";
          echo $error;
        }
      }
    }
  }
}

include_once('./template/login.html');
include_once('./template/footer.html');


function login_validate($rules) {
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