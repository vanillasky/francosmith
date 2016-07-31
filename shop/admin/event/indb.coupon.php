<?
include "../lib.php";
include_once "../../lib/sms.class.php";
require_once("../../lib/qfile.class.php");
@require_once "../../lib/load.class.php";
@require_once "../../lib/upload.lib.php";
$qfile = new qfile();

if($_POST[mode] == 'register' || $_POST[mode] == 'modify' || $_POST[mode] == 'register_mobile' || $_POST[mode] == 'modify_mobile'){

	if($_POST[priodtype] == 1){
		$_POST[sdate] = $_POST[priod];
		if($_POST[priod_edate]){
			$_POST[edate] = $_POST[priod_edate]." 23:59:59";
		}
	} else {
		if($_POST[sdate]){
			$_POST[sdate] = $_POST[sdate]." ".$_POST[shour].":".$_POST[smin].":00";
		}
		if($_POST[edate]){
			$_POST[edate] = $_POST[edate]." ".$_POST[ehour].":".$_POST[emin].":59";
		}
	}
	if($_POST[perc] == '원') $_POST[perc] = "";
	$_POST[price] .= $_POST[perc];
	$setquery = "
			`goodstype` 	= '$_POST[goodstype]',
			`priodtype` 	= '$_POST[priodtype]',
			`coupontype` 	= '$_POST[coupontype]',
			`coupon` 		= '$_POST[name]',
			`summa` 		= '$_POST[summa]',
			`sdate` 		= '$_POST[sdate]',
			`edate` 		= '$_POST[edate]',
			`excPrice` 		= '$_POST[excPrice]',
			`payMethod` 	= '$_POST[payMethod]',
			`ability` 		= '$_POST[ability]',
			`price` 		= '$_POST[price]',
			`coupon_img` 	= '$_POST[coupon_img]',
			`eactl` 		= '$_POST[eactl]',
			`dncnt` 		= '$_POST[dncnt]',
			`duplctl` 		= '$_POST[duplctl]',
			`edncnt` 		= '$_POST[edncnt]'
		";
}

if($_POST[mode] == 'applyAdd' || $_POST[mode] == 'applyMod'){
	$setquery = "
			couponcd		= '$_POST[couponcd]',
			membertype		= '$_POST[membertype]',
			member_grp_sno  = '$_POST[member_grp_sno]',
			regdt			= now()
		";
	if($_POST[m_ids])$_POST[m_ids] = array_unique($_POST[m_ids]);
}

