var Autocomplete = (function() {
    var a = {};
    
    a.addInput = function(input, prereqs) {
        var timer;
        input.addEventListener("keyup", function(evt) {
            timer = setTimeout(200, function() {
                var xmlhttp = new XMLHttpRequest();

                //When a response is found, send it off to the handler
                xmlhttp.onreadystatechange = function() {
                    console.log(xmlhttp.responseText);
                    if (xmlhttp.readyState == 4 ) {
                        if(xmlhttp.status == 200){
                            //console.log(xmlhttp.responseText);
                        }
                    }
                };

                var params="tag=" + input.value + "&prereqs=" + (prereqs?1:0);

                xmlhttp.open("POST", "autocomplete.php", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send(params);
            });
        });
    }
    
    return a;
}());