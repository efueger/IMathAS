function toggleshow(bnum) {
    var node = document.getElementById('block' + bnum);
    var butn = document.getElementById('butb' + bnum);
    if (node.className == 'forumgrp') {
        node.className = 'hidden';
        butn.src = imasroot + '/img/expand.gif';
    } else {
        node.className = 'forumgrp';
        butn.src = imasroot + '/img/collapse.gif';
    }
}
function toggleitem(inum) {
    var node = document.getElementById('item' + inum);
    var butn = document.getElementById('buti' + inum);
    if (node.className == 'blockitems') {
        node.className = 'hidden';
        butn.value = 'Show';
    } else {
        node.className = 'blockitems';
        butn.value = 'Hide';
    }
}
function expandall() {
    var messageCount =  $( "#messageCount" ).val();
    for (var i = 0; i < messageCount; i++) {
        var node = document.getElementById('block' + i);
        var butn = document.getElementById('butb' + i);
        node.className = 'forumgrp';
        butn.src = imasroot + '/img/collapse.gif';
    }
}
function collapseall() {
    var messageCount =  $( "#messageCount" ).val();
    for (var i = 0; i <= messageCount; i++) {
        var node = document.getElementById('block' + i);
        var butn = document.getElementById('butb' + i);
        node.className = 'hidden';
        butn.src = imasroot + '/img/expand.gif';
    }
}
function showall() {
    var messageCount =  $( "#messageCount" ).val();
    for (var i = 0; i <= messageCount; i++) {
        var node = document.getElementById('item' + i);
        var buti = document.getElementById('buti' + i);
        node.className = "blockitems";
        buti.value = "Hide";
    }
}
function hideall() {
    var messageCount =  $( "#messageCount" ).val();
    for (var i = 0; i <= messageCount; i++) {
        var node = document.getElementById('item' + i);
        var buti = document.getElementById('buti' + i);
        node.className = "hidden";
        buti.value = "Show";
    }
}