<?
$location = "���̹� ���� > ���̹� ���� ����/����";
include "../_header.php";
include "../../conf/fieldset.php";

$strPath = "../../conf/naverCheckout.cfg.php";
if(file_exists($strPath)) require $strPath;

if(!$checkoutCfg['testYn'])$checkoutCfg['testYn']='n';
if(!$checkoutCfg['useYn'])$checkoutCfg['useYn']='n';
if(!$checkoutCfg['detailImg'])$checkoutCfg['detailImg']=0;
if(!$checkoutCfg['cartImg'])$checkoutCfg['cartImg']=0;
if(!$checkoutCfg['ncMemberYn'])$checkoutCfg['ncMemberYn']='n';
if(!$checkoutCfg['imgType'])$checkoutCfg['imgType']="A";
if(!$checkoutCfg['imgColor'])$checkoutCfg['imgColor']="1";
if(!$checkoutCfg['mobileButtonTarget'])$checkoutCfg['mobileButtonTarget']="self";

$checked['testYn'][$checkoutCfg['testYn']] = "checked";
$checked['useYn'][$checkoutCfg['useYn']] = "checked";
$checked['detailImg'][$checkoutCfg['detailImg']] = "checked";
$checked['cartImg'][$checkoutCfg['cartImg']] = "checked";
$checked['ncMemberYn'][$checkoutCfg['ncMemberYn']] = "checked";
$checked['mobileButtonTarget'][$checkoutCfg['mobileButtonTarget']] = "checked";
$selected['imgType'][$checkoutCfg['imgType']] = "selected";
$selected['imgColor'][$checkoutCfg['imgColor']] = "selected";
$selected['mobileImgType'][$checkoutCfg['mobileImgType']] = "selected";
$selected['mobileImgColor'][$checkoutCfg['mobileImgColor']] = "selected";

// ȸ���������� ����
if($joinset['status'] == '1') $joinsetStatus = "<span style=\"color:#0000FF;\">������������ (��밡��)</span>";
else $joinsetStatus = "<span style=\"color:#FF0000;\">������ ���� �� ���� (���Ұ�)</span>";

// ȸ�����Լ��� ����
$resnoUse = ($checked['useField']['resno'] == "checked") ? "<span style=\"color:#0000FF;\">O üũ</span>" : "<span style=\"color:#FF0000;\">X ��üũ</span>";
$resnoReq = ($checked['reqField']['resno'] == "checked") ? "<span style=\"color:#0000FF;\">O üũ</span>" : "<span style=\"color:#FF0000;\">X ��üũ</span>";

// �Ǹ�Ȯ��/������ ����
if(($ipin['useyn'] == 'y' && $ipin['id']) && !($realname['useyn'] == 'y' && $realname['id'])) {
	$ipinStatus = ($ipin['useyn'] == 'y' && $ipin['id']) ? "<span style=\"color:#FF0000;\">���</span>" : "<span style=\"color:#FF0000;\">������</span>";
	$realStatus = ($realname['useyn'] == 'y' && $realname['id']) ? "<span style=\"color:#FF0000;\">���</span>" : "<span style=\"color:#FF0000;\">������</span>";

} else {
	$ipinStatus = ($ipin['useyn'] == 'y' && $ipin['id']) ? "<span style=\"color:#0000FF;\">���</span>" : "<span style=\"color:#0000FF;\">������</span>";
	$realStatus = ($realname['useyn'] == 'y' && $realname['id']) ? "<span style=\"color:#0000FF;\">���</span>" : "<span style=\"color:#0000FF;\">������</span>";
}

if($checkoutCfg[e_exceptions]){
	$res = $db->query("select * from gd_goods where goodsno in (".implode(',',$checkoutCfg['e_exceptions']).")");
	while($tmp = $db->fetch($res))$e_exceptions[] = $tmp;
}

// �ֹ�API
$config = Core::loader('config');
$checkoutapi = $config->load('checkoutapi');

// �ΰ� ���� URL
$tmpProtocol = explode("/", $_SERVER['SERVER_PROTOCOL']);
$tmpURL = ($cfg['shopUrl']) ? str_replace("/", "", $cfg['shopUrl']) : str_replace("/", "", $_SERVER['HTTP_HOST']);
$idPlusURLHeader = strtolower($tmpProtocol[0])."://".$tmpURL.$cfg['rootDir'];

// �ֹ����� ���� (�ù�� ������ �ʿ��ϹǷ� �ε�)
	@include(dirname(__FILE__).'/../order/_cfg.integrate.php');
?>

<?php include dirname(__FILE__).'/../naverCommonInflowScript/configure.php'; ?>

