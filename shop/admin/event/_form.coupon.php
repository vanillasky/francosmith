<?
$couponcd = $_GET['couponcd'];
$query = "select category from ".GD_COUPON_CATEGORY." where couponcd='$_GET[couponcd]' order by category";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$i++;
	$r_category[$data[category]] = $i;
}

$query = "select * from ".GD_COUPON." where couponcd = '$couponcd'";
$data = $db->fetch($query);

$perc = (substr($data[price],-1) == '%') ? "%" :  "��";
$data[price] = str_replace('%','',$data[price]);
$data[coupontype] = (!$data[coupontype]) ? "0" :  $data[coupontype];
$data[ability] = (!$data[ability]) ? "0" :  $data[ability];
$data[goodstype] = (!$data[goodstype]) ? "0" :  $data[goodstype];
$data[priodtype] = (!$data[priodtype]) ? "0" :  $data[priodtype];
$data[coupon_img] = (!$data[coupon_img]) ? "0" :  $data[coupon_img];
$data[payMethod] = (!$data[payMethod]) ? "0" :  $data[payMethod];

if($data[sdate]){
	$sdate = date("Y-m-d",strtotime($data[sdate]));
	$shour = date("H",strtotime($data[sdate]));
	$smin = date("i",strtotime($data[sdate]));
}
if($data[edate]){
	$edate = date("Y-m-d",strtotime($data[edate]));
	$ehour = date("H",strtotime($data[edate]));
	$emin = date("i",strtotime($data[edate]));
}

if( $data[edncnt]==null ) $data[edncnt]=0;
if( $data[dncnt] == null ) $data[dncnt]=1;
$checked[coupon_img][$data[coupon_img]] = "checked";
$checked[coupontype][$data[coupontype]] = "checked";
$checked[ability][$data[ability]] = "checked";
$checked[goodstype][$data[goodstype]] = "checked";
$checked[priodtype][$data[priodtype]] = "checked";
$checked[eactl][$data[eactl]] = "checked";
$checked[duplctl][$data[duplctl]] = "checked";
$checked[payMethod][$data[payMethod]] = "checked";

$selected['perc'][$perc] = "selected";

if($data[priodtype] == 1)$data[priod] = $data[sdate];
?>
<script language=javascript>
function checkform(form){
	if( form.priodtype['0'].checked && (!form.sdate.value || !form.edate.value )){
		alert('������ , �������� �Է��ϼ���');
		form.sdate.focus();
		return false;
	}
	if( form.priodtype['1'].checked && (!form.priod.value || form.priod.value < 1 )){
		alert('�����߱����� 1�� �̻����� �Է��ϼ���');
		form.focus();
		return false;
	}
	if(!form.price.value){
		alert('���αݾ��� �Է��ϼ���');
		form.price.focus();
		return false;
	}
	var fieldname = eval("form.elements['goodstype']");
	if (fieldname[1].checked) {
		var fieldname1 = eval("form.elements['category[]']");
		var fieldname2 = eval("form.elements['e_refer[]']");
		if(!fieldname1 && !fieldname2){
			alert('���û�ǰ�� ������!');
			return false;
		}
	}
	if(!chkForm(form)) return false;
	return true;
}

function exec_add()
{
	var ret;
	var str = new Array();
	var obj = document.forms[0]['cate[]'];
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
	var obj = document.getElementById('objCategory');
	oTr = obj.insertRow();
	oTd = oTr.insertCell();
	oTd.id = "currPosition";
	oTd.innerHTML = str.join(" > ");
	oTd = oTr.insertCell();
	oTd.innerHTML = "\<input type=text name=category[] value='" + ret + "' style='display:none'>";
	oTd = oTr.insertCell();
	oTd.innerHTML = "<a href='javascript:void(0)' onClick='cate_del(this.parentNode.parentNode)'><img src='../img/i_del.gif' align=absmiddle></a>";
}

function cate_del(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objCategory');
	obj.deleteRow(idx);
}

function displayLayer(layerid){
	document.getElementById('priodid0').style.display='none';
	document.getElementById('priodid1').style.display='none';
	document.getElementById(layerid).style.display='block';
}

