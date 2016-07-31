<?
include "../_header.popup.php";
require_once ('./_inc/config.inc.php');
include "../../conf/config.pay.php";

$goodsno = isset($_GET['goodsno']) ? $_GET['goodsno'] : '';
$goodsno11st = isset($_GET['goodsno11st']) ? $_GET['goodsno11st'] : '';

if ($goodsno11st != '') {
	list($goodsno) = $db->fetch("select goodsno from ".GD_SHOPLE_GOODS_MAP." where 11st='{$goodsno11st}'");
}

$shople = Core::loader('shople');
$data = $shople->getGoods($goodsno);

### �ʼ��ɼ�
$optnm = explode("|",$data['optnm']);
$query = "select * from ".GD_SHOPLE_GOODS_OPTION." where goodsno='$goodsno' ORDER BY `sort`";
$res = $db->query($query);
while ($tmp=$db->fetch($res)){
	$tmp = array_map("htmlspecialchars",$tmp);
	$opt1[] = $tmp['opt1'];
	$opt2[] = $tmp['opt2'];
	$opt[$tmp['opt1']][$tmp['opt2']] = $tmp;

	### ����� ���
	$stock += $tmp['stock'];
}
if ($opt1) $opt1 = array_unique($opt1);
if ($opt2) $opt2 = array_unique($opt2);
if (!$opt){
	$opt1 = array('');
	$opt2 = array('');
}

$checked['tax'][$data['tax']] = "checked";
$checked['usestock'][$data['usestock']] = "checked";
$checked['runout'][$data['runout']] = "checked";
$checked['age_flag'][$data['age_flag']] = "checked";

$imgs = $urls = explode("|",$data['img_m']);

$checked[image_attach_method][file] = $checked[image_attach_method][url] = 'checked';

if (preg_match('/^http(s)?:\/\//',$imgs[0])) {
	$checked[image_attach_method][file] = '';
	$imgs	= array();
}
else {
	$urls	= array();
	$checked[image_attach_method][url] = '';
}

