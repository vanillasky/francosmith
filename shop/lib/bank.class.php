<?

/**
 * Bank class
 * 계좌통합 자동입금확인 송수신 클래스
 */

class Bank
{
	var $act, $ordno, $MID;

	function Bank($act, $ordno)
	{
		$this->act = $act;
		$this->ordno = $ordno;
		$this->getMid();

		### 송신전 상점아이디 체크(로그파일 생성 방지)
		if ( $this->act == 'send' )
		{
			ob_start();
			$result = readurl("http://bankmatch.godo.co.kr/sock_ismid.php?MID={$this->MID}&hashdata=" . md5($this->MID));
			if ( $result != 'true' ) return;
			ob_end_clean();
		}

		$this->log( "START" );
		$this->log( "DEBUG [__datetime__] <__ordno__> USER_IP:{$_SERVER['REMOTE_ADDR']}" );

		$this->$act();

		$this->log( "END" );
	}

	### 송신
	function send()
	{
		$this->log( "DEBUG [__datetime__] <__ordno__> MID:{$this->MID}" );
		if ( $this->MID == '' ) return;

		### 주문건 체크
		ob_start();
		$data = $GLOBALS[db]->fetch("SELECT o.ordno, b.bank, b.account, o.bankSender, o.settleprice, o.settlelog FROM ".GD_ORDER." as o LEFT JOIN ".GD_LIST_BANK." as b ON o.bankAccount=b.sno WHERE ordno='{$this->ordno}'", "ASSOC");
		if ( $this->err( ob_get_clean() ) ) return;

		$data[account] = str_replace(array("-", " "), "", $data[account]);	# 계좌

		$this->log( "DEBUG [__datetime__] <__ordno__> ORDER_DATA:[\nordno:{$data[ordno]}\nbank:{$data[bank]}\naccount:{$data[account]}\nbankSender:{$data[bankSender]}\nsettleprice:{$data[settleprice]}\n]" );

		if ( $data[ordno] == '' || $data[bank] == '' || $data[account] == '' ) return;

		/***************************************************************************************************
		*  hashdata 생성
		*    - 데이터 무결성을 검증하는 데이터로 요청시 필수 항목.
		*    - MID+ordno+bank+account+settleprice+bankSender 를 조합한후 md5 방식으로 생성한 해쉬값.
		***************************************************************************************************/

		$MID			= $this->MID;				# 상점아이디
		$ordno			= trim($data[ordno]);		# 주문번호
		$bank			= trim($data[bank]);		# 은행
		$account			= trim($data[account]);		# 계좌
		$bankSender		= trim($data[bankSender]);	# 입금자
		$settleprice		= trim($data[settleprice]);	# 금액
		$hashdata		= md5($MID . $ordno . $bank . $account . $bankSender . $settleprice);	# hashdata 생성

		$urlQuery			= "MID={$MID}&ordno={$ordno}&bank={$bank}&account={$account}&bankSender={$bankSender}&settleprice={$settleprice}&hashdata={$hashdata}";
		$this->log( "DEBUG [__datetime__] <__ordno__> URL_QUERY:{$urlQuery}" );

		$result = readurl("http://bankmatch.godo.co.kr/sock_insert.php?{$urlQuery}");

		if ( preg_match("/\n|\r/", $result) > 0 )
			$this->log( "DEBUG [__datetime__] <__ordno__> RESULT:[\n{$result}\n]" );
		else
			$this->log( "DEBUG [__datetime__] <__ordno__> RESULT:{$result}" );

		if ( preg_match("/^true\|/i",$result) ) // 성공
		{
			ob_start();
			$data[settlelog] .= "계좌통합 : 무통장주문정보 전송 (" . date('Y-m-d_H:i:s') . ")\n";
			$GLOBALS[db]->query("UPDATE ".GD_ORDER." SET settlelog='{$data[settlelog]}' WHERE ordno='{$this->ordno}'");
			if ( $this->err( ob_get_clean() ) ) return;
		}
	}

