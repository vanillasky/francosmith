<?
// deprecated. redirect to new page;
header('location: ./adm_goods_manage_color.php?'.$_SERVER['QUERY_STRING']);
exit;
	$location = "상품일괄관리 > 빠른 대표색상 수정";
	include "../_header.php";
	include "../../lib/page.class.php";

	if (!$_GET['page_num']) $_GET['page_num'] = 10;

	list($total) = $db->fetch("SELECT COUNT(goodsno) FROM ".GD_GOODS);

	$selected['page_num'][$_GET['page_num']] = "selected";
	$selected['skey'][$_GET['skey']] = "selected";
	$selected['brandno'][$_GET['brandno']] = "selected";
	$selected['sbrandno'][$_GET['sbrandno']] = "selected";
	$checked['open'][$_GET['open']] = "checked";
	$checked['isToday'][$_GET['isToday']] = "checked";

	if($_GET['sCate']) {
		$sCategory = array_notnull($_GET['sCate']);
		$sCategory = $sCategory[count($sCategory)-1];
	}

	if($_GET['indicate'] == 'search') {
		$orderby = "a.goodsno DESC";

		if ($_GET['cate']) {
			$category = array_notnull($_GET['cate']);
			$category = $category[count($category)-1];
		}

		$db_table = GD_GOODS." a LEFT JOIN ".GD_GOODS_OPTION." b ON a.goodsno = b.goodsno AND link ";

		if ($category || $_GET['unlink'] == 'Y') {
			$db_table .= " LEFT JOIN ".GD_GOODS_LINK." c ON a.goodsno = c.goodsno ";
			$where[] = ($_GET['unlink'] == 'Y') ? "ISNULL(c.goodsno)" : "category LIKE '$category%'";
		}
		if ($_GET['sword']) $where[] = $_GET['skey']." LIKE '%".$_GET['sword']."%'";
		if ($_GET['brandno']) $where[] = "brandno = '".$_GET['brandno']."'";
		if ($_GET['unbrand'] == 'Y') $where[] = "brandno = '0'";
		if ($_GET['open']) $where[] = "open = ".substr($_GET['open'], -1);
		if ($_GET['price'][0] && $_GET['price'][1]) $where[] = " b.price BETWEEN ".$_GET['price'][0]." AND ".$_GET['price'][1]." ";
		if ($_GET['regdt'][0] && $_GET['regdt'][1]) $where[] = " a.regdt BETWEEN DATE_FORMAT(".$_GET['regdt'][0].",'%Y-%m-%d 00:00:00') AND DATE_FORMAT(".$_GET['regdt'][1].",'%Y-%m-%d 23:59:59') ";
		if ($_GET['searchColor']) {
			$arr_searchColor = explode("#", $_GET['searchColor']);
			$tmp = array();
			foreach($arr_searchColor as $k => $v) {
				if($v) $tmp[] = " color LIKE '%$v%' ";
			}
			$where[] = '('.implode(' OR ',$tmp).')';
		}

		$pg = new Page($_GET['page'],$_GET['page_num']);
		$pg->field = "a.goodsno, a.goodsnm, a.open, a.regdt, a.brandno, a.inpk_prdno, a.totstock, a.color, a.img_s, b.link, b.reserve, b.price";
		$pg->cntQuery = "select count(1) from ( select count(1) from ".$db_table.(sizeof($where) > 0 ? "where ".implode(" and ",$where) : '' )." group by a.goodsno ) sub";
		$pg->setQuery($db_table, $where, $orderby ,'group by a.goodsno');
		$pg->exec();

		$res = $db->query($pg->query);
	}

	// 브랜드
	$brands = array();
	$bRes = $db->query("select * from gd_goods_brand order by sort");
	while ($tmp=$db->fetch($bRes)) $brands[$tmp['sno']] = $tmp['brandnm'];

	// 색상
	$colorList = array();
	$CL_rs = $db->query("SELECT itemnm FROM ".GD_CODE." WHERE groupcd = 'colorList' ORDER BY sort");
	while($CL_row = $db->fetch($CL_rs)) $colorList[] = $CL_row['itemnm'];
?>

