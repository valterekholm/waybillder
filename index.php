<?php
require_once("db.php");
require_once("html.php");
require_once("functions.php");
require_once("sess.php");

error_reporting(E_ALL);

//$BODY_ELEMENT = "body"; //TODO: make user defined
?>
<!DOCTYPE html>
<html>
    <head>
	<link rel="stylesheet" href="../jquery-ui.css">
	<!-- link rel="stylesheet" href="https://ajax.aspnetcdn.com/ajax/jquery.ui/1.12.1/themes/cupertino/jquery-ui.css" -->
	<link rel="stylesheet" href="style.css" type="text/css">
        <script src="../jquery-3.4.1.js"></script>
	<!--script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.1.min.js"></script-->
        <script src="../jquery-ui.js"></script>
	<!--script src="https://ajax.aspnetcdn.com/ajax/jquery.ui/1.12.1/jquery-ui.js"></script-->
        <script src="waybillder.js" type="text/javascript"></script>
        <script src="dialogesBoxes.js" type="text/javascript"></script>
        <script>
      //using jquery ui

            $(function () {
                $("#palette p").draggable({
			helper: "clone",
			start: function(){
				console.log("started");
				$(".waybill").addClass("dropHere");
			},
			stop: function(){
				$(".waybill").removeClass("dropHere");
			}
		});
            });

            $(function () {
                $(".waybill").droppable({
                    drop: function (event, ui) {
                        console.log("Dropped at " + this.id);
                        var draggable = ui.draggable[0]; //varför [0]?
                        console.log(draggable);
                        /*save*/

                        var wbId = getAfter_(this.id);

			var artId = getAfter_(draggable.id);

                        var queryArgs = "add_article_to_waybill=yes&waybill_id=" + wbId + "&article_id=" + artId;

			var amount = null;

			if(draggable.className.indexOf("empty") > -1){
				console.log("Empty tag");
			}
			else{
				//amount = prompt("Ange antal:"); //ange i input istället (som standard number)
				//if(innerHtml.indexOf("'")>-1){	alert("Found quote"); }
			}

                        if (amount != null) {
                            queryArgs = queryArgs + "&amount=" + encodeURI(amount);
                        }

                        postAjax("ajax_operations.php", queryArgs, function (resp) {
                            alert(resp);
                            location.reload();
                        });



                        $(this)
                                .addClass("ui-state-highlight");
                    }
                });
            });

  $( function() {
    $( "#leftSide" ).resizable();
  } );

            function getAfter_(text) {
                var pos_ = text.lastIndexOf("_");
                pos_++;
                return text.substr(pos_);
            }

        </script>
    </head>
    <body>

<?php
$db = new db();
$html = new html();
$sess = new sess("eng");//or "swe"

$lang = $sess->lang();

echo printMenu(array(getWord("Start",$lang)=>"index.php", getWord("Choose waybill", $lang)=>"choose_waybill.php"));

$wb = $sess->getChoosenWaybill();//else null
$customer = getWord("No customer", $lang);
$waybill_id = getWord("None chosen", $lang);
$waybill_date = "";


echo "<div id='getInfo' style='position:fixed; top:0; right:0; width:40px; height:40px; background:green;font-size:40px' onClick='showGuide()'><a href='#'>?</a></div>";



$sql_c = "SELECT * FROM customer";
$res = $db->select_query($sql_c);
$rows_c = $res->fetchAll();


//insert into html_element (id, name, is_empty_tag) values (1, 'table', 0);
$sql = "select * from article";

$res = $db->select_query($sql);
error_log(print_r($res, true));
$rows_a = $res->fetchAll();

if(count($rows_a) == 0){
	$html->p("Du måste registrera en kund. <a href='#'>OK</a>");
}
else{
	foreach ($rows_a as $row) {
	}
}

if($wb > 0){
//get article rows for this waybill
//$sql_ar = "SELECT * FROM article_row ar LEFT JOIN articlerow_article ON ( ar.id = id_article_row ) LEFT JOIN article a ON (id_article = a.id) WHERE id_waybill = $wb";
$sql_ar = "SELECT ar.id, COALESCE(other_price,price) as price, amount, name, name2 " .
"FROM article_row ar LEFT JOIN articlerow_article aa ON ( ar.id = id_article_row ) ".
"LEFT JOIN article a ON (id_article = a.id) WHERE id_waybill =$wb";
error_log($sql_ar);
$res_ar = $db->select_query($sql_ar);
$rows_ar = $res_ar->fetchAll();

if(count($rows_ar) == 0){
	$html->p("Inga rader");
}

$sql_cu = "SELECT w.id, w.created_date, c.legal_name, c.other_name FROM waybill w LEFT JOIN customer c ON ( w.id_customer = c.id ) WHERE w.id = $wb";
$res_cu = $db->select_query($sql_cu);
$row_cu = $res_cu->fetch();//1 row only

$customer = "-";

if(!empty($row_cu)){
	$customer = $row_cu["legal_name"] . " / " . $row_cu["other_name"];
	$waybill_id = $row_cu["id"];
	$waybill_date = $row_cu["created_date"];
}

}

