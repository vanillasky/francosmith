<?php
include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.dacom.php";

	// return value
	// true  : 결과연동이 성공
	// false : 결과연동이 실패

	function write_success($noti){
		global $db, $r_step, $r_step2, $sess, $r_settlekind, $cfg;

		//결제에 관한 log남기게 됩니다. log path수정 및 db처리루틴이 추가하여 주십시요.
		$log_noti = array_merge( array( 'log_date' => date('Y-m-d H:i:s') ), $noti );
		write_log(dirname(__FILE__) . "/../../../log/dacom_write_success_" . date('Ym') . ".log", $log_noti);

		$ordno = $noti[oid];
		$settlelog = addslashes(settlelog($noti));

		### 결제 정보 저장
		$step = 1;
		$qrc1 = "cyn='y', cdt=now(), cardtno='".$noti['transaction']."',";
		$qrc2 = "cyn='y',";

		$modeMail = 1;
		### 가상계좌 결제시 계좌정보 저장
		if ($noti[paytype] == 'SC0040' && $noti[cflag] != 'I'){
			$vAccount = "vAccount='{$noti[financename]} {$noti[accountnumber]}',";
			$step = 0; $qrc1 = $qrc2 = "";
			$modeMail = 0;
		}

		### 실데이타 저장
		$escrowyn = ( $noti[useescrow] == 'Y' ? "y" : "n" );
		$cashreceipt = ( trim($noti[receiptnumber]) ? $noti[receiptnumber] : '' );
		$pre = $db->fetch("select step2, emoney, m_no from ".GD_ORDER." where ordno='$ordno'");
		$db->query("update ".GD_ORDER." set step='$step', step2='', $qrc1 escrowyn='$escrowyn', $vAccount settlelog=concat(ifnull(settlelog,''),'$settlelog'), cashreceipt='$cashreceipt' where ordno='$ordno'");
		$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

		### 주문로그 저장
		orderLog($ordno,$r_step2[$pre[step2]]." > ".$r_step[$step]);

		if ($noti[paytype] == 'SC0040' && $noti[cflag] != 'R');
		else {
			### 재고 처리
			setStock($ordno);

			### 상품구입시 적립금 사용
			if ($pre[m_no] && $pre[emoney]){
				setEmoney($pre[m_no],-$pre[emoney],"상품구입시 적립금 결제 사용",$ordno);
			}
		}

		return true;
	}

	function write_overlap($noti){
		global $db;

		//결제에 관한 log남기게 됩니다. log path수정 및 db처리루틴이 추가하여 주십시요.
		$log_noti = array_merge( array( 'log_date' => date('Y-m-d H:i:s') ), $noti );
		write_log(dirname(__FILE__) . "/../../../log/dacom_write_overlap_" . date('Ym') . ".log", $log_noti);

		$ordno = $noti[oid];
		$settlelog = addslashes(settlelog($noti));

		$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

		return true;
	}

	function write_failure($noti){
		global $db;

		//결제에 관한 log남기게 됩니다. log path수정 및 db처리루틴이 추가하여 주십시요.
		$log_noti = array_merge( array( 'log_date' => date('Y-m-d H:i:s') ), $noti );
		write_log(dirname(__FILE__) . "/../../../log/dacom_write_failure_" . date('Ym') . ".log", $log_noti);

		$ordno = $noti[oid];
		$settlelog = addslashes(settlelog($noti));

		$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$noti['transaction']."' where ordno='$ordno'");
		$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno' and istep=50");

		return true;
	}

	function write_hasherr($noti) {
		global $db;

		//결제에 관한 log남기게 됩니다. log path수정 및 db처리루틴이 추가하여 주십시요.
		$log_noti = array_merge( array( 'log_date' => date('Y-m-d H:i:s') ), $noti );
		write_log(dirname(__FILE__) . "/../../../log/dacom_write_hasherr_" . date('Ym') . ".log", $log_noti);

		$ordno = $noti[oid];
		$settlelog = addslashes(settlelog($noti));

		$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$noti['transaction']."' where ordno='$ordno'");
		$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno' and istep=50");

		return true;
	}

	function settlelog($noti){
		$ordno = $noti[oid];
		$tmp_log = array();

		// 데이콤에서 받은 value
		$tmp_log[] = "결과코드 : {$noti[respcode]} (0000(성공) 그외 실패)";
		$tmp_log[] = "결과내용 : {$noti[respmsg]}";
		$tmp_log[] = "해쉬값 : {$noti[hashdata]}";
		$tmp_log[] = "거래번호 : {$noti[transaction]} (데이콤부여)";
		$tmp_log[] = "상점아이디 : {$noti[mid]}";
		$tmp_log[] = "주문번호 : {$noti[oid]}";
		$tmp_log[] = "거래금액 : {$noti[amount]}";
		$tmp_log[] = "결제수단코드 : {$noti[paytype]}";
		$tmp_log[] = "거래일시 : {$noti[paydate]} (승인일시/이체일시)";
		$tmp_log[] = "구매자ID : {$noti[buyerid]}";
		$tmp_log[] = "결제기관 : {$noti[financecode]} {$noti[financename]} (카드/은행)";
		$tmp_log[] = "최종 에스크로 적용 여부 : {$noti[useescrow]} (Y:적용, N:미적용)";

		if ( $noti[paytype] == 'SC0010' ){ // 신용카드
			$tmp_log[] = "승인번호 : {$noti[authnumber]}";
			$tmp_log[] = "카드번호 : {$noti[cardnumber]}";
			$tmp_log[] = "할부개월수 : {$noti[cardperiod]}";
			$tmp_log[] = "무이자할부여부 : {$noti[nointerestflag]}";
		}

		if ( $noti[paytype] == 'SC0030' ){ // 계좌이체
			$tmp_log[] = "예금주 주민등록번호 : -";
			$tmp_log[] = "계좌소유주이름 : {$noti[accountowner]}";
			$tmp_log[] = "계좌번호 : {$noti[accountnumber]}";
		}

		if ( $noti[paytype] == 'SC0060' ){ // 휴대폰
			$tmp_log[] = "휴대폰소지자 주민등록번호 : -";
			$tmp_log[] = "휴대폰번호 : {$noti[telno]}";
		}

		if ( $noti[paytype] == 'SC0040' ){ // 무통장입금(가상계좌)
			$tmp_log[] = "계좌번호 : {$noti[accountnumber]}";
			$tmp_log[] = "입금인 : {$noti[payer]}";
			$tmp_log[] = "무통장입금 플래그 : {$noti[cflag]} ('R':계좌할당, 'I':입금, 'C':입금취소)";
			$tmp_log[] = "입금총액 : {$noti[tamount]}";
			$tmp_log[] = "현입금액 : {$noti[camount]}";
			$tmp_log[] = "입금또는취소일시 : {$noti[bankdate]}";
			$tmp_log[] = "입금순서 : {$noti[seqno]}";
		}

		if ( $noti[paytype] == 'SC0030' || $noti[paytype] == 'SC0040' ){ // 계좌이체/무통장입금(가상계좌)
			$tmp_log[] = "현금영수증 승인번호 : {$noti[receiptnumber]}";
		}

		$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";
		return $settlelog;
	}

	function write_log($file, $noti) {
		$fp = fopen($file, "a+");
		ob_start();
		print_r($noti);
		$msg = ob_get_contents();
		ob_end_clean();
		fwrite($fp, $msg);
		fclose($fp);
		@chmod( $file, 0707 );
	}


	function get_param($name){
		global $HTTP_POST_VARS, $HTTP_GET_VARS;
		if (!isset($HTTP_POST_VARS[$name]) || $HTTP_POST_VARS[$name] == "") {
			if (!isset($HTTP_GET_VARS[$name]) || $HTTP_GET_VARS[$name] == "") {
				return false;
			} else {
				 return $HTTP_GET_VARS[$name];
			}
		}
		return $HTTP_POST_VARS[$name];
	}

?>