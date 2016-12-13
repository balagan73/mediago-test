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
  $user_id = $result[0]['id'];
  $is_active = $result[0]['is_active'];
  $is_admin = $result[0]['is_admin'];
  if ($is_admin && $is_active) {
    if (isset($_GET['delete'])) {
      $db->deleteUser($_GET['delete']);
      if ($user_id == $_GET['delete']) {
        header("Location: login.php");
      }
    }

    if (isset($_GET['ban'])) {
      $db->banUser($_GET['ban'], $_GET['value']);
      if ($user_id == $_GET['ban']) {
        header("Location: login.php");
      }
    }

    if (isset($_GET['switch'])) {
      $db->switchAdmin($_GET['switch'], $_GET['value']);
      if ($user_id == $_GET['switch'] && $_GET['value'] == 0) {
        header("Location: login.php");
      }
    }

    $_SESSION['login'] = time();
    $navbar = "<nav class='navbar navbar-default'>
    <div class='container-fluid'>
      <ul class='nav navbar-nav navbar-right'>
        <li class='active'><a href='logout.php'>Kilépés</a></li>
      </ul>
    </div>
  </nav>";
    echo $navbar;

    if (isset($_GET['list'])) {
      include_once('./template/admin_list_start.html');

      $result = $db->listUsers();
      echo "<ul>";
      foreach($result as $user) {
        $id = $user['id'];
        $username = $user['username'];
        $is_admin = $user['is_admin'];
        $is_active = $user['is_active'];
        $login_time = $user['login_time'];
        $reg_date = $user['reg_date'];
        $color = $is_admin ? "red" : "black";
        $admin_button = $is_admin ?
          "<a href='admin_page.php?switch=$id&value=0&list'>Admin jog elvétele</a>"
          : "<a href='admin_page.php?switch=$id&value=1&list'>Admin jog megadása</a>";
        $ban_button = $is_active ?
          "<a href='admin_page.php?ban=$id&value=0&list'>Tiltás</a>"
          : "<a href='admin_page.php?ban=$id&value=1&list'>Aktiválás</a>";
        echo "<div class='row'>";
        echo "<li style='color:$color'>$username, regisztráció: $reg_date,
        utolsó belépés: $login_time
        </li><button><a href='admin_page.php?delete=$id&list'>Törlés</a></button>

        <button>$ban_button</button>
        <button>$admin_button</button></div>";
      }
      echo "</ul>";
      include_once('./template/admin_list.html');
    }
    else {
      include_once('./template/admin_page.html');
    }
    include_once('./template/footer.html');
  }
  else {
    header("Location: login.php");
  }
}
else {
  header("Location: login.php");
}