function chk_msg(val){
	if(val == 1) document.getElementById('actid').innerHTML='�����մϴ�';
	else document.getElementById('actid').innerHTML='�����մϴ�';
}

function chk_coupontype(){
	var f = document.forms[0];
	document.getElementById('goodsallid').style.display = 'none';
	document.getElementById('goodsallid2').style.display = 'none';
	document.getElementById('tgt').innerHTML = '�� ��ǰ�Ǹűݾ���';
	document.getElementById('applyMsg').innerHTML = '�����������';
	document.getElementById('goodstypeMsg1').innerHTML = '��� ��ǰ�� ������ �� ������ ����� �� �ֽ��ϴ�';
	document.getElementById('goodstypeMsg2').innerHTML = 'Ư����ǰ �� Ư��ī�װ��� ��ǰ�� ������ �� ������ ����� �� �ֽ��ϴ�';
	if(f.coupontype[1].checked){
		document.getElementById('tgt').innerHTML = '������ �߱�(����)�Ǵ� ������ ��ǰ �Ǹűݾ���';
		document.getElementById('applyMsg').innerHTML = '�����߱޻�ǰ<br/><font class=extext>(������ ����Ǵ� ��ǰ)</font>';
		document.getElementById('goodstypeMsg1').innerHTML = '��ü��ǰ�� �߱��մϴ�';
		document.getElementById('goodstypeMsg2').innerHTML = 'Ư�� ��ǰ �� Ư�� ī�װ��� �߱��մϴ�';
		document.getElementById('goodsallid').style.display = 'block';
		if(document.getElementsByName('duplctl')[0].checked == true)document.getElementById('goodsallid2').style.display = 'block';
		return;
	}
}

function coupon_img_upload(){
	if(document.getElementsByName('coupon_img')[4].checked == true){
		document.getElementById('cp_img_upload').style.display = '';
	}else{
		document.getElementById('cp_img_upload').style.display = 'none';
	}
}
</script>

<table class=tb style="margin-bottom:10px;">
<col class=cellC>
<tr>
	<td>������ ������ PC���� ���θ��� ����ϼ����� �������� ���˴ϴ�.</br>
		����ϼ������� ����� �� �ִ� ������ "����ϼ����� ���������"���� ������ ���� �����մϴ�.
	</td>
</tr>
</table>

<form method=post action="indb.coupon.php" onsubmit="return checkform(this)" enctype="multipart/form-data">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=couponcd  value="<?=$_GET[couponcd]?>">

<table class=tb>
<col class=cellC><col class=cellL style='padding:5,0,5,5'>

<tr>
	<td>�����̸�</td>
	<td><input type=text name='name' size=40 maxlength=30 value="<?=$data[coupon]?>" required class=line> <font class=extext>ex) ���±������, �߼���������</td>
</tr>

<tr>
	<td>��������</td>
	<td><input type=text name='summa' size=40 maxlength=70 value="<?=$data[summa]?>" class=line> <font class=extext> ex) Ư���̺�Ʈ! ������ǰ 10% ��������</td>
</tr>

