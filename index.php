<? 
	include_once "shop/_header.php";

	$bgm = $config->load('bgm');

	if (empty($_GET['behind']) === false && $_GET['behind'] == '1') {
		// ¹è°æÀ½¾Ç
		if( empty($bgm['use']) === false && $bgm['use'] == 'y' && empty($bgm['file']) === false ){
			// bgm º¼·ý °è»ê
			$bgm['volume'] = (( 100 - $bgm['volume'] ) * -20);
			echo sprintf('<embed src="%s" loop="%s" volume="%s" width="0" height="0" hidden="true" type="audio/x-ms-wma" autostart="true" showcontrols="false"/>'
				, $bgm['file']
				, $bgm['loof'] == 'y' ? '-1' : 'false'
				, $bgm['volume']
			);
		}
	}else if ( $bgm['urlFix'] == 'y' ){
		require_once 'shop/intact.frameset.php';
	}else{
		header("location:shop/index.php" . ($_SERVER[QUERY_STRING] ? "?{$_SERVER[QUERY_STRING]}" : ""));
	}
?>
