initstack = new Array();
window.onload = init;
$(document).ready(function(){

//    $('.dataTables_filter input').get(0).type = 'text';
    $('.dataTables_filter').prop('type', 'text');
});

function jQuerySubmit(url, data, successCallBack) {
    $.post(
        url,
        data,
        eval(successCallBack)
    );
}

function jQuerySubmitAjax(url, type, data, successCallBack, errorCallBack) {
    alert(url);
    $.ajax({
        url: url,
        type:type,
        data: data,
        beforeSend: function() {
        },
        afterSend: function(){
        },
        success: successCallBack,
        error: errorCallBack
    });
}


function isElementExist(element)
{
    if ($(element).length){
        return true;
    }
    return false;
}

function capitalizeFirstLetter(str)
{
    return str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
        return letter.toUpperCase();
    });
}

function init() {
    for (var i=0; i<initstack.length; i++) {
    var foo = initstack[i]();
    }
}

function CommonPopUp(message)
{

    var html = '<div><p>'+message+'</p></div>';
    $('<div id="dialog"></div>').appendTo('body').html(html).dialog({
        modal: true, title: 'Message', zIndex: 10000, autoOpen: true,
        width: 'auto', resizable: false,
        closeText: "hide",
        buttons: {
            "Okay": function () {
                $('#searchText').val(null);

                $(this).dialog('destroy').remove();
                return false;
            }
        },
        close: function (event, ui) {
            $(this).remove();
        }

    });

}

function createDataTable(classNameHandler){
    bPaginate = $('.'+classNameHandler).attr('bPaginate');
    if(bPaginate.length > 0){
        bPaginate = $.parseJSON(bPaginate);
    }else{
        bPaginate = true;
    }
    $('.'+classNameHandler).DataTable({"bPaginate": bPaginate});

}

function initEditor()
{
    tinyMCE.init({
        selector: "textarea",
        width: "100%",
        theme : "advanced",
        theme_advanced_buttons1 : "fontselect,fontsizeselect,formatselect,bold,italic,underline,strikethrough,separator,sub,sup,separator,cut,copy,paste,pasteword,undo,redo",
        theme_advanced_buttons2 : "justifyleft,justifycenter,justifyright,justifyfull,separator,numlist,bullist,outdent,indent,separator,forecolor,backcolor,separator,hr,anchor,link,unlink,charmap,image,advlist,table,tablecontrols,separator,code,separator,asciimath,asciimathcharmap,asciisvg",
        theme_advanced_buttons3 : "",
        theme_advanced_fonts : "Arial=arial,helvetica,sans-serif,Courier New=courier new,courier,monospace,Georgia=georgia,times new roman,times,serif,Tahoma=tahoma,arial,helvetica,sans-serif,Times=times new roman,times,serif,Verdana=verdana,arial,helvetica,sans-serif",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_source_editor_height: "500",
        plugins : 'asciimath,asciisvg,dataimage,table,inlinepopups,paste,media,advlist',
        gecko_spellcheck : true,
        extended_valid_elements : 'iframe[src|width|height|name|align],param[name|value],@[sscr]',
        theme_advanced_resizing : true,
        table_styles: "Gridded=gridded;Gridded Centered=gridded centered",
        cleanup_callback : "imascleanup",
        convert_urls: false,
        AScgiloc : '../../../filter/graph/svgimg.php',
        ASdloc : '/js/d.svg'

    });
}
