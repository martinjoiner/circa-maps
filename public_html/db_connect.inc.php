<?php 

$dotenv = new Dotenv\Dotenv(  $_SERVER['DOCUMENT_ROOT'] . '/../' );
$dotenv->load();

$dotenv->required(['DB_HOST', 'DB_USERNAME', 'DB_PASSWORD', 'DB_DATABASE']);

$db_host = getenv('DB_HOST');
$db_username = getenv('DB_USERNAME');
$db_password = getenv('DB_PASSWORD');
$db_database = getenv('DB_DATABASE');

try {
	$db = new \PDO('mysql:host=' . $db_host . ';dbname=' . $db_database . ';charset=utf8', $db_username, $db_password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage() . "\n";
}