<tr>
	<td>�����߱޹��</td>
	<td>
		<div><input type=radio name=coupontype value='0' class=null <?=$checked[coupontype][0]?> onclick="chk_coupontype();"> ��ڹ߱� <font class=extext>(������� �� ��������Ʈ���� ��ڰ� Ư��ȸ������ �߱��մϴ�)</font></div>
		<div><input type=radio name=coupontype value='1' class=null <?=$checked[coupontype][1]?> onclick="chk_coupontype();"> ȸ�������ٿ�ε� <font class=extext>(��ǰ���������� ȸ���� ���� ������ �ٿ�ε�޽��ϴ�)</font></div>

		<div id='goodsallid' style="padding:3 0 10 11">
	    <table border=1 bordercolor=#cccccc style="border-collapse:collapse" width=635>
		<tr><td bgcolor=white style="padding:5 0 7 2">
		<div style='padding-left:10'>�� ������ �� �ٿ�ε� Ƚ���� <input type='text' style='text-align:right' name='dncnt' size=3 value='<?=$data[dncnt]?>' onkeydown='onlynumber()' maxlength='9'>ȸ�� �����մϴ� <font class=extext>(�������� �θ�  ������)</font></div>
		<div style='padding-left:5'><input type=checkbox name='eactl' value='1' <?=$checked[eactl][1]?> class=null> ������ ����� �ϳ��� ��ǰ�� �ѹ��� ������ �ֹ��� �� ���������� ��� �����մϴ�</div>
		<div style='padding: 1 0 5 28'><font class=extext>(üũ���ϸ� ���� ��ǰ�� �ѹ��� ������ �ֹ��� �Ѱ��� �������� ����)&nbsp;</font></div>
		<div style='padding-left:5'><input type=checkbox name='duplctl' value='1' <?=$checked[duplctl][1]?> class=null onclick="chk_coupontype();"> ������ ����� �� ������ �ֹ��ÿ��� ���� ��ǰ�� �����ٿ�ε带 ����մϴ�</div>
		<div style='padding: 1 0 2 28'><font class=extext>(üũ���ϸ� ������ �ֹ��� ���� ��ǰ�� �����ٿ�ε� ������)&nbsp;</font></div>
		</div>
		<div id='goodsallid2'><input type='hidden' style='text-align:right' name='edncnt' size=3 maxlength=9 value='<?=$data[edncnt]?>'></td></tr></table>
		</div>

		<div><input type=radio name=coupontype value='2' class=null <?=$checked[coupontype][2]?> onclick="chk_coupontype();"> ȸ�������ڵ��߱� <font class=extext>(ȸ�����Խ� �ڵ��߱޵˴ϴ�)</font></div>
		<div><input type=radio name=coupontype value='3' class=null <?=$checked[coupontype][3]?> onclick="chk_coupontype();"> ������ �ڵ��߱� <font class=extext>(������ ��ۿϷ�ÿ� �ڵ��߱޵˴ϴ�)</font></div>

		<div style="padding-top:4"></div>
	</td>
</tr>

<tr>
	<td>�������</td>
	<td>
		<input type=radio name=ability value='0' class=null <?=$checked[ability][0]?> onclick='chk_msg(this.value);'> ���������� �����մϴ� <font class=extext>(���Ž� �ٷ� ���εǴ� ����)</font>&nbsp;&nbsp;
		<input type=radio name=ability value='1' class=null <?=$checked[ability][1]?> onclick='chk_msg(this.value);'> ���������� �����մϴ� <font class=extext>(���� ��(��ۿϷ�) �����Ǵ� ����)</font>
	</td>
</tr>

<tr>
	<td>�����ݾ�</td>
	<td>
		<div>
		<span id='tgt'></span>&nbsp;<input type=text class=line name='price' size=10 style="text-align:right" maxlength=15 value="<?=$data[price]?>" required onkeydown='onlynumber();'>&nbsp;<select name='perc'><option value='��' <?=$selected['perc']['��']?>>��</option><option value='%' <?=$selected['perc']['%']?>>%</option></select>&nbsp;<span id=actid>����/�������ִ� ������ �����մϴ�</span></div>
		</td>
</tr>

