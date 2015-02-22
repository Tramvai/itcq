<?php
$host = 'localhost';
$database = 'itcq';
$username = 'root';
$password = 'root';

function connectToDatabase($host,$database,$username,$password) {
    // Learned from http://www.w3schools.com/php/php_mysql_connect.asp
    try {
        $connection = new PDO("mysql: host=$host; dbname=$database; charset=utf8", $username, $password);

        // Set the PDO error mode to exception: http://php.net/manual/en/pdo.error-handling.php
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $connection;
    } catch (PDOException $e) {
        returnError($e->getMessage());
    }
}
?>
