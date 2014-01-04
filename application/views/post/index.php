<div class="well well-small count-header">
    <div class="pull-left">
        <span class="">Displaying <?=strtoupper($current_status);?>&nbsp;&nbsp;<?=$pager['min_offset']." - ".$pager['max_offset']." Posts"; ?></span>&nbsp;|&nbsp;
        <?php if ($post_summary != 0){ ?>
            <?php foreach($post_summary as $status => $count) { ?>
                <span><a href="<?=SITE_URL; ?>/post/<?=$status; ?>/"><?=ucwords(strtolower($status))." Posts"; ?>&nbsp;<span class="label label-info"><?=$count; ?></span></a></span>
            <?php } ?>
        <?php } else { ?>
            <span><a href="<?=SITE_URL; ?>/post/all/">All Posts <span class="badge badge-info">0</span></a></span>
        <?php } ?>
    </div>
    <div class="pull-right">
        <?php if ($pager['totalPages'] > 0) { ?>
        <div class="pagination pagination-right">
            <ul>
                <li><a href="<?=SITE_URL."/post/".$current_status."/".$pager['prev']."/"; ?>">&laquo; Prev</a></li>
                <li class="disabled"><a href="<?=SITE_URL."/post/".$current_status."/".$pager['current']."/"; ?>"><?=$pager['current']; ?></a></li>
                <li><a href="<?=SITE_URL."/post/".$current_status."/".$pager['next']."/"; ?>">Next &raquo;</a></li>
            </ul>
        </div>
        <?php } ?>
    </div>
    <div class="clearfix"></div>
</div>
<ul class="list-panel">
    <?php if ($postList){ ?>
    <?php foreach($postList as $post){ ?>
    <li id="post-<?=$post['Post']['id']; ?>" class="item media">
        <input class="pull-left" type="checkbox" name="postId[]" value="<?=$post['Post']['id']; ?>">
        <a class="pull-left" href="#"><img class="img-polaroid" src="<?=$post['Post']['user_profile_image']; ?>"></a>
        <div class="media-body pull-left">
            <section><span class="title"><?=$post['Post']['user_name']; ?></span><span class="muted">&nbsp;&nbsp;<?='@'.$post['Post']['user_screen_name']; ?></span> </section>
            <p><?=createAside($post['Post']['post_text'],250); ?></p>
            <section class="muted"><i class="<?='icon-'.$post['Post']['source']; ?>"></i> <a target="_blank" href="<?=$post['Post']['post_url']; ?>"><?=strtolower($post['Post']['post_url']); ?></a></section>
        </div>
        <div class="media-action pull-right">
            <span><i class="icon-search"></i> <?=$post['Stream']['keyword']; ?></span>
            <span><i class="icon-calendar"></i> <?=date('r', $post['Post']['date_published_ts']); ?></span>
            <span class="button-bar">
                <?php $btnType = ($post['Post']['post_status'] == 'approved') ? 'btn-success' : ( ($post['Post']['post_status'] == 'rejected') ? 'btn-danger' : 'btn-warning'); ?>
                <button class="posts-action btn btn-small <?=$btnType; ?>" type="button" data-type="post" data-action="change-status" id="<?=$post['Post']['id']; ?>" data-value="<?=$post['Post']['post_status']; ?>" title="Click to Change Status"><?=ucwords($post['Post']['post_status']); ?></button>
                <a href="javascript:void(0);" class="posts-action btn btn-mini" data-type="post" data-action="delete" id="<?=$post['Post']['id']; ?>" title="Delete this Post"><i class="icon-trash"></i></a>
            </span>
        </div>
    </li>
    <?php } } else { ?>
        <h5>No Posts found for this Status.<br>Please check other status or else make new calls.</h5>
    <?php } ?>
</ul>