<tr>
	<td id="applyMsg">�����߱޻�ǰ</td>
	<td>
	<table width=100% cellpadding=0 cellspacing=0>
		<tr>
			<td><input type=radio name=goodstype value='0' class=null <?=$checked[goodstype][0]?>> <span id="goodstypeMsg1"></span></td>
		</tr>
		<tr>
			<td height=10></td>
		</tr>

		<tr>
			<td><input type=radio name=goodstype value='1' class=null <?=$checked[goodstype][1]?>> <span id="goodstypeMsg2"></span>&nbsp;<font class=extext>(�Ʒ����� �˻��� ����)</font></td>
		</tr>
		<tr>
			<td height=5></td>
		</tr>
		<tr>
			<td>
			<?
			$query = "
			select
				a.goodsno,b.goodsnm,b.img_s,c.price
			from
				".GD_COUPON_GOODSNO." a,
				".GD_GOODS." b,
				".GD_GOODS_OPTION." c
			where
				a.goodsno=b.goodsno
				and a.goodsno=c.goodsno and c.link and go_is_deleted <> '1' and go_is_display = '1'
				and a.couponcd = '$_GET[couponcd]'

			";
			$res = $db->query($query);
			?>
			<script src="../../lib/js/categoryBox.js"></script>
			<div style="padding-top:3px"></div>
			<div style=padding-left:8><font class=small1 color=FF0066><img src="../img/icon_list.gif" align="absmiddle">ī�װ� ���� (ī�װ����� �� ������ ������ưŬ��)</font></div>
			<div style=padding-left:8><script>new categoryBox('cate[]',4,'','');</script>
			<a href="javascript:exec_add()"><img src="../img/btn_coupon_cate.gif"></a></div>
			<div class="box" style="padding:10 0 0 10">
			<table  cellpadding=8 cellspacing=0 id=objCategory bgcolor=f3f3f3 border=0 bordercolor=#cccccc style="border-collapse:collapse">
			<?
			if ($r_category){ foreach ($r_category as $k=>$v){ ?>
			<tr>
				<td id=currPosition><?=strip_tags(currPosition($k))?></td>
				<td><input type=text name=category[] value="<?=$k?>" style="display:none">
				<input type=hidden name=sort[] value="<?=-$v?>" class="sortBox right" maxlength=10 <?=$hidden[sort]?>></td>
				<td><a href="javascript:void(0)" onClick="cate_del(this.parentNode.parentNode)"><img src="../img/i_del.gif" border=0 align=absmiddle></a>
				</td>
			</tr>
			<? }} ?>
			</table>
			</div>
			<div style="padding-top:13px"></div>
			<div style=padding-left:8><font class=small1 color=FF0066><img src="../img/icon_list.gif" align="absmiddle">��ǰ ���� (��ǰ�˻� �� ����)</font></div>
			<div id=divRefer style="position:relative;z-index:99;padding-left:8">
				<div style="padding:5px 0px 0px 0px;"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_refer[]', 'referX');" align="absmiddle" /> <font class="extext">������: ��ǰ���� �� �ݵ�� �ϴ� ���(����)��ư�� �����ž� ���� ������ �˴ϴ�.</font></div>
				<div id="referX" style="padding-top:3px;">
					<?php while ($v = $db->fetch($res)){ ?>
						<a href="../../goods/goods_view.php?goodsno=<?php echo $v['goodsno']; ?>" target="_blank"><?php echo goodsimg($v['img_s'], '40,40', '', 1); ?></a>
						<input type=hidden name="e_refer[]" value="<?php echo $v['goodsno']; ?>" />
					<?php } ?>
				</div>
			</div>
			</td>
		</tr>
	</table>
	</td>
</tr>



<tr>
	<td>�����̹���</td>
	<td>
	<table cellpadding=0 cellspacing=0>
		<tr>
			<td align=center><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/common/coupon01.gif"><div><input type=radio class=null name=coupon_img value=0 <?=$checked[coupon_img][0]?> onclick ="coupon_img_upload();"></div></td>
			<td width=5></td>
			<td align=center><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/common/coupon02.gif"><div><input type=radio class=null name=coupon_img value=1 <?=$checked[coupon_img][1]?> onclick ="coupon_img_upload();"></div></td>
			<td width=5></td>
			<td align=center><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/common/coupon03.gif"><div><input type=radio class=null name=coupon_img value=2 <?=$checked[coupon_img][2]?> onclick ="coupon_img_upload();"></div></td>
			<td width=5></td>
			<td align=center><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/common/coupon04.gif"><div><input type=radio class=null name=coupon_img value=3 <?=$checked[coupon_img][3]?> onclick ="coupon_img_upload();"></div></td>
    	</tr>
		</table>
	</td>
