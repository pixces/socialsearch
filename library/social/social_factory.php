<?php
/**
 * Created by IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 09/09/13
 * Time: 4:13 PM
 * To change this template use File | Settings | File Templates.
 */ 
class Social_Factory{

    /**
     * Factory to create instances
     * for Source Call Classes for each type
     * @param $type
     * @return sourcecall_interface SourceCall_Facebook|SourceCall_gPlus|SourceCall_Twitter
     */
    public static function sourceCall_Factory($type){

        switch($type){
            case 'twitter':
                return new SourceCall_Twitter();
                break;
            case 'facebook':
                return new SourceCall_Facebook();
                break;
            case 'gplus':
                return new SourceCall_gPlus();
                break;
        }
    }


    /**
     * Factory to create instances
     * of parsers based on type supplied
     * @param string $type
     * @return parser_interface parser_facebook|parser_gplus|parser_twitter
     */
    public static function parser_factory($type){

        switch($type){
            case 'twitter':
                return new Parser_Twitter();
                break;
            case 'facebook':
                return new Parser_Facebook();
                break;
            case 'gplus':
                return new Parser_gPlus();
                break;
        }

    }


}
