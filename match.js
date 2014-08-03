var current = 0;

window.onload = function() {
    var nextButtons = document.getElementsByClassName("secondary");
    var features = document.getElementsByClassName("feature");
    for (var i=0; i<nextButtons.length; i++) {
        nextButtons[i].onclick = function() {
            features[current].className = "feature hidden";
            features[current+1].className = "feature";
            current++;
        };
    }
};