?>
<script type="text/javascript" src="./_inc/common.js"></script>
<script type="text/javascript">
	/* �ɼ� �κ� ���� */
	function delopt1part(rid)
	{
		var obj = document.getElementById(rid);
		var tbOption = document.getElementById('tbOption');
		if (tbOption.rows.length>2) tbOption.deleteRow(obj.rowIndex);
	}
	function delopt2part(cid)
	{
		var obj = document.getElementById(cid);
		var tbOption = document.getElementById('tbOption');

		if (tbOption.rows[0].cells.length<3) return;
		for (i=0;i<tbOption.rows.length;i++){
			tbOption.rows[i].deleteCell(obj.cellIndex);
		}
	}

	/*** ��üũ ***/
	function chkForm2(obj)
	{
		if (!chkOption()) return false;
		if (!chkForm(obj)) return false;
	}

	/*** ��ǰ ����/��� ***/
	function addopt1()
	{
		var name;
		var fm = document.forms[0];
		var tbOption = document.getElementById('tbOption');
		var Rcnt = tbOption.rows.length;
		oTr = tbOption.insertRow();
		oTr.id = "trid_" + Rcnt;

		for (i=0;i<tbOption.rows[0].cells.length;i++){
			oTd = oTr.insertCell();
			switch (i){
				case 0: oTd.innerHTML = "<input type=text class='opt gray' name=opt1[] value='�ɼǸ�1' required label='1���ɼǸ�' ondblclick=\"delopt1part('"+oTr.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\" style='width:110px;'>";
				break;
				case 1:	oTd.innerHTML = "<input type=text name=option[price][] class='opt gray' value='' style='width:65px;'>"; break;
				case 2:	oTd.innerHTML = "<input type=text name=option[consumer][] class='opt gray' value='' style='width:65px;'>"; break;
				default: oTd.innerHTML = "<input type=text name=option[stock][] class='opt gray' value='���' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\"><input type=hidden name=option[optno][]>"; break;
			}
		}
	}

	function addopt2()
	{
		var name;
		var tbOption = document.getElementById('tbOption');
		if (tbOption.rows.length<2){
			alert('1���ɼ��� ���� �߰����ּ���');
			return;
		}

		var Ccnt = tbOption.rows[0].cells.length;

		for (i=0;i<tbOption.rows.length;i++){
			oTd = tbOption.rows[i].insertCell();
			if(!i)oTd.id = "tdid_"+Ccnt;
			oTd.innerHTML = (i) ? "<input type='text' name=option[stock][] class='opt gray' value='���' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">" : "<input type='text' class='opt gray' name=opt2[] value='�ɼǸ�2' required label='2���ɼǸ�' ondblclick=\"delopt2part('"+oTd.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">";
		}
	}

	function delopt1()
	{
		var tbOption = document.getElementById('tbOption');
		if (tbOption.rows.length>2) tbOption.deleteRow();
	}
	function delopt2()
	{
		var tbOption = document.getElementById('tbOption');
		if (tbOption.rows[0].cells.length<7) return;
		for (i=0;i<tbOption.rows.length;i++){
			tbOption.rows[i].deleteCell();
		}
	}

	/*** �ڵ����� �����ʵ忡 �Է°� ���� ***/
	function autoPrice(obj)
	{
		var name = obj.name;
		var el = document.getElementsByName('option[' + name + '][]');
		el[0].value = obj.value;
	}

	function vOption()
	{
		document.fm.stock.disabled = !document.fm.stock.disabled;
		openLayer('objOption');
	}

	function chkOptName(obj){
		if(obj.value=='�ɼǸ�2' || obj.value=='�ɼǸ�1'){
			obj.className = 'fldtitle';
			obj.value = '';
		}
		if(obj.value=='���'){
			obj.className = 'opt';
			obj.value = '';
		}
	}

	function chkOptNameOver(obj){
		if(obj.value==''){
			obj.className = 'opt gray';
			if(obj.name == 'opt1[]') obj.value = '�ɼǸ�1';
			if(obj.name == 'opt2[]') obj.value = '�ɼǸ�2';
			if(obj.name == 'option[stock][]') obj.value = '���';
		}
	}

	function chkOption(){
		var obj = document.getElementsByName('opt1[]');
		var chk = false;
		for(var i=0;i < obj.length;i++){
			 chkOptName(obj[i]);
			 if(obj[i].value == '' && obj.length > 1){
				alert('�ɼ� 1�� �ʼ� �׸��Դϴ�.');
				obj[i].focus();
				return false;
			 }
			 if( (obj[i].value || obj.length > 1) && !chk) chk = true;
		}

		var obj = document.getElementsByName('opt2[]');
		for(var i=0;i < obj.length;i++){
			chkOptName(obj[i]);
			if(chk && obj[i].value == '' && obj.length > 1){
				alert('�ɼ� 2�� �ʼ� �׸��Դϴ�.');
				obj[i].focus();
				return false;
			}
		}

		var obj = document.getElementsByName('option[stock][]');
		for(var i=0;i < obj.length;i++){
			chkOptName(obj[i]);
		}

		return true;
	}

	function setShipDisabled()
	{
		obj = document.getElementsByName('delivery_type');
		for (i = 0; i < obj.length; i++){
			isDisabled = (obj[i].checked == true ? false : true);
			inputObj = obj[i].parentNode.parentNode.getElementsByTagName('td')[1].getElementsByTagName('input');

			for (j = 0; j < inputObj.length; j++){
				inputObj[j].disabled = isDisabled;
				inputObj[j].style.backgroundColor = (isDisabled ? '#DDDDDD' : '#FFFFFF');
			}
		}
	}

	function fnSetImageAttachForm() {

		var m, obj = document.fm.image_attach_method;

		for (var i=0; i <obj.length; i++) {
			if (obj[i].checked)
			 var m = obj[i].value;
		}

		if (m == 'file') {
			document.getElementById('image_attach_method_upload_wrap').style.display = 'block';
			document.getElementById('image_attach_method_link_wrap').style.display = 'none';
		}
		else {
			document.getElementById('image_attach_method_upload_wrap').style.display = 'none';
			document.getElementById('image_attach_method_link_wrap').style.display = 'block';
		}

	}
</script>

<div class="title title_top" style="margin-top:10px;">���� �����Ǹ� ��ǰ ������� <span>11������ ������ ����Ÿ���� Ȯ�� �� ���������� ����մϴ�. &nbsp;&nbsp; <font color="#FF1800"><b>*</b></font> ǥ�õ� �׸��� �ʼ��Է»����Դϴ�.</span></div>

<form name="fm" method="post" action="./indb.goods.php" enctype="multipart/form-data" onsubmit="return chkForm2(this)" target="ifrmHidden">
<input type="hidden" name="goodsno" value="<?=$goodsno?>">

