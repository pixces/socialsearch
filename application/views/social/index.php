<?php if ($user){ ?>
<div>
    <img src="<?=$user['image']; ?>" />
    <h2>Welcome, <?=$user['user_name']; ?></h2>
    <p>You are logged-in to <?=$media; ?></p>
    <p>Click here to <a target="_blank" href="<?=SITE_URL."/social/logout_facebook"; ?>">logout</a></p>
</div>
<?php } else { ?>
<div>
    Signup Using:
    <a href="<?=SITE_URL."/social/login_facebook"; ?>" target="_blank">Facebook</a>
    <a href="<?=SITE_URL."/social/login_twitter"; ?>" target="_blank">Twitter</a>
</div>
<?php } ?>