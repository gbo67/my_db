<?php 
require_once "php/db_con.php";
require_once "php/constants.php";

$db = new MySQL();
$db->connectDB();

$ladies = $db->getMembers("sex", "female");
echo $ladies[0];

?>