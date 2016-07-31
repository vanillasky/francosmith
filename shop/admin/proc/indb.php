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

	case "getPanel": # 고도몰 기획코너

		header("Content-type: text/html; charset=euc-kr");

		/* 사용권 계약서 */
		if ($_POST['idnm'] == 'maxlicense' && $_POST['section'] == 'header') { // 사용권 계약서 보기
			$filenm = 'max.popup.license.htm?sno='.$godo['sno'];
		}

		/* 상단 서비스정보 */
		else if ($_POST['idnm'] == 'maxtab' && empty($_POST['section']) === false) { // 탭팝업
			$filenm = 'season4/max.tab.htm?sno='.$godo['sno'].'&section='.$_POST['section'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}

		/* 메인 본문 서비스정보 */
		else if ($_POST['idnm'] == 'maxtop' && $_POST['section'] == 'basic') { // 상단 대배너
			if ($godo['ecCode'] == 'self_enamoo_season'){
				$filenm = 'season4/max.top.self.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'].'&inipgStep='.$godo['pgStep'];
			} else {
				$filenm = 'season4/max.top.rental.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'].'&inipgStep='.$godo['pgStep'];
			}
		}
		else if ($_POST['idnm'] == 'maxservice' && $_POST['section'] == 'basic') { // 중앙 부가서비스 배너
			if ($godo['ecCode'] == "self_enamoo_season"){
				$filenm = 'season4/max.service.self.basic.htm?sno='.$godo['sno'].'&ecCode='.$godo['ecCode'].'&freeType='.$godo['freeType'].'&webCode='.$godo[webCode];
			} else {
				$filenm = 'season4/max.service.rental.basic.htm?sno='.$godo['sno'].'&ecCode='.$godo['ecCode'].'&freeType='.$godo['freeType'].'&webCode='.$godo[webCode];
			}
		}
		else if ($_POST['idnm'] == 'maxbottom' && $_POST['section'] == 'basic') { // 하단 디자인샵
			$filenm = 'season4/max.bottom.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if ($_POST['idnm'] == 'maxpopup' && $_POST['section'] == 'basic') { // 팝업창
			$filenm = 'season4/max.popup.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'].'&convertYN='.$godo[convertVer];
		}

		/* 메인 좌측 서비스정보 */
		else if ($_POST['idnm'] == 'godoinfo' && $_POST['section'] == 'basic') { // 좌측 플래시배너
			$filenm = 'max.godoinfo.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'].'&webCode='.$godo['webCode'].'&sdate='.$godo['sdate'];
		}
		else if ($_POST['idnm'] == 'maxleft' && $_POST['section'] == 'basic') { // 좌측 하단배너
			if ($godo['ecCode'] == "self_enamoo_season"){
				$filenm = 'season4/max.left.self.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'].$ini_Data;
			} else {
				$filenm = 'season4/max.left.rental.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'].$ini_Data;
			}
		}

		/* 메인 우측 서비스정보 */
		else if ($_POST['idnm'] == 'maxright' && $_POST['section'] == 'basic') { // 우측 배너
			if ($godo['ecCode'] == "self_enamoo_season"){
				$filenm = 'season4/max.right.self.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
			} else {
				$filenm = 'season4/max.right.rental.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
			}
		}
		else if ($_POST['idnm'] == 'maxoperate' && $_POST['section'] == 'basic') { // 쇼핑몰 운영 비법
			$filenm = 'season4/max.operate.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}

		/* 서브 좌측 서비스정보 */
		else if ($_POST['idnm'] == 'panelside' && $_POST['section'] == 'basic') {
			$filenm = 'season4/max.side.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if ($_POST['idnm'] == 'panelside' && $_POST['section'] == 'design') {
			$filenm = 'season4/max.side.design.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if ($_POST['idnm'] == 'panelside' && $_POST['section'] == 'event') {
			$filenm = 'season4/max.side.event.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}

		/* 디자인관리 */
		else if ($_POST['idnm'] == 'designBanner' && $_POST['section'] == 'design') { // 팝업창
			$filenm = 'season4/max.designBanner.design.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if ($_POST['idnm'] == '80skins' && $_POST['section'] == 'design'){ // 무료 디자인스킨
			$isCachePanel = 'no';

			// 스킨
			$tmp = array();
			$skinDir = dirname(__FILE__) . "/../../data/skin/";
			$odir = @opendir( $skinDir );
			while (false !== ($rdir = readdir($odir))) {
				// 디렉토리인지를 체크
				if(is_dir($skinDir . $rdir)){
					if ( !ereg( "\.$", $rdir ) ) {
						array_push($tmp, $rdir);
					}
				}
			}
			$setSkins = implode(',',$tmp);

			// 모바일샵V2 스킨
			$tmp = array();
			$skinDir = dirname(__FILE__) . "/../../data/skin_mobileV2/";
			$odir = @opendir( $skinDir );
			while (false !== ($rdir = readdir($odir))) {
				// 디렉토리인지를 체크
				if(is_dir($skinDir . $rdir)){
					if ( !ereg( "\.$", $rdir ) ) {
						array_push($tmp, $rdir);
					}
				}
			}
			$mv2Skins = implode(',',$tmp);
			$setSkins = $setSkins . '||' . $mv2Skins;

			// 용량
			if(!$du) @include "../../conf/du.php";
			$size = $du['disk'];
			$tday = date('Ymd');
			$limitDisk = $godo['maxDisk'];
			if($godo['diskGoods']&&$godo['diskSdate']<=$tday&&$godo['diskEdate']>=$tday)$limitDisk += $godo['diskGoods'];
			$arr = array("7".$godo['sno']."9",$godo['ecCode'],$godo['webCode'],$size,$limitDisk,$cfg['rootDir'],$godo['freeType']);
			$tmp = urlencode(serialize($arr));
			$filenm = 'freeSkin/freeSkinStyle.php?key='.$tmp.'&setSkins='.$setSkins;
		}

		/* PG 설정 */
		else if($_POST['idnm'] == 'inicis_banner' && $_POST['section'] == 'pg') { // 이니시스
			$filenm = 'pg_info/inicis_info.php?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if($_POST['idnm'] == 'kcp_banner' && $_POST['section'] == 'pg') { // KCP
			$filenm = 'pg_info/kcp_info.php?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if($_POST['idnm'] == 'dacom_banner' && $_POST['section'] == 'pg') { // 데이콤
			$filenm = 'pg_info/lg_info.php?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if($_POST['idnm'] == 'allat_banner' && $_POST['section'] == 'pg') { // 올앳
			$filenm = 'pg_info/allat_info.php?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if($_POST['idnm'] == 'easypay_banner' && $_POST['section'] == 'pg') { // 이지페이
			$filenm = 'pg_info/easypay_info.php?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}

		/* 쇼핑몰기본관리 */
		else if ($_POST['idnm'] == 'diskAddInfo' && $_POST['section'] == 'basic') { // 디스크 용량 서비스 안내
			$filenm = 'diskAdd/rental_info.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}

		/* 프로모션관리 */
		else if($_POST['idnm'] == 'enest_info' && $_POST['section'] == 'marketing') { // 모바일마케팅 안내
			$filenm = 'enest/mobile_marketing.php?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}

		/* 마케팅관리 */
		else if ($_POST['idnm'] == 'promotion' && $_POST['section'] == 'interpark') { // 인터파크 오픈스타일 프로모션
			$filenm = 'max.promotion.interpark.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if ($_POST['idnm'] == 'enamoophone' && $_POST['section'] == 'marketing') { // 이나무폰 안내
			$filenm = 'enamoophone.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}
		else if ($_POST['idnm'] == 'nateClipping' && $_POST['section'] == 'marketing') { // 싸이월드 스크랩 안내/신청
			include "../../lib/lib.enc.php";
			$key = serialize(array($godo['sno'],$cfg['rootDir']));
			$tmp = godoConnEncode($key);
			$filenm = 'clipping/info.php?key='.$tmp;
			$isCachePanel = 'no';
		}
		else if($_POST['idnm'] == 'daumcpc' && $_POST['section'] == 'marketing') { // 다음쇼핑하우 신청
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

		/* 모바일샵관리 */
		else if($_POST['idnm'] == 'mobileshop' && $_POST['section'] == 'design') { // 팝업창
			$filenm = 'mobileshop.design.top.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
		}

		// 고도 아카데미 & 신규서비스
		else if ($_POST['idnm'] == 'maxmiddle' && $_POST['section'] == 'basic') {
			if ($godo['ecCode'] == "self_enamoo_season"){
				$filenm = 'season4/max.middle.self.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
			} else {
				$filenm = 'season4/max.middle.rental.basic.htm?sno='.$godo['sno'].'&service='.$godo['ecCode'].'&freeType='.$godo['freeType'];
			}
		}

		// 캐시 사용
		if ($isCachePanel == 'no') {
			if ($filenm != '') $out = readurl("http://gongji.godo.co.kr/userinterface/{$filenm}");
			if (strpos($out, 'Not Found') !== false) $out = '';
		}
		else if (($out = Core::helper('Cache','admin_panel')->get($filenm, 1800)) === false) {	// 1800 = 30분

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
			msg('기본용량이 증설되었습니다.');
		} else {
			msg($out);
		}
	break;

}

go($_SERVER[HTTP_REFERER]);

?>
