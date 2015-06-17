$(document).ready(function ()
{
    var forumid= $('#forumid').val();
    var ShowRedFlagRow = -1;
    $("#show-all-link").hide();
    $('#result').hide();
    $('#noThread').hide();
    $('.forumResult').hide();
    jQuerySubmit('get-thread-ajax',{forumid: forumid,ShowRedFlagRow:ShowRedFlagRow },'threadSuccess');
    limitToTagShow();

    $('#change-button').click(function(){


        var searchText = $('#searchText').val();
        var courseid = $('#courseid').val();
        if(searchText.length>0)
        {
            if(searchText.match(/^[a-z A-Z 0-9-]+$/))
            {

                $('#flash-message').hide();
                if(document.getElementById('searchAll').checked)
                {
                    $('#searchpost').show();
                    $('#flash-message').hide();

                    jQuerySubmit('get-search-post-ajax',{search: searchText, courseid: courseid},'postSearchSuccess');
                }
                else
                {
                    $('#searchpost').show();
                    $('#flash-message').hide();
                    jQuerySubmit('get-only-post-ajax',{search: searchText, courseid: courseid,forumid:forumid},'postSearchUnchecked');

                }
            }
            else
            {
                $('#flash-message').show();
                $('#flash-message').html("<div class='alert alert-danger'>Search text can contain only alphanumeric values");
                $('#searchText').val(null);
            }
        }else
        {
            $('#flash-message').show();
            $('#flash-message').html("<div class='alert alert-danger'>Search text cannot be blank");
        }

    });
});
function postSearchSuccess(response)
{
    response = JSON.parse(response);

    if (response.status == 0)
    {
        $('#searchpost').empty();
        var courseid = $('#courseid').val();
        var postData = response.data.data;
        $.each(postData, function(index, Data)
        {
            var result = Data.message.replace(/<[\/]{0,1}(p)[^><]*>/ig,"");
            var html = "<div class='block'>";
            html += "<b><label  class='subject'>"+Data.subject+"</label></b>";
            html += "&nbsp;&nbsp;&nbsp;in(&nbsp;<label class='forumname'>"+Data.forumname+"</label>)";
            html += "<br/>Posted by:&nbsp;&nbsp;<label class='postedby'>"+Data.name+"</label>";
            html += "&nbsp;&nbsp;<label id='postdate'>"+Data.postdate+"</label>";
            html += "</div><div class=blockitems>";
            html += "<label id='message'>"+result+"</label>";
            html += "<p><a href='post?courseid=" + courseid + "&threadid=" + Data.threadId +"&forumid="+ Data.forumiddata+"'</a>Show full thread</p>";
            html += "</div>\n";
            $('#searchpost').append(html);
        });
        $('.threadDetails').hide();
        $('.forumResult').show();
    }
    else
    {
        $('#searchpost').hide();
        $('.forumResult').hide();
        var msg ="No result found for your search";
        CommonPopUp(msg);
    }
}

