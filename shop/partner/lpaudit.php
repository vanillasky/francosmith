<?
	// 링크프라이스에	셋업이 적용된	파일과 lpfront파일의 절대경로를	나열해주세요.
	function get_base_dir( $fname ) {
        $tmp = explode( "/", realpath( $fname ) );
        array_pop( $tmp );
        return implode( "/", $tmp );
	}
	$SERVER_DIR = get_base_dir(__FILE__);
	$SERVER_DIR = str_replace('/partner','',$SERVER_DIR);
	
	$files = array(
	  "$SERVER_DIR/partner/lpfront.php",
	  "$SERVER_DIR/partner/daily_fix.php",
 	  "$SERVER_DIR/order/order_end.php"	  
	);

	header("Content-type:text/plain");

	for($i=0;$i<count($files);$i++)
	{
		$file = $files[$i];

		if(!file_exists($file)) 
		{
			$size = -1;
			$time = "";
		}
		else
		{
			$size  = filesize($file);
			$time = date("YmdHis", filemtime($file));
		}

		echo "$file\t$size\t$time\n";
	}
	
?>
