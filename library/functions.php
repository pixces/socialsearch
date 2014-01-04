<?php

function createAside($text,$length){
    if (!$text){
        return false;
    }

    $string = stripSlashesDeep(strip_tags(nl2br($text)));

    if (strlen($string) <= $length){
        return $string;
    }

    $strPart = substr($string,0,$length);
    return $strPart.'[..]';
}




function get_navigation($args)
{

    $navigation = '<div class="' . $args['container_class'] . '">';
    $navigation .= '<ul id="menu-' . $args['menu'] . '" class="menu">';

    $tabs = get_tabs();
    foreach ($tabs as $k => $v) {

        if ($v['type'] != 'pages') {
            $url = SITE_URL . DS . $v['type'] . DS . $k;
        } else {
            $url = SITE_URL . DS . $k;
        }

        if (strtolower($v['name']) == $args['current']) {
            $current = "current_page_item";
        } else {
            $current = '';
        }

        $navigation .= '<li id="menu-item-' . $v['id'] . '" class="' . $current . '"><a href="' . $url . '">' . ucwords($v['name']) . '</a>';

        if (isset($v['submenu'])) {
            $navigation .= '<ul class="sub-menu">';
            foreach ($v['submenu'] as $sk => $sv) {

                if ($sv['type'] != 'pages') {
                    $url = SITE_URL . DS . $sv['type'] . DS . $k . DS . $sk;
                } else {
                    $url = SITE_URL . DS . $k;
                }
                $navigation .= '<li id="menu-item-' . $sv['id'] . '" class=""><a href="' . $url . '">' . ucwords($sv['name']) . '</a></li>';
            }
            $navigation .= "</ul>";
        }

        $navigation .= '</li>';
    }
    $navigation .= "</ul></div>";
    if ($navigation) {
        echo $navigation;
    }

}

function get_tabs($type = null)
{

    $header = array(
        'categories' => array(0, 17, 11),
        'pages' => array(1, 2, 3)

    );

    $footer_payment = array(
        'pages' => array(6, 8, 7)
    );

    $footer_general = array(
        'pages' => array(4, 5, 9)
    );

    #TODO: Create dynamic tab list for both, header and footer defined by page / post / course /recipe id's


    $tabs = array(
        'courses' => array(
            'name' => 'courses',
            'id' => 1,
            'type' => 'categories',
            'submenu' => array(
                'continental-speciality-courses' => array(
                    'name' => 'Continental Speciality Courses',
                    'id' => 2,
                    'type' => 'categories'
                ),
                'drinks' => array(
                    'name' => 'drinks',
                    'id' => 4,
                    'type' => 'categories'
                ),
                'food-for-kids' => array(
                    'name' => 'Food for Kids',
                    'id' => 5,
                    'type' => 'categories'
                ),
                'healthy-kids-healthy-world' => array(
                    'name' => 'Healthy Kids Healthy World',
                    'id' => 6,
                    'type' => 'categories'
                ),
                'hindustan-ke-dil-se' => array(
                    'name' => 'Hindustan ke Dil se',
                    'id' => 7,
                    'type' => 'categories'
                ),
                'muhabbat-e-mughal' => array(
                    'name' => 'Muhabbat e Mughal',
                    'id' => 8,
                    'type' => 'categories'
                ),
                'snacks' => array(
                    'name' => 'Snacks',
                    'id' => 9,
                    'type' => 'categories'
                ),
            )
        )
    );

    $tabs['recipes'] = array(
        'name' => 'recipes',
        'id' => 17,
        'type' => 'categories',
        'submenu' => array(
            'latest-recipes' => array('name' => 'Latest Recipes', 'id' => 18, 'type' => 'categories'),
            'mughlai-special' => array('name' => 'Mughlai Special', 'id' => 19, 'type' => 'categories'),
            'top-5-dishes' => array('name' => 'Top 5 Dishes', 'id' => 20, 'type' => 'categories'),
            'zebis-special' => array('name' => 'Zebi\'s Special', 'id' => 21, 'type' => 'categories')
        )
    );

    $tabs['kitchen-wisdom'] = array(
        'name' => 'Kitchen Wisdom',
        'id' => 11,
        'type' => 'categories',
        'submenu' => array(
            'baking-tips' => array('name' => 'Baking Tips', 'id' => 12, 'type' => 'categories'),
            'general-tips' => array('name' => 'General Tips', 'id' => 13, 'type' => 'categories'),
            'household-tips' => array('name' => 'Household Tips', 'id' => 14, 'type' => 'categories'),
            'measurements' => array('name' => 'Measurements', 'id' => 16, 'type' => 'categories'),
            'kitchen-maintenance-cleanup' => array('name' => 'Kitchen Maintenance & Cleanup', 'id' => 23, 'type' => 'categories')
        )
    );
    $tabs['food-dictionery'] = array('name' => 'Food Dictionery', 'id' => 1, 'type' => 'pages');
    $tabs['about-zebi'] = array('name' => 'About Zebi', 'id' => 2, 'type' => 'pages');
    $tabs['contact'] = array('name' => 'Contact', 'id' => 3, 'type' => 'pages');

    return $tabs;
}

