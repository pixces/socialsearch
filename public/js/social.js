/**
 * Created with IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 08/09/13
 * Time: 2:48 PM
 * To change this template use File | Settings | File Templates.
 */

$(function(){

    //disable the alert box for display
    $(".alert").hide();

    //validate and save stream data
    $("#streamAdd").on("submit",SOCIAL.streamAdd);

    //on stream action
    $(document).on("click",'.stream-action',SOCIAL.streamAction);

    //on post action
    $(document).on('click','.posts-action',SOCIAL.postsAction);



});