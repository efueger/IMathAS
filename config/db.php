<?php
if ($_SERVER['RDS_HOSTNAME'] != '' ) {
  $dbhost = $_SERVER['RDS_HOSTNAME'];
  $dbport = $_SERVER['RDS_PORT'];
  $dbname = $_SERVER['RDS_DB_NAME'];

  $dsn = "mysql:host={$dbhost};port={$dbport};dbname={$dbname}";
  $username = $_SERVER['RDS_USERNAME'];
  $password = $_SERVER['RDS_PASSWORD'];
} else {
  $dbhost = 'localhost';
  $dbname = 'imathasdb';

  $dsn = "mysql:host={$dbhost};dbname={$dbname}";
  $username = 'root';
  $password = 'root';
}

return [
    'class' => 'yii\db\Connection',
    'dsn' => $dsn,
    'username' => $username,
    'password' => $password,
    'charset' => 'utf8',
];