function destroyUserSession()
{

    unset($_SESSION['user_id']);
    unset($_SESSION['user_session']);
    unset($_SESSION['cart_id']);
    unset($_SESSION['user_loggedin_status']);

}

function cleanOldSessions()
{
    $userSession = new User_Session();
    $userSession->cleanOldSessions();
}

function createNewUser()
{

    if (!isset($_SESSION['user_id'])) {

        #destroy all existing user session details
        destroyUserSession();

        #do old session cleanup
        cleanOldSessions();

        #create a new user session
        $userSession = new User_Session();
        $userSession->generateNewUserSession();
    }
}


/**
 * functions.php
 */

/* function show menu items */
function show_menu_items($type, $home = false, $listFormat = false, $params = array())
{
    global $config;
    //by default format the result as a list

    if (!$type) {
        return false;
    }

    $linkObj = new Link();
    if ($config['BASIC']['sef_url'] != 0) {
        $linkObj->sef = true;
    }

    //$type = "primary";
    switch ($type) {
        case 'top':
            //this is the top menu which will have only secondy one level menu
            $siteLink = $linkObj->get_secondary_links();
            break;
        case 'primary':
            //this is the main navigation.
            //primary menus with sub menu
            $siteLink = $linkObj->get_links(0, true);
            break;
        case 'footer':
            //this is footer menu with only submenu of services
            $siteLink = $linkObj->get_links(2, false);
            break;
    }


    if (!$listFormat) {
        $linkList = array();
        if ($home) {
            $linkList[] = '<a href="./">Home</a>';
        }
        if ($siteLink) {
            foreach ($siteLink as $link) {
                $linkList[] = '<a href="' . $link['href'] . '">' . $link['name'] . '</a>';
            }
        }
        return implode('&nbsp;&nbsp;|&nbsp;&nbsp;', $linkList);
    }

    $linkList = "";
    if ($home) {
        $linkList .= "<li><a href='./'>Home</a></li>";
    }

    if ($siteLink) {
        foreach ($siteLink as $key => $link) {
            $linkList .= "<li><a href='" . $link['href'] . "'>" . $link['name'] . "</a>";
            if ($link['sub']) {
                $subLink = array();
                foreach ($link['sub'] as $sub) {
                    $subLink[] = "<li><a href='" . $sub['href'] . "'>" . $sub['name'] . "</a></li>";
                }
                $linkList .= "<ul>" . implode("", $subLink) . "</ul>";
            }
            $linkList .= "</li>";
        }
    }


    //else format the links as a list;
    if ($params) {
        $id = isset($params['id']) ? $params['id'] : '';
        $class = isset($params['class']) ? $params['class'] : '';
    }

    $listStart = "<ul";
    if ($id) {
        $listStart .= " id='" . $id . "'";
    }
    if ($class) {
        $listStart .= " class='" . $class . "'";
    }
    $listStart .= ">";

    $listEnd = "</ul>";
    return $listStart . $linkList . $listEnd;
}

