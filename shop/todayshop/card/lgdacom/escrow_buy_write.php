<?php
include "../../../lib/library.php";
include "../../../conf/config.php";
//include "../../../conf/pg.lgdacom.php";
// 투데이샵 사용중인 경우 PG 설정 교체
resetPaymentGateway();

    //	return value
	//  true  : 결과연동이 성공
	//  false : 결과연동이 실패

	function write_success($noti){
		global $db;

        //결제에 관한 log남기게 됩니다. log path수정 및 db처리루틴이 추가하여 주십시요.
        $log_noti = array_merge( array( 'log_date' => date('Y-m-d H:i:s') ), $noti );
	    write_log(dirname(__FILE__) . "/../../../log/dacom_escrow_write_success_" . date('Ym') . ".log", $log_noti);

    	$ordno = $noti[oid];
    	$settlelog = settlelog($noti);

		### 결제완료 처리
		$db->query("update ".GD_ORDER." set settlelog=concat(if(settlelog is null,'',settlelog),'$settlelog') where ordno='$ordno'");

	    return true;
	}

	function write_failure($noti){
		global $db;

        //결제에 관한 log남기게 됩니다. log path수정 및 db처리루틴이 추가하여 주십시요.
        $log_noti = array_merge( array( 'log_date' => date('Y-m-d H:i:s') ), $noti );
	    write_log(dirname(__FILE__) . "/../../../log/dacom_escrow_write_failure_" . date('Ym') . ".log", $log_noti);

    	$ordno = $noti[oid];
    	$settlelog = settlelog($noti);

		### 결제완료 처리
		$db->query("update ".GD_ORDER." set settlelog=concat(if(settlelog is null,'',settlelog),'$settlelog') where ordno='$ordno'");

	    return true;
	}

    function write_hasherr($noti) {
		global $db;

        //결제에 관한 log남기게 됩니다. log path수정 및 db처리루틴이 추가하여 주십시요.
        $log_noti = array_merge( array( 'log_date' => date('Y-m-d H:i:s') ), $noti );
	    write_log(dirname(__FILE__) . "/../../../log/dacom_escrow_write_hasherr_" . date('Ym') . ".log", $log_noti);

    	$ordno = $noti[oid];
    	$settlelog = settlelog($noti);

		### 결제완료 처리
		$db->query("update ".GD_ORDER." set settlelog=concat(if(settlelog is null,'',settlelog),'$settlelog') where ordno='$ordno'");

		return true;
    }

    function settlelog($noti){
    	$ordno = $noti[oid];
    	$tmp_log = array();

		// 데이콤에서 받은 value
		$tmp_log[] = "결과구분 : {$noti[txtype]} (C=수령확인결과, R=구매취소요청, D=구매취소결과, N=NC처리결과 )";
		$tmp_log[] = "상점아이디 : {$noti[mid]}";
		$tmp_log[] = "거래번호 : {$noti[tid]} (데이콤부여)";
		$tmp_log[] = "주문번호 : {$noti[oid]}";
		$tmp_log[] = "구매자주민번호 : {$noti[ssn]}";
		$tmp_log[] = "구매자IP : {$noti[ip]}";
		$tmp_log[] = "구매자 mac : {$noti[mac]}";
		$tmp_log[] = "해쉬값 : {$noti[hashdata]}";
		$tmp_log[] = "상품정보키 : {$noti[productid]}";
		$tmp_log[] = "구매확인 요청일시 : {$noti[resdate]}";

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

