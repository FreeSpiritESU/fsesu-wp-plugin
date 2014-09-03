jQuery(document).ready(function($) {
    $('.event').click(function() {
        var url = '//'+$(location).attr('hostname')+'/'; 
        var details = url+'event-details'
        var id = $(this).attr("data-id");
        var img = url+'wp-content/plugins/fsesu-wp-plugin/assets/images/close.png';
        $('#page').after("<div id='ajax'></div><div id='ajaxBox'><div id='loading'>Loading...</div><div id='ajaxBoxTitle'><div id='ajaxBoxClose'><img src='"+img+"' title='Close' alt='Close' /></div></div><div id='ajaxBoxContents'></div></div>"),
        $.ajax({
            method: "get",url: details,data: "id="+id,
            beforeSend: function(){$("#loading").show("fast");},
            complete: function(){ $("#loading").hide("fast");},
            success: function(html){
                showPopup();
                $("#ajaxBoxContents").html(html);
            }
        });

        $('#ajax').click(function() {
            hidePopup();
        });

        $('#ajaxBox').click(function() {
            hidePopup();
        });

        $('#ajaxBoxClose').click(function() {
            hidePopup();
        });

        $('#ajaxBoxTitle').click(function() {
            hidePopup();
        });
    });
    
    function hidePopup(){
        //disables popup only if it is enabled
        $("#ajax").fadeOut("slow").remove();
        $("#ajaxBox").fadeOut("slow").remove();
        $("#loading").fadeOut("slow").remove();
    }
    
    
    
    function centrePopup(){
        var windowWidth = document.documentElement.clientWidth;
        var windowHeight = document.documentElement.clientHeight;
        var popupWidth = $("#ajaxBox").width();
    
        $("#ajaxBox").css({
            "top": windowHeight/4,
            "left": windowWidth/2-popupWidth/2
        });
    
    
        $("#ajax").css({
            "height": windowHeight
        });
    
    }
    
    function showPopup() {
        centrePopup();
        $("#ajax").css({"opacity": "0.75"}).fadeIn("slow");
        $("#ajaxBox").show("slow");
    }
});