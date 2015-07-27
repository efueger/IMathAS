$(document).ready(function () {
    initEditor();
    $("#msg-btn").click(function () {
        tinyMCE.triggerSave();
        var cid = $(".send-msg").val();
        var receiver = $(".msg-sender").val();
        var sender = $(".msg-receiver").val();
        var subject = $(".subject").val();
        var body = $("#message").val();
        var parentId = $(".parent-id").val();
        var baseId = $(".base-id").val();
        var isReplied = $(".is-replied").val();
        var messageDetails = {cid: cid, sender: sender, receiver: receiver, subject: subject, body: body, parentId: parentId, baseId: baseId, isReplied: isReplied};
        jQuerySubmit('reply-message-ajax', messageDetails, 'replyMessage');
    });

});

function replyMessage(response) {
    var cid = $(".send-msg").val();
    var result = JSON.parse(response);
    if (result.status == 0) {
        window.location = "index?cid=" + cid;
    }
}