function makeList(&$item, $key)
{
    $item = "<li>" . $item . "</li>";
}

function create_snippet($string)
{

    #decode all htmlentities
    #substr string to 197 chars and add trailing...

    $string = html_entity_decode($string);
    $string = substr($string, 0, 170);

    #now make sure that the string do not ends in an incomplete word
    $pos = strrpos($string, ' ');

    $string = substr($string, 0, $pos) . "...";

    return $string;
}

function create_home_url($contentObject)
{
    global $config;

    $url = $config['BSIC']['base_url'];

    if ($config['BASIC']['sef_url'] != 0) {
        $url .= "" . $contentObject->content_sef_title;
    } else {
        $url .= "page.php?id=" . $contentObject->content_id;
    }

    return '<a href="' . $url . '" class="moreLink">more</a>';
}

// function to clean the variables
//detecting the data type of the variable
function clean($var, $array = 0)
{
    if ($array == 0) {
        $data = sanitize($var);
    } else if ($array == 1) {
        foreach ($var as $key => $val) {
            $data[$key] = sanitize($val);
        }
    }
    return $data;
}

function sanitize($str){

    if (!$str){
        return;
    }
    return $str;
}


/**
 * function to clean up the database
 * when the user enters into the system
 */
function doCleanUp()
{

    global $db;

    $oldTime = date("z") - 1;
    //delete all the entries in the user table
    //which are older than 1 day
    $sQl = "delete from users where user_date = '" . $oldTime . "'";
    $result = $db->query($sQl);
}


/***
 * function to check if the user
 * is logged in or not
 */
function isUserLoggedIn()
{

    global $db;
    if ($_SESSION['loggedIn'] === true) {
        $userId = $_SESSION['userId'];
        $sQl = "select * from slh_users where user_id = '$userId'";
        $result = $db->get_row($sQl);
        if ($result) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}


/**
 * function to do
 * user logout process
 */
function doUserLogout()
{

    unset($_SESSION['loggedIn']);
    unset($_SESSION['userId']);

    session_destroy();

    //redirect to the home page
    header("location: index.php");
    exit;
}

/**
 * function to convert
 * a string in to a well format
 * data structure.
 */
function string2date($string)
{
    if (empty($string)) {
        return false;
    }
    $timeStamp = strtotime($string);
    return date("M d, Y", $timeStamp);
}


/**
 * function to generate
 * randon 5 character string
 */
function generateCaptcha($count, $type = NULL)
{

    if (is_null($type)) {
        $listVal = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrsduvwxyz123456789";
    } else {
        $listVal = '1234056789';
    }

    $num = "";
    for ($i = 0; $i < $count; $i++) {
        $num .= $listVal[rand(0, strlen($listVal))];
    }
    return $num;
}


/**
 * to validate email id
 */
function is_valid($email)
{
    if (!$email) {
        return false;
    }

    if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
        return true;
    } else {
        return false;
    }
}

/**
 * get basic site configuration
 */
function getConfiguration()
{
    global $db;
    //$ini = file_get_contents("settings.ini");
    $config = parse_ini_file("settings.ini", true);
    return $config;
}


/**
 * function to convert the GET array into
 * GET URL
 */

function arrayToQuerystring($myArray)
{

    foreach ($myArray as $key => $value) {

        if ($key == 'pn') continue;

        $queryParts[] = urlencode($key) . "=" . urlencode($value);

    }

    return implode('&', $queryParts);

}


/**
 * function to create the activation code
 * for the user for email validation
 */

function md5_hash($str)
{

    return md5($str);

}