<style type="text/css">
img {border:none;}
</style>
<script type="text/javascript">
function chkYn()
{
	var chk = document.getElementsByName('useYn');
	var lyr = document.getElementById('sub');
	lyr.disabled=true;
	for(var i=0;i<lyr.getElementsByTagName('a').length;i++) {
		lyr.getElementsByTagName('a')[i].style.visibility = 'hidden';
	}
	for(var i=0;i<chk.length;i++)
	{
		if(chk[0].checked == true) {
			lyr.disabled=false;
			for(var i=0;i<lyr.getElementsByTagName('a').length;i++) {
				lyr.getElementsByTagName('a')[i].style.visibility="visible";
			}
			if (document.getElementById('naverCheckoutApiRequest') != null) {
				document.getElementById('sub_api').disabled=true;
			} else {
				document.getElementById('sub_api').disabled=false;
			}
		}
	}
}
function copy_txt(val)
{
	window.clipboardData.setData('Text', val);
}
function checkForm(f)
{
	var obj = f.useYn;
	if(obj[0].checked && !chkForm(f)) return false;
	return true;
}
function set_imgColorOtion(se1){
	var t = 1;
	var i = 0;
	var k = 0;
	var se2 = document.getElementsByName('imgColor')[0];
	if(se1.selectedIndex >= 2) t = 3;

	for ( i = se2.length-1 ; i > -1 ; i--) {
		se2.options[i].value = null;
		se2.options[i] = null;
	}
	for (i=0;i<t;i++){
		k = i+1;
		se2.options[i] = new Option(k+'����');
		se2.options[i].value = i+1;
	}
}
function preview(){
	var se1 = document.getElementsByName('imgType')[0];
	var se2 = document.getElementsByName('imgColor')[0];
	var img = '';
	img = se1.options[se1.selectedIndex].value + se2.options[se2.selectedIndex].value;
	document.getElementById('previewImg').innerHTML = "<img src='http://gongji.godo.co.kr/userinterface/naverCheckout/images/"+img+"'/>";
}
function set_mobileImgColorOtion(se1){
	var t = 1;
	var i = 0;
	var k = 0;
	var se2 = document.getElementsByName('mobileImgColor')[0];
	t = 1;

	for ( i = se2.length-1 ; i > -1 ; i--) {
		se2.options[i].value = null;
		se2.options[i] = null;
	}
	for (i=0;i<t;i++){
		k = i+1;
		se2.options[i] = new Option(k+'����');
		se2.options[i].value = i+1;
	}
}
function mobilePreview(){
	var se1 = document.getElementsByName('mobileImgType')[0];
	var se2 = document.getElementsByName('mobileImgColor')[0];
	var img = '';
	img = se1.options[se1.selectedIndex].value + se2.options[se2.selectedIndex].value;
	document.getElementById('previewMobileImg').innerHTML = "<img src='http://gongji.godo.co.kr/userinterface/naverCheckout/images/"+img+"'/>";
}
function chk_add_category(cate)
{
	var i=0;
	var j=0;
	var category = document.getElementsByName('e_category[]');
	for(i=0;i<category.length;i++){
		for(j=3;j<=cate.length;j=j+3){
			if(cate.substring(0,j)==category[i].value)return false;
		}
	}
	return true;
}
function exec_add()
{
	var ret;
	var str = new Array();
	var obj = document.forms['fm']['cate[]'];
	for (i=0;i<obj.length;i++){
		if (obj[i].value){
			str[str.length] = obj[i][obj[i].selectedIndex].text;
			ret = obj[i].value;
		}
	}
	if (!ret){
		alert('ī�װ��� �������ּ���');
		return;
	}
	if(!chk_add_category(ret)){
		alert('�ߺ��� ī�װ� �Դϴ�.');
		return;
	}
	var obj = document.getElementById('objCategory');
	oTr = obj.insertRow();
	oTd = oTr.insertCell();
	oTd.id = "currPosition";
	oTd.innerHTML = str.join(" > ");
	oTd = oTr.insertCell();
	oTd.innerHTML = "\<input type=text name=e_category[] value='" + ret + "' style='display:none'>";
	oTd = oTr.insertCell();
	oTd.innerHTML = "<a href='javascript:void(0)' onClick='cate_del(this.parentNode.parentNode)'><img src='../img/i_del.gif' align=absmiddle></a>";
}

function cate_del(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objCategory');
	obj.deleteRow(idx);
}

function showExample(layerID) {
	exObj = document.getElementById(layerID);
	if(exObj.style.display == "none") exObj.style.display = "";
	else exObj.style.display = "none"
}

