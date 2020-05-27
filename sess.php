<?php
class sess{

private $lang;

function __construct($lang = "swe"){
	session_start();
	$this->lang = $_SESSION["lang"] = $lang;
}

public function getChoosenWaybill(){
	if(!empty($_SESSION["wb"])){
		error_log("!empty session wb");
		return $_SESSION["wb"];//an id (int)
	}
	else{
		error_log("empty session wb");
		return null;
	}
}

public function setChoosenWaybill($id){
	error_log("setChoosenWaybill($id)");
	$_SESSION["wb"] = $id;
}

public function clearChoosenWaybill(){
	error_log("clearChoosenWaybill");
	$_SESSION["wb"] = null;
}

public function lang(){
	return $this->lang;
}

}
?>
