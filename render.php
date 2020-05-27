<?php
require_once("db.php");
require_once("html.php");
require_once("sess.php");
include "functions.php";


//TODO: connect to web_page_id


$db = new db();
$html = new html();
$sess = new sess();

$wep = $sess->getChoosenWebpage();
?>
<!DOCTYPE html>
<html>
<head>
<?php

$sql = "select c.*, e.name element_name from element_css c left join html_element e on (c.name = e.id)";
$res = $db->select_query($sql);
$rows = $res->fetchAll();
if(count($rows)>0){
echo "<style>";
foreach($rows as $row){


echo $row["element_name"] . "{ " . $row["css"] . " }";
//TODO: connect to web_page_id

}
echo "</style>";
}//if

?>
</head>


<?php
//get base node
$sql = "SELECT n.id, n.element_id, n.parent_node_id, e.name FROM nodes n JOIN html_element e ON (element_id = e.id) WHERE ISNULL(parent_node_id) AND web_page_id = $wep";

$res = $db->select_query($sql);
if($res->rowCount()==0){
	echo "Error: could not find base-node, a node whith parent=null";
}
else{
	$row = $res->fetch();//one row only

	$found = array();

	$base_id = $row["id"];
	//echo $base_id;

	$found[] = $base_id;

	printLevel($base_id, 0, true);
}
?>
<!-- a href="index.php">Start</a-->
</html>