?>
<div id="leftSide" class="full-height wbMenuOffset">
        <div id="u-customer-dialog-form">
            <form>
                <div>
                    <label>C id</label>
                    <input id="cId" readonly>
                </div>
                <div>
                    <label>Name</label>
                    <input id="cName">
                </div>
                <div>
                    <label>Name2</label>
                    <input id="cName2">
                </div>

            </form>
        </div></div><section id="waybill_<?=$wb?>" class="waybill"><div><!--table-->
<div class='caption'><span><?=getWord("Customer", $lang) .": ". $customer?></span><span><?=getWord("Waybill", $lang) . ": #$waybill_id"?></span><span><?="$waybill_date"?></span></div>
<div><div><?=getWord("Article", $lang)?></div><div><?=getWord("Amount", $lang)?></div><div><?=getWord("Price per unit", $lang)?></div><div><?=getWord("£", $lang)?></div><div></div></div>
<!--div id="articleRows"-->
<?php
if(!empty($rows_ar))
foreach($rows_ar as $ar){
	$subtotal = $ar["amount"] * $ar["price"];
	//error_log($subtotal);
	?>
	<div>
	<div><input value="<?=$ar["name"]?>"></input></div><!--td-->
	<div><input value="<?=$ar["amount"]?>"></div><!--td-->
	<div><input id="pricerow_<?=$ar["id"]?>" type="number" step="0.25" value="<?=$ar["price"]?>" onInput="handleInput(this,'update_row_price','price')"></div><!--td-->
	<div><?=$subtotal?></div><!--td-->
	<div><a href='#' id="deleterow_<?=$ar["id"]?>" onclick='deleteRow(this)'><?=getWord("Delete",$lang)?></a></div><!--td-->
	</div>
	<?php
}
?>
</div></section><div id="palette" class="full-height wbMenuOffset">
	<div id="articles">
        <?php
        $html->h3("Articles:");
        foreach ($rows_a as $row) {
            	//$html->p($row["id"] . " " . $row["name"], array("id" => "a_" . $row["id"], "class" => ""));
        }
        ?>

        </div>
            <form id="add_article">
                <fieldset class="form">
                    <legend>Add article</legend>
			<input type="hidden" value="<?=$wb?>" id="add_a_wb">

                    <input type="text" id="a_name" name="a_name" placeholder="name">
                    <input type="text" id="a_name2" name="a_name2" placeholder="إسم second name">
			<input type="number" id="add_a_price" name="add_a_price" placeholder="ثمن price">
                    <input type="button" onClick="addArticle()" value="زد add">
                </fieldset>
            </form>
	    <form id="alter_article">
		<fieldset class="form">
			<legend>Edit article</legend>
			<input type="text" id="edit_a_name" name="edit_a_name" placeholder="article name">
		</fieldset>
	    </form>

	</div>

<?php
?>
</div>

<script>

window.onload = function(){
console.log("load");
var arts = [];
<?php

        foreach ($rows_a as $row) {
            	echo "console.log(".json_encode($row).");\n";
            	echo "var art = ".json_encode($row).";\n";
?>
		arts.push(art);
		var name = art.name;
		var id = "a_" + art.id;
		var elem = document.createElement("p");
		elem.innerHTML = "<div>"+name+"</div>";
		elem.id = id;
		document.querySelector("#articles").appendChild(elem);
<?php
        }
?>
	console.log(arts);
}//onload

	function addArticle(){
		var src_val = document.querySelector("#a_name").value;
		var src_val2 = document.querySelector("#a_name2").value;
		var src_val3 = document.querySelector("#add_a_price").value;
		var wb = document.querySelector("#add_a_wb").value;
		var queryArgs = "add_article=yes&name=" + src_val + "&name2="+src_val2+"&price="+src_val3+"&wb=" + wb;
		postAjax("ajax_operations.php", queryArgs, function (resp) {
			alert(resp);
			location.reload();
		});
	}

	function deleteElementCss(id){ //delete_e_css"]) && isset($_GET["e_css_id"
	}

	function deleteRow(aelem){
		var id = getAfter_(aelem.id);
		console.log(id);
		var queryArgs = "delete_row=yes&row_id=" + id;
		getAjax("ajax_operations.php?"+queryArgs, function (resp) {
			alert(resp);
			location.reload();
		});

	}
        </script>



    </body>
</html>
