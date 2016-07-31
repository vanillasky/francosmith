<?

include "../lib.php";
include "../../lib/openmarket.class.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

switch ($_GET[mode]){

	case "quickRegister":

		header("Content-type: text/html; charset=euc-kr");

		### ��ǰ����
		$goodsno = $_GET['goodsno'];
		$data = $db->fetch("select * from ".GD_GOODS." where goodsno='$goodsno'");
		$goodsnm = strip_tags($data['goodsnm']);
		$data = array_map("stripslashes",$data);
		$data = array_map("addslashes",$data);

		### �����Ѱ���
		$oSend = new openmarketSend();
		$out = $oSend->isExists();
		if ($out[1] != 'true'){
			header("Status: ��ǰ�� : <b>{$goodsnm} <font color=0077B5>(���: ��Ͻ���!)</b></font>^" . $oSend->isExistsMsg, true, 400);
			echo "";
			exit;
		}

		### ������ ����
		if (isset($_GET['goodsnm']) === true) $data['goodsnm'] = $_GET['goodsnm'];
		if (isset($_GET['goodscd']) === true) $data['goodscd'] = $_GET['goodscd'];
		if (isset($_GET['maker']) === true) $data['maker'] = $_GET['maker'];
		if (isset($_GET['shortdesc']) === true) $data['shortdesc'] = $_GET['shortdesc'];

		## ������
		if (isset($_GET['origin']) === true) $data['origin'] = $_GET['origin'];
		if (in_array($data['origin'], array('����', '�ѱ�', '���ѹα�')) === true){
			$data['origin_kind'] = 1;
		}
		else {
			$data['origin_kind'] = 2;
			$data['origin_name'] = $data['origin'];
		}

		## �귣���
		if (isset($_GET['brandnm']) === true){
			$data['brandnm'] = $_GET['brandnm'];
		}
		else {
			list($data['brandnm']) = $db->fetch("select brandnm from ".GD_GOODS_BRAND." where sno='{$data['brandno']}'");
		}

		## ���¸��� �з��ڵ�
		if (isset($_GET['category']) === true){
			$data['category'] = $_GET['category'];
		}
		else {
			list($data['category']) = $db->fetch("select openmarket from ".GD_GOODS_LINK." as a left join ".GD_CATEGORY." as b on a.category = b.category  where openmarket!='' and goodsno='{$data['goodsno']}' order by a.category limit 1");
		}

		## ����
		list($price, $data['consumer']) = $db->fetch("select price, consumer from ".GD_GOODS_OPTION." where goodsno='{$data['goodsno']}' and link");
		if (isset($_GET['price']) === true) $data['price'] = $_GET['price'];
		else $data['price'] = $price;

		### �ʼ������� ����
		$needs = $oSend->verifyData($data);
		if (count($needs)){
			header("Status: ��ǰ�� : <b>{$goodsnm} <font color=0077B5>(���: ��Ͻ���!)</b></font>^". implode('^', $needs), true, 400);
			echo "";
			exit;
		}

		### ��ǰ������ ����
		$query = "
		insert into ".GD_OPENMARKET_GOODS." set
			goodsno			= '{$goodsno}',
			regdt			= now(),
			goodsnm			= '{$data['goodsnm']}',
			goodscd			= '{$data['goodscd']}',
			maker			= '{$data['maker']}',
			origin_kind		= '{$data['origin_kind']}',
			origin_name		= '{$data['origin_name']}',
			brandnm			= '{$data['brandnm']}',
			tax				= '{$data['tax']}',
			shortdesc		= '{$data['shortdesc']}',
			longdesc		= '{$data['longdesc']}',
			img_m			= '{$data['img_m']}',
			category		= '{$data['category']}',
			max_count		= '0',
			optnm			= '{$data['optnm']}',
			price			= '{$data['price']}',
			consumer		= '{$data['consumer']}',
			runout			= '{$data['runout']}',
			usestock		= '{$data['usestock']}',
			age_flag		= 'N'
		";
		$db->query($query);

		### �ʼ��ɼ� ����
		$incept = array("goodsno","opt1","opt2","stock");
		$res = $db->query("select * from ".GD_GOODS_OPTION." where goodsno='{$goodsno}'");
		$optCnt = $db->count_($res);
		while ($odata=$db->fetch($res,1)){ unset($qr);
			if ($odata){
				if (isset($_GET['stock']) === true && $optCnt == 1){
					$odata['stock'] = $_GET['stock'];
				}
				foreach ($odata as $k=>$v){
					if (in_array($k,$incept)) $qr[] = "$k='".addslashes($v)."'";
				}
				$query = "insert into ".GD_OPENMARKET_GOODS_OPTION." set ".implode(",",$qr);
				$db->query($query);
			}
		}

		### ������ ����
		ob_start();
		$res = $oSend->putGoods($goodsno, 'register');
		ob_end_clean();

		echo "��ǰ�� : <b>{$goodsnm} <font color=0077B5>(���: ��ϼ���!)</b></font>";
		exit;
		break;

	case "quickModify":

		header("Content-type: text/html; charset=euc-kr");

		$goodsno = $_GET['goodsno'];
		$data = $db->fetch("select * from ".GD_OPENMARKET_GOODS." where goodsno='$goodsno'");
		$goodsnm = strip_tags($data['goodsnm']);
		$data = array_map("stripslashes",$data);
		$data = array_map("addslashes",$data);

		### �����Ѱ���
		$oSend = new openmarketSend();
		$out = $oSend->isExists();
		if ($out[1] != 'true'){
			header("Status: ��ǰ�� : <b>{$goodsnm} <font color=0077B5>(���: ��Ͻ���!)</b></font>^" . $oSend->isExistsMsg, true, 400);
			echo "";
			exit;
		}

		### ������
		if (in_array($_GET['origin'], array('����', '�ѱ�', '���ѹα�')) === true){
			$_GET['origin_kind'] = 1;
			$_GET['origin_name'] = '';
		}
		else {
			$_GET['origin_kind'] = 2;
			$_GET['origin_name'] = $_GET['origin'];
		}

		### �ʼ������� ����
		$needs = $oSend->verifyData($data);
		if (count($needs)){
			header("Status: ��ǰ�� : <b>{$goodsnm} <font color=0077B5>(���: ��Ͻ���!)</b></font>^". implode('^', $needs), true, 400);
			echo "";
			exit;
		}

		### ��ǰ������ ����
		$query = "
		update ".GD_OPENMARKET_GOODS." set
			moddt			= now(),
			goodsnm			= '{$_GET['goodsnm']}',
			goodscd			= '{$_GET['goodscd']}',
			maker			= '{$_GET['maker']}',
			origin_kind		= '{$_GET['origin_kind']}',
			origin_name		= '{$_GET['origin_name']}',
			brandnm			= '{$_GET['brandnm']}',
			shortdesc		= '{$_GET['shortdesc']}',
			category		= '{$_GET['category']}',
			price			= '{$_GET['price']}'
		where
			goodsno = '$goodsno'
		";
		$res = $db->query($query);

		### �ʼ��ɼ� ����
		list($optCnt) = $db->fetch("select count(*) from ".GD_OPENMARKET_GOODS_OPTION." where goodsno='{$goodsno}'");
		if ($optCnt == 1) $db->query("update ".GD_OPENMARKET_GOODS_OPTION." set stock='{$_GET['stock']}' where goodsno='{$goodsno}'");

		### ������ ����
		ob_start();
		$res = $oSend->putGoods($goodsno, 'modify');
		ob_end_clean();

		echo "��ǰ�� : <b>{$goodsnm} <font color=0077B5>(���: ". ($res ? '��������' : '��������') ."!)</b></font>";
		exit;
		break;

	case "delGoods":

		$goodsno = $_GET['goodsno'];
		$data = $db->fetch("select * from ".GD_OPENMARKET_GOODS." where goodsno='{$goodsno}'");
		$div = explode("|",$data['img_m']);
		foreach ($div as $v){
			if (preg_match('/^openmarket_/', $v) > 0) @unlink("../../data/goods/".$v);
		}
		$db->query("delete from ".GD_OPENMARKET_GOODS." where goodsno='{$goodsno}'");
		$db->query("delete from ".GD_OPENMARKET_GOODS_OPTION." where goodsno='{$goodsno}'");

		### �����뷮 ���
		setDu('goods');
		break;

	case "srchCategory": # ���¸��� �з��˻� ��û

		header("Content-type: text/xml; charset=euc-kr");

		if ( $_GET[srchName] == '' )
		{
			header("Status: �˻�� �Է��ϼž� �մϴ�.", true, 400);
			echo "";
			exit;
		}

		$out = readurl("http://godosiom.godo.co.kr/sock_getCategory.php?srchName={$_GET[srchName]}");
		echo $out;
		exit;
		break;

	case "stepCategory": # ���¸��� �з� ��û

		header("Content-type: text/xml; charset=euc-kr");

		$out = readurl("http://godosiom.godo.co.kr/sock_getCategory.php?callCate={$_GET[callCate]}");
		echo $out;
		exit;
		break;

	case "saveCategory": # ���¸��� �з����� ��û

		if ( $_GET[catno] == '' )
		{
			header("Status: ������ ���¸��� �з��� �����ϴ�.", true, 400);
			echo "";
			exit;
		}

		if ( $_GET[category] == '' )
		{
			header("Status: ���¸��� �з��� ���� �� �����θ��з��� �����ϴ�.", true, 400);
			echo "";
			exit;
		}

		if ($_GET['samelow'] == 'Y'){
			$res = $db->query("update ".GD_CATEGORY." set openmarket = '{$_GET[catno]}' where category like '{$_GET[category]}%'");
		}
		else {
			$res = $db->query("update ".GD_CATEGORY." set openmarket = '{$_GET[catno]}' where category = '{$_GET[category]}'");
		}

		if ( $res )
			echo "OK";
		else
		{
			header("Status: ���¸��� �з� ������ ���еǾ����ϴ�.", true, 400);
			echo "";
			exit;
		}
		exit;
		break;

	case "getCategoryName": # ���¸��� �з��� ���

		header("Content-type: text/html; charset=euc-kr");

		$out = readurl("http://godosiom.godo.co.kr/sock_getCategory.php?cat_to_name={$_GET[catno]}");
		echo $out;
		exit;
		break;

	case "getUseable": # ���¸��� ������ üũ

		header("Content-type: text/html; charset=euc-kr");

		$openmarket = new openmarket();
		$out = $openmarket->isExists();
		echo $out[1];
		exit;
		break;

}


switch ($_POST[mode]){

	case "set":

		ob_start();
		$confFile = "../../conf/openmarket.php";
		if (file_exists($confFile))
		{
			include $confFile;
			if (is_array($omCfg)){
				$omCfg = array_map("stripslashes",$omCfg);
				$omCfg = array_map("addslashes",$omCfg);
			}
		}
		$omCfg = array_merge($omCfg,$_POST['omCfg']);

		$qfile->open($confFile);
		$qfile->write("<? \n");
		$qfile->write("\$omCfg = array( \n");
		foreach ($omCfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		@chmod($confFile,0707);
		ob_end_clean();
		break;

}

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];
go($_POST[returnUrl]);

?>