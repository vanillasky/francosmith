<?
$location = "��ǰ�ϰ����� > ���� ��ǥ���� ����";
include "../_header.php";
include "../../lib/page.class.php";

$goodsHelper = Clib_Application::getHelperClass('admin_goods');

// �Ķ���� ����
$params = array(
	'page' => Clib_Application::request()->get('page', 1),
	'page_num' => Clib_Application::request()->get('page_num', 10),
	'cate' => Clib_Application::request()->get('cate'),
	'skey' => Clib_Application::request()->get('skey'),
	'sword' => Clib_Application::request()->get('sword'),
	'regdt' => Clib_Application::request()->get('regdt'),
	'goods_price' => Clib_Application::request()->get('goods_price'),
	'open' => Clib_Application::request()->get('open'),
	'soldout' => Clib_Application::request()->get('soldout'),
	'brandno' => Clib_Application::request()->get('brandno'),
	'sort' => Clib_Application::request()->get('sort', 'goodsno desc'),

	'searchColor' => Clib_Application::request()->get('searchColor'),
);

// ��ǰ ���
$goodsList = $goodsHelper->getGoodsCollection($params);

// ����¡
$pg = $goodsList->getPaging();

// ����
$colorList = array();
$CL_rs = $db->query("SELECT itemnm FROM ".GD_CODE." WHERE groupcd = 'colorList' ORDER BY sort");
while($CL_row = $db->fetch($CL_rs)) $colorList[] = $CL_row['itemnm'];

