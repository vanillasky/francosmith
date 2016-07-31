<?
include "../_header.popup.php";
include "../../conf/config.pay.php";
require "../../lib/load.class.php";

$goodsHelper   = Clib_Application::getHelperClass('front_goods');

$goodsModel    = $goodsHelper->getGoodsModel(Clib_Application::request()->get('goodsno'));
$categoryModel = $goodsHelper->getCategoryModel(Clib_Application::request()->get('category'), $goodsModel);

$icon = $goodsModel->getIconHtml('../');

$view = $goodsHelper->getGoodsDataArray($goodsModel, $categoryModel);
extract(Clib_Application::storage()->get());
$view['icon'] = $icon;

?>
<script src="../../lib/js/countdown.js"></script>
<style>
/* goods_spec list */
#goods_spec table { width:100%; }
#goods_spec .top { border-top:1px solid #DDDDDD; border-bottom:1px solid #DDDDDD; background:#f7f7f7; }
#goods_spec .sub { border-bottom-width:1; border-bottom-style:solid;border-bottom-color:#DDDDDD; margin-bottom:10px; }
#goods_spec th, #goods_spec td { padding:3px; }
#goods_spec th { width: 80px; text-align:right; font-weight:normal; }
#goods_spec td { text-align:left; }

.goods-multi-option {display:none;}
.goods-multi-option table {border:1px solid #D3D3D3;}
.goods-multi-option table td {border-bottom:1px solid #D3D3D3;padding:10px;}
</style>

<script>
var price = new Array();
var reserve = new Array();
var consumer = new Array();
var memberdc = new Array();
var realprice = new Array();
var couponprice = new Array();
var special_discount_amount = new Array();
var coupon = new Array();
var cemoney = new Array();
var opt1img = new Array();
var opt2icon = new Array();
var opt2kind = "{optkind[1]}";
var oldborder = "";

<?
	// �ɼǺ� ��������
	$i = 0;
	if(is_array($opt)) foreach($opt as $oList_k => $oList_v) {
		foreach($oList_v as $oField_k => $oField_v) {
			$tmpKey = get_js_compatible_key($oField_v['opt1']).(($oField_v['opt2']) ? '|'.get_js_compatible_key($oField_v['opt2']) : '');
			if($i == 0) echo "var fkey = \"$tmpKey\";";
?>
price['<?=$tmpKey?>'] = <?=$oField_v['price']?>;
reserve['<?=$tmpKey?>'] = <?=$oField_v['reserve']?>;
consumer['<?=$tmpKey?>'] = <?=$oField_v['consumer']?>;
memberdc['<?=$tmpKey?>'] = <?=$oField_v['memberdc']?>;
realprice['<?=$tmpKey?>'] = <?=$oField_v['realprice']?>;
coupon['<?=$tmpKey?>'] = <?=$oField_v['coupon']?>;
couponprice['<?=$tmpKey?>'] = <?=$oField_v['couponprice']?>;
cemoney['<?=$tmpKey?>'] = <?=$oField_v['coupon_emoney']?>;
special_discount_amount['<?=$tmpKey?>'] = <?=$oField_v['special_discount_amount']?>;
<?
			$i++;
		}
	}

	// �ɼǺ� �̹���
	if(is_array($opt1img)) foreach($opt1img as $k => $v) echo "opt1img['$k'] = \"$v\"\n";

	// �ɼ�2�� ������
	if(is_array($opticon[1])) foreach($opticon[1] as $k => $v) echo "opt2icon['$k'] = \"$v\"\n";
?>

/* �ʼ� �ɼ� �и��� ��ũ��Ʈ start */
var opt = new Array();
opt[0] = new Array("('1���ɼ��� ���� �������ּ���','')");
<?
	$i = 1;
	if(is_array($opt)) foreach($opt as $k => $v) {
		$tmpOpt = "opt['$i'] = new Array(\"('== �ɼǼ��� ==','')\",";
		foreach($v as $k2 => $v2) {
			$tmpOpt .= "\"('".$v2['opt2'];
			if($v2['price'] != $view['price']) $tmpOpt .= "(".number_format($view['price'])."��)";
			if($view['usestock'] && !$v2['stock']) $tmpOpt .= " [ǰ��]";
			$tmpOpt .= "','".$v2['opt2']."','";
			if($view['usestock'] && !$v2['stock']) $tmpOpt .= "soldout";
			$tmpOpt .= "')\"";

			if(isset($v[$k2 + 1])) $tmpOpt .= ",";
		}
		$tmpOpt .= ");";
		echo $tmpOpt."\n";
		$i++;
	}
?>

function subOption(obj) {
	var el = document.getElementsByName('opt[]');
	var sub = opt[obj.selectedIndex];

	while(el[1].length > 0) el[1].options[el[1].options.length - 1] = null;

	for(i = 0; i < sub.length; i++) {
		var div = sub[i].replace("')", "").split("','");
		eval("el[1].options[i] = new Option" + sub[i]);

		if (div[2] == "soldout") {
			el[1].options[i].style.color = "#808080";
			el[1].options[i].setAttribute('disabled', 'disabled');
		}
	}

	el[1].selectedIndex = el[1].preSelIndex = 0;
	if (el[0].selectedIndex == 0) chkOption(el[1]);
}
/* �ʼ� �ɼ� �и��� ��ũ��Ʈ end */

function chkOptimg() {
	var opt = document.getElementsByName('opt[]');
	var key = opt[0].selectedIndex;
	var opt1 = opt[0][key].value;
	var ropt = opt1.split('|');
	chgOptimg(ropt[0])
}

function chgOptimg(opt1) {
	if(opt1img[opt1]) objImg.src = "../../data/goods/" + opt1img[opt1];
	else objImg.src = "../../data/goods/<?=$view['r_img'][0]?>";
}

function chkOption(obj) {
	if(!selectDisabled(obj)) return false;
}

function act(target) {
	var form = document.frmView;
	form.action = target + ".php";

	var opt_cnt = 0, data;

	nsGodo_MultiOption.clearField();

	for (var k in nsGodo_MultiOption.data) {
		data = nsGodo_MultiOption.data[k];
		if (data && typeof data == 'object') {
			nsGodo_MultiOption.addField(data, opt_cnt);
			opt_cnt++;
		}
	}

	if(opt_cnt > 0) form.submit();
	else if(chkGoodsForm(form)) form.submit();
}

function chgImg(obj) {
	var objImg = document.getElementById('objImg');

	if(obj.getAttribute("ssrc")) objImg.src = obj.src.replace(/\/t\/[^$]*$/g, '/')+obj.getAttribute("ssrc");
	else objImg.src = obj.src.replace("/t/","/");
}

function applyGoods() {
	var applyCheck = 0;
	var opt = "";
	var addopt = "";
	var addopt_inputable = "";
	var stock = "";
	<? if($view['runout']) { ?>
	alert("ǰ���� ��ǰ�Դϴ�.");
	return;
	<? } ?>

<? if($opt) { ?>
	for(var i in nsGodo_MultiOption.data) {
		if(nsGodo_MultiOption.data[i] !== null) if(nsGodo_MultiOption.data[i].opt_price != undefined) {
			if(opt)		opt = opt + "|||";
			if(addopt)	addopt = addopt + "|||";
			if(addopt_inputable)	addopt_inputable = addopt_inputable + "|||";
			if(stock)	stock = stock + "|||";

			opt = opt + nsGodo_MultiOption.data[i].opt;
			addopt = addopt + nsGodo_MultiOption.data[i].addopt;
			addopt_inputable = addopt_inputable + nsGodo_MultiOption.data[i].addopt_inputable;
			stock = stock + "" + nsGodo_MultiOption.data[i].ea;

			applyCheck++;
		}
	}
<? } ?>

	if(!applyCheck) {
<? if($opt) { /* �ɼ��� ������ ������ applyCheck���� �����ؾ� �� */ ?>
		alert("�ɼ�<?=($addopt) ? ' �� �߰��ɼ�' : ''?>�� ������ �ּ���.");
		return false;
<? } else { /* �ɼ��� ������ applyCheck���� �����Ƿ� ���� ���õ� �߰��ɼ��̳�  */ ?>
		// �߰��ɼ� �˻�
		aoList = document.getElementsByName('addopt[]');
		temp_ea = document.getElementsByName('ea');
		stock = temp_ea[0].value;

		if(aoList.length) {
			for(i = 0; i < aoList.length; i++) {
				if(aoList[i].selectedIndex == 0) {
					alert("�߰��ɼ��� ������ �ּ���.");
					aoList[i].focus();
					return false;
				}

				if(addopt) addopt = addopt + ",";
				addopt = addopt + aoList[i].value;
			}
		}
<? } ?>
	}

	<? if($opt) { ?>$('S_opt').value = opt;<? } ?>
	<? if($addopt) { ?>$('S_addopt').value = addopt;<? } ?>
	<? if($addopt_inputable) { ?>$('S_addopt_inputable').value = addopt_inputable;<? } ?>
	$('S_stock').value = stock;

	frmView.submit();
}

function resizeIframe(iFrameID) {
	if(parent._ID(iFrameID)) {
		parent._ID(iFrameID).style.height = document.body.scrollHeight;
		document.body.style.margin = '0';
		return true;
	}
}

window.onload = function() {
	resizeIframe('selectiFrame_<?=$_GET['goodsno']?>');
}
</script>

<!-- Start indiv -->
<div class="indiv">

<!-- ��ǰ �̹��� -->
<div style="margin:0px auto 0px auto">
<div style="width:200px;float:left;text-align:center;">
<div style="padding-bottom:10"><?=goodsimg($view['r_img'][0], 150, 'id=objImg', 2)?></div>
<div style="padding-bottom:10">
</div>
	<div align=center>
		<? foreach($view['t_img'] as $k => $v) echo goodsimg($v, 45, "onmouseover='chgImg(this)' class='hand' style='border:1px #CCCCCC solid;", 2); ?>
	</div>
</div>
<!-- ��ǰ ���� ����Ʈ -->
<div id="goods_spec" style="width:350px;float:left;">
<form name="frmView" method="post" action="../order/indb.self_order.php" />
<input type="hidden" name="mode" id="mode" value="addGoods" />
<input type="hidden" name="memID" id="memID" value="<?=$_REQUEST['memID']?>" />
<input type="hidden" name="S_opt" id="S_opt" value="" />
<input type="hidden" name="S_addopt" id="S_addopt" value="" />
<input type="hidden" name="S_addopt_inputable" id="S_addopt_inputable" value="" />
<input type="hidden" name="S_stock" id="S_stock" value="" />
<input type="hidden" name="goodsno" id="goodsno" value="<?=$view['goodsno']?>" />
<input type="hidden" name="goodsCoupon" id="goodsCoupon" value="<?=$view['coupon']?>" />
<div style="padding:10px 0 10px 5px" align="left"><b style="font:bold 12pt ����;"><?=$view['goodsnm']?></b></div>
<div style="padding:0 0 10px 5px;font:11px dotum;letter-spacing:-1px;color:#666666"><?=$view['shortdesc']?></div>

<table border="0" cellpadding="0" cellspacing="0" class="top">
	<tr><td height="2"></td></tr>

	<? if ($view['sales_status'] == 'ing') { ?>
	<!--tr><td colspan="2"><span style="padding-bottom:5px; padding-left:14px; color:#EF1C21">������ �Ǹ���!!</span></td></tr-->
	<? } elseif($view['sales_status'] == 'range') { ?>
	<tr><th>�����ð� :</th><td><span id="el-countdown-1" style="padding-bottom:5px;font:13pt bold;color:#EF1C21"></span></td></tr>
	<script type="text/javascript">
	Countdown.init('<?=date('Y-m-d H:i:s', $view['sales_range_end'])?>', 'el-countdown-1');
	</script>
	<? } elseif($view['sales_status'] == 'before') { ?>
	<tr><td colspan="2"><span style="padding-bottom:5px; padding-left:14px; color:#EF1C21"><?=date('Y-m-d H:i:s', $view['sales_range_start'])?> �ǸŽ����մϴ�.</span></td></tr>
	<? } elseif($view['sales_status'] == 'end') { ?>
	<tr><td colspan="2"><span style="padding-bottom:5px; padding-left:14px; color:#EF1C21">�ǸŰ� ����Ǿ����ϴ�.</span></td></tr>
	<? } ?>

	<? if($view['runout'] && $cfg_soldout['price'] == 'image') { ?>
	<tr><th>�ǸŰ��� :</th><td><img src="../data/goods/icon/custom/soldout_price"></td></tr>
	<? } else if($view['runout'] && $cfg_soldout['price'] == 'string') { ?>
	<tr><th>�ǸŰ��� :</th><td><b><?=$cfg_soldout['price_string']?></b></td></tr>
	<? } else if(!$view['strprice']) { ?>
	<tr>
		<th>�ǸŰ��� :</th>
		<td>
			<? if($view['consumer']) { ?><strike><span id="consumer"><?=number_format($view['consumer'])?></span></strike> ��<? } ?>
			<b><span id="price"><?=number_format($view['price'])?></span>��</b>
		</td>
	</tr>
		<? if($view['special_discount_amount']) { ?>
	<tr>
		<th>��ǰ���αݾ� :</th>
		<td style="font-weight:bold"><?=number_format($view['special_discount_amount'])?>��</td>
	</tr>
		<? } ?>
		<? if($view['memberdc']) { ?>
	<tr>
		<th>ȸ�����ΰ� :</th>
		<td style="font-weight:bold"><span id="obj_realprice"><?=number_format($view['realprice'])."�� (-".number_format($view['memberdc'])."��)"?></span></b></td>
	</tr>
		<? } ?>
		<? if($view['coupon']) { ?>
	<tr><th>�������밡 :</th>
	<td>
	<span id="obj_coupon" style="font-weight:bold;color:#EF1C21"><?=number_format($view['couponprice'])."�� (-".number_format($view['coupon'])."��)"?></span>
	<div><?=$view['about_coupon']?></div>
	</td></tr>
		<? } ?>
	<tr><th>������ :</th><td><span id="reserve"><?=number_format($view['reserve'])?></span>��</td></tr>
		<? if($view['coupon_emoney']) { ?>
	<tr><th>���������� :</th>
	<td>
	<span id="obj_coupon_emoney" style="font-weight:bold; color:#EF1C21"></span> &nbsp;<span style="font:bold 9pt tahoma; color:#FF0000" ><?=number_format($view['coupon_emoney'])?>��</span>
	</td></tr>
		<? } ?>
		<? if($view['delivery_type'] == 1) { ?>
	<tr><th>��ۺ� :</th><td>������</td></tr>
		<? } else if($view['delivery_type'] == 2) { ?>
	<tr><th>������ۺ� :</th><td><?=number_format($view['goods_delivery'])?>��</td></tr>
		<? } else if($view['delivery_type'] == 3) { ?>
	<tr><th>���ҹ�ۺ� :</th><td><?=number_format($view['goods_delivery'])?>��</td></tr>
		<? } ?>
	<? } else { ?>
	<tr><th>�ǸŰ��� :</th><td><b><?=$view['strprice']?></b></td></tr>
	<? } ?>
</table>

<table border="0" cellpadding="0" cellspacing="0">
	<tr><td height="5"></td></tr>
	<? if($view['goodscd']) { ?><tr><th>��ǰ�ڵ� :</th><td><?=$view['goodscd']?></td></tr><? } ?>
	<? if($view['origin']) { ?><tr><th>������ :</th><td><?=$view['origin']?></td></tr><? } ?>
	<? if($view['maker']) { ?><tr><th>������ :</th><td><?=$view['maker']?></td></tr><? } ?>
	<? if($view['brand']) { ?><tr><th>�귣�� :</th><td><?=$view['brand']?></td></tr><? } ?>
	<? if($view['launchdt']) { ?><tr><th>����� :</th><td><?=$view['launchdt']?></td></tr><? } ?>
	<? foreach($view['ex'] as $k => $v) { ?><tr><th><?=$k?> :</th><td><?=$v?></td></tr><? } ?>

	<? if(!$opt) { ?>
	<tr><th>���ż��� :</th>
	<td>
	<? if(!$view['runout']) { ?>
	<div style="float:left;"><input type="text" name="ea" size="2" value="<?=$view['min_ea'] ? $view['min_ea'] : '1' ?>" class="line" style="text-align:right;height:18px" step="<?=$view['sales_unit'] ? $view['sales_unit'] : '1' ?>" min="<?=$view['min_ea'] ? $view['min_ea'] : '1' ?>" max="<?=$view['max_ea'] ? $view['max_ea'] : '0' ?>" onblur="chg_cart_ea(frmView.ea,'set');"></div>
	<div style="float:left;padding-left:3">
	<div style="padding:1 0 2 0"><img src="../img/btn_plus.gif" onClick="chg_cart_ea(frmView.ea,'up')" style="cursor:pointer"></div>
	<div><img src="../img/btn_minus.gif" onClick="chg_cart_ea(frmView.ea,'dn')" style="cursor:pointer"></div>
	</div>
	<div style="padding-top:3; float:left">��</div>
	<div style="padding-left:10px;float:left" class="stxt">
	<? if($view['min_ea'] > 1) { ?><div>�ּұ��ż��� : <?=$view['min_ea']?>��</div><? } ?>
	<? if($view['max_ea'] > 1) { ?><div>�ִ뱸�ż��� : <?=$view['max_ea']?>��</div><? } ?>
	<? if($view['sales_unit'] > 1) { ?><div>�����ֹ����� : <?=$view['sales_unit']?>��</div><? } ?>
	</div>
	<? } else { ?>
	ǰ���� ��ǰ�Դϴ�
	<? } ?>
	</td></tr>
	<? } else { ?>
	<input type="hidden" name="ea" value="<?=$view['min_ea'] ? $view['min_ea'] : '1' ?>" step="<?=$view['sales_unit'] ? $view['sales_unit'] : '1' ?>" min="<?=$view['min_ea'] ? $view['min_ea'] : '1' ?>" max="<?=$view['max_ea'] ? $view['max_ea'] : '0' ?>">
	<? } ?>

	<? if($view['chk_point']) { ?>
	<tr><th>����ȣ�� :</th><td><?=str_repeat("��", $view['chk_point'])?></td></tr>
	<? } ?>
	<? if($view['icon']) { ?><tr><th>��ǰ���� :</th><td><?=$view['icon']?></td></tr><? } ?>
</table>

<? if(!$view['strprice']) { ?>

<!-- �߰� �ɼ� �Է��� -->
<? if ($addopt_inputable) { ?>
<!--{ ? _addopt_inputable }-->
<table border=0 cellpadding=0 cellspacing=0 class=top>
	<?
	$idx = 0;
	foreach($addopt_inputable as $k => $v) {
	?>
	<tr><th><?=$k?> :</th>
	<td>
		<input type="hidden" name="_addopt_inputable[]" value="">
		<input type="text" name="addopt_inputable[]" label="<?=$k?>" option-value="<?=$v['sno']?>^<?=$k?>^<?=$v['opt']?>^<?=$v['addprice']?>" value="" <? if ($addopt_inputable_req[$idx]) {?>required fld_esssential<? } ?> maxlength="<?=$v['opt']?>">
	</td></tr>
	<?
		$idx++;
	}
	?>
</table>
<? } ?>



<!-- ���⼱ ������ ��ü�� �ɼ�����.. -->
	<? if($opt && ($typeOption == "single" || $typeOption == "double")) { ?>
<table border="0" cellpadding="0" cellspacing="0" class="top">
	<tr><td height="6"></td></tr>
	<tr><th valign="top"><?=$view['optnm']?> :</th>
	<td>
	<div>
	<select name="opt[]" onchange="chkOption(this);chkOptimg();nsGodo_MultiOption.set();resizeIframe('selectiFrame_<?=$_GET['goodsno']?>');" required msgR="<?=$view['optnm']?> ������ ���ּ���">
	<option value="">== �ɼǼ��� ==</option>
		<? foreach($opt as $k => $v) { ?>
			<? foreach($v as $k2 => $v2) { ?>
	<option value="<?=$v2['opt1'].(($v2['opt2']) ? "|".$v2['opt2'] : "")?>" <? if($view['usestock'] && !$v2['stock']) { ?>disabled class="disabled"<? } ?>><?=$v2['opt1'].(($v2['opt2']) ? "/".$v2['opt2'] : "")?>
	<? if($v2['price'] != $view['price']) { echo "(".number_format($v2['price'])."��)"; } ?>
	<? if($view['usestock'] && !$v2['stock']) { ?> [ǰ��]<? } ?>
	</option>
			<? } ?>
		<? } ?>
	</select></div>
	</td>
	</tr>
	<tr><td height="6"></td></tr>
</table>
	<? } ?>

<!-- �߰� �ɼ� -->
<table border="0" cellpadding="0" cellspacing="0" class="sub">
	<? $tmpi = 0; if(is_array($addopt)) foreach($addopt as $k => $v) { ?>
	<tr><th><?=$k?> :</th>
	<td>
		<? if($addoptreq[$tmpi]) { ?>
	<select name="addopt[]" required label="<?=$k?>" onchange="nsGodo_MultiOption.set();resizeIframe('selectiFrame_<?=$_GET['goodsno']?>');">
	<option value="">==<?=$k?> ����==</option>
		<? } else { ?>
	<select name="addopt[]" label="<?=$k?>" onchange="nsGodo_MultiOption.set();resizeIframe('selectiFrame_<?=$_GET['goodsno']?>');">
	<option value="">==<?=$k?> ����==</option>
	<option value="-1">���þ���</option>
		<? } ?>
		<? foreach($v as $k2 => $v2) { ?>
	<option value="<?=$v2['sno']?>^<?=$k?>^<?=$v2['opt']?>^<?=$v2['addprice']?>"><?=$v2['opt']?>
			<? if($v2['addprice']) { echo "(".number_format($v2['addprice'])."�� �߰�)"; } ?>
	</option>
		<? } ?>
	</select>
	</td></tr>
	<? $tmpi++; } ?>
</table>

<!-- ? �ɼ� ������ -->
<script>
var nsGodo_MultiOption = function() {

	function size(e) {

		var cnt = 0;
		var type = '';

		for (var i in e) {
			cnt++;
		}

		return cnt;
	}

	return {
		_soldout : <?=$view['runout'] ? 'true' : 'false'?>,
		data : [],
		data_size : 0,
		_optJoin : function(opt) {

			var a = [];

			for (var i=0,m=opt.length;i<m ;i++)
			{
				if (typeof opt[i] != 'undefined' && opt[i] != '')
				{
					a.push(opt[i]);
				}
			}

			return a.join(' / ');

		},
		getFieldTag : function (name, value) {
			var el = document.createElement('input');
			el.type = "hidden";
			el.name = name;
			el.value = value;

			return el;

		},
		clearField : function() {

			var form = document.getElementsByName('frmView')[0];

			var el;

			for (var i=0,m=form.elements.length;i<m ;i++) {
				el = form.elements[i];

				if (typeof el == 'undefined' || el.tagName == "FIELDSET") continue;

				if (/^multi\_.+/.test(el.name)) {
					el.parentNode.removeChild(el);
					i--;
				}

			}

		},
		addField : function(obj, idx) {

			var _tag;
			var form = document.getElementsByName('frmView')[0];

			for(var k in obj) {

				if (typeof obj[k] == 'undefined' || typeof obj[k] == 'function' || (k != 'opt' && k != 'addopt' && k != 'ea' && k != 'addopt_inputable')) continue;

				switch (k)
				{
					case 'ea':
						_tag = this.getFieldTag('multi_'+ k +'['+idx+']', obj[k]);
						form.appendChild(_tag);
						break;
					case 'addopt_inputable':
					case 'opt':
					case 'addopt':
						//hasOwnProperty
						for(var k2 in obj[k]) {
							if (typeof obj[k][k2] == 'function') continue;
							_tag = this.getFieldTag('multi_'+ k +'['+idx+'][]', obj[k][k2]);
							form.appendChild(_tag);
						}

						break;
					default :
						continue;
						break;
				}
			}
		},
		set : function() {

			var add = true;

			// ���� �ɼ�
			var opt = document.getElementsByName('opt[]');
			for (var i=0,m=opt.length;i<m ;i++ )
			{
				if (typeof(opt[i])!="undefined") {
					if (opt[i].value == '') add = false;
				}
			}

			// �߰� �ɼ�?
			var addopt = document.getElementsByName('addopt[]');
			for (var i=0,m=addopt.length;i<m ;i++ )
			{
				if (typeof(addopt[i])!="undefined") {
					if (addopt[i].value == '' /*&& addopt[i].getAttribute('required') != null*/) add = false;
				}
			}

			// �Է� �ɼ��� �̰����� üũ ���� �ʴ´�.
			if (add == true)
			{
				this.add();
			}
		},
		del : function(key) {

			this.data[key] = null;
			var tr = document.getElementById(key);
			tr.parentNode.removeChild(tr);
			this.data_size--;

			// �� �ݾ�
			this.totPrice();

		},
		add : function() {

			var self = this;

			if (self._soldout)
			{
				alert("ǰ���� ��ǰ�Դϴ�.");
				return;
			}

			var form = document.frmView;
			if(!(form.ea.value>0))
			{
				alert("���ż����� 1�� �̻� �����մϴ�");
				return;
			}
			else
			{
				try
				{
					var step = form.ea.getAttribute('step');
					if (form.ea.value % step > 0) {
						alert('���ż����� '+ step +'�� �����θ� �����մϴ�.');
						return;
					}
				}
				catch (e)
				{}
			}

			if (chkGoodsForm(form)) {

				var _data = {};

				_data.ea = document.frmView.ea.value;
				_data.sales_unit = document.frmView.ea.getAttribute('step') || 1;
				_data.opt = new Array;
				_data.addopt = new Array;
				_data.addopt_inputable = new Array;

				// �⺻ �ɼ�
				var opt = document.getElementsByName('opt[]');

				if (opt.length > 0) {

					_data.opt[0] = opt[0].value;
					_data.opt[1] = '';
					if (typeof(opt[1]) != "undefined") _data.opt[1] = opt[1].value;

					var key = _data.opt[0] + (_data.opt[1] != '' ? '|' + _data.opt[1] : '');

					// ����
					if (opt[0].selectedIndex == 0) key = fkey;
					key = self.get_key(key);	// get_js_compatible_key ����

					if (typeof(price[key])!="undefined"){

						_data.price = price[key];
						_data.reserve = reserve[key];
						_data.consumer = consumer[key];
						_data.realprice = realprice[key];
						_data.couponprice = couponprice[key];
						_data.coupon = coupon[key];
						_data.cemoney = cemoney[key];
						_data.memberdc = memberdc[key];
						_data.special_discount_amount = special_discount_amount[key];

					}
					else {
						// @todo : �޽��� ����
						alert('�߰��� �� ����.');
						return;
					}

				}
				else {
					// �ɼ��� ���� ���(or �߰� �ɼǸ� �ִ� ���) �̹Ƿ� ��Ƽ �ɼ� ������ �Ұ�.
					return;
				}

				// �߰� �ɼ�
				var addopt = document.getElementsByName('addopt[]');
				for (var i=0,m=addopt.length;i<m ;i++ ) {

					if (typeof addopt[i] == 'object') {
						_data.addopt.push(addopt[i].value);
					}

				}

				// �Է� �ɼ�
				var addopt_inputable = document.getElementsByName('addopt_inputable[]');
				for (var i=0,m=addopt_inputable.length;i<m ;i++ ) {

					if (typeof addopt_inputable[i] == 'object') {
						var v = addopt_inputable[i].value.trim();
						if (v) {
							var tmp = addopt_inputable[i].getAttribute("option-value").split('^');
							tmp[2] = v;
							_data.addopt_inputable.push(tmp.join('^'));
						}

						// �ʵ尪 �ʱ�ȭ
						addopt_inputable[i].value = '';

					}

				}

				// �̹� �߰��� �ɼ�����
				if (self.data[key] != null)
				{
					alert('�̹� �߰��� �ɼ��Դϴ�.');
					return false;
				}

				// �ɼ� �ڽ� �ʱ�ȭ
				for (var i=0,m=addopt.length;i<m ;i++ )
				{
					if (typeof addopt[i] == 'object') {
						addopt[i].selectedIndex = 0;
					}
				}
				//opt[0].selectedIndex = 0;
				//subOption(opt[0]);

				document.getElementById('el-multi-option-display').style.display = 'block';

				// �� �߰�
				var childs = document.getElementById('el-multi-option-display').childNodes;
				for (var k in childs)
				{
					if (childs[k].tagName == 'TABLE') {
						var table = childs[k];
						break;
					}
				}

				var td, tr = table.insertRow(0);
				var html = '';

				tr.id = key;

				// �Է� �ɼǸ�
				td = tr.insertCell(-1);
				html = '<div style="font-size:11px;color:#010101;padding:3px 0 0 8px;">';
				var tmp,tmp_addopt = [];
				for (var i=0,m=_data.addopt_inputable.length;i<m ;i++ )
				{
					tmp = _data.addopt_inputable[i].split('^');
					if (tmp[2]) tmp_addopt.push(tmp[2]);
				}
				html += self._optJoin(tmp_addopt);
				html += '</div>';

				// �ɼǸ�
				html += '<div style="font-size:11px;color:#010101;padding:3px 0 0 8px;">';
				html += self._optJoin(_data.opt);
				html += '</div>';

				// �߰� �ɼǸ�
				html += '<div style="font-size:11px;color:#A0A0A0;padding:3px 0 0 8px;">';
				var tmp,tmp_addopt = [];
				for (var i=0,m=_data.addopt.length;i<m ;i++ )
				{
					tmp = _data.addopt[i].split('^');
					if (tmp[2]) tmp_addopt.push(tmp[2]);
				}
				html += self._optJoin(tmp_addopt);
				html += '</div>';

				td.innerHTML = html;

				// ����
				td = tr.insertCell(-1);
				html = '';
				html += '<div style="float:left;"><input type=text name=_multi_ea[] id="el-ea-'+key+'" size=2 value='+ _data.ea +' style="border:1px solid #D3D3D3;width:30px;text-align:right;height:20px" onblur="nsGodo_MultiOption.ea(\'set\',\''+key+'\',this.value);"></div>';
				html += '<div style="float:left;padding-left:3">';
				html += '<div style="padding:1 0 2 0"><img src="../img/btn_multioption_ea_up.gif" onClick="nsGodo_MultiOption.ea(\'up\',\''+key+'\');" style="cursor:pointer"></div>';
				html += '<div><img src="../img/btn_multioption_ea_down.gif" onClick="nsGodo_MultiOption.ea(\'down\',\''+key+'\');" style="cursor:pointer"></div>';
				html += '</div>';
				td.innerHTML = html;

				// �ɼǰ���
				_data.opt_price = _data.price;
				for (var i=0,m=_data.addopt.length;i<m ;i++ )
				{
					tmp = _data.addopt[i].split('^');
					if (tmp[3]) _data.opt_price = _data.opt_price + parseInt(tmp[3]);
				}
				for (var i=0,m=_data.addopt_inputable.length;i<m ;i++ )
				{
					tmp = _data.addopt_inputable[i].split('^');
					if (tmp[3]) _data.opt_price = _data.opt_price + parseInt(tmp[3]);
				}
				td = tr.insertCell(-1);
				td.style.cssText = 'padding-right:10px;text-align:right;font-weight:bold;color:#6A6A6A;';
				html = '';
				html += '<span id="el-price-'+key+'">'+comma( _data.opt_price *  _data.ea) + '��</span>';
				html += '<a href="javascript:void(0);" onClick="nsGodo_MultiOption.del(\''+key+'\');return false;"><img src="../img/btn_multioption_del.gif"></a>';
				td.innerHTML = html;

				self.data[key] = _data;
				self.data_size++;

				// �� �ݾ�
				self.totPrice();


			}
		},
		ea : function(dir, key,val) {	// up, down

			var min_ea = 0, max_ea = 0, remainder = 0;

			if (document.frmView.min_ea) min_ea = parseInt(document.frmView.min_ea.value);
			if (document.frmView.max_ea) max_ea = parseInt(document.frmView.max_ea.value);

			if (dir == 'up') {
				this.data[key].ea = (max_ea != 0 && max_ea <= this.data[key].ea) ? max_ea : parseInt(this.data[key].ea) + parseInt(this.data[key].sales_unit);
			}
			else if (dir == 'down')
			{
				if ((parseInt(this.data[key].ea) - 1) > 0)
				{
					this.data[key].ea = (min_ea != 0 && min_ea >= this.data[key].ea) ? min_ea : parseInt(this.data[key].ea) - parseInt(this.data[key].sales_unit);
				}

			}
			else if (dir == 'set') {

				if (val && !isNaN(val))
				{
					val = parseInt(val);

					if (max_ea != 0 && val > max_ea)
					{
						val = max_ea;
					}
					else if (min_ea != 0 && val < min_ea) {
						val = min_ea;
					}
					else if (val < 1)
					{
						val = parseInt(this.data[key].sales_unit);
					}

					remainder = val % parseInt(this.data[key].sales_unit);

					if (remainder > 0) {
						val = val - remainder;
					}

					this.data[key].ea = val;

				}
				else {
					alert('������ 1 �̻��� ���ڷθ� �Է��� �ּ���.');
					return;
				}
			}

			document.getElementById('el-ea-'+key).value = this.data[key].ea;
			document.getElementById('el-price-'+key).innerText = comma(this.data[key].ea * this.data[key].opt_price) + '��';

			// �ѱݾ�
			this.totPrice();

		},
		totPrice : function() {
			var self = this;
			var totprice = 0;
			for (var i in self.data)
			{
				if (self.data[i] !== null && typeof self.data[i] == 'object') totprice += self.data[i].opt_price * self.data[i].ea;
			}

			document.getElementById('el-multi-option-total-price').innerText = comma(totprice) + '��';
		},
		get_key : function(str) {

			str = str.replace(/&/g, "&amp;").replace(/\"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

			var _key = "";

			for (var i=0,m=str.length;i<m;i++) {
				_key += str.charAt(i) != '|' ? str.charCodeAt(i) : '|';
			}

			return _key.toUpperCase();
		}
	}
}();

function chkGoodsForm(form) {

	if (form.min_ea)
	{
		if (parseInt(form.ea.value) < parseInt(form.min_ea.value))
		{
			alert('�ּұ��ż����� ' + form.min_ea.value+'�� �Դϴ�.');
			return false;
		}
	}

	if (form.max_ea)
	{
		if (parseInt(form.ea.value) > parseInt(form.max_ea.value))
		{
			alert('�ִ뱸�ż����� ' + form.max_ea.value+'�� �Դϴ�.');
			return false;
		}
	}

	try
	{
		var step = form.ea.getAttribute('step');
		if (form.ea.value % step > 0) {
			alert('���ż����� '+ step +'�� ������ �����մϴ�.');
			return false;
		}
	}
	catch (e)
	{}

	var res = chkForm(form);

	// �Է¿ɼ� �ʵ尪 ����
	if (res)
	{
		var addopt_inputable = document.getElementsByName('addopt_inputable[]');
		for (var i=0,m=addopt_inputable.length;i<m ;i++ ) {

			if (typeof addopt_inputable[i] == 'object') {
				var v = addopt_inputable[i].value.trim();
				if (v) {
					var tmp = addopt_inputable[i].getAttribute("option-value").split('^');
					tmp[2] = v;
					v = tmp.join('^');
				}
				else {
					v = '';
				}
				document.getElementsByName('_addopt_inputable[]')[i].value = v;
			}
		}
	}

	return res;

}
</script>

<!-- / -->
<? } ?>
</form>
</div>
<div id="el-multi-option-display" class="goods-multi-option" style="padding:10px 0px 10px 25px;float:left;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<col width=""><col width="50"><col width="80">
	</table>
	<div style="width:100%; font-size:12px;text-align:right;padding:10px 20px 10px 0;border-bottom:1px solid #D3D3D3;margin-bottom:5px;">
		<img src="../img/btn_multioption_br.gif" align="absmiddle"> �� �ݾ� : <span style="color:#E70103;font-weight:bold;" id="el-multi-option-total-price"></span>
	</div>
</div>
</div>

<div style="padding-top:5px; clear:left; text-align:right;"><a href="javascript:;" onclick="act('indb.self_order')"><img src="../img/su_btn04.gif" align="absmiddle" /></a></div>

</div>
<!-- End indiv -->

<iframe name="ifrmHidden" src='/shop/blank.txt' style="display:none;width:100%;height:600"></iframe>
