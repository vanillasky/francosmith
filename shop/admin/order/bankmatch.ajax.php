<?

include "../lib.php";

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

switch ( $mode ){

	case "accountList": # �Աݳ��� ��û

		header("Content-type: text/html; charset=euc-kr");

		if ( $godo[sno] == '' || $godo[sno] == '0' )
		{
			header("Status: ���θ� ȯ�������� �������̵� ��� �ֽ��ϴ�. ������ �����ϼ���.", true, 400);
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
		*  hashdata ����
		*    - ������ ���Ἲ�� �����ϴ� �����ͷ� ��û�� �ʼ� �׸�.
		*    - MID �� �������� md5 ������� ������ �ؽ���.
		***************************************************************************************************/

		$MID		= sprintf("GODO%05d",$godo[sno]);	# �������̵�
		$hashdata	= md5($MID);						# hashdata ����

		$out = readurl("http://bankmatch.godo.co.kr/sock_listing.php?MID={$MID}&{$query}&hashdata={$hashdata}");

		if ( !preg_match("/^false[ |]*/i",$out) ) // ����
			echo $out; // JSON ���
		else // ����
		{
			$out = preg_replace("/^false[ |]*-[ |]*/i", "", $out);
			header("Status: [�ε� ����] {$out}", true, 400);
			echo "";
			exit;
		}

		break;

	case "bankMatching": # ��(Matching) ��û

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
		*    - MID �� �������� md5 ������� ������ �ؽ���.
		***************************************************************************************************/

		$MID		= sprintf("GODO%05d",$godo[sno]);	# �������̵�
		$hashdata	= md5($MID);						# hashdata ����

		$out = readurl("http://bankmatch.godo.co.kr/sock_matching.php?MID={$MID}&bkdate[0]={$_GET[bkdate][0]}&bkdate[1]={$_GET[bkdate][1]}&hashdata={$hashdata}");

		if ( preg_match("/^true\|/i",$out) ) // ����
		{
			# [0] ��� �޽���, [1] ������( [..] ��Ī�� �ֹ���ȣ�� )
			$out = preg_replace("/^true\|/i", "", $out);
			if ( $out ) $datas = explode("|", $out);
			echo "<b>��(Matching) <font color=0077B5>(���: ó������!)</font></b>" . (count($datas) ? "^" . implode("|", $datas) : "");
		}
		else // ����
		{
			# [0] ��� �޽���, [..] ���� �޽���
			$out = preg_replace("/^false[ |]*-[ |]*/i", "", $out);
			header("Status: <b>��(Matching) <font color=0077B5>(���: ó������!)</font></b>" . (trim($out) ? "^{$out}" : ""), true, 400);
			echo "";
			exit;
		}

		break;

	case "bankUpdate": # �Աݳ��� ����

		header("Content-type: text/html; charset=euc-kr");

		if ( $godo[sno] == '' || $godo[sno] == '0' )
		{
			header("Status: ���θ� ȯ�������� �������̵� ��� �ֽ��ϴ�. ������ �����ϼ���.", true, 600);
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
		*  hashdata ����
		*    - ������ ���Ἲ�� �����ϴ� �����ͷ� ��û�� �ʼ� �׸�.
		*    - MID �� �������� md5 ������� ������ �ؽ���.
		***************************************************************************************************/

		$MID		= sprintf("GODO%05d",$godo[sno]);	# �������̵�
		$hashdata	= md5($MID . $_GET[bkcode] . $_GET[gdstatus] . $_GET[gddatetime] . $_GET[bkmemo4]);	# hashdata ����

		$out = readurl("http://bankmatch.godo.co.kr/sock_update.php?MID={$MID}&{$query}&hashdata={$hashdata}");

		if ( preg_match("/^true\|/i",$out) ) // ����
		{
			echo "<b>{subject} ���� <font color=0077B5>(���: ó������!)</font></b>";
		}
		else // ����
		{
			# [0] ��� �޽���, [..] ���� �޽���
			$out = preg_replace("/^false[ |]*-[ |]*/i", "", $out);
			header("Status: <b>{subject} ���� <font color=0077B5>(���: ó������!)</font></b>" . (trim($out) ? "^{$out}" : ""), true, 400);
			echo "";
			exit;
		}

		break;
}

?>