<style type="text/css">
	.paletteColor { width:15px; height:15px; cursor:pointer; border:1px #DDDDDD solid; }
	.paletteColor_selected { float:left; width:15px; height:15px; margin:1px; cursor:pointer; border:1px #DDDDDD solid; }
	.optBox { width:50%; padding:3px 0px; text-align:center; }
	.optBoxLine { border-bottom:1px #DCD8D6 solid; }

	.selColorText { margin-top:3px; font-size:11px; font-family:dotum; color:#0070C0; float:left; }
</style>

<script language="javascript">
	// rgb코드 -> 16진수코드
	function convColor(colorCode) {
		if(colorCode.toLowerCase().indexOf('rgb') == 0) {
			colorCode = colorCode.toLowerCase().replace(/rgb/g, '');
			colorCode = colorCode.toLowerCase().replace(/\(/g, '');
			colorCode = colorCode.toLowerCase().replace(/\)/g, '');
			colorCode = colorCode.toLowerCase().replace(/ /g, '');

			colorCode_tempList = colorCode.split(',');
			colorCode = "";

			for(i = 0; i < colorCode_tempList.length; i++) {
				tmpCode = parseInt(colorCode_tempList[i]).toString(16);
				if(String(tmpCode).length == 1) tmpCode = "0" + tmpCode;
				colorCode += tmpCode;
			}
			colorCode = "#" + colorCode;
		}

		return colorCode;
	}

	// 색상을 선택했을 때 히든필드에 값을 저장
	function selectColor(targetColor, targetID, colorInput) {
		targetColor = convColor(targetColor);

		targetColor = targetColor.toUpperCase();
		tempColor = $(colorInput);

		if(tempColor.value.indexOf(targetColor) != -1) alert("이미 추가된 색상입니다.");
		else tempColor.value = tempColor.value + targetColor;

		if(tempColor.value) color2Tag(targetID, colorInput);
	}

	// 히든필드에 저장된 색상값을 태그로 표시
	function color2Tag(targetID, colorInput, divWrap) {
		var colorTag = $(targetID);
		var colorText = $(colorInput).value;
		var tempColor = "";

		if(typeof(divWrap) == "undefined") divWrap = "";

		colorTag.innerHTML = "";
		for(i = 0; i < colorText.length; i = i + 7) {
			tempColor = colorText.substr(i, 7);
			if(tempColor) {
				if(i > 0 && i % 63 == 0 && divWrap) {
					colorTag.innerHTML += "</div><div style=\"height:1px; font-size:1px;\">&nbsp;</div><div style=\"text-align:left;\">";
				}
				colorTag.innerHTML += "<div href=\"javascript:;\" style=\"background-color:" + tempColor + "\" class=\"paletteColor_selected\" ondblclick=\"deleteColor('" + targetID + "', this.style.backgroundColor, '" + colorInput + "', '" + divWrap + "');\"></div>";
			}
		}

		if(colorTag.innerHTML) {
			colorTag.innerHTML += "<div style=\"clear:left;\"></div>";
			if(divWrap) colorTag.innerHTML = "<div style=\"text-align:left;\">" + colorTag.innerHTML + "</div>";
		}
		else {
			colorTag.innerHTML = "&nbsp;";
		}
	}

	// 색상을 제거
	function deleteColor(targetID, delColor, colorInput, divWrap) {
		delColor = convColor(delColor);

		delColor = delColor.toUpperCase();
		$(colorInput).value = $(colorInput).value.toUpperCase();
		$(colorInput).value = $(colorInput).value.replace(delColor, "");
		color2Tag(targetID, colorInput, divWrap);
	}

	// 선택된 상품별 색상 설정
	function applyColor() {
		setOptObj = document.getElementsByName('colorSetOpt');
		chkGoods = document.getElementsByName('chk[]');
		colorText = $('optColor').value.toUpperCase();
		colorList = colorText.split("#");
		var validColorList = new Array();

		// 색상 일괄 적용 옵션 검사
		if(!PubChkSelect(listForm['chk[]'])) { alert( "적용하실 상품을 선택하여 주십시요." ); return; }
		for(i = 0; i < setOptObj.length; i++) if(setOptObj[i].checked == true) var setOptValue = setOptObj[i].value;
		if(!setOptValue) { alert("색상 일괄 적용 옵션을 선택하여 주십시요."); return; }
		if(!colorText && setOptValue != "2") { alert("적용하실 색상을 선택하여 주십시요."); return; }

		// 선택된 색상을 정리
		if(setOptValue == "0") {
			tempNo = 0;
			for(i = 0; i < colorList.length; i++) {
				if(colorList[i]) {
					validColorList[tempNo] = "#" + colorList[i];
					tempNo++;
				}
			}
		}

		// 상품별 처리
		for(ii = 0; ii < chkGoods.length; ii++) {
			if(chkGoods[ii].checked) {
				switch(setOptValue) {
					case "0" :
						for(var j = 0; j < validColorList.length; j++) {
							targetColor = validColorList[j].toUpperCase();
							tempColor = $("setColor_" + chkGoods[ii].value);
							if(tempColor.value.indexOf(targetColor) == -1) tempColor.value = tempColor.value + targetColor;
						}
						break;
					case "1" :
						$("setColor_" + chkGoods[ii].value).value = colorText;
						break;
					case "2" :
						$("setColor_" + chkGoods[ii].value).value = "";
						break;
				}

				color2Tag("goodsColor_" + chkGoods[ii].value, "setColor_" + chkGoods[ii].value, "divWrap");
			}
		}

		listForm.submit();
	}
</script>

<div class="title title_top">빠른 대표색상 수정<span>등록된 상품의 대표색상을 빠르고 편리하게 수정, 관리 하실 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=40')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

<!-- 상품출력조건 : start -->
<form name="searchForm" onsubmit="return chkForm(this)">
<input type="hidden" name="indicate" value="search">

<table class="tb">
<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
<tr>
	<td>분류선택</td>
	<td colspan="3"><script>new categoryBox('cate[]', 4, '<?=$category?>');</script></td>
</tr>
<tr>
	<td>검색어</td>
	<td>
		<select name="skey">
			<option value="goodsnm" <?=$selected['skey']['goodsnm']?>>상품명</option>
			<option value="a.goodsno" <?=$selected['skey']['a.goodsno']?>>고유번호</option>
			<option value="goodscd" <?=$selected['skey']['goodscd']?>>상품코드</option>
			<option value="keyword" <?=$selected['skey']['keyword']?>>유사검색어</option>
		</select>
		<input type="text" name="sword" value="<?=$_GET['sword']?>" class="line">
	</td>
	<td>브랜드</td>
	<td>
		<select name="brandno">
			<option value="">-- 브랜드 선택 --</option>
			<? foreach($brands as $sno => $brandnm){ ?>
			<option value="<?=$sno?>" <?=$selected['brandno'][$sno]?>><?=$brandnm?></option>
			<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>상품가격</td>
	<td>
		<font class="small" color="#444444">
		<input type="text" name="price[]" value="<?=$_GET['price'][0]?>" onkeydown="onlynumber()" size="15" class="rline" /> 원 -
		<input type="text" name="price[]" value="<?=$_GET['price'][1]?>" onkeydown="onlynumber()" size="15" class="rline" /> 원
		</font>
	</td>
	<td>상품출력여부</td>
	<td class="noline">
		<input type="radio" name="open" value="" <?=$checked['open']['']?>>전체
		<input type="radio" name="open" value="11" <?=$checked['open'][11]?>>출력상품
		<input type="radio" name="open" value="10" <?=$checked['open'][10]?>>미출력상품
	</td>
</tr>
<tr>
	<td>상품등록일</td>
	<td colspan="3">
		<input type="text" name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" /> -
		<input type="text" name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" />
		<a href="javascript:setDate('regdt[]', <?=date("Ymd")?>, <?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('regdt[]', <?=date("Ymd", strtotime("-7 day"))?>, <?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('regdt[]', <?=date("Ymd", strtotime("-15 day"))?>, <?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('regdt[]', <?=date("Ymd", strtotime("-1 month"))?>, <?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('regdt[]', <?=date("Ymd", strtotime("-2 month"))?>, <?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td>대표색상</td>
	<td colspan="3">
		<input type="hidden" name="searchColor" id="searchColor" value="<?=$_GET['searchColor']?>" />
		<div style="margin-bottom:5px;"><table border="0" cellpadding="0" cellspacing="2" bgcolor="#FFFFFF"><tr><?
	for($i = 0, $imax = count($colorList); $i < $imax; $i++) {
		echo "<td><div class=\"paletteColor\" style=\"background-color:#".$colorList[$i].";\" onclick=\"selectColor(this.style.backgroundColor, 'selectedSearchColor', 'searchColor')\"></div></td>";
	}
		?></tr></table></div>
		<div class="selColorText">선택색상 : </div><div id="selectedSearchColor" style="float:left;">&nbsp;</div>
	</td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>


<div style="margin-top:20px;">

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td align="left" class="pageInfo ver8">총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode['total']?></b>개, <b><?=$pg->page['now']?></b> of <?=$pg->page['total']?> Pages</td>
	<td align="right">
		<img src="../img/sname_output.gif" align=absmiddle>
		<select name=page_num onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>개 출력
		<? } ?>
		</select>
	</td>
</tr>
</table>
</div>
</form>
<!-- 상품출력조건 : end -->

<form name="listForm" method="post" action="indb.php">
<input type="hidden" name="mode" value="colorModify">
<input type="hidden" name="category" value="<?=$category?>">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="9"></td></tr>
<tr class="rndbg">
	<th width="35"><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class="white"><font class="small1"><b>선택</a></th>
	<th width="60"><font class="small1"><b>번호</b></font></th>
	<th colspan="2"><font class="small1"><b>상품명</b></font></th>
	<th width="100"><font class="small1"><b>등록일</b></font></th>
	<th width="90"><font class="small1"><b>판매가</b></font></th>
	<th width="100"><font class="small1"><b>옵션1</b></font></th>
	<th width="100"><font class="small1"><b>옵션2</b></font></th>
	<th width="160"><font class="small1"><b>대표색상</b></font></th>
</tr>
<tr><td class="rnd" colspan="9"></td></tr>
<?
$idx = 0;
while(is_resource($res) && $data=$db->fetch($res)) {
	$stock = $data['totstock'];
	$notDel = ($data['inpk_prdno'] && $inpkOSCfg['use'] == 'Y' ? 'notInpk' : '');

	$optQuery = "SELECT opt1, opt2 FROM ".GD_GOODS_OPTION." WHERE goodsno = '".$data['goodsno']."' ORDER BY optno";
	$optResult = $db->query($optQuery);
	$optRows = array();
	while($optData = $db->fetch($optResult)) $optRows[] = $optData;

	if($data['color']) {
		$tempColorList = explode("#", $data['color']);
		$tempColorHTML = "";
		$tempColorHTML .= "<div style=\"text-align:left;\">";
		for($i = 0, $imax = count($tempColorList); $i < $imax; $i++) {
			if($i > 0 && $i % 10 == 0) $tempColorHTML .= "</div><div style=\"height:1px; font-size:1px;\">&nbsp;</div><div style=\"text-align:left;\">";
			if($tempColorList[$i]) $tempColorHTML .= "<div href=\"javascript:;\" style=\"background-color:".$tempColorList[$i]."\" class=\"paletteColor_selected\"></div>";
		}
		$tempColorHTML .= "</div\">";
	}
	else {
		$tempColorHTML = "&nbsp;";
	}
?>
<tr><td height="4" colspan="12"></td></tr>
<tr>
	<td align="center" class="noline"><input type="checkbox" name="chk[]" value="<?=$data['goodsno']?>" /></td>
	<td align="center"><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td width="50"><a href="../../goods/goods_view.php?goodsno=<?=$data['goodsno']?>" target="_blank"><?=goodsimg($data['img_s'], 40, '', 1)?></a></td>
	<td><a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>', 825, 600)"><font class="small1" color="0074BA"><?=$data['goodsnm']?></a></td>
	<td align="center"><font class="ver81" color="#444444"><?=substr($data['regdt'], 0, 10)?></td>
	<td align="right" style="padding-right:10px" nowrap><font class="ver8" color="#444444"><b><?=number_format($data['price'])?></b></font></td>
	<td align="center" colspan="2">
<? if(count($optRows) && ($optRows[0]['opt1'] || $optRows[0]['opt2'])) { ?>
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<col class="optBox ver81"><col class="optBox ver81">
	<?
	for($i = 0, $imax = count($optRows); $i < $imax; $i++) {
		if($imax > 1 && $imax - 1 != $i) $optBoxLine = "class=\"optBoxLine\"";
		else $optBoxLine = "";
	?>
		<tr align="center">
			<td <?=$optBoxLine?>><?=$optRows[$i]['opt1']?></td>
			<td <?=$optBoxLine?>><?=$optRows[$i]['opt2']?></td>
		</tr>
	<? } ?>
		</table>
<? } ?>
	</td>
	<td align="left"><input type="hidden" name="setColor[<?=$data['goodsno']?>]" id="setColor_<?=$data['goodsno']?>" value="<?=$data['color']?>" /><div id="goodsColor_<?=$data['goodsno']?>"><?=$tempColorHTML?></div></td>
</tr>
<tr><td height="4"></td></tr>
<tr><td colspan="9" class="rndline"></td></tr>
<?
	$idx++;
}
?>
</table>

<div align="center" class="pageNavi"><font class="ver8"><?=$pg->page['navi']?></font></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td rowspan="2">상품 대표색상<br />일괄 수정/적용</td>
	<td class="noline">
		<input type="radio" name="colorSetOpt" id="colorSetOpt_0" value="0" /> <label for="colorSetOpt_0">선택한 상품의 대표색상을 아래에 선택된 색상으로 일괄 추가 합니다.</label><br />
		<input type="radio" name="colorSetOpt" id="colorSetOpt_1" value="1" /> <label for="colorSetOpt_1">선택한 상품의 대표색상을 아래에 선택된 색상으로 일괄 변경 합니다.</label><br />
		<input type="radio" name="colorSetOpt" id="colorSetOpt_2" value="2" /> <label for="colorSetOpt_2">선택한 상품의 대표색상을 초기화 합니다.</label>
	</td>
</tr>
<tr>
	<td>
		<input type="hidden" name="optColor" id="optColor" />
		<div style="margin-bottom:5px;"><table border="0" cellpadding="0" cellspacing="2" bgcolor="#FFFFFF"><tr><?
	for($i = 0, $imax = count($colorList); $i < $imax; $i++) {
		echo "<td><div class=\"paletteColor\" style=\"background-color:#".$colorList[$i].";\" onclick=\"selectColor(this.style.backgroundColor, 'selectedColor', 'optColor')\"></div></td>";
	}
		?></tr></table></div>
		<div class="selColorText">선택색상 : </div><div id="selectedColor" style="float:left;">&nbsp;</div>
	</td>
</tr>
</table>
<div class="button"><a href="javascript:;" onclick="applyColor();"><img src="../img/btn_modify.gif" /></a></div>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr>
	<td>
		상품에 적용된 대표색상으로 상품검색이 가능합니다.<br />
		검색된 상품들의 옵션값을 확인 후, 대표색상을 일괄 추가,변경,수정 할 수 있습니다.<br /><br /><br />
		상품대표색상 일괄 수정/적용<br />
		<img src="../img/icon_list.gif" align="absmiddle">일괄추가 : 리스트에서 대표색상을 추가할 상품을 선택하시고 아래 일괄적용 선택에서 ‘일괄 추가 합니다’ 를 선택합니다.<br />
		　　　　　 상품에 추가할 대표색상을 선택 후 [수정] 버튼을 클릭하시면, 기존 대표색상은 그대로 있고, 추가로 선택한 색상이 대표색상에 추가되어 적용 됩니다.<br />
		<img src="../img/icon_list.gif" align="absmiddle">일괄변경 : 리스트에서 대표색상을 변경할 상품을 선택하시고 아래 일괄적용 선택에서 ‘일괄 변경 합니다’ 를 선택합니다.<br />
		　　　　　 변경할 대표색상을 선택 후 [수정] 버튼을 클릭하시면, 기존 대표색상은 삭제되고, 추가로 선택한 색상이 대표 색상으로 변경되어 적용 됩니다.<br />
		<img src="../img/icon_list.gif" align="absmiddle">초 기 화 : 리스트에서 대표색상을 초기화 할 상품을 선택하시고 아래 일괄적용 선택에서 ‘초기화 합니다’ 를 선택합니다.<br />
		　　　　　 [수정] 버튼을 클릭하시면, 기존 대표색상이 삭제되어 초기화 됩니다.
	</td>
</tr>
</table>
</div>
<script>
	window.onload = function() {
		cssRound('MSG01');
		color2Tag('selectedColor', 'optColor');
		color2Tag('selectedSearchColor', 'searchColor');
	}
</script>


<? include "../_footer.php"; ?>
