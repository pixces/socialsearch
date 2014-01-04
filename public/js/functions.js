/**
 * Created with IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 08/09/13
 * Time: 1:29 AM
 * To change this template use File | Settings | File Templates.
 */
var SITE_URL = $('base').attr('href'),
    SELF_HREF = window.location.href,
    Loader = '<img class="loading" id="post_loader" src="' + SITE_URL + '/public/images/ajax_loader_02.gif"/>',
    postLoader = '<img class="loading" id="post_loader" src="' + SITE_URL + '/public/images/ajax_loader_05.gif"/>',
    isPostLoading = false,
    needLoadMore = false,
    loadOffset;

var UTILS = {
    numb:/^([0-9]*)$/,
    address:/^([a-zA-Z][a-zA-Z0\@\_\-\#\.\s]*)$/,
    email:/^[a-z0-9\-\_\+]+(\.[a-z0-9\-\_\+]+)*\@(([a-z0-9\-\_\+]+(\.[a-z0-9\-\_\+]+)*)(\.[a-z]{2,3})|([0-9]+\.){3}[0-9]+)$/i,
    alpha:/^[a-zA-Z\s]*$/,
    name:/^([a-zA-Z][a-zA-Z0\''\_\-\.\s]*)$/,
    zip:/^([0-9]{5,6})$/,
    phone:/^([0-9]{8,12})$/,
    end:0,

    'isEmpty':function (data) {
        if (data != '') {
            return false;
        } else {
            return true;
        }
    },
    'isPhone':function (phone) {
        return !!UTILS.phone.exec(phone);
    },

    'isZipcode':function (code) {
        return !!UTILS.zip.exec(code);
    },

    'isEmail':function (email) {
        return !!UTILS.email.exec(email);
    },

    'isNumber':function (num) {
        return !!UTILS.numb.exec(num);
    },

    'isValidCheckbox':function (obj_id){

        var check = 0;
        var objId = obj_id;

        $("input[id="+ objId +"]").each(function(){

            if ( this.checked )
            {
                check++;
            }

        });

        if ( check == 0) { return false; } else { return true; }
    },


    'length':function (string) {
        return string.length;
    },

    'redirect_window':function (url) {
        window.location.href = url;
    },

    //valid type message / error
    'show_message':function (message, type) {
        if (type == 'success') {
            $(".alert").removeClass().addClass('alert alert-success');
        } else if (type == 'error') {
            $(".alert").removeClass().addClass('alert alert-error');
        }
        $(".alert span.message").html(message);
        $(".alert").show();

        //remove this messsage after some time
        UTILS.remove_message();

    },

    //adnimate and remove message/error box
    'remove_message':function () {
        $(".alert").animate({opacity:1.0}, 8000).fadeOut('slow');
    },

    //reset form
    'reset':function(formId) {
        $("#"+formId).each(function () {
            this.reset();
        });
    },

    //show mask
    'mask':function(){

        //Get the screen height and width
        var maskHeight = $(document).height();
        var maskWidth = $(window).width();

        //Set height and width to mask to fill up the whole screen
        $('#mask').css({'width':maskWidth,'height':maskHeight});

        //transition effect
        $('#mask').fadeIn(1500);
        $('#mask').fadeTo("slow",0.8);
    },

    //show the modal window
    'showModal':function(id,link){

        var Id  = typeof id !== 'undefined' ? id : '#dialog';
        var link = typeof link !== 'undefined' ? link : '';

        if (link){
            var fetchUrl = SITE_URL+ "/" + link +"/";
        }

        //now display the modal window
        //Get the window height and width
        var winH = $(window).height();
        var winW = $(window).width();

        //Set the popup window to center
        $(Id).css('top', winH/2-$(Id).height()/2);
        $(Id).css('left', winW/2-$(Id).width()/2);

        //transition effect
        $(Id).fadeIn(2000);
    },

    //display image upload form during edit
    'showImageUpload':function(){
        $("#oldImage").val('');
        $('.image_display').hide('slow');
        $('.add_image').show('slow');
    },

    //Validates if the user is logged-in and
    //return true / false with membership type
    'validateUser':function(){
        var fetchUrl = SITE_URL + '/users/validateUser/';
        $.get(fetchUrl,function(data){
            return data;
        },'json');
        return;
    },

    //display delete confirmation box
    'confirmDelete':function(type, string){
        var t = confirm("Sure!, you want to delete " + type +" "+ string + "?");
        return t;
    },

    'displayFieldError':function( idStr , str ){
        var $obj = $("#"+idStr);
        $obj.html(str).show();
    }

}

/* valid for admin section */
var SOCIAL;
SOCIAL = {
    'loadStream': function(){
        var fetchUrl = SITE_URL + '/stream/streamList/';
        $(".streamList").load(fetchUrl);
    },

    'streamAdd':function(){

        //hide all error box
        $(".spanErrorText").hide();

        //hide message display
        $("#msgBox").hide();

        var errorCount = 0;
        var source = 0;

        if ( $("#inputTerm").val() == "" ){
            errorCount += 1;
            UTILS.displayFieldError('errorKeyword','Enter Search Keyword.');
        }

        //validate checkboxes
        if ( $("#source_tw").is(':checked') ){
            source++;
        }
        if ( $("#source_fb").is(':checked') ){
            source++;
        }
        if ( $("#source_gp").is(':checked') ){
            source++;
        }

        if (source == 0){
            errorCount += 1;
            UTILS.displayFieldError('errorSource','Select a social media source.');
        }

        if ( errorCount != 0 ){
            return false;
        }

        //now submit this form
        var dataStr = $("#streamAdd").serialize();
        var fetchUrl = SITE_URL + '/stream/addStream/';

        $.post(fetchUrl,dataStr,function(data){

            var status = data.status;
            var message = data.message;

            //display message
            UTILS.show_message(message, status);

            //reset all the form fields
            UTILS.reset('streamAdd');

            //reload the stream
            SOCIAL.loadStream();
            return false;
        },"json");
        return false;
    },

    'streamAction':function(e){

        e.stopPropagation();

        var obj = $(this);
        var stream_id = $(obj).attr('id');
        var action = $(obj).attr('data-action');
        var name = $(obj).attr('data-name');

        var fetchUrl = SITE_URL + '/stream/';

        switch(action){
            case 'delete':
                fetchUrl += "delete/";
                var count = $(".count").text();

                var rowId = "stream-"+stream_id;
                $("#" + rowId).css({"background": '#FFEAEA'});

                var t = UTILS.confirmDelete('Search Keyword',name);
                if (!t){
                    $("#" + rowId).css({"background": 'none'});
                    return false;
                }

                //do the query to remove category
                $.post(fetchUrl, {id: stream_id, 'name': name},
                    function (data) {

                        var status = data.status;
                        var message = data.message;

                        if (status != 'error') {
                            //remove selected row from the dom
                            $("#" + rowId).animate({opacity: 1.0}, 500).fadeOut('slow');
                            $(".count").text( parseInt(count)-1 );
                        } else {
                            $("#" + rowId).css({"background": 'none'});
                        }
                        UTILS.show_message(message, status);

                    }, "json");
                break;
            case 'change-status':

                fetchUrl += "change_status/";
                var status = $(obj).attr('data-value');

                $.post(fetchUrl, {'id': stream_id, 'data': status}, function (data) {

                    if (data.status == 'success') {

                        var newStatus = data.newstatus;

                        if(newStatus == 'active'){
                            $(obj).removeClass().addClass('stream-action btn btn-small btn-success');
                        } else {
                            $(obj).removeClass().addClass('stream-action btn btn-small btn-warning');
                        }
                        //remove all existing data and plot the new values
                        $(obj).attr('data-value', newStatus);
                        $(obj).text(newStatus);

                    } else {
                        alert("Sorry, cannot change user status");
                        return false;
                    }
                }, 'json');
                break;
        }
        return false;
    },

    'postsAction':function(e){

        e.stopPropagation();

        var obj = $(this);
        var post_id = $(obj).attr('id');
        var action = $(obj).attr('data-action');
        var name = $(obj).attr('data-value');

        var fetchUrl = SITE_URL + '/post/';

        switch(action){
            case 'delete':
                fetchUrl += "delete/";
                var count = $(".count").text();

                var rowId = "post-"+post_id;
                $("#" + rowId).css({"background": '#FFEAEA'});

                var t = UTILS.confirmDelete('Social Post');
                if (!t){
                    $("#" + rowId).css({"background": 'none'});
                    return false;
                }

                //do the query to remove category
                $.post(fetchUrl, {id: post_id },
                    function (data) {

                        var status = data.status;
                        var message = data.message;

                        if (status != 'error') {
                            //remove selected row from the dom
                            $("#" + rowId).animate({opacity: 1.0}, 500).fadeOut('slow');
                            $(".count").text( parseInt(count)-1 );
                        } else {
                            $("#" + rowId).css({"background": 'none'});
                        }
                        UTILS.show_message(message, status);

                    }, "json");
                break;
            case 'change-status':

                fetchUrl += "change_status/";
                var status = $(obj).attr('data-value');

                $.post(fetchUrl, {'id': post_id, 'data': status}, function (data) {

                    if (data.status == 'success') {

                        var newStatus = data.newstatus;

                        if(newStatus == 'approved'){
                            $(obj).removeClass().addClass('posts-action btn btn-small btn-success');
                        } else {
                            $(obj).removeClass().addClass('posts-action btn btn-small btn-danger');
                        }
                        //remove all existing data and plot the new values
                        $(obj).attr('data-value', newStatus);
                        $(obj).text(newStatus);

                        //also hide this post from the ui as it is migrated to another ui
                        //var rowId = 'post-'+post_id;
                        //$("#" + rowId).animate({opacity: 1.0}, 500).fadeOut('slow');

                    } else {
                        alert("Sorry, cannot change user status");
                        return false;
                    }
                }, 'json');
                break;
        }
        return false;
    }

};


