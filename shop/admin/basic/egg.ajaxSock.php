<?

include "../lib.php";

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

switch ( $mode ){

	case "usafeRequest": # ���ں������񽺽�û ��û

		header("Content-type: text/html; charset=euc-kr");

		if ( $godo[sno] == '' || $godo[sno] == '0' )
		{
			header("Status: ���θ� ȯ�������� �������̵� ��� �ֽ��ϴ�. ������ �����ϼ���.", true, 400);
			echo "";
			exit;
		}

		/***************************************************************************************************
		*  hashdata ����
		*    - ������ ���Ἲ�� �����ϴ� �����ͷ� ��û�� �ʼ� �׸�.
		*    - godosno �� �������� md5 ������� ������ �ؽ���.
		***************************************************************************************************/

		$data[godosno]	= $godo[sno];				# �������̵�
		$data[hashdata]	= md5($godo[sno]);		# hashdata ����
		$data = array_merge ($data, $_GET);

		$out = readpost("http://www.godo.co.kr/userinterface/_usafe/sock_request.php", $data);

		if ( preg_match("/^true\|/i",$out) ) // ����
			echo 'true';
		else // ����
		{
			$out = preg_replace("/^false[ |]*-[ |]*/i", "", $out);
			header("Status: [��û ����] {$out}", true, 400);
			exit;
		}

		break;
}
?>