function postSearchUnchecked(response)
{
    response = JSON.parse(response);

    if (response.status == 0)
    {
        $('#searchpost').empty();
        var courseid = $('#courseid').val();
        var postData = response.data.data;
        $.each(postData, function(index, Data)
        {
            var result = Data.message.replace(/<[\/]{0,1}(p)[^><]*>/ig,"");
            var html = "<div class='block'>";
            html += "<b><label  class='subject'>"+Data.subject+"</label></b>";
            html += "&nbsp;&nbsp;&nbsp;in(&nbsp;<label class='forumname'>"+Data.forumname+"</label>)";
            html += "<br/>Posted by:&nbsp;&nbsp;<label class='postedby'>"+Data.name+"</label>";
            html += "&nbsp;&nbsp;<label id='postdate'>"+Data.postdate+"</label>";
            html += "</div><div class=blockitems>";
            html += "<label id='message'>"+result+"</label>";
            html += "<p><a href='post?courseid=" + courseid + "&threadid=" + Data.threadId +"&forumid="+ Data.forumiddata+"'</a>Show full thread</p>";
            html += "</div>\n";
            $('#searchpost').append(html);
        });
        $('.threadDetails').hide();
        $('.forumResult').show();
    }
    else
    {
        $('.forumResult').hide();
        $('#searchpost').hide();
        var msg ="No result found for your search";
        CommonPopUp(msg);
    }
}
function threadSuccess(response)
{
    response = JSON.parse(response);
    var fid = $('#forumid').val();
    var checkFlagValue;
    var count;
    var courseId = $('#course-id').val();
    if (response.status == 0) {
        var threads = response.data.threadArray;
        //var uniquesDataArray = response.data.uniquesDataArray;

        var html = "";
        $.each(threads, function (index, thread) {
            if (fid == thread.forumiddata) {
                count =0;
                $.each(threads,function (index,data)
                {
                    if(thread.threadId == data.threadId)
                    {
                        count++;
                    }
                });
                count--;
                if(thread.parent == 0){

                    html += "<tr> <td><a href='post?courseid="+courseId+"&threadid="+thread.threadId+"&forumid="+fid+"'>" + (thread.subject) +  "</a>"+ thread.name+" </td>";
                    if (thread.tagged == 0 ) {
                        html += " <td> <img src='../../img/flagempty.gif'  onclick='changeImage(this," + false + "," + thread.threadId + ")' ><a href='move-thread?forumId=" + thread.forumiddata + "&courseId=" + courseId + "&threadId=" + thread.threadId + "'>Move</a> <a href='modify-post?forumId=" + thread.forumiddata + "&courseId=" + courseId + "&threadId=" + thread.threadId + "'>Modify</a><a href='#' name='tabs' data-var='" + thread.threadId + "' class='mark-remove'> Remove </a></td> ";
                    }
                    else {
                        html += " <td> <img src='../../img/flagfilled.gif'  onclick='changeImage(this," + true + "," + thread.threadId + ")' ><a href='move-thread?forumId=" + thread.forumiddata + "&courseId=" + courseId + "&threadId=" + thread.threadId + "'>Move</a> <a href='modify-post?forumId=" + thread.forumiddata + "&courseId=" + courseId + "&threadId=" + thread.threadId + "'>Modify</a><a href='#' name='tabs' data-var='" + thread.threadId + "' class='mark-remove'> Remove </a></td> ";
                    }
                    html += "<td>" + count + "</td>";
                    $.each(thread.countArray, function (index, count) {
                        count.usercount--;
                        if (thread.userright >= 20) {
                            html += "<td><a href='#' name='view-tabs' data-var='" + thread.threadId + "' >" + thread.views + "(" + count.usercount + ")" + "</a></td>";
                        } else {
                            html += "<td>" + thread.views + "(" + count.usercount + ")" + "</td>";
                         }
                    });
                    html += "<td>" + thread.postdate + "</td>";
                }
           }
        });
        $(".forum-table-body").append(html);
        $('.forum-table').DataTable({"ordering": false});



    }
    else if (response.status == -1) {

        $('#data').hide();
        $('#noThread').show();


    }
    $("a[name=tabs]").on("click", function () {
        var threadsid = $(this).attr("data-var");
        var html = '<div><p>Are you sure? This will remove your thread.</p></div>';
        $('<div id="dialog"></div>').appendTo('body').html(html).dialog({
            modal: true, title: 'Message', zIndex: 10000, autoOpen: true,
            width: 'auto', resizable: false,
            closeText: "hide",
            buttons: {
                "Cancel": function () {
                    $(this).dialog('destroy').remove();
                    return false;
                },
                "confirm": function () {
                    $(this).dialog("close");
                    var threadId = threadsid;
                    jQuerySubmit('mark-as-remove-ajax', {threadId:threadId}, 'markAsRemoveSuccess');
                    return true;
                }
            },
            close: function (event, ui) {
                $(this).remove();
            }
         });
    });

     $("a[name=view-tabs]").on("click", function () {
        var threadsid = $(this).attr("data-var");

        var html = '<div><p><strong>Thread views</strong> </p></div><br><table><thead><tr><th>Name</th><th>Last Views</th></tr></thead>' +
            '<tbody><tr><td>sssss</td>' +
            '<td>uniquesDataArray</td></tr>' +
            '</tbody></table>';



        $('<div id="dialog"></div>').appendTo('body').html(html).dialog({
            modal: true, title: 'Message', zIndex: 10000, autoOpen: true,
            width: 'auto', resizable: false,
            closeText: "hide"
        });

    });
}

function changeImage(element,checkFlagValue, rowId) {

    if(checkFlagValue == false){
        element.src = element.bln ? '../../img/flagempty.gif' : '../../img/flagfilled.gif';
        element.bln = !element.bln;
    }
    if(checkFlagValue ==true ){
        element.src = element.bln ? '../../img/flagfilled.gif' : '../../img/flagempty.gif';
        element.bln = !element.bln;
    }
    var row = {rowId: rowId};
    jQuerySubmit('change-image-ajax', row, 'changeImageSuccess');

}
function changeImageSuccess(response) {
}
function markAsRemoveSuccess(response) {
    var forumid = $("#forumid").val();
    var courseid = $("#course-id").val();
    var result = JSON.parse(response);
    if(result.status == 0)
    {
        window.location = "thread?cid="+courseid+"&forumid="+forumid;
    }

}

function limitToTagShow() {

    $("#limit-to-tag-link").click(function () {
        $(".forum-table-body").empty();
        $("#limit-to-tag-link").hide();
        $("#limit-to-new-link").hide();
        $("#show-all-link").show();
        var ShowRedFlagRow = 1;
        var forumid= $('#forumid').val();
        var thread = {forumid: forumid , ShowRedFlagRow: ShowRedFlagRow};
        jQuerySubmit('get-thread-ajax',thread,'threadSuccess');

    });
    $("#show-all-link").click(function () {
        $(".forum-table-body").empty();
        $("#limit-to-tag-link").show();
        $("#show-all-link").hide();
        $("#limit-to-new-link").show();
        ShowRedFlagRow = 0;

        var forumid= $('#forumid').val();
        var thread = {forumid: forumid , ShowRedFlagRow: ShowRedFlagRow};
        jQuerySubmit('get-thread-ajax',thread,'threadSuccess');

    });
}