	### 수신
	function receive()
	{
		$this->log( "DEBUG [__datetime__] <__ordno__> TRANS_ARGUMENT:[\n" . str_replace( array("=", "&"), array(":", "\n"), getVars($except='', $_GET) ) . "\n]" );

		$this->log( "DEBUG [__datetime__] <__ordno__> MID:{$this->MID}" );
		if ( $this->MID == '' )
		{
			echo "false - 쇼핑몰 환경정보에 상점아이디 비어 있음";
			return;
		}

		### 해쉬값 검증
		$hashdata	= $_GET[hashdata];	# 해쉬값
		$hashdata2	= md5($this->MID . $_GET[ordno] . $_GET[bkcode] . $_GET[GOcode]);

		if ( $hashdata2 != $hashdata ) // 해쉬값 검증 실패
		{
			$this->log( "DEBUG [__datetime__] <__ordno__> HASHDATA:데이터 무결성 실패" );
			echo "false - 데이터 무결성 실패";
			return;
		}

		### 주문건 체크
		ob_start();
		$data = $GLOBALS[db]->fetch("select ordno, step, step2, settlelog from ".GD_ORDER." where ordno='$_GET[ordno]'");
		if ( $this->err( ob_get_clean() ) ) return;

		$status = $GLOBALS[r_stepi][$data[step]][$data[step2]];
		$this->log( "DEBUG [__datetime__] <__ordno__> ORDER_DATA:[\nordno:{$data[ordno]}\nstep:{$data[step]}\nstep2:{$data[step2]}\nstatus:{$status}\n]" );

		if ( $data[ordno] == '' )
		{
			echo "false - 쇼핑몰에 주문정보가 없음";
			return;
		}

		if ( $data['step'] == '0' && $data['step2'] == '44' ){
			echo "false - 취소된 주문임";
			return;
		}

		if ( $data[step] != '0' ){
			echo "before";
			return;
		}

		### 진행상황별 처리
		ob_start();
		@ctlStep($_GET[ordno],1,'stock');
		if ( $this->err( ob_get_clean() ) ) return;

		### 결제메모 남기기
		ob_start();
		$data[settlelog] .= "계좌통합 : 무통장주문 입금확인 처리 (" . date('Y-m-d_H:i:s') . ")\n";
		$GLOBALS[db]->query("UPDATE ".GD_ORDER." SET settlelog='{$data[settlelog]}' WHERE ordno='{$_GET[ordno]}'");
		if ( $this->err( ob_get_clean() ) ) return;

		### 성공메시지 출력
		$this->log( "INFO [__datetime__] <__ordno__> RESULT:성공" );
		echo 'true';
	}

	### 주문상태기준으로 필터
	function filterOrderStep()
	{
		$this->log( "DEBUG [__datetime__] <__ordno__> TRANS_ARGUMENT:[\n" . str_replace( array("=", "&"), array(":", "\n"), getVars($except='', $_GET) ) . "\n]" );

		$this->log( "DEBUG [__datetime__] <__ordno__> MID:{$this->MID}" );
		if ( $this->MID == '' )
		{
			echo "false - 쇼핑몰 환경정보에 상점아이디 비어 있음";
			return;
		}

		### 해쉬값 검증
		$hashdata	= $_GET['hashdata'];	# 해쉬값
		$hashdata2	= md5($this->MID);

		if ( $hashdata2 != $hashdata ) // 해쉬값 검증 실패
		{
			$this->log( "DEBUG [__datetime__] <__ordno__> HASHDATA:데이터 무결성 실패" );
			echo "false - 데이터 무결성 실패";
			return;
		}

		### 필터링
		ob_start();
		$resOrdno = array();
		$tmp = explode('|', $_GET['ordnos']);
		foreach($tmp as $ordno)
		{
			$data = $GLOBALS['db']->fetch("select ordno, step, step2 from ".GD_ORDER." where ordno='$ordno'");
			if ( $data['ordno'] != '' && $data['step'] == '0' && $data['step2'] == '0' ) $resOrdno[] =  $data['ordno'];
		}
		if ( $this->err( ob_get_clean() ) ) return;

		### 성공메시지 출력
		$this->log( "INFO [__datetime__] <__ordno__> RESULT:".implode('|', $resOrdno) );
		echo implode('|', $resOrdno);
	}

	### ERROR
	function err( $obOut='' )
	{
		if ( mysql_errno() )
		{
			$this->log( "DEBUG [__datetime__] <__ordno__> DB_ERROR:[" . mysql_errno() . "] " . mysql_error() );
			if ( $this->act == 'receive' ) echo "false - 데이타베이스 실행 실패"; # 수신경우만
			return true;
		}
		else if ( $obOut )
		{
			if ( preg_match("/<span style='font:8pt tahoma'><b>\[ERROR\]<\/b>/i", $obOut) ) return false; # 메일관련 메시지는 통과
			$obOut = strip_tags( str_replace( "<br>", "\n", $obOut ) );
			$tmp_o = explode( "\n", $obOut );
			$tmp_n = array();
			foreach ( $tmp_o as $str )
				if ( trim($str) != '' ) $tmp_n[] = trim($str);
			$obOut = implode( "\n", $tmp_n );
			if ( count($tmp_n) > 1 ) $obOut = "[\n$obOut\n]";

			$this->log( "DEBUG [__datetime__] <__ordno__> CODE_ERROR:" . $obOut );
			if ( $this->act == 'receive' ) echo "false - 코드 실행 실패"; # 수신경우만
			return true;
		}
	}

	### 상점아이디
	function getMid()
	{
		$file = dirname(__FILE__)."/../conf/godomall.cfg.php";
		if (is_file($file)){
			$file = file($file);
			$godo = decode($file[1],1);
		}
		if ( $godo[sno] > 0 ) $this->MID= sprintf("GODO%05d",$godo[sno]);
	}

	### 로그 남기기
	function log( $msg )
	{
		if ( $msg == 'START' ) $msg = "INFO  [__datetime__] <__ordno__> START";
		else if ( $msg == 'END' ) $msg = "INFO  [__datetime__] <__ordno__> END";

		$msg = str_replace( array('__datetime__', '__ordno__'), array(date('Y-m-d_H:i:s:B'), $this->ordno), $msg ) . "\n";
		error_log($msg, 3, $tmp = dirname(__FILE__) . "/../log/bank" . ucfirst($this->act) . "_" . date('Ymd') . ".log");
		@chmod( $tmp, 0707 );
	}


}

?>