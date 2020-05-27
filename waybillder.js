antiBumpDelay = 200;
askResize = true;
sendTimeout = 3000;
sendDelay = 700;//wait before sending (with ajax)

function getAjax(url, success) {
	console.log("getAjax med " + url + " och " + success);
	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	xhr.open('GET', url);
	xhr.onreadystatechange = function() {
		if (xhr.readyState>3 && xhr.status==200) success(xhr.responseText);
		else if (xhr.readyState>3 && xhr.status>=500){ alert("Server error (" + xhr.responseText + ")") }
		else if (xhr.readyState>3 && xhr.status>=400){ alert("Client error (" + xhr.responseText + ")") }
	};
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhr.send();
	return xhr;
}


function postAjax(url, data, success) {
	console.log("postAjax till url " + url);
	var params = typeof data == 'string' ? data : Object.keys(data).map(
		function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
	).join('&');

	console.log(params);

	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
	xhr.open('POST', url, true);
	xhr.onreadystatechange = function() {
		if (xhr.readyState>3 && xhr.status==200) {
			console.log("postAjax succeeded");
			success(xhr.responseText);
		}
		else if (xhr.status>=500) alert("Server error");
		else if (xhr.status>=400) alert("Client error");
	};

	/*xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');*/
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.send(params);
	return xhr;
}

function showGuide(){
var infoDiv = document.createElement("div");
infoDiv.style = "width:100%;height:33%;background:rgba(230,250,240,.8); position:fixed;bottom:0;left:0; border: 0 solid blue; border-top-width:5px; text-align: center;";
document.body.appendChild(infoDiv);

var tbl = document.createElement("table");
tbl.className = "infotable";
tbl.style="font-size:40px;text-align:center;width:100%;font-family:sans;line-height:2em;";
tbl.innerHTML = "<thead></thead><tbody><tr><td style='width:15%'></td><td>B</td><td style='width:15%'>A</td></tr></tbody>";

infoDiv.appendChild(tbl);

var info = document.createElement("tfoot");
var infoCell = info.insertRow().insertCell(0);
infoCell.style="margin:1%; border: 2px solid gray;";
infoCell.colSpan=3;
infoCell.innerHTML = "Drag an article from A to B";

tbl.appendChild(info);

var dismiss = document.createElement("button");
dismiss.innerHTML = "Dismiss";
dismiss.addEventListener("click", function(){
	var div = this.parentElement;
	var gpar = div.parentElement;
	gpar.removeChild(div);
});
infoDiv.appendChild(dismiss);
}

window.onresize = function(){
	if(askResize){
	setTimeout(function(){
		setTimeout(function(){askResize = true},2000);
		if(confirm("Window resized, please reload page")){ location.reload(); }
	},antiBumpDelay);
	}
	askResize = false;
}


function getAfter_(text) {
	var pos_ = text.lastIndexOf("_");
	pos_++;
	return text.substr(pos_);
}

function handleInput(elem, action, parameter){
elem.dataset.sendme=0;

clearTimeout(sendTimeout);

sendTimeout = setTimeout(function(){setSendme(elem,action,parameter)},sendDelay);

}

function setSendme(e,a,p){
//e.dataset.sendme=1;
var url="ajax_operations.php";

var fullUrl = url + "?" + a + "=yes&row_id="+getAfter_(e.id)+"&" + p + "=" + e.value;
console.log(fullUrl);

getAjax(fullUrl, function (resp) {
alert(resp);
location.reload();
});

}