</tr>
<tr>
	<td>�����̹��� ���� ���</td>
	<td>
	<table cellpadding=0 cellspacing=0>
		<tr>
			<td>
					<?
					if(!empty($data['coupon_img_file'])){
						$coupon_img_path = "../../data/skin/".$cfg['tplSkin']."/img/common/".$data['coupon_img_file'];
					?>
					<img src="<?=$coupon_img_path?>" border="0" align ="absbottom"/>
					<? } ?>
					<div style="padding:2 0 0 0;"> <input type=radio class=null name=coupon_img value=4 <?=$checked[coupon_img][4]?> onclick ="coupon_img_upload();"> ���
					<span id="cp_img_upload" <? if($data['coupon_img'] != "4"){ ?>style="display:none"<?}?>>
					<input type="file" name="coupon_img_file"/> <span class="small1 extext">(��������� 140 x 50)</span>
				</span>
				</div>
			</td>
    	</tr>
		</table>
	</td>
</tr>
<tr>
	<td>����Ⱓ</td>
	<td>
	<input type=radio class=null name=priodtype value=0 <?=$checked[priodtype][0]?> onclick="javascript:displayLayer('priodid0')"> ������, ������ ����
	&nbsp;&nbsp;<input type=radio class=null name=priodtype value=1 <?=$checked[priodtype][1]?> onclick="javascript:displayLayer('priodid1')"> �߱��Ϸκ��� �Ⱓ ����
	<div id=priodid0 style="display:none;">
		<input type=text name=sdate size=10 maxlength=10 value="<?=$sdate?>" onclick="calendar(event,'-')" onkeydown="onlynumber()" class=line>
		<select name="shour">
		<? for($i = 0; $i < 24; $i++) { ?>
			<option value="<? printf('%02d',$i)?>" <?=($shour == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
		<? } ?>
		</select>��
		<select name="smin">
		<? for($i = 0; $i <= 59; $i++) { ?>
			<option value="<? printf('%02d',$i)?>" <?=($smin == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
		<? } ?>
		</select>�� ~
		<input type=text name=edate size=10 maxlength=10 value="<?=$edate?>" onclick="calendar(event,'-')" onkeydown="onlynumber()"  class=line>
		<select name="ehour">
		<? for($i = 0; $i < 24; $i++) { ?>
			<option value="<? printf('%02d',$i)?>" <?=($ehour == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
		<? } ?>
		</select>��
		<select name="emin">
		<? for($i = 0; $i <= 59; $i++) { ?>
			<option value="<? printf('%02d',$i)?>" <?=($emin == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
		<? } ?>
		</select>��
	</div>
	<div id=priodid1 style="display:none;">
		&nbsp; �����߱��Ϸκ��� <input type=text name=priod value="<?=$data[priod]?>" size=5 maxlength=3 onkeydown='onlynumber()'> �ϱ��� ���Ⱓ�� �����մϴ�.<br>
		&nbsp; ��� �������� <input type=text name=priod_edate size=10 maxlength=10 value="<?=$edate?>" onclick="calendar(event,'-')"  class=line> �� �����մϴ�. �Է����� ���� ��� ������ �����ϴ�.
	</div>
	</td>
</tr>
<tr>
	<td>�����������</td>
	<td>
		<input type=text name=excPrice size=10 style="text-align:right" maxlength=10 value="<?=$data[excPrice]?>" class=line> �� �̻� ���Žÿ��� ��밡�� <font class=extext>(�������� �θ� ���űݾ׿� ������� ����� �����մϴ�)</font></td>
</tr>
<tr>
	<td>�������ܻ������</td>
	<td><input type=radio name="payMethod" value="0" class=null <?=$checked['payMethod'][0]?>> �������� ��� ����
	<input type=radio name="payMethod" value="1" class=null <?=$checked['payMethod'][1]?>> ������ �Աݿ����� ��밡��
	<div><font class="extext">������ �Աݿ����� ���� ��� �����ϵ��� �����ϴ°��� ������������������ ���� �� �� �ֽ��ϴ�.</font> &nbsp;<a href="javascript:popupLayer('../event/popup.credit_financial_law.php',750,430);"><font class="extext_l">[�ڼ��� ����]</font></a></div>
	</td>
</tr>
</table>

<div class=button>
<input type=image src="../img/btn_<?=$_GET[mode]?>.gif">
<a href="coupon.php"><img src="../img/btn_cancel.gif"></a>
</div>
</form>
<script>chk_msg(0);chk_coupontype();displayLayer('priodid<?=$data[priodtype]?>');</script>