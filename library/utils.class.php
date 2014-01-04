<?php
/*
 * Common utility methods
 */

class Utils
{
    public static function filter($string, $size = null)
    {
        #remove all formating from the string
        $string = strip_tags($string);
        $string = stripslashes($string);

        if (!is_null($size)) {
            if (strlen($string) > $size) {
                $substring = substr($string, 0, $size);

                #get position of the last "SPACE" character after substring
                $pos = strrpos($substring, ' ');
                if ($pos !== false) {
                    $substring = substr($string, 0, $pos);
                }
                return $substring;
            }
        }
        return $string;
    }

    /**
     * Function to remove all unwanted chars
     * from the given string
     */
    public static function sanitize($string)
    {
        $search = array(
            '@<script[^>]*?>.*?</script>@si', // Strip out javascript
            '@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
            '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@' // Strip multi-line comments
        );

        $str = strip_tags($string);
        $str = preg_replace($search, '', $str);

        return $str;
    }

    public static function createUUID($seed = 'u')
    {
        $dateComp = date('z');
        $randomNum = mt_rand();
        return $seed . '-' . $dateComp . '-' . $randomNum;
    }


    /* end class */
}
