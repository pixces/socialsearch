<div id="addForm" class="box box-shadow">
    <form class="form-horizontal" id="streamAdd" name="add_stream" method="post">
        <input type="hidden" name="formAction" value="doAddStream" id="formAction">
        <div class="control-group">
            <label class="control-label" for="inputTerm">Search Term</label>
            <div class="controls">
                <input class="input-xxlarge" type="text" id="inputTerm" placeholder="Enter Search Term..." name="keyword"><br>
                <label class="checkbox inline">
                    <input type="checkbox" id="exact_match" value="y" name="is_phrase"> Exact Match
                </label>
                <label class="checkbox inline">
                    <input type="checkbox" id="profile" value="y" name="is_profile"> Is Twitter Profile
                </label>
                <br><span id="errorKeyword" class="spanErrorText text-error">Please enter search term.</span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Source</label>
            <div class="controls">
                <label class="checkbox inline">
                    <input type="checkbox" id="source_tw" value="y" name="is_twitter" checked="checked"> Twitter
                </label>
                <label class="checkbox inline">
                    <input type="checkbox" id="source_fb" value="y" name="is_facebook"> Facebook
                </label>
                <label class="checkbox inline">
                    <input type="checkbox" id="source_gp" value="y" name="is_gplus"> Google Plus
                </label>
                <br><span id="errorSource" class="spanErrorText text-error">Please select a social media source.</span>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                 <button type="submit" class="btn btn-primary">Add Search Term</button>
            </div>
        </div>
    </form>
</div>
<div class="streamList">
    <p>Preparing Stream List....</p>
    <img class="loading" id="post_loader" src="<?=SITE_IMAGE; ?>ajax_loader_01.gif"/>
</div>
<script type="text/javascript">
    $(function(){
        setTimeout(function() {
            SOCIAL.loadStream();
        }, 500);
    });
</script>