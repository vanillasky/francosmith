<?php

/* Capture Print Prefilter Function */
/* <!-- capture_print("sms") --> ▶ <!-- capture_start("sms") -->내용<!-- capture_end ("sms") --> 변경 */

function capture_print( $source, $tpl ){

    $replace = array();

	preg_match_all( "/<!-- *capture_start *\([^-->]*\) *-->/is", $source, $matches );

	foreach ( $matches[0] as $request ){

		$key = trim( preg_replace( array( "/^<!-- *capture_start *\( *[\'|\"]*/is", "/[\'|\"]* *\) *-->/" ), "", $request ) );

		preg_match( "/<!-- *capture_start *\( *[\'|\"]*" . $key . "[\'|\"]* *\) *-->.*<!-- *capture_end *\( *[\'|\"]*" . $key . "[\'|\"]* *\) *-->/is", $source, $matches);

		$replace['search'][] = "/<!-- *capture_print *\( *[\'|\"]*" . $key . "[\'|\"]* *\) *-->/is";
		$replace['replace'][] = $matches[0];
	}

	if ( count( $replace['search'] ) ) $source = preg_replace( $replace['search'], $replace['replace'], $source );

    return $source;
}
?>
