<?php

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}


/* ---------------------------------------------------- */
/* Disable autoptimize plugin on editor                 */
/* ---------------------------------------------------- */
function stlst_disable_autoptimize() {

    if (strpos($_SERVER['REQUEST_URI'], 'stylist_frame') !== false || strpos($_SERVER['REQUEST_URI'], 'stylist') !== false) {
        return true;
    } else {
        return false;
    }

}

add_filter('autoptimize_filter_noptimize','stlst_disable_autoptimize',10,0);


/* ---------------------------------------------------- */
/* Getting Last Post Title 								*/
/* ---------------------------------------------------- */
function stlst_getting_last_post_title() {
    $last = wp_get_recent_posts(array(
        "numberposts" => 1,
        "post_status" => "publish"
    ));

    if (isset($last['0']['ID'])) {
        $last_id = $last['0']['ID'];
    } else {
        return false;
    }

    $title = get_the_title($last_id);

    if (strstr($title, " ")) {
        $words = explode(" ", $title);
        return $words[0];
    } else {
        return $title;
    }

}



/* ---------------------------------------------------- */
/* Clean protocol from URL 								*/
/* ---------------------------------------------------- */
function stlst_urlencode($v) {
    $v = explode("://", urldecode($v));
    return urlencode($v[1]);
}


/* ---------------------------------------------------- */
/* Hover/Focus System									*/
/* ---------------------------------------------------- */
/*
Replace 'body.stlst-selector-hover' to hover.
replace 'body.stlst-selector-focus' to focus.
replace 'body.stlst-selector-link' to link.
replace 'body.stlst-selector-active' to active.
replace 'body.stlst-selector-visited' to visited.
*/
function stlst_hover_focus_match($data) {

    preg_match_all('@body.stlst-selector-(.*?){@si', $data, $keys);

    foreach ($keys[1] as $key) {

        $keyGet = substr($key, 0, 7);
        if ($keyGet == 'visited') {
            $keyQ = $keyGet;
        }

        $keyGet = substr($key, 0, 6);
        if ($keyGet == 'active') {
            $keyQ = $keyGet;
        }

        $keyGet = substr($key, 0, 4);
        if ($keyGet == 'link') {
            $keyQ = $keyGet;
        }

        $keyGet = substr($key, 0, 5);
        if ($keyGet == 'hover' || $keyGet == 'focus') {
            $keyQ = $keyGet;
        }

        $dir  = 'body.stlst-selector-' . $key;
        $dirt = 'body.stlst-selector-' . $key . ':' . $keyQ;

        $dirt = str_replace(array(
            'body.stlst-selector-hover',
            'body.stlst-selector-focus',
            'body.stlst-selector-visited',
            'body.stlst-selector-active',
            'stlst-selector-link'
        ), array(
            'body',
            'body'
        ), $dirt);
        $data = (str_replace($dir, $dirt, $data));
    }

    $data = str_replace('.stlst-selected', '', $data);

    return $data;

}

/* --------------------------------------------------------- */
/* Encoding & Decoding the data; Used for import and export  */
/* --------------------------------------------------------- */
function stlst_encode($value) {
    $func = 'base64' . '_encode';
    return $func($value);
}

function stlst_decode($value) {
    $func = 'base64' . '_decode';
    return $func($value);
}


/* ---------------------------------------------------- */
/* stripslashes data                                    */
/* ---------------------------------------------------- */
function stlst_stripslashes($v){

    $v = preg_replace("/\\\\(@|\.|\/|!|\*|#|\?|\+)/i", "TP09BX$1", $v);
    $v = stripslashes($v);
    $v = preg_replace("/(TP09BX)/i", "\\", $v);

    return $v;

}