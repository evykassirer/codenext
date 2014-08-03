var colours = ["#16a085", "#4596DE", "#1abc9c", "#43459d", "#3498db", "#8e44ad", "#c0392b", "#d35400", "#f39c12"];
var randomButton;
var flagButton;
var $_GET = {};
var xmlhttp;
if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
} else {// code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
}

function loadNew(joke_id) {
	xmlhttp.onreadystatechange = function() {
  		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
    		document.getElementById("dynamic").innerHTML=xmlhttp.responseText;
    		init();
    		changeBackground();
    		if (!joke_id) window.history.pushState({"joke_id":document.getElementById("jokeId").value},"", "?joke="+document.getElementById("jokeId").value);
    	}
  	};
  	var url="";
  	if (joke_id) {
  		url="getjoke.php?joke="+joke_id;
  	} else {
  		url="getjoke.php?last="+document.getElementById("jokeId").value;
  	}
	xmlhttp.open("GET",url,true);
	xmlhttp.send();
}

function loadFlag() {
	var continueMsg = confirm("Remember, flagging is intented only for things that aren't jokes, are offensive, or are spam. Are you sure you want to flag this?");
    if (continueMsg==true) {
		xmlhttp.onreadystatechange = function() {
	  		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
	    		alert(xmlhttp.responseText);
	    	}
	  	};
		xmlhttp.open("GET","flag.php?joke_id="+document.getElementById("jokeId").value,true);
		xmlhttp.send();
	}
}

function changeBackground() {
	document.getElementsByTagName("body")[0].style.background = colours[Math.floor(Math.random()*colours.length)];
}

function reload() {
	document.getElementsByTagName("body")[0].style.background = "#444444";
	loadNew();
}

function init() {
	randomButton = document.getElementById("randomButton");
	if (randomButton) {
		randomButton.onclick = reload;
	}
	flagButton = document.getElementById("flagButton");
	if (flagButton) {
		flagButton.onclick = loadFlag;
	}
    var args = location.search.substr(1).split(/&/);
    for (var i=0; i<args.length; ++i) {
        var tmp = args[i].split(/=/);
        if (tmp[0] != "") {
            $_GET[decodeURIComponent(tmp[0])] = decodeURIComponent(tmp.slice(1).join("").replace("+", " "));
        }
    }
}

window.onload = function() {
	changeBackground();
	init();
	document.getElementById("addJoke").disabled=true;
	window.onkeyup = function () {
		var textareas = document.getElementsByTagName("textarea");
		var empty=false;
		for (var i=0; i<textareas.length; i++) {
			if (textareas[i].value.replace(/\s/g, '')=="") {
				empty=true;
				break;
			}
		}
		if (empty) {
			document.getElementById("addJoke").disabled=true;
		} else {
			document.getElementById("addJoke").disabled=false;
		}
	}
	window.onpopstate = function (event) {
		if (event.state) {
			loadNew(event.state.joke_id);
		}
	}
};