window.onload = function() {
	sel_imgType = document.getElementById('imgType');
	sel_imgColor = document.getElementById('imgColor');
	imgColor_value = "<?=$checkoutCfg['imgColor']?>";

	set_imgColorOtion(sel_imgType);
	if(imgColor_value) {
		sel_imgColor.options[imgColor_value - 1].selected = true;
	}

	preview();

	sel_mobileImgType = document.getElementById('mobileImgType');
	sel_mobileImgColor = document.getElementById('mobileImgColor');
	mobileImgColor_value = "<?=$checkoutCfg['mobileImgColor']?>";

	set_mobileImgColorOtion(sel_mobileImgType);
	if(mobileImgColor_value) {
		sel_mobileImgColor.options[mobileImgColor_value - 1].selected = true;
	}

	mobilePreview();
}
</script>

<div style="width:800px">

<form name="fm" method="post" action="indb.php" onsubmit="return checkForm(this)" target="ifrmHidden" id="naver-service-configure"/>

<div class="title title_top">���̹� üũ�ƿ� ����/���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=14')"><img src="../img/btn_q.gif"></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr height="30">
	<td>��뿩��</td>
	<td class="noline">
	<label><input type="radio" name="useYn" value="y" onclick="chkYn();" <?php echo $checked['useYn']['y'];?>/>���</label><label><input type="radio" name="useYn" value="n" <?php echo $checked['useYn']['n'];?> onclick="chkYn();" />������</label>
	</td>
</tr>
<tr height="30">
	<td>�׽�Ʈ�ϱ�</td>
	<td class="noline">
	<label><input type="radio" name="testYn" value="y"  <?php echo $checked['testYn']['y'];?>/>���</label><label><input type="radio" name="testYn" value="n" <?php echo $checked['testYn']['n'];?> />������</label>
	<div style="padding-top:5;" class="small1 extext">
	<div>�׽�Ʈ�� ��뿡 �����Ͻø� �����ڷ� �α��� ���¿����� üũ�ƿ� ��ư�� �������ϴ�.</div>
	<div>���� ���񽺰� ���� ������ üũ�ƿ� ��� ���� ���̹��� �׽�Ʈ ������ �����ǰ� �˴ϴ�.</div>
	</div>
	</td>
</tr>
<tr height="30">
	<td>��۾�ü ����</td>
	<td>
		<select name="default_dlv_company">
			<? foreach ($integrate_cfg['dlv_company']['checkout'] as $k => $v) { ?>
			<option value="<?=$k?>" <?=($checkoutCfg['default_dlv_company'] == $k) ? 'selected' : ''?>><?=$v?></option>
			<? } ?>
		</select>
	</td>
</tr>
<tr height="30">
	<td>���� ��ۺ�</td>
	<td>
	<input type="text" name="collect" value="<?php echo $checkoutCfg['collect'];?>" onKeyDown="onlynumber()" required />
	</td>
</tr>
</table>
<p/>
<div id="sub" disabled>

<div class="title title_top">���̹� üũ�ƿ� ��������</div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="50">���̹� ������ ID</td>
	<td>
	<input type="text" name="naverId" value="<?php echo $checkoutCfg['naverId'];?>" required msgR="���̹� ������ ID�� �ʼ��Դϴ�." />
	</td>
</tr>
<tr>
	<td height="50">���� ����Ű</td>
	<td>
	<input type="text" style="width:400" name="connectId" value="<?php echo $checkoutCfg['connectId'];?>" required msgR="���� ����Ű�� �ʼ��Դϴ�." />
	</td>
</tr>
<tr>
	<td height="50">�̹��� ����Ű</td>
	<td>
	<input type="text" style="width:400" name="imageId" value="<?php echo $checkoutCfg['imageId'];?>"  required msgR="�̹��� ����Ű�� �ʼ��Դϴ�." />
	</td>
</tr>
</table>
<p/>
<div class="title title_top">���̹� üũ�ƿ� ��ư ����</div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="30">PC ��<br/>��ư����</td>
	<td>
	<div style="padding:5 0 5 0;">
	<select name="imgType" id="imgType" onchange="set_imgColorOtion(this);preview();">
	<option value='A' <?=$selected['imgType']['A']?>>AŸ��</option>
	<option value='B' <?=$selected['imgType']['B']?>>BŸ��</option>
	<option value='C' <?=$selected['imgType']['C']?>>CŸ��</option>
	<option value='D' <?=$selected['imgType']['D']?>>DŸ��</option>
	<option value='E' <?=$selected['imgType']['E']?>>EŸ��</option>
	</select>
	<select name="imgColor" id="imgColor" onchange="preview()">
	<option value='1'>1����</option>
	<option value='2'>2����</option>
	<option value='3'>3����</option>
	</select>
	</div>
	<div style="padding:0 0 5 0;" id="previewImg"></div>
	</td>
