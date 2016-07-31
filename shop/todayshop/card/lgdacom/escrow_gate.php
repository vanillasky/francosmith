<?
include "../../../lib/library.php";
include "../../../conf/config.php";
//include "../../../conf/pg.$cfg[settlePg].php";
include "../../../conf/pg.escrow.php";

// �����̼� ������� ��� PG ���� ��ü
resetPaymentGateway();

// �ֹ�����
$ordno = $_GET[ordno];
$query = "
select
	orddt,deliverycomp,ddt,deliverycode,nameOrder,mobileOrder,confirmdt,nameReceiver
from
	".GD_ORDER." a
	left join ".GD_LIST_DELIVERY." b on a.deliveryno = b.deliveryno
where
	a.ordno = '$ordno'
";
$data = $db->fetch($query);

// �߼������� ��ϵǸ� �������� �Ʒ� 15���ù�翡 ���������� �ڵ����� Ȯ����
$compcode = array();
$compcode['�������']		= 'KE'; // �������
$compcode['�����ù�']		= 'LG'; // �����ù�
$compcode['�����ù�']		= 'AJ'; // �����ù�
$compcode['�ٷο�ĸ']		= 'YC'; // ���ο�ĸ
$compcode['��ü���ù�']		= 'PO'; // ��ü���ù�
$compcode['�����ù�']		= 'EZ'; // �����ù�
$compcode['Ʈ���']			= 'TN'; // Ʈ���
$compcode['�����ù�']		= 'HJ'; // �����ù�
$compcode['�����ù�']		= 'HD'; // �����ù�
$compcode['�ѹ̸��ù�']		= 'FE'; // �ѹ̸��ù�
//$compcode['']				= 'BE'; // Bell Express
//$compcode['']				= 'CJ'; // CJ GLS
$compcode['�Ｚ�ù�HTH']	= 'SS'; // HTH
$compcode['KGB�ù�']		= 'KB'; // KGB�ù�
$compcode['KT������']		= 'KT'; // KT�������ù�

//**************************//
// ��۰�� �۽� PHP
// �߼۰� �������� �� �Ѱ����� �۽�.
//**************************//

if($pg['serviceType'] == "test"){
	$service_url = "http://pgweb.dacom.net:7085/pg/wmp/mertadmin/jsp/escrow/rcvdlvinfo.jsp";	// �׽�Ʈ��
}else{
	$service_url = "http://pgweb.dacom.net/pg/wmp/mertadmin/jsp/escrow/rcvdlvinfo.jsp";		// ���񽺿�
}

$datasize		= 1;													// ������ �����ϴ� ��������
$mid			= (("test" == $pg['serviceType'])?"t":"").$pg['id'];	// ����ID
$mertkey		= $pg['mertkey'];										// ����Ű
$oid			= $ordno;												// �ֹ���ȣ
$productid		= "";													// ��ǰID
$orderdate		= date_form( $data['orddt'] );							// �ֹ�����
$dlvtype		= "03";													// ��ϳ��뱸��(03:�߼�, 01:���� �� ����)

if ( "03" == $dlvtype )
{
	// �߼�����(�������� ��ǰ�� ��۾�ü�� ���Ͽ� �����ο��� �߼��� ����)

	$dlvdate		= date_form( $data['ddt'] );						// �߼�����
	$dlvcompcode	= $compcode[ $data['deliverycomp'] ];				// ���ȸ���ڵ�
	$dlvcomp		= str_replace( " ", "||", $data['deliverycomp'] );	// ���ȸ���
	$dlvno			= $data['deliverycode'];							// ������ȣ
	$dlvworker		= $data['nameOrder'];								// ����ڸ�
	$dlvworkertel	= $data['mobileOrder'];								// �������ȭ��ȣ

	if ( $dlvcompcode == '' ){											// ��������(�� 15����� �ù�� ���� �Ǵ� ���� ���� ���)
		$rcvdate		= date_form( $data['confirmdt'] );				// �Ǽ�������
		$rcvname		= $data["nameReceiver"];						// �Ǽ����θ�
		$rcvrelation	= '����';										// ����
	}

	$hashdata = md5($mid.$oid.$dlvdate.$dlvcompcode.$dlvno.$mertkey);	// ����Ű
}
else if ( "01" == $dlvtype )
{
	// ��������(��ǰ�� ������(�Ǵ� �븮��)�� ������ ������ ����)
	$rcvdate		= date_form( $data['confirmdt'] );					// �Ǽ�������
	$rcvname		= $data['nameReceiver'];							// �Ǽ����θ�
	$rcvrelation	= '����';											// ����

	$hashdata = md5($mid.$oid.$dlvtype.$rcvdate.$mertkey);				// ����Ű
}


// �������� ��۰������������� ȣ���Ͽ� ������������
/*
*	�Ʒ� URL �� ȣ��� �Ķ������ ���� ������ �߻��ϸ� �ش� URL�� ������������ ȣ��˴ϴ�.
*	��ۻ����� �Ķ���ͷ� ��Ͻ� ������ "||" ���� �����Ͽ� �ֽñ� �ٶ��ϴ�.
*/
$str_url = $service_url."?mid=$mid&oid=$oid&productid=$productid&orderdate=$orderdate&dlvtype=$dlvtype&rcvdate=$rcvdate&rcvname=$rcvname&rcvrelation=$rcvrelation&dlvdate=$dlvdate&dlvcompcode=$dlvcompcode&dlvno=$dlvno&dlvworker=$dlvworker&dlvworkertel=$dlvworkertel&hashdata=$hashdata";

/*
*	curl ���
*	php 4.3 ���� �̻󿡼� ��밡��
*/

$ch = curl_init();

curl_setopt ($ch, CURLOPT_URL, $str_url);
curl_setopt ($ch, CURLOPT_COOKIEJAR, COOKIE_FILE_PATH);
curl_setopt ($ch, CURLOPT_COOKIEFILE, COOKIE_FILE_PATH);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

$fp = curl_exec ($ch);

if(curl_errno($ch)){
	// ������н� DB ó�� ���� �߰�
	echo '<script>alert("���Ȯ�ο�û�� ������ó���Ǿ����ϴ�. ��õ��ϼ���.\\n' . str_replace( array("\n","\r"), "", curl_error($ch) ) . '");</script>'; // ������ó�� �Ǿ����� DB ó��
}else{
	if(trim($fp)=="OK"){
		// ����ó���Ǿ����� DB ó��
		$db->query("update ".GD_ORDER." set escrowconfirm=1 where ordno='$ordno'");
		echo '<script>alert("���Ȯ�ο�û�� ����ó���Ǿ����ϴ�.");</script>'; // ����ó���Ǿ����� DB ó��
	}else{
		// ������ó�� �Ǿ����� DB ó��
		echo '<script>alert("���Ȯ�ο�û�� ������ó���Ǿ����ϴ�. ��õ��ϼ���.\\n' . str_replace( array("\n","\r"), "", curl_error($ch) ) . '");</script>'; // ������ó�� �Ǿ����� DB ó��
	}
}
curl_close($ch);

//**********************************
// �Ʒ� �ִ� �״�� ����Ͻʽÿ�.
//**********************************
function get_param($name)
{
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

### YYYYMMDDHHSS ���� ����
function date_form( $dt ){
	$dt = str_replace( array( "-", ":", " " ), "", $dt );
	$dt = substr( $dt, 0, -2 );
	return $dt;
}
?>