<!-- ī�װ� ���� -->
<input type="hidden" name="category" value="<?=$data['full_dispno']?>" id="catnm" required label="11���� ī�װ�">
<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> �� 11���� ī�װ� ��Ī</b></font> <font class="small1" color="#6d6d6d">(�� ���θ� ��ǰ�� 11���� ī�װ��� ��Ī�Ͽ� ����ϼ���.)</font></div>
<div class="box" style="padding-left:0px">
<table width="100%" cellpadding=1 cellspacing=0 border=1 bordercolor="#cccccc" style="border-collapse:collapse">
<tr>
	<td style="padding:20px 10px" bgcolor=f8f8f8 id="catnm_text">
	<a href="javascript:popupLayer('../shople/popup.config.category.php?full_dispno=<?=$data[full_dispno]?>&idnm=catnm',750,550);">
	<?=($data[full_name] ? $data[full_name] : '�̰��� Ŭ���ؼ� ī�װ��� �Է����ּ���.' )?>
	</a>
	</td>
</tr>
</table>
</div>

<!-- ��ǰ�⺻���� -->
<div style="height:30px"></div>
<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> �� ��ǰ�⺻����</b></font> <font class="small1" color="#6d6d6d">(��ǰ��, �𵨸�, �귣����� Ȯ���Ͻð�, 11������ ����ϱ� ���� ������ �ʿ��� �κ��� �����ϼ���.)</font></div>
<table class="tb">
<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
<tr>
	<td width="120" nowrap>��ǰ��<font color="#FF1800"><b>*</b></font></td>
	<td width="50%"><input type="text" name="goodsnm" style="width:100%" value="<?=$data['goodsnm']?>" required label="��ǰ��"></td>
	<td width="120" nowrap>�𵨸�(��ǰ�ڵ�)<font color="#FF1800"><b>*</b></font></td>
	<td width="50%"><input type="text" name="goodscd" style="width:100%" value="<?=$data['goodscd']?>" required label="�𵨸�"></td>
</tr>

<tr>
	<td>�̿���</td>
	<td class="noline">
	<input type="radio" name="age_flag" value="N" <?=$checked['age_flag']['N']?>> �̼����� ���Ű���
	<input type="radio" name="age_flag" value="Y" <?=$checked['age_flag']['Y']?>> �̼����� ���ԺҰ�
	</td>
</tr>
</table>

<!-- ����/��� -->
<div style="height:30px"></div>
<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> �� ����/���</b></font> <font class="small1" color="#6d6d6d">(����, ��� Ȯ���Ͻð�, 11������ ����ϱ� ���� ������ �ʿ��� �κ��� �����ϼ���.)</font></div>
<table class="tb">
<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
<tr>
	<td width="120" nowrap>�ǸŰ�<font color="#FF1800"><b>*</b></font></td>
	<td width="50%"><input type="text" name="price" size="10" value="<?=$data['price']?>" required label="�ǸŰ�">��</td>
	<td width="120" nowrap>���<font color="#FF1800"><b>*</b></font></td>
	<td width="50%"><input type="text" name="stock" size="10" value="<?=$stock?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)">��</td>
</tr>
<tr>
	<td>����<font color="#FF1800"><b>*</b></font></td>
	<td><input type="text" name="consumer" size="10" value="<?=$data['consumer']?>" required label="����">��</td>
	<td>�ִ뱸�� ������</td>
	<td><input type="text" name="max_count" size="10" value="<?=$data['max_count']?>">�� <font class="small1" color="#6d6d6d">������ ������ 0�� �Է��ϼ���.</font></td>
</tr>
<tr>
	<td>�������</td>
	<td class=noline><input type=checkbox name=usestock <?=$checked[usestock][o]?>> �ֹ��� ������� <font class=small color=444444>(üũ���ϸ� ��� ������� �������Ǹ�)</font></td>
	<td>ǰ����ǰ</td>
	<td class=noline><input type=checkbox name=runout value=1 <?=$checked[runout][1]?>> ǰ���� ��ǰ�Դϴ�</td>
</tr>
<tr>
	<td>����/�����<font color="#FF1800"><b>*</b></font></td>
	<td class="noline">
	<input type="radio" name="tax" value="1" <?=$checked['tax'][1]?> required label="����/�����"> ����
	<input type="radio" name="tax" value="0" <?=$checked['tax'][0]?> required label="����/�����"> �����
	</td>
	<td></td>
	<td></td>
</tr>
</table>

<div style="margin:10px 0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:vOption()" onfocus="blur()"><img src="../img/btn_priceopt_add.gif" align="absmiddle"></a> <font class="small" color="#444444">�̻�ǰ�� �ɼ��� �������ΰ�� ����ϼ���</font></div>

