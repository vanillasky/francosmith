<?php
include "../lib.php";
require_once("../../lib/smartSearch.class.php");

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

switch($_GET['mode']){
	case 'add' :
	case 'option' :

		$menu_title = array();

		if($_GET['mode'] == 'add'){
			$fieldnm = "a.ex_title";
		}else if($_GET['mode'] == 'option'){
			$fieldnm = "a.optnm";
		}

		$query = $db->query("SELECT ".$fieldnm." FROM ".GD_GOODS." as a inner join ".GD_GOODS_LINK." as b on a.goodsno=b.goodsno WHERE ".$fieldnm." != '' and b.category like '".$_GET['cate']."%' GROUP BY ".$fieldnm);

		while($data=$db->fetch($query)){
			$data_array = explode('|',$data[0]);
			for($i=0,$m=count($data_array);$i<$m;$i++){
				$v = trim($data_array[$i]);
				if(!empty($v)) $menu_title[] = $v;
			}
		}

		$menu_title = array_unique($menu_title);	// 중복 된 값 제거

		// 기존 설정된 테마 정보가 있을 경우는 동기화 후에도 그 정보를 그대로 출력하기 위한 부분
		if($_GET['themeno']) {
			$themeQuery = "SELECT ".(($_GET['mode'] == "add") ? "ex" : "opt")." AS oList FROM ".GD_GOODS_SMART_SEARCH." WHERE sno = '".$_GET['themeno']."'";
			$themeData = $db->fetch($themeQuery);

			$tmpArr1 = explode(PHP_EOL, $themeData['oList']); // 추가옵션별로 나눔
			$setMenu_title = array();
			for($i =0 , $imax = count($tmpArr1); $i < $imax; $i++) {
				$tmpArr2 = explode(_OPT_PIPE_._OPT_PIPE_, $tmpArr1[$i]); // 추가옵션명과 값으로 나눔
				if($tmpArr2[0]) $setMenu_title[] = $tmpArr2[0];
			}
		}
		$tmpHeader = ($_GET['mode'] == "add") ? "e" : "o"; // tr태그의 id 접두어 정의

		$tmpNo = 0;
		$t_menu .= "<table cellpadding=\'0\' cellspacing=\'0\' width=\'95%\'>";
		if(is_array($setMenu_title)) foreach($setMenu_title as $k=>$v) {
			$v = smartSearch::html_encode($v);
			$t_menu .= "<tr id=\"".$tmpHeader."_Item_".$tmpNo."\"><td><input type=\'checkbox\' name=\'goods_".$_GET['mode']."_menu[]\' value=\'$v\' checked /><input type=\'text\' style=\'width:130px; border:0px;\' name=\'t_goods_".$_GET['mode']."_menu[]\' value=\'".$v."\' readonly /></td><td align=\'right\'><a href=\'javascript:;\' onclick=\"ssMoveItem(\'".$tmpHeader."_Item_".$tmpNo."\', \'".$tmpHeader."_Item_".(($tmpNo == 0) ? $tmpNo : ($tmpNo - 1))."\')\" class=\'mvArw\'>↑</a> <a href=\'javascript:;\' onclick=\"ssMoveItem(\'".$tmpHeader."_Item_".$tmpNo."\', \'".$tmpHeader."_Item_".(($tmpNo == (count($search['opt']) - 1)) ? $tmpNo : ($tmpNo + 1))."\')\" class=\'mvArw\'>↓</a></td></tr>";
			$tmpNo++;
		}
		if(is_array($menu_title)) foreach($menu_title as $k=>$v) {
			if(!in_array($v, $setMenu_title)) {
				$v = smartSearch::html_encode($v);
				$t_menu .= "<tr id=\"".$tmpHeader."_Item_".$tmpNo."\"><td><input type=\'checkbox\' name=\'goods_".$_GET['mode']."_menu[]\' value=\'$v\' /><input type=\'text\' style=\'width:130px; border:0px;\' name=\'t_goods_".$_GET['mode']."_menu[]\' value=\'".$v."\' readonly /></td><td align=\'right\'><a href=\'javascript:;\' onclick=\"ssMoveItem(\'".$tmpHeader."_Item_".$tmpNo."\', \'".$tmpHeader."_Item_".(($tmpNo == 0) ? $tmpNo : ($tmpNo - 1))."\')\" class=\'mvArw\'>↑</a> <a href=\'javascript:;\' onclick=\"ssMoveItem(\'".$tmpHeader."_Item_".$tmpNo."\', \'".$tmpHeader."_Item_".(($tmpNo == (count($search['opt']) - 1)) ? $tmpNo : ($tmpNo + 1))."\')\" class=\'mvArw\'>↓</a></td></tr>";
				$tmpNo++;
			}
		}
		$t_menu .= "</table>";

		echo "<script>parent.document.getElementById('goods_".$_GET['mode']."_menu').innerHTML='".$t_menu."';</script>";

		break;

	case "checkThemeName" :
		$sql = "SELECT COUNT(sno) FROM ".GD_GOODS_SMART_SEARCH." WHERE themenm = '".$_GET['themeName']."' AND sno != '".$_GET['sno']."'";
		list($cnt) = $db->fetch($sql);
		$checkThemeName = ($cnt) ? "2" : "1";
		echo "
		<script language=\"javascript\">parent.document.getElementById('checkResult').value = '".$checkThemeName."';</script>";
		break;
}
?>