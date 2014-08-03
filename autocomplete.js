var Autocomplete = (function() {
    var a = {};
    var calcLength = document.createElement("div");
    calcLength.style.position = "absolute";
    calcLength.style.width = "auto";
    calcLength.style.height = "auto";
    calcLength.style.visibility = "hidden";
    
    a.getSuggestion = function(input, suggestion) {
        var xmlhttp = new XMLHttpRequest();
        
        var value = input.value;
        if (value.lastIndexOf(",") != -1) {
            value = value.substring(value.lastIndexOf(",")+1).trim();
        }
        
        if (value.length>0) {

            //When a response is found, send it off to the handler
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4) {
                    if(xmlhttp.status == 200 && xmlhttp.responseText.trim().length>0){

                        //get position to show suggestions at
                        calcLength.style.fontSize = input.style.fontSize;
                        calcLength.style.fontFamily = input.style.fontFamily;
                        calcLength.innerHTML = input.value;
                        suggestion.style.left = "" + (input.parentElement.offsetWidth - input.offsetWidth + calcLength.offsetWidth + 1) + "px";
                        suggestion.style.display = "block";
                        suggestion.innerHTML = xmlhttp.responseText;
                    } else {
                        suggestion.style.display = "none";
                    }
                }
            };

            var params="tag=" + value + "&prereqs=" + (prereqs?1:0);

            xmlhttp.open("POST", "autocomplete.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send(params);
            
        } else {
            suggestion.style.display = "none";
        }
    };
    
    a.addInput = function(input, prereqs) {
        var timer;
        var suggestion = document.createElement("div");
        suggestion.className = "suggestion";
        suggestion.style.display="none";
        input.parentElement.appendChild(suggestion);
        
        input.addEventListener("keydown", function(evt) {
            clearInterval(timer);
            timer = setTimeout(function() {
                a.getSuggestion(input, suggestion);
            }, 75);
            
            if (evt.keyCode == 13 || evt.keyCode == 9) {
                if (suggestion.style.display == "block" && suggestion.innerHTML.length>0) {
                    if (input.value.lastIndexOf(",") != -1) {
                        input.value = input.value.substring(0, input.value.lastIndexOf(",")+1) + " " + suggestion.innerHTML + ", ";
                    } else {
                        input.value = suggestion.innerHTML + ", ";
                    }
                }
                if(evt.preventDefault) {
                    evt.preventDefault();
                }
                return false;
            }
        });
    }
    
    window.addEventListener("load", function() {
        document.body.appendChild(calcLength);
    });
    
    return a;
}());