<?php

//add article to waybill
if(isset($_POST["add_article_to_waybill"]) && isset($_POST["waybill_id"]) && isset($_POST["article_id"])){

	require_once("db.php");
	require_once("sess.php");

	$waybill_id = $_POST["waybill_id"];
	$article_id = $_POST["article_id"];
	error_log(print_r($_POST, true));
	error_log("add article to waybill, adding with no amount or price");

	//if(isset($_GET[""])){

	$w = (int) preg_replace('/[^0-9]/', '', $waybill_id);
	$a = (int) preg_replace('/[^0-9]/', '', $article_id);

	$db = new db();

	$sql1 = "INSERT INTO article_row (id_waybill) VALUE (?)";
	$values1 = array($w);
	//then assume a new id is made (so use it for id_article_row)

	$sql2 = "INSERT INTO articlerow_article (id_article, id_article_row) VALUES (?,?)";//then later other_price?
	$values2 = array($a);//expect a 2:nd value to be added

	$result = $db->transaction_2_insert_q_use_last_id($sql1,$values1,$sql2,$values2);

	if($result){
		echo "Query worked";
	}
	else{
		http_response_code(500);//Internal Server Error
		echo "Query failed";
	}

	//$row_count = $db->insert_query_get_id("INSERT INTO article_row (id_waybill) VALUE (?)", array($w));


//från exempel, gör om
/*
try{
 
    //We start our transaction.
    $db->conn->beginTransaction();
 
 
    //Query 1: Attempt to insert the payment record into our database.
    $sql = "INSERT INTO payments (user_id, amount) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
            $userId, 
            $paymentAmount,
        )
    );
    
    //Query 2: Attempt to update the user's profile.
    $sql = "UPDATE users SET credit = credit + ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
            $paymentAmount, 
            $userId
        )
    );
    
    //We've got this far without an exception, so commit the changes.
    $pdo->commit();
    
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
    //An exception has occured, which means that one of our database queries
    //failed.
    //Print out the error message.
    echo $e->getMessage();
    //Rollback the transaction.
    $pdo->rollBack();
}
*/

	/*
	if($row_count > 0){
		echo "got 3 args ok from AJAX, row_count: $row_count";
	}
	else{
		http_response_code(500);//Internal Server Error
		echo "Query failed";
	}*/
}

//delete waybill - with on delete cascade set on table articlerow_article - only the row in articlerow_article should be needed to delete...

if(isset($_GET["add_waybill"]) && isset($_GET["customer"])){
	error_log(print_r($_GET), true);
	require_once("db.php");
	$db = new db();
	
	$cust = $_GET["customer"];
	
	$sql = "INSERT INTO waybill (id_customer) VALUE (?)";
	$row_count = $db->insert_query($sql, array($cust));
	if($row_count > 0){
		echo "got 2 args ok from AJAX, row_count: $row_count";
	}
	else{
		http_response_code(500);//Internal Server Error
		echo "Query failed";
	}
	
}

if(isset($_POST["add_article"]) && isset($_POST["name"]) && isset($_POST["wb"])){

	require_once("db.php");
	require_once("sess.php");

	$sess = new sess();

	$add_article = $_POST["add_article"];
	$name = $_POST["name"];

	$values = array($name);
	$keys = array("name");


	$name2 = "";
	if(!empty($_POST["name2"])){
		$name2 = $_POST["name2"];
		$values[] = $name2;
		$keys[] = "name2";
	}

	$price = "";
	if(!empty($_POST["price"])){
		$price = $_POST["price"];
		$values[] = $price;
		$keys[] = "price";
	}

	//$wb = $_POST["wb"];
	error_log("add article, " . print_r($_POST, true));
	$wb = $sess->getChoosenWaybill();
	error_log("add article med waybill id $wb");

	$db = new db();


	//error_log(print_r($keys, true));

	$columns = implode(",", $keys);

	/* Create a string for the parameter placeholders filled to the number of params */
	$place_holders = implode(',', array_fill(0, count($values), '?'));


	//$qm = str_repeat("?,", count($keys));
	//$qm = rtrim($qm, ",");

	$sql = "INSERT INTO article ($columns) VALUES ($place_holders)";
	error_log("'sql': " . $sql);
	error_log(print_r($values, true));

	$row_count = $db->insert_query($sql,$values);

	if($row_count > 0){
		echo "got 3 args ok from AJAX, row_count: $row_count";
	}
	else{
		http_response_code(500);//Internal Server Error
		echo "Query failed";
	}
}
//add element
//is_empty be 'yes' or *

