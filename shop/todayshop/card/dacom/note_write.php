<?php
include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.dacom.php";

	// return value
	// true  : ��������� ����
	// false : ��������� ����

	function write_success($noti){
		global $db, $r_step, $r_step2, $sess, $r_settlekind, $cfg;

		//������ ���� log����� �˴ϴ�. log path���� �� dbó����ƾ�� �߰��Ͽ� �ֽʽÿ�.
		$log_noti = array_merge( array( 'log_date' => date('Y-m-d H:i:s') ), $noti );
		write_log(dirname(__FILE__) . "/../../../log/dacom_write_success_" . date('Ym') . ".log", $log_noti);

		$ordno = $noti[oid];
		$settlelog = addslashes(settlelog($noti));

		### ���� ���� ����
		$step = 1;
		$qrc1 = "cyn='y', cdt=now(), cardtno='".$noti['transaction']."',";
		$qrc2 = "cyn='y',";

		$modeMail = 1;
		### ������� ������ �������� ����
		if ($noti[paytype] == 'SC0040' && $noti[cflag] != 'I'){
			$vAccount = "vAccount='{$noti[financename]} {$noti[accountnumber]}',";
			$step = 0; $qrc1 = $qrc2 = "";
			$modeMail = 0;
		}

		### �ǵ���Ÿ ����
		$escrowyn = ( $noti[useescrow] == 'Y' ? "y" : "n" );
		$cashreceipt = ( trim($noti[receiptnumber]) ? $noti[receiptnumber] : '' );
		$pre = $db->fetch("select step2, emoney, m_no from ".GD_ORDER." where ordno='$ordno'");
		$db->query("update ".GD_ORDER." set step='$step', step2='', $qrc1 escrowyn='$escrowyn', $vAccount settlelog=concat(ifnull(settlelog,''),'$settlelog'), cashreceipt='$cashreceipt' where ordno='$ordno'");
		$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

		### �ֹ��α� ����
		orderLog($ordno,$r_step2[$pre[step2]]." > ".$r_step[$step]);

		if ($noti[paytype] == 'SC0040' && $noti[cflag] != 'R');
		else {
			### ��� ó��
			setStock($ordno);

			### ��ǰ���Խ� ������ ���
			if ($pre[m_no] && $pre[emoney]){
				setEmoney($pre[m_no],-$pre[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
			}
		}

		return true;
	}

	function write_overlap($noti){
		global $db;

		//������ ���� log����� �˴ϴ�. log path���� �� dbó����ƾ�� �߰��Ͽ� �ֽʽÿ�.
		$log_noti = array_merge( array( 'log_date' => date('Y-m-d H:i:s') ), $noti );
		write_log(dirname(__FILE__) . "/../../../log/dacom_write_overlap_" . date('Ym') . ".log", $log_noti);

		$ordno = $noti[oid];
		$settlelog = addslashes(settlelog($noti));

		$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

		return true;
	}

	function write_failure($noti){
		global $db;

		//������ ���� log����� �˴ϴ�. log path���� �� dbó����ƾ�� �߰��Ͽ� �ֽʽÿ�.
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

		//������ ���� log����� �˴ϴ�. log path���� �� dbó����ƾ�� �߰��Ͽ� �ֽʽÿ�.
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

		// �����޿��� ���� value
		$tmp_log[] = "����ڵ� : {$noti[respcode]} (0000(����) �׿� ����)";
		$tmp_log[] = "������� : {$noti[respmsg]}";
		$tmp_log[] = "�ؽ��� : {$noti[hashdata]}";
		$tmp_log[] = "�ŷ���ȣ : {$noti[transaction]} (�����޺ο�)";
		$tmp_log[] = "�������̵� : {$noti[mid]}";
		$tmp_log[] = "�ֹ���ȣ : {$noti[oid]}";
		$tmp_log[] = "�ŷ��ݾ� : {$noti[amount]}";
		$tmp_log[] = "���������ڵ� : {$noti[paytype]}";
		$tmp_log[] = "�ŷ��Ͻ� : {$noti[paydate]} (�����Ͻ�/��ü�Ͻ�)";
		$tmp_log[] = "������ID : {$noti[buyerid]}";
		$tmp_log[] = "������� : {$noti[financecode]} {$noti[financename]} (ī��/����)";
		$tmp_log[] = "���� ����ũ�� ���� ���� : {$noti[useescrow]} (Y:����, N:������)";

		if ( $noti[paytype] == 'SC0010' ){ // �ſ�ī��
			$tmp_log[] = "���ι�ȣ : {$noti[authnumber]}";
			$tmp_log[] = "ī���ȣ : {$noti[cardnumber]}";
			$tmp_log[] = "�Һΰ����� : {$noti[cardperiod]}";
			$tmp_log[] = "�������Һο��� : {$noti[nointerestflag]}";
		}

		if ( $noti[paytype] == 'SC0030' ){ // ������ü
			$tmp_log[] = "������ �ֹε�Ϲ�ȣ : {$noti[pid]}";
			$tmp_log[] = "���¼������̸� : {$noti[accountowner]}";
			$tmp_log[] = "���¹�ȣ : {$noti[accountnumber]}";
		}

		if ( $noti[paytype] == 'SC0060' ){ // �޴���
			$tmp_log[] = "�޴��������� �ֹε�Ϲ�ȣ : {$noti[pid]}";
			$tmp_log[] = "�޴�����ȣ : {$noti[telno]}";
		}

		if ( $noti[paytype] == 'SC0040' ){ // �������Ա�(�������)
			$tmp_log[] = "���¹�ȣ : {$noti[accountnumber]}";
			$tmp_log[] = "�Ա��� : {$noti[payer]}";
			$tmp_log[] = "�������Ա� �÷��� : {$noti[cflag]} ('R':�����Ҵ�, 'I':�Ա�, 'C':�Ա����)";
			$tmp_log[] = "�Ա��Ѿ� : {$noti[tamount]}";
			$tmp_log[] = "���Աݾ� : {$noti[camount]}";
			$tmp_log[] = "�ԱݶǴ�����Ͻ� : {$noti[bankdate]}";
			$tmp_log[] = "�Աݼ��� : {$noti[seqno]}";
		}

		if ( $noti[paytype] == 'SC0030' || $noti[paytype] == 'SC0040' ){ // ������ü/�������Ա�(�������)
			$tmp_log[] = "���ݿ����� ���ι�ȣ : {$noti[receiptnumber]}";
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