</tr>
<tr>
	<td height="30">����� ��<br/>��ư����</td>
	<td>
	<div style="padding:5px 0 5px 0;">
	<select name="mobileImgType" id="mobileImgType" onchange="set_mobileImgColorOtion(this);mobilePreview();">
	<option value="MA" <?=$selected['mobileImgType']['MA']?>>MAŸ��</option>
	<option value="MB" <?=$selected['mobileImgType']['MB']?>>MBŸ��</option>
	</select>
	<select name="mobileImgColor" id="mobileImgColor" onchange="mobilePreview()">
	<option value="1">1����</option>
	<option value="2">2����</option>
	</select>
	</div>
	<div style="padding:0 0 5px 0;" id="previewMobileImg"></div>
	<div style="padding: 10px 0" class="noline">
		<span>��ư��ũ Ÿ�� : </span>
		<input type="radio" name="mobileButtonTarget" value="self" id="mobileButtonTarget-self" <?php echo $checked['mobileButtonTarget']['self']; ?>/>
		<label for="mobileButtonTarget-self">����â</label>
		<input type="radio" name="mobileButtonTarget" value="new" id="mobileButtonTarget-new" <?php echo $checked['mobileButtonTarget']['new']; ?>/>
		<label for="mobileButtonTarget-new">��â</label>
	</div>
	</td>
</tr>
</table>
<p/>

<div class="title title_top">���̹� üũ�ƿ� ���ܻ�ǰ����</div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="50">���� ��ǰ</td>
	<td>
	<div id=divExceptions style="display:<?=$display[relationis]?>;position:relative;z-index:99">
		<div style="padding:5px 0px 0px 5px;"><a href="javascript:;" onclick="javascript:popupGoodschoice('e_exceptions[]', 'exceptionsX');"><img src="../img/btn_goodsChoice.gif" class="hand" align="absmiddle" border="0" /></a></div>
		<div style="padding:5px 0px 0px 5px;"><font class="extext">������: ��ǰ���� �� �ݵ�� �ϴ� ���(����)��ư�� �����ž� ���� ������ �˴ϴ�.</font></div>
		<div id="exceptionsX" style="padding:3px 0px 0px 5px;">
			<?php
				if ($e_exceptions){
					foreach ($e_exceptions as $v){
			?>
				<a href="../../goods/goods_view.php?goodsno=<?php echo $v['goodsno']; ?>" target="_blank"><?php echo goodsimg($v['img_s'], '40,40', '', 1); ?></a>
				<input type=hidden name="e_exceptions[]" value="<?php echo $v['goodsno']; ?>" />
			<?php
					}
				}
			?>
		</div>
	</div>
	</td>
</tr>
<tr>
	<td height="50">���� ī�װ�</td>
	<td>
	<div style="padding:5 0 0 5"><script>new categoryBox('cate[]',4,'','','fm');</script>
	<a href="javascript:exec_add()"><img src="../img/btn_coupon_cate.gif"></a></div>
	<div class="box" style="padding:10 0 10 10">
	<table cellpadding="8" cellspacing=0 id="objCategory" bgcolor="f3f3f3" border="0" bordercolor="#cccccc" style="border-collapse:collapse">
	<?
	if ($checkoutCfg['e_category']){ foreach ($checkoutCfg['e_category'] as $k){ ?>
	<tr>
		<td id="currPosition"><?=strip_tags(currPosition($k))?></td>
		<td><input type="text" name="e_category[]" value="<?=$k?>" style="display:none">
		<td><a href="javascript:void(0)" onClick="cate_del(this.parentNode.parentNode)"><img src="../img/i_del.gif" border=0 align=absmiddle></a>
		</td>
	</tr>
	<? }} ?>
	</table>
	</div>
	</td>
</tr>
</table>
<p/>
<div class="title title_top">���θ��� ���̹� üũ�ƿ� ��ư �����ϱ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=15')"><img src="../img/btn_q.gif"></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="50">ġȯ�ڵ�</td>
	<td>
	<div div style="padding-top:5;">{naverCheckout} <img class="hand" src="../img/i_copy.gif" onclick="copy_txt('{naverCheckout}')" alt="�����ϱ�" align="absmiddle"/></div>
	<div style="padding-top:10;" class="small1 extext">
	<div>�����Ͻ� <b>ġȯ�ڵ�</b>�� <b>��ǰ��ȭ��</b>�� <b>��ٱ���</b> �������� �����Ͻø� üũ�ƿ� ����� �����մϴ�.</div>
	</div>
	</td>