if(isset($_GET["add_element"]) && isset($_GET["e_name"]) && isset($_GET["is_empty"])){

        require_once("db.php");

        $add_element = $_GET["add_element"];
        $e_name = $_GET["e_name"];
        $is_empty = $_GET["is_empty"];
        error_log("add E" . print_r($_GET, true));

	$empty = $is_empty == "yes" ? 1 : 0;
        $db = new db();

        $row_count = $db->insert_query("INSERT INTO html_element (name, is_empty_tag) VALUES (?,?)", array($e_name, $empty));

        if($row_count > 0){
                echo "got 3 args ok from AJAX, row_count: $row_count";
        }
        else{
                http_response_code(500);//Internal Server Error
                echo "Query failed";
        }
}

if(isset($_GET["add_webpage"]) && isset($_GET["name"])){

        require_once("db.php");

        $name = $_GET["name"];
        error_log(print_r($_GET, true));

        $db = new db();

        $row_count = $db->insert_query("INSERT INTO web_page (name) VALUES (?)", array($name));

        if($row_count > 0){
                echo "got 3 args ok from AJAX, row_count: $row_count";
        }
        else{
                http_response_code(500);//Internal Server Error
                echo "Query failed";
        }
}

if(isset($_GET["choose_waybill"]) && isset($_GET["wb_id"])){

	require_once("sess.php");
	$id = $_GET["wb_id"];

	$sess = new sess();
	$sess->setChoosenWaybill($id);
	echo "Have choosen id $id";
}


if(isset($_GET["update_row_price"]) && isset($_GET["row_id"]) && isset($_GET["price"])){
	error_log("update_node");

	require_once("db.php");

	$row_id = $_GET["row_id"];
	$price = $_GET["price"];

	error_log("update_row_price, " . print_r($_GET, true));

	//see if the new price is the same as the currently saved
	$sql = "SELECT other_price, price FROM articlerow_article ara ".
	"JOIN article a ".
	"ON ( id_article = a.id ) WHERE id_article_row = $row_id";

	$db = new db();
	$stmt = $db->select_query($sql);
	error_log("stmt: " . print_r($stmt, true));
	$row = $stmt->fetch();
	$op = $row["other_price"];
	$p = $row["price"];
	error_log($op . " " . $p);//"current_price"
	if($op == $price){
		error_log("same other price");
		echo "same other price";
		exit;
	}
	else if($p == $price){
		//wants original price
		$sql = "UPDATE articlerow_article SET other_price = NULL WHERE id_article_row = ?";
		$values = array($row_id);
	}
	else{
		$sql = "UPDATE articlerow_article SET other_price = ? WHERE id_article_row = ?";
		$values = array($price, $row_id);
	}
	$row_count = $db->update_query($sql, $values, false);

        if($row_count > 0){
                echo "got 3 args ok from AJAX, row_count: $row_count";
        }
        else{
                http_response_code(500);//Internal Server Error
                echo "Query failed";
        }
}

if(isset($_POST["update_node"]) && isset($_POST["node_id"]) && isset($_POST["element_id"]) && isset($_POST["inner_html"]) && isset($_POST["parent_id"])){
	error_log("update_node POST");

	require_once("db.php");

	$node_id = $_POST["node_id"];
	$element_id = $_POST["element_id"];
	$parent_id = $_POST["parent_id"];
	$inner_html = $_POST["inner_html"];

	error_log("update node POST, " . print_r($_POST, true));

	$sql = "UPDATE nodes SET element_id = ?, parent_node_id = ?, inner_html = ?  WHERE id = ?";
	$values = array($element_id, $parent_id, $inner_html, $node_id);
	$db = new db();

	$row_count = $db->update_query($sql, $values, false);

        if($row_count > 0){
                echo "got 4 args ok from AJAX, row_count: $row_count";
        }
        else{
                http_response_code(500);//Internal Server Error
                echo "Query failed";
        }
}
if(isset($_POST["update_element_css"]) && isset($_POST["e_name"]) && isset($_POST["wep"]) && isset($_POST["css"])){
	error_log("update_element_css POST");

	require_once("db.php");

	$e_name = $_POST["e_name"];
	$wep = $_POST["wep"];
	$css = $_POST["css"];

	error_log(print_r($_POST, true));
	//update via join match
	//UPDATE element_css c INNER JOIN html_element e ON c.name = e.id SET css = '' WHERE e.name = 'h1' AND web_page_id = 5;
	$sql = "UPDATE element_css c INNER JOIN html_element e ON c.name = e.id SET css = ? WHERE e.name = ? AND web_page_id = ?";
	$values = array($css, $e_name, $wep);
	$db = new db();

	$row_count = $db->update_query($sql, $values, false);

	error_log("row_count: $row_count");

        if($row_count > 0){
                echo "got 4 args ok from AJAX, row_count: $row_count";
        }
        else{
                http_response_code(500);//Internal Server Error
                echo "Query failed";
        }
}