<div id="objOption" style="display:none; margin-left:20px;">
<div style="padding-bottom:10px">
<font class="small" color="black"><b>�ɼǸ�1</b> : <input type="text" name="optnm[]" value="<?=$optnm[0]?>">
<a href="javascript:addopt1()" onfocus="blur()"><img src="../img/i_add.gif" align="absmiddle"></a> <a href="javascript:delopt1()" onfocus="blur()"><img src="../img/i_del.gif" align="absmiddle"></a><span style="width:20px"></span>
<b>�ɼǸ�2</b></font> : <input type="text" name="optnm[]" value="<?=$optnm[1]?>">
<a href="javascript:addopt2()" onfocus="blur()"><img src="../img/i_add.gif" align="absmiddle"></a> <a href="javascript:delopt2()" onfocus="blur()"><img src="../img/i_del.gif" align="absmiddle"></a><span style="width:20px"></span>
</div>
<?if(count($opt)>1 || $opt1[0] != null || $opt2[0] != null){?><script type="text/javascript">vOption();</script><?}?>
<div style="margin:10px 0"><font class="small" color="#444444">����� �ɼǸ�1�� �ɼǸ�2�� ����Ŭ���Ͻÿ� �ɼ��� �����Ͻ� �� �ֽ��ϴ�.<br>
�ɼǸ�1�� �ִ� 9�� �̳�, �ɼǸ�2�� �ִ� 30�������� �Է��Ͻ� �� �ֽ��ϴ�. ������ �ʰ��� �����ʹ� �ݿ����� ���� �� �ֽ��ϴ�.</font></div>

	<table id="tbOption" border="1" bordercolor="#cccccc" style="border-collapse:collapse">
	<tr align="center">
		<td>&nbsp;</td>
		<td><span style="color:#333333;font-weight:bold;">�ǸŰ�</span></td>
		<td><span style="color:#333333;font-weight:bold;">����</span></td>
		<?
			$j=4;
			foreach ($opt2 as $v){
			$j++;
		?>
		<td id='tdid_<?=$j?>'><input type="text" name="opt2[]" <?if($v != ''){?>class=fldtitle value="<?=$v?>"<?}else{?>class="opt gray" value='�ɼǸ�2'<?}?> <?if($j>5){?> ondblclick="delopt2part('tdid_<?=$j?>')"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)"></td>
		<? } ?>
	</tr>
		<?
		$i=0;
		$op2=$opt2[0]; foreach ($opt1 as $op1){
		$i++;
		?>
	<tr id="trid_<?=$i?>">
		<td nowrap><input type="text" name="opt1[]" <?if($op1 != ''){?>class=fldtitle value="<?=$op1?>"<?}else{?>class="opt gray" value='�ɼǸ�1'<?}?> <?if($i != 1){?>ondblclick="delopt1part('trid_<?=$i?>')"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)" style="width:110px;"></td>
		<td><input type="text" name="option[price][]" class="opt gray" value="<?=$opt[$op1][$op2][price]?>" style="width:65px;"></td>
		<td><input type="text" name="option[consumer][]" class="opt gray" value="<?=$opt[$op1][$op2][consumer]?>" style="width:65px;"></td>
		<? foreach ($opt2 as $op2){ ?>
		<td><input type="text" name="option[stock][]" <?if($opt[$op1][$op2][stock]){?>class="opt" value="<?=$opt[$op1][$op2][stock]?>"<?}else{?>class="opt gray" value="���"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)"><input type="hidden" name="option[optno][]" value="<?=$opt[$op1][$op2][optno]?>"></td>
		<? } ?>
	</tr>
	<? } ?>
	</table>


</div>

