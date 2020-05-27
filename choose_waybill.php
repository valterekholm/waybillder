<?php

require_once("db.php");
require_once("html.php");
require_once("sess.php");
require_once("functions.php");

$db = new db();
$html = new html();
$sess = new sess();
$lang = $sess->lang();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
        <link rel="stylesheet" href="https://ajax.aspnetcdn.com/ajax/jquery.ui/1.12.1/themes/cupertino/jquery-ui.css">
        <link rel="stylesheet" href="Treant.css">
        <link rel="stylesheet" href="style.css">
        <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.1.min.js"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.ui/1.12.1/jquery-ui.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.js" integrity="sha256-lUVl8EMDN2PU0T2mPMN9jzyxkyOwFic2Y1HJfT5lq8I=" crossorigin="anonymous"></script>
        <script src="waybillder.js"></script>
        <script src="dialogesBoxes.js"></script>

</head>
<body>

<?php

printMenu();

$sql = "SELECT w.id, w.created_date, c.legal_name, c.other_name FROM waybill w LEFT JOIN customer c ON ( id_customer = c.id )";
$sql2 = "SELECT * FROM customer";

$res = $db->select_query($sql);
$res2 = $db->select_query($sql2);

?>

<div class="content">

<article>
<?php

$html->p(getWord("Choosen waybill", $lang) . ": [" . $sess->getChoosenWaybill() . "]");

if($res){

$rows = $res->fetchAll();

if(empty($rows)){
	$html->p(getWord("No saved waybills", $lang));
}
else{
$html->h1(getWord("Saved waybills", $lang));
echo "<table class='cleartable'>";
echo "<tr><th>".getWord("Number", $lang)."</th><th>".getWord("Customer", $lang)."</th><th>".getWord("Date", $lang)."</th><th>".getWord("Choose", $lang)."</th></tr>";
foreach($rows as $row){

$html->tr("<td>".$row["id"]."</td><td>".$row["legal_name"]." / ".$row["other_name"]."</td><td>".$row["created_date"]."</td><td><button onclick='chooseWb(".$row["id"].")'>".getWord("Choose", $lang)."</button></td>");


}
echo "</table>";



}


}
else{
$html->p("No result from query");
}

?>
</article>
</div>

<form>
<fieldset>
<legend><?=getWord("Create waybill", $lang)?></legend>
<label for="customer"><?=getWord("Customer", $lang)?></label>


<select id="customer">
<?php
if($res2){
$rows2 = $res2->fetchAll();
foreach($rows2 as $r){
	?>
	<option value="<?=$r["id"]?>"><?=$r["legal_name"]." / ".$r["other_name"]?></option>
	<?php
}
}
?>

</select>

<input value="'<?=date("Y-m-d H:i:s")?>'" readonly>

<!--input type="text" id="pagename"-->
<input type="button" value="save" onclick="savewaybill()">
</fieldset>
</form>

<button onClick="clearWb()">Clear choice</button>

<script type="text/javascript">


function savewaybill(){
var customer = document.querySelector("#customer");
getAjax("ajax_operations.php?add_waybill=yes&customer=" + encodeURI(customer.value), function(result){location.reload()});
}

function chooseWb(id){
getAjax("ajax_operations.php?choose_waybill=yes&wb_id=" + id, function(result){alert(result);location.reload();});
}

function clearWb(){
getAjax("ajax_operations.php?clear_choosen_waybill=yes", function(result){alert(result);location.reload();});
}

</script>
</body>
</html>
