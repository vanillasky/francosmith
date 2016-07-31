<?php

/* Include Prefilter Function */
/* { = include_file( "popup/test.htm" ) } ¢º { = this->define( 'popuptesthtm', 'popup/test.htm' ) } { # popuptesthtm } º¯°æ */

function include_file( $source, $tpl ){

    global $include_file_idx;
	$replace = array();

	preg_match_all( "/\{ *= *include_file[^\{\}]*\}/is", $source, $matches );

	foreach ( $matches[0] as $request ){

		$file = trim( preg_replace( array( "/^\{ *= *include_file *\(/is", "/\) *\}/" ), "", $request ) );
		//$key = trim( preg_replace( array( "/^ *[\'|\"]/is", "/[\'|\"] *$/", "/[\/|\.|+| |\"|\']/" ), "", $file ) );
		$key = "tpl_include_file_".++$include_file_idx;

		$replace['request'][] = $request;
		$replace['define'][] = "{ = this->define( '" . $key . "', " . $file . " ) } " . "{ # " . $key . " }";
	}

	if ( count( $replace['request'] ) ) $source = str_replace( $replace['request'], $replace['define'], $source );

    return $source;
}
?>
