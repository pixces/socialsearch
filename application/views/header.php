<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>iAdmin Panel</title>
    <base href="<?=SITE_URL; ?>">
    <link rel="shortcut icon" href="<?=SITE_IMAGE; ?>favicon.ico" type="image/x-icon" />
    <link href='http://fonts.googleapis.com/css?family=Ropa+Sans|Roboto|Source+Sans+Pro:900italic,400,600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" media="all" href="<?=SITE_CSS; ?>bootstrap.css">
    <link rel="stylesheet" type="text/css" media="all" href="<?=SITE_CSS; ?>style.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript" ></script>
    <script src="<?=SITE_JS; ?>bootstrap.js" type="text/javascript"></script>
    <script src="<?=SITE_JS; ?>social.js" type="text/javascript"></script>
    <script src="<?=SITE_JS; ?>functions.js" type="text/javascript"></script>
</head>
<body>
<!-- Start Header //-->
<div id="page-top-outer">
    <div id="page-top-inner">
        <div class="top pull-left">
            <div id="logo">
                <a href="<?=SITE_URL; ?>">
                    <img src="<?=SITE_IMAGE . 'cms/logo-xcmss.png'; ?>" />
                </a>
            </div>
            <?php if ($pagetype != 'login') { ?>
                <div id="navbar">
                    <ul id="nav">
                        <li><a class="" href="<?=SITE_URL; ?>/stream/"><i></i>Stream</a></li>
                        <li><a class="" href="<?=SITE_URL; ?>/post/"><i></i>Posts</a></li>
                    </ul>
                </div>
            <?php } ?>
        </div>
        <div id="top-welcome">
            <?=$welcome_note; ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<!-- End Header -->
<!-- Start Main //-->
<div id="page-body-outer">
    <div id="page-body-inner">
        <div>
            <h1>
                <?=ucwords(strtolower($pageTitle)); ?>
                <?php if($addUrl){ ?>
                    <a class="addlink" href="<?=$addUrl; ?>"><i class="icon-plus-sign icon-white"></i> Add New</a>
                <?php } ?>
            </h1>
            <!-- crumbs
            <div class="crumbs">
                <ul class="breadcrumb">
                    <li><a href="#">Home</a> <span class="divider">/</span></li>
                    <li><a href="#">Library</a> <span class="divider">/</span></li>
                    <li class="active">Data</li>
                </ul>
            </div>
            crumbs end -->
        </div>
        <!-- display notification on all pages -->
        <div class="alert">
            <span class="message">Default message goes here....</span>
        </div>
        <!-- Notification ends -->
        <!-- main content area start -->
        <div>