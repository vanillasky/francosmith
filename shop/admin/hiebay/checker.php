<?
	echo '<script>alert("서비스가 종료되었습니다");history.go(-1);</script>';
	exit;

	if(!function_exists('json_decode')){
		function json_decode($content, $assoc=false) {
			require_once '../../lib/json.class.php';

			if($assoc) $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			else $json = new Services_JSON;

			return $json->decode($content);
		}
	}

	$fsConfig = array();

	// 하이! eBay 기본 설정
		$fsConfig['apiUrl'] = "http://api.forseller.kr";

	// 하이! eBay 접속 확인
		$fsConfig['ch1'] = curl_init();
		curl_setopt($fsConfig['ch1'], CURLOPT_URL, $fsConfig['apiUrl']."/get-time");
		curl_setopt($fsConfig['ch1'], CURLOPT_RETURNTRANSFER, true);
		curl_setopt($fsConfig['ch1'], CURLOPT_CONNECTTIMEOUT, 3);
		$fsConfig['rs1'] = json_decode(curl_exec($fsConfig['ch1']));
		curl_close($fsConfig['ch1']);

		if($fsConfig['rs1']->Ack != "Success") msg("하이! eBay접속이 원활하지 않습니다.\\n\\n잠시 후 다시 시도해 주세요.", 'info.php');

	// 토큰 정의
		list($fsConfig['token']) = $db->fetch("SELECT value FROM gd_env WHERE category = 'forseller' AND name='token'");

	// 하이! eBay 관련 정보
		if($fsConfig['token']) {
			$fsConfig['ch2'] = curl_init();
			curl_setopt($fsConfig['ch2'], CURLOPT_URL, "http://forsellerrelay.godo.co.kr/gate/shop-status.php?token=".$fsConfig['token']);
			curl_setopt($fsConfig['ch2'], CURLOPT_RETURNTRANSFER, true);
			$fsConfig['rs2'] = curl_exec($fsConfig['ch2']);
			curl_close($fsConfig['ch2']);

			if(substr($fsConfig['rs2'], 0, 4) == "DONE") {
				$fsConfig['info'] = json_decode(substr($fsConfig['rs2'], 4));
			}
		}

	// 토큰검사
		if(!$ignoreToken && !$fsConfig['token']) {
			msg("서비스 신청 후 사용이 가능합니다.", -1);
		}
?>