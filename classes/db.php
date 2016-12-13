<?php

class Db {
  private $dbname;
  private $username;
  private $password;
  private $servername;
  private $conn;

  public function __construct() {
    $config = file_get_contents("config.ini");
    $lines = explode("\n", $config);
    $config = array();
    foreach ($lines as $line) {
      list ($k, $v) = explode(":", $line);
      $config[trim($k)] = trim($v);
    }

    $this->dbname = $config['dbname'];
    $this->username = $config['username'];
    $this->password = $config['password'];
    $this->servername = $config['address'];
  }

  public function connect() {
    try {
      $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e)
    {
      echo $e->getMessage();
    }
  }

  public function createTable($tablename = "nagy_zsolt_users") {
    $sql = "CREATE TABLE IF NOT EXISTS $tablename (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(30) NOT NULL,
      hash VARCHAR(60) NOT NULL,
      email VARCHAR(50),
      reg_date TIMESTAMP,
      login_time TIMESTAMP,
      is_admin BOOLEAN,
      is_active BOOLEAN
    )";
    $this->conn->exec($sql);
  }

  public function listUsers() {
    $stmt = $this->conn->prepare("SELECT * FROM nagy_zsolt_users");
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }

  public function banUser($id, $value) {
    $stmt = $this->conn->prepare("UPDATE nagy_zsolt_users
      SET is_active = :value WHERE nagy_zsolt_users.id=:id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':value', $value);
    $stmt->execute();
  }

  public function deleteUser($id) {
    $stmt = $this->conn->prepare("DELETE FROM nagy_zsolt_users
      WHERE nagy_zsolt_users.id=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
  }

  public function switchAdmin($id, $value) {
    $stmt = $this->conn->prepare("UPDATE nagy_zsolt_users
      SET is_admin = :value WHERE nagy_zsolt_users.id=:id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':value', $value);
    $stmt->execute();
  }

  public function updateLogin($id){
    $stmt = $this->conn->prepare("UPDATE nagy_zsolt_users
      SET login_time = CURRENT_TIMESTAMP WHERE nagy_zsolt_users.id=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
  }


  public function getUser($user) {
    $stmt = $this->conn->prepare("SELECT * FROM nagy_zsolt_users
      WHERE username=:username");
    $stmt->bindParam(':username', $user);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }

  public function createUser($username, $email, $password, $admin) {
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $this->conn->prepare("INSERT INTO nagy_zsolt_users (username,
      hash, email, is_admin, is_active)
      VALUES (:username, :hash, :email, :is_admin, :is_active)");

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':hash', $hash);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':is_admin', $admin);
    $stmt->bindValue(':is_active', 1);
    $stmt->execute();

  }

}