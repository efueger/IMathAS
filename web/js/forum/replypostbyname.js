$(document).ready(function ()
{
    tinymce.init({
        selector: "textarea",
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
    });

    $(".reply-btn").click(function()
    {
        tinyMCE.triggerSave();
        var courseid = $(".courseid").val();
        var forumid = $(".forumid").val();
        var threadid = $(".threadid").val();
        var subject = $("#sub").val();
        var body = $("#postreply").val();
        var replyDetails = {couserid : courseid,forumid : forumid,threadid : threadid,subject : subject,body : body};
        jQuerySubmit('reply-list-post-ajax',replyDetails,'replyPostSuccess');
    });

});

function replyPostSuccess(response)
{alert('ihhihi');
    console.log(response);
    response = JSON.parse(response);
    var courseId = $(".courseid").val();
    var forumId = $(".forumid").val();
    var threadId = $(".threadid").val();
    if(response.status == 0)
    {
        window.location = "list-post-by-name?cid="+courseId+"&forumid="+forumId;
    }
}