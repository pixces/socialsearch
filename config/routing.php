<?php

$routing = array(
    //'/admin\/(.*?)\/(.*?)\/(.*)/' 	        => 'admin/\1_\2/\3',
    //'/admin\/(.*?)\/(.*?)\/(.*)/' 	        => '\1/admin_\2/\3',
    '/api\/v1\/(.*?)\/(.*)/'                    => '\1/api_\1_\2',          //todo: make it more meaningful
    '/stream\/(.*?)\/(.*)/'                     => 'stream/\1/\2',
    '/call\/(.*?)\/(.*)/'                       => 'call/\1/\2',
    '/post\/(all|new|approved|rejected)\/(.*)/' => 'post/index/\1/\2',
    '/post\/(.*?)\/(.*)/'                       => 'post/\1/\2',
    '/test\/(.*?)\/(.*)/'                       => 'test/\1/\2',
    '/(.*?)\/(.*)/'                             => '\1/\2'
);

$default['controller'] = 'post';
$default['action'] = 'index';