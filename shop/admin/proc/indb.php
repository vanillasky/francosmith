<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
include "../../conf/config.php";
$cfg = (array)$cfg;
$qfile = new qfile();

switch ($_POST[mode]){

	case "imgSize":

		$cfg = array_merge($cfg,(array)$_POST);
		$cfg = array_map("stripslashes",$cfg);
		$cfg = array_map("addslashes",$cfg);

		$qfile->open("../../conf/config.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfg = array( \n");
		foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		if(preg_match('/goods/',$_SERVER['HTTP_REFERER'])){
			go($_SERVER['HTTP_REFERER']);
		}else{
			echo "<script>parent.closeLayer()</script>";
		}

		exit;
		break;

	case "getPanel": # ���� ��ȹ�ڳ�

		header("Content-type: text/html; charset=euc-kr");

		/* ���� ��༭ */
		if ($_POST['idnm'] == 'maxlicense' && $_POST['section'] == 'header') { // ���� ��༭ ����
			$filenm = 'max.popup.license.htm?sno='.$godo['sno'];
		}

		/* ��� �������� */
		else if ($_POST['idnm'] == 'maxtab' && empty($_POST['section']) === false) { // ���˾�
			$filenm = 'season4/max.tab.htm?sno='.$godo['sno'].'&section='.$_POST['section'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}

		/* ���� ���� �������� */
		else if ($_POST['idnm'] == 'maxtop' && $_POST['section'] == 'basic') { // ��� ����
			if ($godo['ecCode'] == 'self_enamoo_season'){
				$filenm = 'season4/max.top.self.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'].'&inipgStep='.$godo['pgStep'];
			} else {
				$filenm = 'season4/max.top.rental.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'].'&inipgStep='.$godo['pgStep'];
			}
		}
		else if ($_POST['idnm'] == 'maxservice' && $_POST['section'] == 'basic') { // �߾� �ΰ����� ���
			if ($godo['ecCode'] == "self_enamoo_season"){
				$filenm = 'season4/max.service.self.basic.htm?sno='.$godo['sno'].'&ecCode='.$godo['ecCode'].'&freeType='.$godo['freeType'].'&webCode='.$godo[webCode];
			} else {
				$filenm = 'season4/max.service.rental.basic.htm?sno='.$godo['sno'].'&ecCode='.$godo['ecCode'].'&freeType='.$godo['freeType'].'&webCode='.$godo[webCode];
			}
		}
		else if ($_POST['idnm'] == 'maxbottom' && $_POST['section'] == 'basic') { // �ϴ� �����μ�
			$filenm = 'season4/max.bottom.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if ($_POST['idnm'] == 'maxpopup' && $_POST['section'] == 'basic') { // �˾�â
			$filenm = 'season4/max.popup.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'].'&convertYN='.$godo[convertVer];
		}

		/* ���� ���� �������� */
		else if ($_POST['idnm'] == 'godoinfo' && $_POST['section'] == 'basic') { // ���� �÷��ù��
			$filenm = 'max.godoinfo.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'].'&webCode='.$godo['webCode'].'&sdate='.$godo['sdate'];
		}
		else if ($_POST['idnm'] == 'maxleft' && $_POST['section'] == 'basic') { // ���� �ϴܹ��
			if ($godo['ecCode'] == "self_enamoo_season"){
				$filenm = 'season4/max.left.self.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'].$ini_Data;
			} else {
				$filenm = 'season4/max.left.rental.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'].$ini_Data;
			}
		}

		/* ���� ���� �������� */
		else if ($_POST['idnm'] == 'maxright' && $_POST['section'] == 'basic') { // ���� ���
			if ($godo['ecCode'] == "self_enamoo_season"){
				$filenm = 'season4/max.right.self.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
			} else {
				$filenm = 'season4/max.right.rental.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
			}
		}
		else if ($_POST['idnm'] == 'maxoperate' && $_POST['section'] == 'basic') { // ���θ� � ���
			$filenm = 'season4/max.operate.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}

		/* ���� ���� �������� */
		else if ($_POST['idnm'] == 'panelside' && $_POST['section'] == 'basic') {
			$filenm = 'season4/max.side.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if ($_POST['idnm'] == 'panelside' && $_POST['section'] == 'design') {
			$filenm = 'season4/max.side.design.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if ($_POST['idnm'] == 'panelside' && $_POST['section'] == 'event') {
			$filenm = 'season4/max.side.event.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}

		/* �����ΰ��� */
		else if ($_POST['idnm'] == 'designBanner' && $_POST['section'] == 'design') { // �˾�â
			$filenm = 'season4/max.designBanner.design.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if ($_POST['idnm'] == '80skins' && $_POST['section'] == 'design'){ // ���� �����ν�Ų
			$isCachePanel = 'no';

			// ��Ų
			$tmp = array();
			$skinDir = dirname(__FILE__) . "/../../data/skin/";
			$odir = @opendir( $skinDir );
			while (false !== ($rdir = readdir($odir))) {
				// ���丮������ üũ
				if(is_dir($skinDir . $rdir)){
					if ( !ereg( "\.$", $rdir ) ) {
						array_push($tmp, $rdir);
					}
				}
			}
			$setSkins = implode(',',$tmp);

			// ����ϼ�V2 ��Ų
			$tmp = array();
			$skinDir = dirname(__FILE__) . "/../../data/skin_mobileV2/";
			$odir = @opendir( $skinDir );
			while (false !== ($rdir = readdir($odir))) {
				// ���丮������ üũ
				if(is_dir($skinDir . $rdir)){
					if ( !ereg( "\.$", $rdir ) ) {
						array_push($tmp, $rdir);
					}
				}
			}
			$mv2Skins = implode(',',$tmp);
			$setSkins = $setSkins . '||' . $mv2Skins;

			// �뷮
			if(!$du) @include "../../conf/du.php";
			$size = $du['disk'];
			$tday = date('Ymd');
			$limitDisk = $godo['maxDisk'];
			if($godo['diskGoods']&&$godo['diskSdate']<=$tday&&$godo['diskEdate']>=$tday)$limitDisk += $godo['diskGoods'];
			$arr = array("7".$godo['sno']."9",$godo['ecCode'],$godo['webCode'],$size,$limitDisk,$cfg['rootDir'],$godo['freeType']);
			$tmp = urlencode(serialize($arr));
			$filenm = 'freeSkin/freeSkinStyle.php?key='.$tmp.'&setSkins='.$setSkins;
		}

		/* PG ���� */
		else if($_POST['idnm'] == 'inicis_banner' && $_POST['section'] == 'pg') { // �̴Ͻý�
			$filenm = 'pg_info/inicis_info.php?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if($_POST['idnm'] == 'kcp_banner' && $_POST['section'] == 'pg') { // KCP
			$filenm = 'pg_info/kcp_info.php?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if($_POST['idnm'] == 'dacom_banner' && $_POST['section'] == 'pg') { // ������
			$filenm = 'pg_info/lg_info.php?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if($_POST['idnm'] == 'allat_banner' && $_POST['section'] == 'pg') { // �þ�
			$filenm = 'pg_info/allat_info.php?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if($_POST['idnm'] == 'easypay_banner' && $_POST['section'] == 'pg') { // ��������
			$filenm = 'pg_info/easypay_info.php?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}

		/* ���θ��⺻���� */
		else if ($_POST['idnm'] == 'diskAddInfo' && $_POST['section'] == 'basic') { // ��ũ �뷮 ���� �ȳ�
			$filenm = 'diskAdd/rental_info.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}

		/* ���θ�ǰ��� */
		else if($_POST['idnm'] == 'enest_info' && $_POST['section'] == 'marketing') { // ����ϸ����� �ȳ�
			$filenm = 'enest/mobile_marketing.php?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}

		/* �����ð��� */
		else if ($_POST['idnm'] == 'promotion' && $_POST['section'] == 'interpark') { // ������ũ ���½�Ÿ�� ���θ��
			$filenm = 'max.promotion.interpark.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if ($_POST['idnm'] == 'enamoophone' && $_POST['section'] == 'marketing') { // �̳����� �ȳ�
			$filenm = 'enamoophone.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if ($_POST['idnm'] == 'nateClipping' && $_POST['section'] == 'marketing') { // ���̿��� ��ũ�� �ȳ�/��û
			include "../../lib/lib.enc.php";
			$key = serialize(array($godo['sno'],$cfg['rootDir']));
			$tmp = godoConnEncode($key);
			$filenm = 'clipping/info.php?key='.$tmp;
			$isCachePanel = 'no';
		}
		else if($_POST['idnm'] == 'daumcpc' && $_POST['section'] == 'marketing') { // ���������Ͽ� ��û
			include "../../lib/lib.enc.php";

			$shopinfo = array(
				 'shop_url'=>$cfg['shopUrl'],
				 'basic_sno'=>$godo['sno'],
				 'shopName'=>$cfg['shopName'],
				 'shopengName'=>$cfg['shopEng'],
				 'bizNo'=>str_replace('-','',$cfg['compSerial']),
				 'corpnum'=>'',
				 'salenum'=>$cfg['orderSerial'],
				 'tel'=>$cfg['compPhone'],
				 'cstel'=>$cfg['compPhone'],
				 'csmail'=>$cfg['adminEmail'],
				 'joname'=>$cfg['adminName'],
				 'jotel'=>$cfg['compPhone'],
				 'johpnum'=>'',
				 'jomail'=>$cfg['adminEmail']
			);
			$key = serialize($shopinfo);
			$tmp = godoConnEncode($key);
			$filenm = 'daum_cpc/cpc_regist.php?shopinfo='.$tmp;
		}

		/* ����ϼ����� */
		else if($_POST['idnm'] == 'mobileshop' && $_POST['section'] == 'design') { // �˾�â
			$filenm = 'mobileshop.design.top.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}

		// �� ��ī���� & �űԼ���
		else if ($_POST['idnm'] == 'maxmiddle' && $_POST['section'] == 'basic') {
			if ($godo['ecCode'] == "self_enamoo_season"){
				$filenm = 'season4/max.middle.self.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
			} else {
				$filenm = 'season4/max.middle.rental.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
			}
		}

		// ĳ�� ���
		if ($isCachePanel == 'no') {
			if ($filenm != '') $out = readurl("http://gongji.godo.co.kr/userinterface/{$filenm}");
			if (strpos($out, 'Not Found') !== false) $out = '';
		}
		else if (($out = Core::helper('Cache','admin_panel')->get($filenm, 1800)) === false) {	// 1800 = 30��

			if ($filenm != '') $out = readurl("http://gongji.godo.co.kr/userinterface/{$filenm}");
			if (strpos($out, 'Not Found') !== false) $out = '';

			$out = trim($out);

			if ($out) {
				Core::helper('Cache','admin_panel')->set($filenm, $out);
			}

		}
		echo $out;
		exit;
		break;

}

switch ($_GET[mode]){

	case "eduExtend" :
		$data['shop_key'] = $godo['sno'];
		$out = readpost("http://gongji.godo.co.kr/userinterface/season4/freeDisk_API.php", $data);
		if ($out == 'ok') {
			msg('�⺻�뷮�� �����Ǿ����ϴ�.');
		} else {
			msg($out);
		}
	break;

}

go($_SERVER[HTTP_REFERER]);

?>