/*
* select box of country
*/
function getCountrySelect($name = NULL, $selected = 'India')
{
    $countryList = array('Afghanistan', 'Arabia', 'Saudi', 'Argentina', 'Australia', 'Bahrain', 'Bangladesh', 'Bhutan', 'Brazil', 'Cambodia', 'Canada', 'China', 'Colombia', 'Costa Rica', 'Cuba', 'Czech Republic', 'Denmark', 'Egypt', 'Europe', 'European Union', 'Fiji', 'Finland', 'France', 'Germany', 'Ghana', 'Haiti', 'Holland', 'Hong Kong, (China)', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran, Islamic Republic of', 'Iraq', 'Israel', 'Italy', 'Japan', 'Korea, Dem. Peoples Rep.', 'Korea, (South) Republic of', 'Kuwait',
        'Malaysia', 'Maldives', 'Mexico', 'Middle East', 'Morocco', 'Myanmar (ex-Burma)', 'Nepal', 'New Zealand', 'Oman', 'Pakistan', 'Philippines', 'Qatar', 'Russia (Russian Fed.)', 'Saudi Arabia', 'Seychelles', 'Singapore', 'South Africa', 'South America', 'Sri Lanka (ex-Ceilan)', 'Sudan', 'Switzerland', 'Syrian Arab Republic', 'Taiwan', 'Thailand', 'Turkey', 'United Arab Emirates', 'United Kingdom', 'United States');

    if (is_null($name)) {
        $name = "country";
    }

    $sBox = '<select name="' . $name . '" id="countryCombo" style="width:200px;">';
    $sBox .= '<option value="">--- Select Country ---</option>';
    foreach ($countryList as $country) {
        if ($selected != NULL) {
            if ($country == $selected) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
        }
        $sBox .= '<option value="' . $country . '" ' . $sel . '>' . $country . '</option>';
    }
    $sBox .= "</select>";

    return $sBox;
}


function getCitySelect($name)
{

    global $db;
    $citySql = "select `city_name` from rnp_cities order by `city_name` ASC";
    $result = $db->get_results($citySql);

    if ($result) {

        $cityBox = "";
        $cityBox .= '<select name="' . $name . '" id="bill_city" size="1" style="width:200px;"><option value="">Select a City</option>';

        foreach ($result as $city) {
            $cityBox .= '<option value="' . $city->city_name . '">' . $city->city_name . '</option>';
        }
    }
    return $cityBox;
}


function getDateSelect()
{
    $dateBox = "";
    $dateBox .= '<select name="date" id="date" size="1"><option value="">Date</option>';

    for ($x = 1; $x <= 31; $x++) {
        $dateBox .= '<option value="' . str_pad($x, 2) . '">' . str_pad($x, 2) . '</option>';
    }
    $dateBox .= '</select>';

    return $dateBox;
}

function getMonthSelect()
{

    $monthArray = array('Jan', 'feb', 'march', 'april', 'may', 'june', 'july', 'aug', 'sept', 'oct', 'nov', 'dec');

    $dateBox = "";
    $dateBox .= '<select name="month" id="month" size="1"><option value="">Month</option>';

    for ($x = 0; $x < count($monthArray); $x++) {
        $dateBox .= '<option value="' . str_pad(($x + 1), 2) . '">' . ucwords($monthArray[$x]) . '</option>';
    }
    $dateBox .= '</select>';

    return $dateBox;
}

function getYearSelect($future = NULL)
{

    $dateBox = "";
    $dateBox .= '<select name="year" id="year" size="1"><option value="">Year</option>';

    if ($future != NULL) {
        $start = date('Y');

    } else {
        $start = 1940;
    }
    $end = $start + 60;

    for ($x = $start; $x <= $end; $x++) {
        $dateBox .= '<option value="' . str_pad($x, 4) . '">' . str_pad($x, 4) . '</option>';
    }
    $dateBox .= '</select>';

    return $dateBox;
}

/* get page content */
function get_page($link_id)
{
    $page = array();
    global $db;
    $sQl = "select * from pages where link_id = '" . $link_id . "'";
    $result = $db->get_row($sQl);

    if ($result) {
        if ($result->filename) {
            //get the page content;
            $url = SITE_ROOT . "assets/pages/" . $result->filename;
            $content = file_get_contents($url);
        } else {
            $content = "Coming soon";
        }

        $page['title'] = $result->title;
        $page['keyword'] = $result->keyword;
        $page['description'] = $result->description;
        $page['file_name'] = $result->filename;
        $page['content'] = htmlentities($content);
        return $page;
    } else {
        return false;
    }
}

