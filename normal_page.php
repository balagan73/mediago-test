<?php
session_start();
session_regenerate_id();
include_once('./template/header.html');
if (isset($_SESSION['login']) && $_SESSION['login'] > time() - 600) {
  $user = $_SESSION['user'];
  require('./classes/db.php');
  $db = new Db();
  $db->connect();
  $result = $db->getUser($user);
  $is_active = $result[0]['is_active'];
  if ($is_active) {
    $_SESSION['login'] = time();
    $navbar = "<nav class='navbar navbar-default'>
      <div class='container-fluid'>
        <ul class='nav navbar-nav navbar-right'>
          <li class='active'><a href='logout.php'>Kilépés</a></li>
        </ul>
      </div>
    </nav>";
    echo $navbar;
    $tz = new DateTimeZone('Europe/Budapest');
    $date = new DateTime();
    $date->setTimezone($tz);
    echo "<div><h2 class='text-center'>Üdvözöllek $user!</h2></div>";
    echo "<div class='text-center'>A pontos idő: " . $date->format('Y.m.d H:i:s') . "</div>";
    include_once('./template/normal_page.html');
    include_once('./template/footer.html');
  }
  else {
    header("Location: login.php");
  }
}
else {
  header("Location: login.php");
}






