<?php

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}



/* ---------------------------------------------------- */
/* Finding Font Names From CSS data     				*/
/* ---------------------------------------------------- */
function stlst_font_name($a) {

    $a = str_replace(array(

        "font-family:",
        '"',
        "'",
        " ",
        "+!important",
        "!important"

    ), array(

        "",
        "",
        "",
        "+",
        "",
        ""

    ), $a);

    if (strstr($a, ",")) {
        $array = explode(",", $a);
        return $array[0];
    } else {
        return $a;
    }

}