</tr>
<tr>
	<td><div>PC �� ġȯ�ڵ�</div><div style="padding:5 0 5 0">���� ���</div></td>
	<td>
	<div style="padding-top:5"><a href="../../admin/design/codi.php" target="_blank">"���θ� ������ > �����ΰ���"</a> ���� Ʈ�� �޴����� "��ǰ > ��ǰ��ȭ��" �޴�,</div>
	<div style="padding:5 0 5 0"><a href="../../admin/design/codi.php" target="_blank">"���θ� ������ > �����ΰ���"</a> ���� Ʈ�� �޴����� "��ǰ > ��ٱ���" �޴� Ŭ��</div>
	<div style="padding:0 0 5 0">[�ٷα���] �Ǵ� [�ֹ��ϱ�] ��ư �Ʒ��� ġȯ�ڵ� ������ �����մϴ�.</div>
	</td>
</tr>
<tr>
	<td><div>����� �� ġȯ�ڵ�</div><div style="padding:5 0 5 0">���� ���</div></td>
	<td>
	<div style="padding-top:5"><a href="../../admin/mobileShop/codi.php" target="_blank">"���θ� ������ > ����ϼ� > ����ϼ� �����ΰ���"</a> ���� Ʈ�� �޴����� "��ǰ > ��ǰ��ȭ��" �޴�,</div>
	<div style="padding:5 0 5 0"><a href="../../admin/mobileShop/codi.php" target="_blank">"���θ� ������ > ����ϼ� > ����ϼ� �����ΰ���"</a> ���� Ʈ�� �޴����� "��ǰ > ��ٱ���" �޴� Ŭ��</div>
	<div style="padding:0 0 5 0">[�ٷα���] �Ǵ� [�ֹ��ϱ�] ��ư �Ʒ��� ġȯ�ڵ� ������ �����մϴ�.</div>
	</td>
</tr>
</table>
<p/>
<div class="title title_top">�ֹ������ϱ� <span>���̹� üũ�ƿ��� ���� �߻��ϴ� �ֹ��ǰ� ���ǰ��� ������������������ Ȯ���� �� �ֽ��ϴ�.</span></div>
<div style="text-align:center; font-weight:bold; border:solid #e1e1e1 0; border-width:1px 1px 0 1px; background:#F6F6F6; padding:10px;">
	<?php if($checkoutapi['cryptkey']) { ?>
	���̹� üũ�ƿ� API �������Դϴ�.
	<?php } else { ?>
	<a href="indb.api.php" target="ifrmHidden" id="naverCheckoutApiRequest">[���̹� üũ�ƿ� API ��û�ϱ�]</a>
	<?php } ?>
</div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%" id="sub_api" disabled="disabled">
<col class="cellC"><col class="cellL">
<tr height="30">
	<td>�����</td>
	<td class="noline">
	<label><input type="radio" name="linkStock" value="y" <?=frmChecked($checkoutapi['linkStock'],'y')?>/>���</label>
	<label><input type="radio" name="linkStock" value="n" <?=frmChecked($checkoutapi['linkStock'],'n')?>/>������</label>
	<span class="small1 extext">�ֹ��� ������ �� �����ϸ� �ֹ������� ���յ� �� ����˴ϴ�.</span>
	</td>
</tr>
<tr height="30">
	<td>�ֹ����հ���</td>
	<td class="noline">
	<label><input type="radio" name="integrateOrder" value="y" <?=frmChecked($checkoutapi['integrateOrder'],'y')?>/>���</label>
	<label><input type="radio" name="integrateOrder" value="n" <?=frmChecked($checkoutapi['integrateOrder'],'n')?>/>������</label>
	<span class="small1 extext">���θ� �ֹ��������� �ֹ��� �����Ͽ� �����մϴ�.</span>
	</td>
</tr>
</table>

<div class=button>
<input type="image" src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
</div>
</form>



<div style="clear:both;" id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class="small_ex">
<tr>
<td>
	<div>�ݵ�� ���̹� üũ�ƿ� �ɻ簡 �Ϸ� �Ǿ� ���񽺸� ����Ͻ� �� ������ �� ��뿩�θ� ������� �����Ͻʽÿ�.</div>
</td>
</tr>
</table>
</div>

</div>
<script type="text/javascript">
	cssRound('MSG01');
	chkYn();
</script>
<? include "../_footer.php"; ?>