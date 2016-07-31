<?

include "../lib.php";

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

switch ( $mode ){

	case "usafeRequest": # 전자보증서비스신청 요청

		header("Content-type: text/html; charset=euc-kr");

		if ( $godo[sno] == '' || $godo[sno] == '0' )
		{
			header("Status: 쇼핑몰 환경정보에 상점아이디가 비어 있습니다. 고도몰로 문의하세요.", true, 400);
			echo "";
			exit;
		}

		/***************************************************************************************************
		*  hashdata 생성
		*    - 데이터 무결성을 검증하는 데이터로 요청시 필수 항목.
		*    - godosno 를 조합한후 md5 방식으로 생성한 해쉬값.
		***************************************************************************************************/

		$data[godosno]	= $godo[sno];				# 상점아이디
		$data[hashdata]	= md5($godo[sno]);		# hashdata 생성
		$data = array_merge ($data, $_GET);

		$out = readpost("http://www.godo.co.kr/userinterface/_usafe/sock_request.php", $data);

		if ( preg_match("/^true\|/i",$out) ) // 성공
			echo 'true';
		else // 실패
		{
			$out = preg_replace("/^false[ |]*-[ |]*/i", "", $out);
			header("Status: [신청 실패] {$out}", true, 400);
			exit;
		}

		break;
}
?>