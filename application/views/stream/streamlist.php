<?php if ($count > 0) { ?>
<div align="left">Total <b><span class="count"><?=$count; ?></span></b> Search terms added</div>
<table class="table table-hover">
    <thead>
    <tr>
        <th class="column-mini">#</th>
        <th>Search Term</th>
        <th class="column-small">Posts</th>
        <th class="column-small">Type</th>
        <th class="column-small">Source</th>
        <th class="column-mini"></th>
    </tr>
    </thead>
    <tbody>
    <?php if ($list){ ?>
        <?php foreach($list as $stream){ ?>
            <tr id="stream-<?=$stream['Stream']['id']; ?>">
                <td class="column-mini"><input type="checkbox" name="stream_id[]" value="<?=$stream['Stream']['id']; ?>" ></td>
                <td class="column-title">
                    <?=$stream['Stream']['keyword']; ?>
                </td>
                <td class="column-small">
                    <span class="badge badge-info">0</span>
                </td>
                <td class="column-small">
                    <?php if ($stream['Stream']['is_profile'] == 'y') {
                        echo "Profile";
                    } else if ($stream['Stream']['is_phrase'] == 'y') {
                        echo "Exact Match";
                    } else {
                        echo "-";
                    } ?>
                </td>
                <td class="column-small">
                    <?php if ($stream['Stream']['is_twitter'] == 'y') { ?><i class="icon-twitter"></i><?php } ?>
                    <?php if ($stream['Stream']['is_facebook'] == 'y') { ?><i class="icon-facebook"></i><?php } ?>
                    <?php if ($stream['Stream']['is_gplus'] == 'y') { ?><i class="icon-gplus"></i><?php } ?>
                </td>
                <td class="column-mini">
                    <?php $btnType = ($stream['Stream']['status'] == 'active') ? 'btn-success' : 'btn-warning'; ?>
                    <button class="stream-action btn btn-small <?=$btnType; ?>" type="button" data-type="stream" data-action="change-status" id="<?=$stream['Stream']['id']; ?>" data-value="<?=$stream['Stream']['status']; ?>" title="Click to Change Status"><?=ucwords($stream['Stream']['status']); ?></button>
                    <a class="stream-action btn btn-mini" href="javascript:void(0);" id="<?=$stream['Stream']['id']; ?>" data-name="<?=$stream['Stream']['keyword']; ?>" data-action="delete" title="Delete search term <?=$stream['Stream']['keyword']; ?>"><i class="icon-trash"></i></a>
                </td>
            </tr>
    <?php } } ; ?>
    </tbody>
</table>
<?php } else { ?>

    <h5>No Keywords added.<br>Please use the form above, to add new Keywords and setup search streams.</h5>

<?php } ?>