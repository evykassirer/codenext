var Filter = (function() {
    var f = {};
    
    f.filterTags = function(input, results, tags) {
        var value = input.value.replace(" ", "_").toLowerCase();
        for (var i=0; i<tags.length; i++) {
            if (value.length==0 || tags[i].getElementsByTagName("input")[0].checked || tags[i].id.toLowerCase().indexOf(value)==0) {
                tags[i].classList.remove("closed");
            } else {
                tags[i].classList.add("closed");
            }
        }
    };
    
    f.setInput = function(input, results, exclusive) {
        var tags = results.getElementsByClassName("tag");
        var timer;
        var startFilter = function() {
            clearInterval(timer);
            timer = setTimeout(function() {
                f.filterTags(input, results, tags);
            }, 75);
        };
        
        input.addEventListener("keyup", startFilter);
        
        for (var i=0; i<tags.length; i++) {
            tags[i].getElementsByTagName("label")[0].addEventListener("click", function(evt) {
                if (exclusive || evt.target.parentElement.id=="Something_new" || evt.target.parentElement.id=="Nothing_yet") {
                    for (var j=1; j<tags.length; j++) {
                        if (tags[j]==evt.target.parentElement) continue;
                        tags[j].getElementsByTagName("input")[0].checked=false;
                    }
                }
                if (evt.target.parentElement.id!="Something_new" && evt.target.parentElement.id!="Nothing_yet") tags[0].getElementsByTagName("input")[0].checked=false;
            });
        }
    };
    
    return f;
}());

