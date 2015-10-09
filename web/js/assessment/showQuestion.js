/**
 * Created by Tudip on 29-09-2015.
 */

function toggleintroshow(n) {
    var link = document.getElementById("introtoggle"+n);
    var content = document.getElementById("intropiece"+n);
    if (link.innerHTML.match("Hide")) {
        link.innerHTML = link.innerHTML.replace("Hide","Show");
        content.style.display = "none";
    } else {
        link.innerHTML = link.innerHTML.replace("Show","Hide");
        content.style.display = "block";
    }
}
function togglemainintroshow(el) {
    if ($("#intro").hasClass("hidden")) {
        $(el).html("Hide Intro/Instructions");
        $("#intro").removeClass("hidden").addClass("intro");
    } else {
        $("#intro").addClass("hidden");
        $(el).html("Show Intro/Instructions");
    }
}
