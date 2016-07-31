<?
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

switch ($dmode) {
	case "0" :
		### 최초 세팅
		if($set['delivery']['overZipcode']){
			$arr = explode('|',$set['delivery']['overZipcode']);
			foreach($arr as $k => $tmp){
				$arr2 = explode(',',$tmp);
				foreach($arr2 as $code){
					$tmp = Core::loader('Zipcode')->get(array('keyword'=>$code,'where'=>'zipcode'))->current();
					$r_name[$k][] = $tmp['sido'] . " " .$tmp['gugun'];
				}
				$r_name[$k] = array_unique(array_map("trim",$r_name[$k]));
			}
		}

		unset($tmp);
		if($r_name) foreach ($r_name as $v)	$tmp[] = implode(',',$v);
		$qfile->open("../../conf/area.delivery.php");
		$qfile->write("<? \n");
		$qfile->write("\$r_area[deliveryArea] = '".@implode('|',$tmp)."';\n");
		$qfile->write("?>");
		$qfile->close();
		@chmod("../../conf/area.delivery.php",0707);

	break;
	case "1" :
		@include "../../conf/area.delivery.php";
		$chkUnique = array();
		$repetition = "";
		for($i=0,$imax=count($_POST['overZipcodeName']);$i<$imax;$i++) if($_POST['overZipcodeName'][$i]) {
			$arTemp = explode(",", $_POST['overZipcodeName'][$i]);
			for($j=0,$jmax=count($arTemp);$j<$jmax;$j++) {
				if(in_array(trim($arTemp[$j]), $chkUnique) && !preg_match("/".trim($arTemp[$j])."/", $repetition)) {
					if($repetition) $repetition .= ", ";
					$repetition .= trim($arTemp[$j]);
				}
				else $chkUnique[] = trim($arTemp[$j]);
			}
			$_POST['overZipcodeName'][$i] = implode(",",array_map("trim",$arTemp));
		}
		if($repetition) msg("\\'지역별 배송금액\\'에 중복으로 들어간 지역이 있습니다.", -1);
		$tmp[deliveryArea] = implode('|',$_POST[overZipcodeName]);
		$r_area = array_merge($r_area,$tmp);

		$qfile->open("../../conf/area.delivery.php");
		$qfile->write("<? \n");
		$qfile->write("\$r_area[deliveryArea] = '".$r_area[deliveryArea]."';\n");
		$qfile->write("?>");
		$qfile->close();
		@chmod("../../conf/area.delivery.php",0707);

		$rtmp = explode('|',$r_area[deliveryArea]);

		foreach($rtmp as $k => $v){
			$arr = explode(',',$v);
			$tmp2 = array();
			foreach($arr as $v2) {
				$_param = array(
					'keyword' => $v2,
					'where' => 'sido&gugun',
					'group' => 'zipcode',
				);

				$tmp = Core::loader('Zipcode')->get($_param);

				foreach ($tmp as $data) {
					$tmp2[] = substr($data['zipcode'],0,3);
				}
			}
			$tmp2 = array_unique($tmp2);
			$overZipcode[$k] = @implode(',',$tmp2);
		}
	break;
	case "3" :

	$over = explode("|",$set['delivery']['over']);
	foreach($over as $v){
		$val = $v - $set['delivery']['default'];
		if($val >= 0) $overAdd[] = $val;
		else $overAdd[] = $v;
	}
	$set['delivery']['overAdd'] = @implode('|',$overAdd);

	$set = array_map('strip_slashes',$set);
	$set = array_map('add_slashes',$set);

	$qfile->open("../../conf/config.pay.php");
	$qfile->write("<? \n");
	foreach ($set as $k=>$v){
		foreach ($v as $k2=>$v2){
			if($v2)$qfile->write("\$set['$k']['$k2'] = '$v2'; \n");
		}
	}
	$qfile->write("?>");
	$qfile->close();
	@chmod("../../conf/config.pay.php",0707);
	break;
}
?>
