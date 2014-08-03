var current = 0;

window.onload = function() {
    var nextButtons = document.getElementsByClassName("secondary");
    var reviewButtons = document.getElementsByClassName("review");
    var features = document.getElementsByClassName("feature");
    for (var i=0; i<nextButtons.length; i++) {
        nextButtons[i].onclick = function() {
            features[current].className = "feature hidden";
            features[current+1].className = "feature";
            current++;
        };
    }
    for (var i=0; i<reviewButtons.length; i++) {
        reviewButtons[i].onclick = function(evt) {
            evt.target.parentElement.parentElement.getElementsByTagName("form")[0].className="";
            evt.target.parentElement.className="row hidden";
        };
    }
};
