<?

include "../lib.php";

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

switch ( $mode ){

	case "accountList": # 입금내역 요청

		header("Content-type: text/html; charset=euc-kr");

		if ( $godo[sno] == '' || $godo[sno] == '0' )
		{
			header("Status: 쇼핑몰 환경정보에 상점아이디가 비어 있습니다. 고도몰로 문의하세요.", true, 400);
			echo "";
			exit;
		}

		### Create Query
		$tmp = Array();
		foreach ( $_GET as $k => $v )
		{
			if ( is_array($v) )
				foreach ( $v as $sk => $sv )
					$tmp[] = "{$k}[{$sk}]={$sv}";
			else if ( !in_array( $k, array('mode', 'dummy') ) )
				$tmp[] = "{$k}={$v}";
		}

		$query = implode( "&", $tmp );
		$query = str_replace( " ", "+", $query );

		/***************************************************************************************************
		*  hashdata 생성
		*    - 데이터 무결성을 검증하는 데이터로 요청시 필수 항목.
		*    - MID 를 조합한후 md5 방식으로 생성한 해쉬값.
		***************************************************************************************************/

		$MID		= sprintf("GODO%05d",$godo[sno]);	# 상점아이디
		$hashdata	= md5($MID);						# hashdata 생성

		$out = readurl("http://bankmatch.godo.co.kr/sock_listing.php?MID={$MID}&{$query}&hashdata={$hashdata}");

		if ( !preg_match("/^false[ |]*/i",$out) ) // 성공
			echo $out; // JSON 출력
		else // 실패
		{
			$out = preg_replace("/^false[ |]*-[ |]*/i", "", $out);
			header("Status: [로딩 실패] {$out}", true, 400);
			echo "";
			exit;
		}

		break;

	case "bankMatching": # 비교(Matching) 요청

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
		*    - MID 를 조합한후 md5 방식으로 생성한 해쉬값.
		***************************************************************************************************/

		$MID		= sprintf("GODO%05d",$godo[sno]);	# 상점아이디
		$hashdata	= md5($MID);						# hashdata 생성

		$out = readurl("http://bankmatch.godo.co.kr/sock_matching.php?MID={$MID}&bkdate[0]={$_GET[bkdate][0]}&bkdate[1]={$_GET[bkdate][1]}&hashdata={$hashdata}");

		if ( preg_match("/^true\|/i",$out) ) // 성공
		{
			# [0] 결과 메시지, [1] 데이터( [..] 매칭된 주문번호들 )
			$out = preg_replace("/^true\|/i", "", $out);
			if ( $out ) $datas = explode("|", $out);
			echo "<b>비교(Matching) <font color=0077B5>(결과: 처리성공!)</font></b>" . (count($datas) ? "^" . implode("|", $datas) : "");
		}
		else // 실패
		{
			# [0] 결과 메시지, [..] 오류 메시지
			$out = preg_replace("/^false[ |]*-[ |]*/i", "", $out);
			header("Status: <b>비교(Matching) <font color=0077B5>(결과: 처리실패!)</font></b>" . (trim($out) ? "^{$out}" : ""), true, 400);
			echo "";
			exit;
		}

		break;

	case "bankUpdate": # 입금내역 수정

		header("Content-type: text/html; charset=euc-kr");

		if ( $godo[sno] == '' || $godo[sno] == '0' )
		{
			header("Status: 쇼핑몰 환경정보에 상점아이디가 비어 있습니다. 고도몰로 문의하세요.", true, 600);
			echo "";
			exit;
		}

		### Create Query
		$tmp = Array();
		foreach ( $_GET as $k => $v )
		{
			if ( is_array($v) )
				foreach ( $v as $sk => $sv )
					$tmp[] = "{$k}[{$sk}]={$sv}";
			else if ( !in_array( $k, array('mode', 'dummy') ) )
				$tmp[] = "{$k}={$v}";
		}

		$query = implode( "&", $tmp );
		$query = str_replace( " ", "+", $query );

		/***************************************************************************************************
		*  hashdata 생성
		*    - 데이터 무결성을 검증하는 데이터로 요청시 필수 항목.
		*    - MID 를 조합한후 md5 방식으로 생성한 해쉬값.
		***************************************************************************************************/

		$MID		= sprintf("GODO%05d",$godo[sno]);	# 상점아이디
		$hashdata	= md5($MID . $_GET[bkcode] . $_GET[gdstatus] . $_GET[gddatetime] . $_GET[bkmemo4]);	# hashdata 생성

		$out = readurl("http://bankmatch.godo.co.kr/sock_update.php?MID={$MID}&{$query}&hashdata={$hashdata}");

		if ( preg_match("/^true\|/i",$out) ) // 성공
		{
			echo "<b>{subject} 수정 <font color=0077B5>(결과: 처리성공!)</font></b>";
		}
		else // 실패
		{
			# [0] 결과 메시지, [..] 오류 메시지
			$out = preg_replace("/^false[ |]*-[ |]*/i", "", $out);
			header("Status: <b>{subject} 수정 <font color=0077B5>(결과: 처리실패!)</font></b>" . (trim($out) ? "^{$out}" : ""), true, 400);
			echo "";
			exit;
		}

		break;
}

?>