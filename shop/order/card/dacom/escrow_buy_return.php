<?php
// ## �� �������� �������� ���ʽÿ�. ##

// ������ html�±׳� �ڹٽ�ũ��Ʈ�� ���� ��� ������ ������ �� �����ϴ�.

//���� dbó���� write.php���� �����ϴ� �Լ� write_success(),write_failure(),write_hasherr()���� ���� ��ƾ�� �߰��Ͻø� �˴ϴ�.
//���� �� �Լ����� ���� ������ ���� log����� �˴ϴ�. ������������ ����� �����η� �°� �����Ͽ� �ּ���

//hash����Ÿ���� �´� �� Ȯ�� �ϴ� ��ƾ�� �����޿��� ���� ����Ÿ�� �´��� Ȯ���ϴ� ���̹Ƿ� �� ����ϼž� �մϴ�
//�������� ���� ���ӿ��� �ұ��ϰ� ��Ƽ �������� ������ ��Ʈ�� ���� ������ ���� hash ���� ������ �߻��� ���� �ֽ��ϴ�.
//�׷��Ƿ� hash �����ǿ� ���ؼ��� ���� �߻��� ������ �ľ��Ͽ� ��� ���� �� ��ó�� �ּž� �մϴ�.

//���������� ó���� ��쿡�� �����޿��� ������ ���� ���� ���� ��������� �ߺ��ؼ� ���� �� �����Ƿ� ������ ó���� ����Ǿ�� �մϴ�.
//�� �������� ������������ ���ο� ���� 'OK' �Ǵ� 'FAIL' �̶�� ���ڸ� ǥ���ϵ��� �Ǿ����ϴ�.
//PHP������ �ǵ����̸� error_reporting() �Լ��� �̿��Ͽ� ���� �Ŀ��� �ܼ��� ���޼����� ����� ���� �ʵ��� ���ֽʽÿ�.

	// �������� function page
	include("./escrow_buy_write.php");

	// �����޿��� ���� value
	$txtype = "";				// �������(C=����Ȯ�ΰ��, R=������ҿ�û, D=������Ұ��, N=NCó����� )
	$mid="";					// �������̵�
	$tid="";					// �������� �ο��� �ŷ���ȣ
	$oid="";					// ��ǰ��ȣ
	$ssn = "";					// �������ֹι�ȣ
	$ip = "";					// ������IP
	$mac = "";					// ������ mac
	$hashdata = "";				// ������ ���� ������
	$productid = "";			// ��ǰ����Ű
	$resdate = "";				// ����Ȯ�� ��û�Ͻ�
	$resp = false;				// ������� ��������

	$txtype = get_param("txtype");
	$mid = get_param("mid");
	$tid = get_param("tid");
	$oid = get_param("oid");
	$ssn = get_param("ssn");
	$ip = get_param("ip");
	$mac = get_param("mac");
	$hashdata = get_param("hashdata");
	$productid = get_param("productid");
	$resdate = get_param("resdate");

    $mertkey = $pg['mertkey']; //�����޿��� �߱��� ����Ű�� ������ �ֽñ� �ٶ��ϴ�.

    $hashdata2 = md5($mid.$oid.$tid.$txtype.$productid.$ssn.$ip.$mac.$resdate.$mertkey); //

	$value = array( "txtype"		=> $txtype,
					"mid"    		=> $mid,
					"tid" 			=> $tid,
                   	"oid"     		=> $oid,
					"ssn" 			=> $ssn,
					"ip"			=> $ip,
					"mac"			=> $mac,
					"resdate"		=> $resdate,
                   	"hashdata"    	=> $hashdata,
					"productid"		=> $productid,
                   	"hashdata2"  	=> $hashdata2 );

	if ($hashdata2 == $hashdata) {          //�ؽ��� ������ �����ϸ�
		$resp = write_success($value);
	} else {                                //�ؽ��� ������ �����̸�
		write_hasherr($value);
	}

   	if($resp){                              //��������� �����̸�
   		echo "OK";
   	}else{                                  //��������� �����̸�
   		echo "FAIL",$value;
   	}
?>