switch ($_POST[mode]){

	case "config":

		$qfile->open("../../conf/coupon.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfgCoupon = array( \n");
		foreach ($_POST['cfgCoupon'] as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		break;
	case "register":

		$query = "insert into ".GD_COUPON." set ".$setquery.",`regdt`	= now()";
		$db->query($query);
		$newcouponcd = $db->lastID();
		////쿠폰 이미지 업로드
		if($_POST[coupon_img]== '4' && !empty($_FILES['coupon_img_file'])){

			$LoadClass = new LoadClass();
			$target = dirname(__FILE__)."/../../data/skin/".$cfg['tplSkin']."/img/common/";
			$tmpData = dirname(__FILE__)."/../../data/skin/";

			$LoadClass->class_load('upload','upload_file');
			if($_FILES['coupon_img_file'][tmp_name]){
				$tmp = explode('.',$_FILES['coupon_img_file'][name]);
				$ext = $tmp[count($tmp)-1];
				$filename = "coupon_img_".$newcouponcd.".".$ext;
				$LoadClass->class['upload']->upload_set($_FILES['coupon_img_file'],$target.$filename,'image');
				$LoadClass->class['upload']->upload();
				$query =  "update ".GD_COUPON." set `coupon_img_file` = '$filename' where couponcd = '$newcouponcd'";
				$db->query($query);
			}
		}
		////쿠폰 이미지 업로드

		break;
	case "modify":
		$newcouponcd = $_POST[couponcd];

		$query = "update ".GD_COUPON." set ".$setquery . " where couponcd='$newcouponcd'";
		$db->query($query);

		//쿠폰 이미지 업로드
		if($_POST[coupon_img]== '4' && !empty($_FILES['coupon_img_file'])){

			$LoadClass = new LoadClass();
			$target = dirname(__FILE__)."/../../data/skin/".$cfg['tplSkin']."/img/common/";
			$tmpData = dirname(__FILE__)."/../../data/skin/";

			$LoadClass->class_load('upload','upload_file');
			if($_FILES['coupon_img_file'][tmp_name]){
				$tmp = explode('.',$_FILES['coupon_img_file'][name]);
				$ext = $tmp[count($tmp)-1];
				$filename = "coupon_img_".$newcouponcd.".".$ext;
				$LoadClass->class['upload']->upload_set($_FILES['coupon_img_file'],$target.$filename,'image');
				$LoadClass->class['upload']->upload();

				$query =  "update ".GD_COUPON." set `coupon_img_file` = '$filename' where couponcd = '$newcouponcd'";
				$db->query($query);
			}
		}
		//쿠폰 이미지 업로드

		break;
	case "delete" :
		$query = "select * from ".GD_COUPON_APPLY." where couponcd = '$_POST[couponcd]'";
		$res  = $db->query($query);
		while($data = $db->fetch($res)) $Arr_query[] = "delete from ".GD_COUPON_APPLYMEMBER." where applysno='$data[sno]'";

		$Arr_query[] = "delete from ".GD_COUPON_APPLY." where couponcd='$_POST[couponcd]'";
		$Arr_query[] = "delete from ".GD_COUPON_CATEGORY." where couponcd='$_POST[couponcd]'";
		$Arr_query[] = "delete from ".GD_COUPON_GOODSNO." where couponcd='$_POST[couponcd]'";
		$Arr_query[] = "delete from ".GD_COUPON."  where couponcd='$_POST[couponcd]'";
		foreach($Arr_query as $v){
			$db->query($v);
		}

		break;
	case "applyAdd":

		$query = "select * from ".GD_COUPON_APPLY." a
								left join ".GD_COUPON_APPLYMEMBER." b on a.sno=b.applysno
								left join ".GD_MEMBER." c on b.m_no = c.m_no
								left join ".GD_MEMBER_GRP." d on c.level = d.level
					where a.couponcd='$_POST[couponcd]'";
		$res = $db->query($query);
		while($tmp = $db->fetch($res)){
			if($tmp[membertype] == 0){
				msg('이미 발급되어진 쿠폰입니다.!',-1);
			}
			if($tmp[member_grp_sno] == $_POST[member_grp_sno]){
				msg('이미 발급되어진 쿠폰입니다.!',-1);
			}

			if($_POST[membertype] == 2){
				foreach($_POST[m_ids] as $v) if($tmp[m_no] == $v)msg('중복되는 회원이 있습니다.!',-1);
			}

			if($_POST[membertype] == 0){
				msg('중복되는 회원이 있습니다.!',-1);
			}

		}

		$query = "insert into ".GD_COUPON_APPLY." set  ".$setquery;
		$db->query($query);

		$query = "select max(sno) from ".GD_COUPON_APPLY;
		list($newapplysno) = $db->fetch($query);

		break;
	case "applyMod":
		$newapplysno = $_POST[sno];
		$query = "update ".GD_COUPON_APPLY." set ".$setquery . " where sno='$newapplysno'";
		$db->query($query);

		break;
	case "delApply":

		$query = "delete from ".GD_COUPON_APPLYMEMBER." where applysno='$_GET[sno]'";
		$db->query($query);

		$query = "delete from ".GD_COUPON_APPLY." where sno='$_GET[sno]'";
		$db->query($query);
		go('coupon_apply.php?couponcd='.$_GET[couponcd]);
		break;
	case "delApply2":

		$query = "delete from ".GD_COUPON_APPLYMEMBER." where applysno='$_GET[sno]' and m_no='$_GET[m_no]'";
		$db->query($query);

		$query = "select count(*) from ".GD_COUPON_APPLYMEMBER." where applysno='$_GET[sno]'";
		list($cnt) = $db->fetch($query);
		if($cnt == 0){
			$query = "delete from ".GD_COUPON_APPLY." where sno='$_GET[sno]'";
			$db->query($query);
		}
		break;

	case "register_mobile":

		$setquery .= "
			, `c_screen` 	= 'm'";

		$query = "insert into ".GD_COUPON." set ".$setquery.",`regdt`	= now()";
		$db->query($query);


		$newcouponcd = $db->lastID();

		break;

	case "modify_mobile":

		$setquery .= "
			, `c_screen` 	= 'm'";

		$newcouponcd = $_POST[couponcd];

		$query = "update ".GD_COUPON." set ".$setquery . " where couponcd='$newcouponcd'";
		$db->query($query);



		break;


}

$Arr_query= array();

if($_POST[mode] == 'register' || $_POST[mode] == 'modify' || $_POST[mode] == 'register_mobile' || $_POST[mode] == 'modify_mobile'){

	$Arr_query[] = "delete from ".GD_COUPON_CATEGORY." where couponcd='$newcouponcd'";
	$Arr_query[] = "delete from ".GD_COUPON_GOODSNO." where couponcd='$newcouponcd'";

	##카테고리 / 선택상품
	if($_POST[goodstype] == '1'){
		if($_POST[category]){
			foreach($_POST[category] as $v)
				$Arr_query[] = "insert into ".GD_COUPON_CATEGORY." set category='$v', couponcd ='$newcouponcd'";
		}
		if($_POST[e_refer]){
			foreach($_POST[e_refer] as $v)
				$Arr_query[] = "insert into ".GD_COUPON_GOODSNO." set goodsno='$v', couponcd ='$newcouponcd'";

		}
	}
}

if($_POST[mode] == 'applyAdd' || $_POST[mode] == 'applyMod'){
	$Arr_query[] = "delete from ".GD_COUPON_APPLYMEMBER." where applysno='$newapplysno'";
	##카테고리 / 선택상품
	if($_POST[membertype] == '2'){
		if($_POST[m_ids]){

			foreach($_POST[m_ids] as $v)
				$Arr_query[] = "insert into ".GD_COUPON_APPLYMEMBER." set m_no='$v', applysno ='$newapplysno'";
		}
	}

	## sms 발송
	if($_POST[smsyn]){
		if($_POST[membertype] == '0'){
			$squery = "select mobile from ".GD_MEMBER." where mobile AND " . MEMBER_DEFAULT_WHERE;
		}else if($_POST[membertype] == '1'){
			$squery = "select a.mobile from ".GD_MEMBER." a left join ".GD_MEMBER_GRP." b on a.level=b.level where b.sno='$_POST[member_grp_sno]' and a.mobile AND a." . MEMBER_DEFAULT_WHERE;
		}else if($_POST[membertype] == '2'){
			$squery = "select mobile from ".GD_MEMBER." where m_no in (".implode(',',$_POST[m_ids]).") and mobile AND " . MEMBER_DEFAULT_WHERE;
		}
		if($squery){

			$res = $db->query($squery);
			$sms = new Sms();
			$sms_sendlist = $sms->loadSendlist();
			while($data = $db->fetch($res)){
				$sms->log($_POST[msg],$data[mobile],$case,1);
				$sms_sendlist->setSimpleInsert($data[mobile], $sms->smsLogInsertId, '');
				$sms->send($_POST[msg],$data[mobile],$_POST[callback]);
				$sms->update_ok_eNamoo = true;
				$sms->update();

				flush();
			}
		}
	}

}


if(count($Arr_query) > 0) foreach($Arr_query as $v)	$db->query($v);

go($_SERVER[HTTP_REFERER]);
?>