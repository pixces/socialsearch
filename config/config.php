<?php

define('MEMORY_LIMIT', '128M');
@ini_set('memory_limit', MEMORY_LIMIT);

//config file that inclues all the files
ob_start();
session_start();

/** Configuration Variables **/
define ('ENVIRONMENT','development');
define ('SITE_URL','http://localhost:8888/socialsearch');
define ('ENCRYPTION_SEED','3782adf93db49e7239836bb23072f31');
define ('GP','googleplus');
define ('FB','facebook');
define ('TW','twitter');
define ('FB_APP_ID','231422860345111');
define ('FB_SECRET_KEY','67d7134df5ded4d780ea72a351729ec6');
define ('TW_ACCESS_KEY', '54500373-zO7lhekUGXugfzgYuZcyuPylfR35YUqz6q7nr6mPD');
define ('TW_ACCESS_SECRET','V6ylBzbyUA9zXUMQcCkb8m6G4A28fA6fuFq1SJGOZGk');
define ('TW_CONSUMER_KEY','qAYiR5nyrvKboWJk8Lw');
define ('TW_CONSUMER_SECRET', 'A6Zu1EDv6nkGrPezShTcVaasGwVge5EMum2XxHwjo');


