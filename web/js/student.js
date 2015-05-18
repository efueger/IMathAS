$(document).ready(function(){

//    Display Calender

    var startDate = '2015-05-05';
    var endDate = '2015-05-04';
    var reviewDate = '2015-05-09';

    $('.calendar').fullCalendar({

        height: 400,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        businessHours: false, // display business hours
        editable: false,
        events: [
            {
                title: 'Assessment',
                start: endDate

            },
            {
                title: 'Review Assessment',
                start: reviewDate,
                color: '#257e4a'
            }
        ],
        eventClick: function(e) {

            var html = "<p>This is the default dialog.</p>";
            $( "#dialog" ).dialog();

        }
    });

//        Show Dialog Pop Up for Assessment time

    $('.confirmation-require').click(function(e){
        var linkId = $(this).attr('id')
        var timelimit = Math.abs($('#time-limit'+linkId).val());
        var hour = (Math.floor(timelimit/3600) < 10) ? '0'+Math.floor(timelimit/3600) : Math.floor(timelimit/3600);
        var min = Math.floor((timelimit%3600)/60);
        var html = '<div>This assessment has a time limit of '+hour+' hour, '+min+' minutes.  Click OK to start or continue working on the assessment.</div>';
        var cancelUrl = $(this).attr('href');
        e.preventDefault();
        $('<div  id="dialog"></div>').appendTo('body').html(html).dialog({
            modal: true, title: 'Message', zIndex: 10000, autoOpen: true,
            width: 'auto', resizable: false,
            closeText: "hide",
            buttons: {
                "Cancel": function () {
                    $(this).dialog('destroy').remove();
                    return false;
                },
                "Confirm": function () {
                    window.location = cancelUrl;
                   var toremaining = $("toremainingId").val();
                    alert(toremaining);
//                    $(this).dialog("close");
                    return true;

                }
            },
            close: function (event, ui) {
                $(this).remove();
            }
        });
    });
});