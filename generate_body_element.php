<?php

require_once("db.php");
require_once("html.php");


$db = new db();
$html = new html();

//insert into html_element (id, name, is_empty_tag) values (1, 'table', 0);
$sql = "select * from html_element";

$res = $db->select_query($sql);
error_log(print_r($res, true));
$rows = $res->fetchAll();

$sql2 = "INSERT INTO html_element (name) VALUES ('body')";

$found_body = false;

//kolla om redan finns

foreach ($rows as $row) {
       if($row["name"] == "body"){
               $found_body = true;
       }
}
if(!$found_body){
	$res2 = $db->select_query($sql2);
	if($res2) $html->p("OK");
}

?>

<p>
<a href="index.php">Återgå</a>
</p>