// ��ǰ �˻� ��
$searchForm = Clib_Application::form('admin_goods_search')->setData(Clib_Application::request()->gets('get'));
?>
<link rel="stylesheet" type="text/css" href="./css/css.css">
<script type="text/javascript" src="../js/adm_form.js"></script>
<script type="text/javascript" src="./js/goods_list.js"></script>
<style type="text/css">
	.paletteColor { width:15px; height:15px; cursor:pointer; border:1px #DDDDDD solid; }
	.paletteColor_selected { float:left; width:15px; height:15px; margin:1px; cursor:pointer; border:1px #DDDDDD solid; }
	.optBox { width:50%; padding:3px 0px; text-align:center; }
	.optBoxLine { border-bottom:1px #DCD8D6 solid; }

	.selColorText { margin-top:3px; font-size:11px; font-family:dotum; color:#0070C0; float:left; }
</style>

<script language="javascript">
	// rgb�ڵ� -> 16�����ڵ�
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

	// ������ �������� �� �����ʵ忡 ���� ����
	function selectColor(targetColor, targetID, colorInput) {
		targetColor = convColor(targetColor);

		targetColor = targetColor.toUpperCase();
		tempColor = $(colorInput);

		if(tempColor.value.indexOf(targetColor) != -1) alert("�̹� �߰��� �����Դϴ�.");
		else tempColor.value = tempColor.value + targetColor;

		if(tempColor.value) color2Tag(targetID, colorInput);
	}

	// �����ʵ忡 ����� ������ �±׷� ǥ��
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

	// ������ ����
	function deleteColor(targetID, delColor, colorInput, divWrap) {
		delColor = convColor(delColor);

		delColor = delColor.toUpperCase();
		$(colorInput).value = $(colorInput).value.toUpperCase();
		$(colorInput).value = $(colorInput).value.replace(delColor, "");
		color2Tag(targetID, colorInput, divWrap);
	}

	// ���õ� ��ǰ�� ���� ����
	function applyColor() {
		setOptObj = document.getElementsByName('colorSetOpt');
		chkGoods = document.getElementsByName('chk[]');
		colorText = $('optColor').value.toUpperCase();
		colorList = colorText.split("#");
		var validColorList = new Array();

		// ���� �ϰ� ���� �ɼ� �˻�
		if(!PubChkSelect(listForm['chk[]'])) { alert( "�����Ͻ� ��ǰ�� �����Ͽ� �ֽʽÿ�." ); return; }
		for(i = 0; i < setOptObj.length; i++) if(setOptObj[i].checked == true) var setOptValue = setOptObj[i].value;
		if(!setOptValue) { alert("���� �ϰ� ���� �ɼ��� �����Ͽ� �ֽʽÿ�."); return; }
		if(!colorText && setOptValue != "2") { alert("�����Ͻ� ������ �����Ͽ� �ֽʽÿ�."); return; }

		// ���õ� ������ ����
		if(setOptValue == "0") {
			tempNo = 0;
			for(i = 0; i < colorList.length; i++) {
				if(colorList[i]) {
					validColorList[tempNo] = "#" + colorList[i];
					tempNo++;
				}
			}
		}

		// ��ǰ�� ó��
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

<h2 class="title">���� ��ǥ���� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=40');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

<!-- ��ǰ������� : start -->
<form class="admin-form" name="frmList" onsubmit="return chkForm(this)">
<input type="hidden" name="sort" value="<?=Clib_Application::request()->get('sort')?>">

<table class="admin-form-table">
<tr>
	<th>�з�����</th>
	<td colspan="3">
	<script type="text/javascript" src="../../lib/js/categoryBox.js"></script>
	<script type="text/javascript">new categoryBox('cate[]',4,'<?=array_pop(array_notnull(Clib_Application::request()->get('cate')))?>');</script>
	</td>
</tr>
<tr>
	<th>�˻���</th>
	<td>
		<?=$searchForm->getTag('skey');?>
		<?=$searchForm->getTag('sword');?>
	</td>
	<th>�귣��</th>
	<td>
		<?=$searchForm->getTag('brandno');?>
	</td>
</tr>
<tr>
	<th>��ǰ����</th>
	<td colspan="3">
	<input type="text" name="goods_price[]" value="<?=$_GET[goods_price][0]?>" onkeydown="onlynumber()" size="15" class="ar"> �� -
	<input type="text" name="goods_price[]" value="<?=$_GET[goods_price][1]?>" onkeydown="onlynumber()" size="15" class="ar"> ��
	</td>
</tr>
<tr>
	<th>��ǰ�����</th>
	<td colspan=3>
	<input type="text" name="regdt[]" value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="ac"> -
	<input type="text" name="regdt[]" value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="ac">
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<th>��ǰ��������</th>
	<td>
		<?php
		foreach ($searchForm->getTag('open') as $label => $tag) {
			echo sprintf('<label>%s%s</label> ',$tag, $label);
		}
		?>
	</td>
	<th>ǰ����ǰ</th>
	<td>
		<?php
		foreach ($searchForm->getTag('soldout') as $label => $tag) {
			echo sprintf('<label>%s%s</label> ',$tag, $label);
		}
		?>
	</td>
</tr>
<tr>
	<th>��ǥ����</th>
	<td colspan="3">
		<input type="hidden" name="searchColor" id="searchColor" value="<?=$_GET['searchColor']?>" >
		<div style="margin-bottom:5px;"><table border="0" cellpadding="0" cellspacing="2" bgcolor="#FFFFFF"><tr><?
	for($i = 0, $imax = count($colorList); $i < $imax; $i++) {
		echo "<td><div class=\"paletteColor\" style=\"background-color:#".$colorList[$i].";\" onclick=\"selectColor(this.style.backgroundColor, 'selectedSearchColor', 'searchColor')\"></div></td>";
	}
		?></tr></table></div>
		<div class="selColorText">���û��� : </div><div id="selectedSearchColor" style="float:left;">&nbsp;</div>
	</td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>

<div class="admin-list-toolbar">
	<div class="list-information">
		�˻� <b><?=number_format($pg->recode['total'])?></b>�� / <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
	</div>

	<div class="list-tool">
	<ul>
		<li><img src="../img/sname_date.gif"><a href="javascript:nsAdminGoodsList.sort('regdt desc')"><img name="sort_regdt_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('regdt')"><img name="sort_regdt" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_product.gif"><a href="javascript:nsAdminGoodsList.sort('goodsnm desc')"><img name="sort_goodsnm_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('goodsnm')"><img name="sort_goodsnm" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_price.gif"><a href="javascript:nsAdminGoodsList.sort('goods_price desc')"><img name="sort_goods_price_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('goods_price')"><img name="sort_goods_price" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_brand.gif"><a href="javascript:nsAdminGoodsList.sort('brandno desc')"><img name="sort_brandno_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('brandno')"><img name="sort_brandno" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_company.gif"><a href="javascript:nsAdminGoodsList.sort('maker desc')"><img name="sort_maker_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('maker')"><img name="sort_maker" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li>
		<img src="../img/sname_output.gif" align=absmiddle>
		<select name=page_num onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=($v == Clib_Application::request()->get('page_num')) ? 'selected' : ''?>><?=$v?>�� ���
		<? } ?>
		</select>
		</li>
	</ul>
	</div>
</div>

</form>
<!-- ��ǰ������� : end -->

<form class="admin-form" name="listForm" method="post" action="indb_adm_goods_manage_color.php" target="ifrmHidden">
<input type="hidden" name="category" value="<?=array_pop(array_notnull(Clib_Application::request()->get('cate')))?>">

<table class="admin-list-table">
<colgroup>
	<col style="width:35px;">
	<col style="width:100px;">
	<col >
	<col style="width:55px;">
	<col style="width:80px;">
	<col style="width:100px;">
	<col style="width:100px;">
</colgroup>
<thead>
<tr>
	<th><a href="javascript:void(0)" onclick="chkBox(document.getElementsByName('chk[]'),'rev')" class="white">����</a></th>
	<th>�ý��ۻ�ǰ�ڵ�</th>
	<th>��ǰ��</th>
	<th>�Ǹűݾ�</th>
	<th>�����</th>
	<th>�ɼ�:�ɼǰ�</th>
	<th>��ǥ����</th>
</tr>
</thead>
<tbody>
<?
foreach ($goodsList as $goods) {

	if($goods['color']) {
		$tempColorList = explode("#", $goods['color']);
		$tempColorHTML = "";
		$tempColorHTML .= "<div style=\"text-align:left;\">";
		for($i = 0, $imax = count($tempColorList); $i < $imax; $i++) {
			if($i > 0 && $i % 10 == 0) $tempColorHTML .= "</div><div style=\"height:1px; font-size:1px;\">&nbsp;</div><div style=\"text-align:left;\">";
			if($tempColorList[$i]) $tempColorHTML .= "<div href=\"javascript:;\" style=\"background-color:#".$tempColorList[$i]."\" class=\"paletteColor_selected\"></div>";
		}
		$tempColorHTML .= "</div\">";
	}
	else {
		$tempColorHTML = "&nbsp;";
	}
?>
<tr class="ac">
	<td class="vt"><input type="checkbox" name="chk[]" value="<?=$goods['goodsno']?>" ></td>
	<td class="vt"><?=$goods->getReadableId()?> <br />(<?=$goods['goodscd']?>)</td>
	<td class="al vt">
		<div>
			<a href="../../goods/goods_view.php?goodsno=<?=$goods->getId()?>" target=_blank><?=goodsimg($goods[img_s],40,'style="vertical-align:middle;border:1px solid #e9e9e9;"',1)?></a>
			<a href="adm_goods_form.php?mode=modify&goodsno=<?=$goods->getId()?>"><?=$goods->getGoodsName()?></a>
			<a href="adm_goods_form.php?mode=modify&goodsno=<?=$goods->getId()?>" onclick="nsAdminGoodsList.edit('<?=$goods->getId()?>');return false;"><img src="../img/icon_popup.gif"></a>
		</div>
	</td>
	<td class="vt price"><?=number_format($goods->getPrice())?></td>
	<td class="vt"><?=Core::helper('date')->format($goods['regdt'],'Y-m-d')?></td>
	<td class="al">
		<?
		if ($goods->hasOptions() == false) {
			echo '���ϻ�ǰ';
		}
		else {
			$optionInfo = array_combine($goods->getOptionName(), $goods->getOptionValue());
			foreach($optionInfo as $optionName => $optionValue) {
				printf('%s : %s <br />', $optionName, $optionValue);
			}
		}
		?>
	</td>
	<td align="left"><input type="hidden" name="setColor[<?=$goods['goodsno']?>]" id="setColor_<?=$goods['goodsno']?>" value="<?=$goods['color']?>" ><div id="goodsColor_<?=$goods['goodsno']?>"><?=$tempColorHTML?></div></td>
</tr>
<? } ?>
</tbody>
</table>

<div class="admin-list-toolbar">
	<div class="left-buttons">
	<a href="javascript:void(0)" onclick="chkBox(document.getElementsByName('chk[]'),'rev')"><img src="../img/btn_allchoice.gif"></a>
	</div>
	<div class="paging"><?=$pg->page['navi']?></div>
</div>

<table class="admin-form-table">
<tr>
	<th rowspan="2">��ǰ ��ǥ����<br >�ϰ� ����/����</th>
	<td class="noline">
		<input type="radio" name="colorSetOpt" id="colorSetOpt_0" value="0" > <label for="colorSetOpt_0">������ ��ǰ�� ��ǥ������ �Ʒ��� ���õ� �������� �ϰ� �߰� �մϴ�.</label><br >
		<input type="radio" name="colorSetOpt" id="colorSetOpt_1" value="1" > <label for="colorSetOpt_1">������ ��ǰ�� ��ǥ������ �Ʒ��� ���õ� �������� �ϰ� ���� �մϴ�.</label><br >
		<input type="radio" name="colorSetOpt" id="colorSetOpt_2" value="2" > <label for="colorSetOpt_2">������ ��ǰ�� ��ǥ������ �ʱ�ȭ �մϴ�.</label>
	</td>
</tr>
<tr>
	<td>
		<input type="hidden" name="optColor" id="optColor" >
		<div style="margin-bottom:5px;"><table border="0" cellpadding="0" cellspacing="2" bgcolor="#FFFFFF"><tr><?
	for($i = 0, $imax = count($colorList); $i < $imax; $i++) {
		echo "<td><div class=\"paletteColor\" style=\"background-color:#".$colorList[$i].";\" onclick=\"selectColor(this.style.backgroundColor, 'selectedColor', 'optColor')\"></div></td>";
	}
		?></tr></table></div>
		<div class="selColorText">���û��� : </div><div id="selectedColor" style="float:left;">&nbsp;</div>
	</td>
</tr>
</table>
<div class="button"><a href="javascript:;" onclick="applyColor();"><img src="../img/btn_modify.gif" ></a></div>
</form>

<ul class="admin-simple-faq">
	<li>��ǰ�� ����� ��ǥ�������� ��ǰ�˻��� �����մϴ�.</li>
	<li>�˻��� ��ǰ���� �ɼǰ��� Ȯ�� ��, ��ǥ������ �ϰ� �߰�,����,���� �� �� �ֽ��ϴ�.</li>
	<li class="blank"></li>
	<li>��ǰ��ǥ���� �ϰ� ����/����</li>
	<li>
		<dl>
			<dt>�ϰ��߰�</dt>
			<dd>
				����Ʈ���� ��ǥ������ �߰��� ��ǰ�� �����Ͻð� �Ʒ� �ϰ����� ���ÿ��� '�ϰ� �߰� �մϴ�' �� �����մϴ�. <br />
				��ǰ�� �߰��� ��ǥ������ ���� �� [����] ��ư�� Ŭ���Ͻø�, ���� ��ǥ������ �״�� �ְ�, �߰��� ������ ������ ��ǥ���� �߰��Ǿ� ���� �˴ϴ�.
			</dd>
		</dl>

		<dl>
			<dt>�ϰ�����</dt>
			<dd>
				����Ʈ���� ��ǥ������ ������ ��ǰ�� �����Ͻð� �Ʒ� �ϰ����� ���ÿ��� '�ϰ� ���� �մϴ�' �� �����մϴ�.<br />
				������ ��ǥ������ ���� �� [����] ��ư�� Ŭ���Ͻø�, ���� ��ǥ������ �����ǰ�, �߰��� ������ ������ ��ǥ �������� ����Ǿ� ���� �˴ϴ�.
			</dd>
		</dl>

		<dl>
			<dt>�ʱ�ȭ</dt>
			<dd>
				����Ʈ���� ��ǥ������ �ʱ�ȭ �� ��ǰ�� �����Ͻð� �Ʒ� �ϰ����� ���ÿ��� '�ʱ�ȭ �մϴ�' �� �����մϴ�.<br />
				 [����] ��ư�� Ŭ���Ͻø�, ���� ��ǥ������ �����Ǿ� �ʱ�ȭ �˴ϴ�.
			</dd>
		</dl>
	</li>

</ul>

<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){
	color2Tag('selectedColor', 'optColor');
	color2Tag('selectedSearchColor', 'searchColor');
	nsAdminGoodsList.sortInit('<?=Clib_Application::request()->get('sort')?>');
});
</script>

<? include "../_footer.php"; ?>
