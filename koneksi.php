<?php
//-- database configurations
$dbhost='localhost';
$dbuser='root';
$dbpass='';
$dbname='db_expert1111111';
//-- database connections
$db=new mysqli($dbhost,$dbuser,$dbpass,$dbname);
//-- halt and show error message if connection fail
if ($db->connect_error) {
	die('Connect Error ('.$db->connect_errno.')'.$db->connect_error);
}