if(isset($_POST["add_element_css"]) && isset($_POST["element"]) && isset($_POST["wep"]) && isset($_POST["css"])){
	error_log("add_element_css POST");

	require_once("db.php");

	$element = $_POST["element"];
	$wep = $_POST["wep"];
	$css = $_POST["css"];

	error_log(print_r($_POST, true));
	$sql = "INSERT INTO element_css (name, css, web_page_id) VALUES (?,?,?)";
	$values = array($element, $css, $wep);
	$db = new db();

	try{
		$row_count = $db->insert_query($sql, $values, false);
	}
	catch(Exception $e){//only if place is taken
		//error_log("caught: $e");
		echo "The element-css is allready in this web-page";
		//http_response_code(403);
		exit;
	}

	error_log("row_count: $row_count");

        if($row_count > 0){
                echo "got 4 args ok from AJAX, row_count: $row_count";
        }
        else{
                http_response_code(500);//Internal Server Error
                echo "Query failed"; //text wont reach to front-end
        }
}
if(isset($_GET["delete_e_css"]) && isset($_GET["e_css_id"])){
        error_log("delete_element_css----------");
        require_once("db.php");
        $db = new db();
	$e_css_id = $_GET["e_css_id"];
	$sql = "DELETE FROM element_css WHERE id = ?";
	$values = array($e_css_id);

        $row_count = $db->update_query($sql, $values);

        if($row_count > 0){
                echo "got 2 args ok from AJAX, row_count: $row_count";
        }
        else{
                http_response_code(500);//Internal Server Error
                echo "Query failed";
        }

}

//used
if(isset($_GET["delete_row"]) && isset($_GET["row_id"])){
        error_log("delete_row-------------------------------------");

        require_once("db.php");
	$db = new db();

        $row_id = $_GET["row_id"];

        //error_log(print_r($_GET, true));

        $sql = "DELETE FROM article_row WHERE id = $row_id";
        $values = array($row_id);

        $row_count = $db->update_query($sql, $values);

        if($row_count > 0){
		echo "got 2 args ok from AJAX, row_count: $row_count";
        }
        else{
 		http_response_code(500);//Internal Server Error
 		echo "Query failed";
        }
}

//step up amongst siblings
if(isset($_GET["step_up"]) && isset($_GET["node_id"])){
	error_log("step up");

	require_once("db.php");

        $node_id = $_GET["node_id"];

	if(!is_numeric($node_id)){
		echo "Non numeric id";
		exit;
	}
	$db = new db();

	//get parent node
	$sql = "SELECT parent_node_id FROM nodes WHERE id = $node_id";
	$stmt_ = $db->select_query($sql);

	$row_ = $stmt_->fetch();
	$parent_node_id = $row_["parent_node_id"];

	//see if has siblings
	$sql = "SELECT * FROM nodes WHERE parent_node_id = $parent_node_id";
	$stmt = $db->select_query($sql);

	if($stmt && $stmt->rowCount() > 1){
		$rows = $stmt->fetchAll();
		if($rows[0]["id"] != $node_id){
			$lastId = 0;
			foreach($rows as $row){
				if($row["id"] == $node_id){
					$sql = "update nodes t1 inner join nodes t2 on (t1.id, t2.id) in (($lastId,$node_id),($node_id,$lastId)) set t1.element_id = t2.element_id, t1.inner_html = t2.inner_html";
					error_log($sql);
					$row_count = $db->update_query($sql);
					if($row_count>0){
						echo "Affected_rows: $row_count";
						$sql2 = "UPDATE nodes SET parent_node_id = (CASE WHEN parent_node_id = $node_id THEN $lastId WHEN parent_node_id = $lastId THEN $node_id END) WHERE parent_node_id IN($lastId, $node_id)";
						error_log($sql2);
						$stmt2 = $db->update_query($sql2);
					}
					else{
						echo "Didn't work";
					}
					break;
				}
				$lastId = $row["id"];
			}
		}
		else echo "Element is first";
		exit;
	}
	else{
		echo "Element is alone";
		exit;
	}
}

if(isset($_GET["clear_choosen_waybill"])){
	require_once("sess.php");

	$sess = new sess();
	$sess->clearChoosenWaybill();
	echo "Choosen waybill: " . $sess->getChoosenWaybill();
}

/*
if(isset($_GET["add_node"]) && isset($_GET["parent_node_id"]) && isset($_GET["child_element_id"])){

        require_once("db.php");

        $add_node = $_GET["add_node"];
        $parent_node_id = $_GET["parent_node_id"];
        $child_element_id = $_GET["child_element_id"];
        error_log(print_r($_GET, true));

        $p = (int) preg_replace('/[^0-9]/', '', $parent_node_id);
        $c = (int) preg_replace('/[^0-9]/', '', $child_element_id);

        $db = new db();

        $row_count = $db->insert_query("INSERT INTO nodes (element_id, parent_node_id, inner_html) VALUES (?,?,?)", array($c, $p, ""));

        if($row_count > 0){
                echo "got 3 args ok from AJAX, row_count: $row_count";
        }
        else{   
                http_response_code(500);//Internal Server Error
                echo "Query failed";
        }
}
*/
