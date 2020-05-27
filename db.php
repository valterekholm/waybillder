<?php

class db {

    private $user;
    private $pass;
    private $dbname;
    private $conn;

    function __construct() {
        error_log("construct");
        $this->user = "waybillder";
        $this->pass = "6789";
	$this->dbname = "waybillder";
        $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
        $this->conn = new PDO('mysql:host=localhost;dbname='.$this->dbname, $this->user, $this->pass, $options);
	$this->conn->exec("set names utf8"); //PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"

    }

    function select_query($sql, $force_lower_case = true) {
        error_log("select_query, med $sql");
	if($force_lower_case){
		error_log("true");
		$sql = strtolower($sql);
	}
	else error_log("false");

	$stmt = $this->conn->prepare($sql);
        $stmt = $this->conn->query($sql);

        return $stmt;
    }

    //sql - a query with positional placeholders
    //values an indexed array
    function insert_query($sql, $values = array(), $force_lower_case = true){
	error_log("insert_query($sql)");
	error_log("values: " . print_r($values, true));
	if($force_lower_case){
		error_log("true");
		$sql = strtolower($sql);
	}
	else error_log("false");


	$stmt = $this->conn->prepare($sql);
	$res = $stmt->execute($values);
	error_log("stmt: " . print_r($stmt, true));
	error_log("res: " . print_r($res, true));
	$error = $stmt->errorInfo()[0];
	$detail = $stmt->errorInfo()[1];
	if($error == 23000 && $detail == 1062){
		//error_log("Place is taken");
		throw new DomainException('Dublicate error');
	}

	//error_log("errorInfo: " . print_r($stmt->errorInfo(), true));
	return $stmt->rowCount();
    }

    function insert_query_get_id($sql, $values = array(), $force_lower_case = true){
	error_log("insert_query_get_id($sql)");
	error_log("values: " . print_r($values, true));
	if($force_lower_case){
		error_log("true");
		$sql = strtolower($sql);
	}
	else error_log("false");


	$stmt = $this->conn->prepare($sql);
	$res = $stmt->execute($values);
	error_log("stmt: " . print_r($stmt, true));
	error_log("res: " . print_r($res, true));
	$error = $stmt->errorInfo()[0];
	$detail = $stmt->errorInfo()[1];
	if($error == 23000 && $detail == 1062){
		//error_log("Place is taken");
		throw new DomainException('Dublicate error');
	}

	//error_log("errorInfo: " . print_r($stmt->errorInfo(), true));
	return $this->conn->lastInsertId();
    }

    function update_query($sql, $values = array(), $force_lower_case = true){
	error_log("update_query($sql)");
	error_log("values: " . print_r($values, true));
	if($force_lower_case){
		error_log("true");
		$sql = strtolower($sql);
	}
	else error_log("false");

        $stmt = $this->conn->prepare($sql);
        $res = $stmt->execute($values);
        error_log("stmt: " . print_r($stmt, true));
        error_log("res: " . print_r($res, true));
        return $stmt->rowCount();
    }


function array_to_pdo_params($array) {
  $temp = array();
  foreach (array_keys($array) as $name) {
    $temp[] = "`$name` = ?";
  }
  return implode(', ', $temp);
}

	//sql1 (and sql2) are assumed to be insert queries, and sql2 using the last insert id of sql1
	//sql2 and values2 shall be a query like "insert into tbl (name, some_id) values (?,?)" but the last value should be generated from sql1 (insert) query
	//so the values2 should not contain the last value.
	function transaction_2_insert_q_use_last_id($sql1, $values1 = array(), $sql2, $values2 = array()){
		//todo: use PDO::lastInsertId from first query in 2:nd
		//$last_insert_id = $this->insert_query_get_id($sql1, $values1, false);
		error_log("transaction_2_insert_q_use_last_id med $sql1 och $sql2");
		error_log(print_r($values1, true));
		error_log(print_r($values2, true));
		try{
		$this->conn->beginTransaction();
		$stmt1 = $this->conn->prepare($sql1);
		$res1 = $stmt1->execute($values1);
		$last_insert_id = $this->conn->lastInsertId();
		
		//a "where" will be added in query
		//it will use last id
		$values2[] = $last_insert_id;
		error_log("Adding to values2 : $last_insert_id");
		
		$stmt2 = $this->conn->prepare($sql2);
		$res2 = $stmt2->execute($values2);

		
		$this->conn->commit();
		}
		catch(PDOException $e){
			//An exception has occured, which means that one of our database queries
			//failed.
			//Print out the error message.
			echo $e->getMessage();
			//Rollback the transaction.
			$this->conn->rollBack();
			return false;
		}
		return true;
	}

}
?>