<!-- ��ǰ �̹��� -->
	<div style="height:30px"></div>
	<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> �� ��ǰ �̹���</b></font> <font class="small1" color="#6d6d6d">(11������ ������ �̹����� Ȯ���Ͻð�, 11������ ����ϱ� ���� ������ �ʿ��� �κ��� �����ϼ���.)</font></div>

	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>�̹�����Ϲ��</td>
		<td class="noline">
		<label><input type="radio" name="image_attach_method" value="file" onClick="fnSetImageAttachForm();" <?=$checked[image_attach_method]['file']?>>���� ���ε�</label>
		<label><input type="radio" name="image_attach_method" value="url" onClick="fnSetImageAttachForm();" <?=$checked[image_attach_method]['url']?>>�̹���ȣ���� URL �Է�</label>

		</td>
	</tr>
	</table>

	<div id="image_attach_method_upload_wrap">
		<!-- ���� ���ε� -->
		<table class="tb">
		<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
		<? $t = array_map("toThumb",$imgs); ?>
		<tr>
			<td>���̹���</td>
			<td>
			<table>
			<col valign="top" span="2">
			<? for ($i=0;$i<4;$i++){ ?>
			<tr>
				<td>
				<span><input type="file" name="imgs[]" style="width:400px"></span>
				</td>
				<td>
				<?=goodsimg($t[$i],20,"style='border:1px solid #cccccc' onclick=popupImg('../data/goods/$imgs[$i]','../') class=hand",2)?>
				</td>
				<td>
				<? if ($imgs[$i]){ ?>
				<div style="padding:0" class="noline"><input type="checkbox" name="del[imgs][<?=$i?>]"><font class="small" color="#585858">���� (<?=$imgs[$i]?>)</font></div>
				<? } ?>
				</td>
			</tr>
			<? } ?>
			</table>

			</td>
		</tr>
		</table>
		<!--//���� ���ε� -->
	</div>

	<div id="image_attach_method_link_wrap">
		<!-- URL �Է� -->
		<table class="tb">
		<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">

		<tr>
			<td>���̹���</td>
			<td>

			<table>
			<col valign="top">
			<? for ($i=0;$i<4;$i++){ ?>
			<tr>
				<td>
				<span><input type="text" name="urls[]" style="width:400px" value="<?=$urls[$i]?>"></span>
				</td>
				<td>
				<?=goodsimg($urls[$i],20,"style='border:1px solid #cccccc' onclick=popupImg('$urls[$i]','../') class=hand",2)?>
				</td>
			</tr>
			<? } ?>
			</table>

			</td>
		</tr>
		</table>
		<!--//URL �Է� -->
	</div>
	<script type="text/javascript">
	fnSetImageAttachForm();
	</script>

<!-- ��ǰ ���� -->
	<div style="height:30px"></div>
	<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> �� ��ǰ ����</b></font> <font class="small1" color="#6d6d6d">(ȫ������ �� ��ǰ������ Ȯ���Ͻð�, 11������ ����ϱ� ���� ������ �ʿ��� �κ��� �����ϼ���.)</font></div>

	<table border="1" bordercolor="#cccccc" style="border-collapse:collapse">
	<tr><td>
	<table cellpadding="0" cellspacing="0" bgcolor="#f8f8f8">
	<tr><td style="padding:10px 10px 5px 10px"><font class="small1" color="#444444"><font color="#E6008D">�̹��� �ܺθ�ũ</font> �� <font color="#E6008D">11����</font> �ǸŸ� ���� �̹����� ����Ͻ÷��� <font color="#E6008D">�ݵ�� �̹���ȣ���� ����</font>�� �̿��ϼž� �մϴ�.</a></td></tr>
	<tr><td style="padding:0 10px 7px 10px"><font class="small1" color="#444444">�̹���ȣ������ ��û�ϼ̴ٸ� <a href="javascript:popup('http://image.godo.co.kr/login/imghost_login.php',980,700)" name="navi"><img src="../img/btn_imghost_admin.gif" align="absmiddle"></a>, ���� ��û���ϼ̴ٸ� <a href="http://hosting.godo.co.kr/imghosting/service_info.php" target="_blank"><img src="../img/btn_imghost_infoview.gif" align="absmiddle"></a> �� �����ϼ���!</td></tr>
	</table>
	</td></tr></table>

	<div style="padding-top:5px"></div>

	<table class="tb">
	<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
	<tr>
		<td>ȫ������</td>
		<td>
		<input name="shortdesc" style="width:400px;" class="line" maxlength="25" value="<?=htmlspecialchars($data['shortdesc'])?>" onkeydown="chkLen(this, 25, 'sLength')" onkeyup="chkLen(this, 25, 'sLength')">
		(<span id="sLength">0</span>/25)
		<div class="small1" style="color:#6d6d6d; padding-top:5px;">(ȫ���� ���� �߰������� * ǥ�ÿ� �Բ� ��ǰ�� �ϴܿ� ����Ǹ�,�˻���δ� ������� �ʽ��ϴ�. ��/���� 25�� �̳��� �Է��ϼž� �մϴ�.)</div>
		<script type="text/javascript">_ID('sLength').innerHTML = document.getElementsByName('shortdesc')[0].value.length;</script>
		</td>
	</tr>
	</table>
	<div style="height:6px;font-size:0"></div>

	<textarea name="longdesc" style="width:100%;height:400px" type="editor"><?=$data['longdesc']?></textarea>

