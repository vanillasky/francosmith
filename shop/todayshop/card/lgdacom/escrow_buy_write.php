<?php
include "../../../lib/library.php";
include "../../../conf/config.php";
//include "../../../conf/pg.lgdacom.php";
// �����̼� ������� ��� PG ���� ��ü
resetPaymentGateway();

    //	return value
	//  true  : ��������� ����
	//  false : ��������� ����

	function write_success($noti){
		global $db;

        //������ ���� log����� �˴ϴ�. log path���� �� dbó����ƾ�� �߰��Ͽ� �ֽʽÿ�.
        $log_noti = array_merge( array( 'log_date' => date('Y-m-d H:i:s') ), $noti );
	    write_log(dirname(__FILE__) . "/../../../log/dacom_escrow_write_success_" . date('Ym') . ".log", $log_noti);

    	$ordno = $noti[oid];
    	$settlelog = settlelog($noti);

		### �����Ϸ� ó��
		$db->query("update ".GD_ORDER." set settlelog=concat(if(settlelog is null,'',settlelog),'$settlelog') where ordno='$ordno'");

	    return true;
	}

	function write_failure($noti){
		global $db;

        //������ ���� log����� �˴ϴ�. log path���� �� dbó����ƾ�� �߰��Ͽ� �ֽʽÿ�.
        $log_noti = array_merge( array( 'log_date' => date('Y-m-d H:i:s') ), $noti );
	    write_log(dirname(__FILE__) . "/../../../log/dacom_escrow_write_failure_" . date('Ym') . ".log", $log_noti);

    	$ordno = $noti[oid];
    	$settlelog = settlelog($noti);

		### �����Ϸ� ó��
		$db->query("update ".GD_ORDER." set settlelog=concat(if(settlelog is null,'',settlelog),'$settlelog') where ordno='$ordno'");

	    return true;
	}

    function write_hasherr($noti) {
		global $db;

        //������ ���� log����� �˴ϴ�. log path���� �� dbó����ƾ�� �߰��Ͽ� �ֽʽÿ�.
        $log_noti = array_merge( array( 'log_date' => date('Y-m-d H:i:s') ), $noti );
	    write_log(dirname(__FILE__) . "/../../../log/dacom_escrow_write_hasherr_" . date('Ym') . ".log", $log_noti);

    	$ordno = $noti[oid];
    	$settlelog = settlelog($noti);

		### �����Ϸ� ó��
		$db->query("update ".GD_ORDER." set settlelog=concat(if(settlelog is null,'',settlelog),'$settlelog') where ordno='$ordno'");

		return true;
    }

    function settlelog($noti){
    	$ordno = $noti[oid];
    	$tmp_log = array();

		// �����޿��� ���� value
		$tmp_log[] = "������� : {$noti[txtype]} (C=����Ȯ�ΰ��, R=������ҿ�û, D=������Ұ��, N=NCó����� )";
		$tmp_log[] = "�������̵� : {$noti[mid]}";
		$tmp_log[] = "�ŷ���ȣ : {$noti[tid]} (�����޺ο�)";
		$tmp_log[] = "�ֹ���ȣ : {$noti[oid]}";
		$tmp_log[] = "�������ֹι�ȣ : {$noti[ssn]}";
		$tmp_log[] = "������IP : {$noti[ip]}";
		$tmp_log[] = "������ mac : {$noti[mac]}";
		$tmp_log[] = "�ؽ��� : {$noti[hashdata]}";
		$tmp_log[] = "��ǰ����Ű : {$noti[productid]}";
		$tmp_log[] = "����Ȯ�� ��û�Ͻ� : {$noti[resdate]}";

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