/* function to get link list */
function get_addl_link($id)
{
    #get siblings of this link
    $linkObj = new Link($id);

    if ($config['BASIC']['sef_url'] != 0) {
        $linkObj->sef = true;
    }

    $siblings = $linkObj->get_siblings();

    if (sizeof($siblings)) {
        return $siblings;
    } else {
        return false;
    }
}

/* function to get formated link list
 for the footer */

function get_footer_links($id)
{

    $linkObj = new Link;
    $siteLink = $linkObj->get_links($id);

    $list = "";
    foreach ($siteLink as $link) {
        $list .= '<li><a href="' . $link['href'] . '">' . $link['name'] . '</a></li>';
    }
    return "<ul>" . $list . "</ul>";
}

/* function to get the meta info if page id is provided */
function get_meta_info($id = null, $section = 'page')
{
    #default information
    #can be used on all pages
    $meta['page_title'] = "Innoveins.com | SEO | SEM | Banner Ads | Mobile Website | PPC | Mobile Apps | Website development";
    $meta['meta_description'] = "";
    $meta['meta_key'] = "";
    $meta['meta_robots'] = "ALL";

    if (!is_null($id)) {
        $metaObj = new Metatag();
        $tags = $metaObj->get_meta_info($id, $section);

        if ($tags) {
            return $tags;
        } else {
            return $meta;
        }
    } else {
        return $meta;
    }
}

/*
 * list of all measurements
 * for the ingredients option
 */
function get_measurement_list(){

    return $measurement = array(
        "Weight"=>array(
            "g"=>"Grams","kg"=>"Kilograms","oz"=>"Ounces","lb"=>"Pounds",
            "imperial pint weight"=>"Pint (Imperial)","usa pint weight"=>"Pint (US)",
            "imperial quart"=>"Quart (Imperial)","usa dry quart"=>"Quart (US)"
        ),
        "Liquids"=>array(
            "ml"=>"Millilitre","l"=>"Litre","imperial fl oz"=>"Fluid Ounces (Imperial)",
            "usa fl oz"=>"Fluid Ounces (US)","juice"=>"Juice(s)","imperial pint"=>"Pint (Imperial)",
            "usa pint"=>"Pint (US)","imperial quart"=>"Quart (Imperial)","usa liquid quart"=>"Quart (US)"
        ),
        "Size"=>array(
            "small"=>"Small","medium"=>"Medium","large"=>"Large"
        ),
        "Thickness"=>array(
            "thin slice"=>"Thin slice(s)","medium slice"=>"Medium slice(s)","thick slice"=>"Thick slice(s)"
        ),
        "Other"=>array(
            "bag"=>"Bag","bunch"=>"Bunch","bottle"=>"Bottle","box"=>"Box","bulb"=>"Bulb","can"=>"Can",
            "carton"=>"Carton","clove"=>"Clove","cm"=>"centimetre","cube"=>"Cube","cup"=>"Cup","dash"=>"Dash",
            "drizzle"=>"Drizzle","drop"=>"Drop","glass"=>"Glass","handful"=>"Handful","head"=>"Head","inch"=>"Inch",
            "jar"=>"Jar","knob"=>"Knob","leaf"=>"Leaf","packet"=>"Packet","piece"=>"Piece","pinch"=>"Pinch","rib"=>"Rib",
            "scoop"=>"Scoop","sheet"=>"Sheet","shot"=>"Shot","splash"=>"Splash","sprinkle"=>"Sprinkle",
            "sprig"=>"Sprig","stalk"=>"Stalk","stick"=>"Stick","tablespoon"=>"Tablespoon",
            "heaped tablespoon"=>"Tablespoon (heaped)","teaspoon"=>"Teaspoon","heaped teaspoon"=>"Teaspoon (heaped)",
            "tin"=>"Tin","tube"=>"Tube","wedge"=>"Wedge","zest"=>"Zest"
        )
    );

}