<!-- ��ۤ�A/S -->
	<div style="height:30px"></div>
	<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> �� ��ۤ�A/S</b></font> <font class="small1" color="#6d6d6d">(��� �� A/S�� ��ǰ���� ������ �� �ֽ��ϴ�.)</font></div>

	<script type="text/javascript">
	function chk_delivery_type(){
		var obj = document.getElementsByName('delivery_type');
		if(obj[2].checked == true) document.getElementById('gdi').style.display="inline";
		else document.getElementById('gdi').style.display="none";

		if(obj[3].checked == true) document.getElementById('gdi2').style.display="inline";
		else document.getElementById('gdi2').style.display="none";
	}
	</script>

	<table class="tb" id="shipAS">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>��ۺ�</td>
		<td>
		<table cellspacing="0" cellpadding="0" border="0">
		<tr height=40>
			<td>
			<input type="hidden" name="delivery_price" value="2">
			<input type="text" name="delivery_price" value="<?=(!empty($data['delivery_price'])) ? $data['delivery_price'] : $set['delivery']['default'] ?>" size="8" onkeydown="onlynumber()">��
		</tr>
		</table>
		<div><font class=extext>�⺻�����å�� ��ǰ�� ��ۺ� ��å�� <a href="../basic/delivery.php" target=_blank><font class=extext_l>[�⺻���� > ���/�ù�� ����]</font></a> ���� ���� �Ͻ� �� �ֽ��ϴ�.</font></div>
		</td>
	</tr>
	<!--tr>
		<td>��ۺ�</td>
		<td>
		<table cellspacing="0" cellpadding="0" border="0">
		<tr height=40>
			<td>

			<input type="radio" name="delivery_type" value="0" class="null" onclick="chk_delivery_type();">�⺻�����å�� ����
			<input type="radio" name="delivery_type" value="1" checked class="null" onclick="chk_delivery_type();"> ������
			<input type="radio" name="delivery_type" value="2" class="null" onclick="chk_delivery_type();">��ǰ�� ��ۺ� �Է� <span style="display:none;" id="gdi">&nbsp;<input type="text" name="delivery_price" value="0" size="8" onkeydown="onlynumber()">��</span>
			<input type="radio" name="delivery_type" value="3" class="null" onclick="chk_delivery_type();">���ҹ�ۺ� <span style="display:none;" id="gdi2">&nbsp;<input type="text" name="delivery_price2" value="0" size="8" onkeydown="onlynumber()">��</span></td>

		</tr>
		</table>
		<div><font class=extext>�⺻�����å�� ��ǰ�� ��ۺ� ��å�� <a href="../basic/delivery.php" target=_blank><font class=extext_l>[�⺻���� > ���/�ù�� ����]</font></a> ���� ���� �Ͻ� �� �ֽ��ϴ�.</font></div>
		</td>
	</tr-->
	<tr>
		<td>A/S ����<br>(�ȳ�����)</td>
		<td>
		<input name="as_info" style="width:500px;" class="line" maxlength="40" value="<?=htmlspecialchars($data['as_info'])?>" onkeydown="chkLen(this, 40, 'vLength')" onkeyup="chkLen(this, 40, 'vLength')">
		(<span id="vLength">0</span>/40)
		<div class="small1" style="color:#6d6d6d; padding-top:5px;">(A/S ����ó,�Ⱓ ���� �Է��ϼ���. ��/���� 40�� �̳��� �Է��ϼž� �մϴ�.)</div>
		<script type="text/javascript">_ID('vLength').innerHTML = document.getElementsByName('as_info')[0].value.length;</script>
		</td>
	</tr>
	</table>


	<div class="button">
	<input type="image" src="../img/btn_11st.gif" alt="11���� ��ǰ����">
	</div>
</form>

<!-- �������� Ȱ��ȭ ��ũ��Ʈ -->
<script type="text/javascript" src="../../lib/meditor/mini_editor.js"></script>
<script type="text/javascript">mini_editor("../../lib/meditor/");</script>
<SCRIPT type="text/javascript" SRC="../proc/warning_disk_js.php"><!-- not_delete --></SCRIPT>
<script type="text/javascript">table_design_load();</script>

<div style="padding-top:15px"></div>
</body>
</html>
