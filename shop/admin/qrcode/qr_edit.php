<?
$location = "QR Code ���� > QR Code �����";
include "../_header.php";
require_once("../../lib/qrcode.class.php");

$strPath = "../../conf/qr.cfg.php";
if(file_exists($strPath)) {
	require $strPath;
}

$sno = $_REQUEST['sno'];
$qr_type = $_REQUEST['qr_type'];
if($qr_type == "")$qr_type= 'etc';

if(!empty($sno)){
	### qrcode
	$qrdata = $db->fetch("select * from ".GD_QRCODE." where qr_type='etc' and sno=$sno");
}

if(!$qrdata['qr_size'])$qrdata['qr_size']='3';
if(!$qrdata['qr_version'])$qrdata['qr_version']='5';
if(!$qrdata['useLogo']){
	$qrdata['useLogo']=$qrCfg['useLogo'];
}

if(substr($qrdata['qr_string'],0,6) == "MECARD"){
	$qrdata['useType']	= "mcard";
	$divURLVew			= "style='display:none'";
	$divMcardVew		= "style='display:block'";
	if(empty($sno)){
		$qrdata['qr_size']		= "2";
		$qrdata['qr_version'] = "16";
	}
}else if(substr($qrdata['qr_string'],0,4) == "http"){
	$divURLVew			= "style='display:block'";
	$divMcardVew		= "style='display:none'";
	$qrdata['useType'] = "url";
}else{
	$divURLVew			= "style='display:block'";
	$divMcardVew		= "style='display:none'";
	$qrdata['useType'] = "url";
}
$checked['qr_size'][$qrdata['qr_size']] = "selected";
$checked['qr_version'][$qrdata['qr_version']] = "selected";
$checked['useLogo'][$qrdata['useLogo']] = "checked";
$checked['useType'][$qrdata['useType']] = "checked";
$checked['logoLocation'][$qrCfg['logoLocation']] = "checked";

$logoPath = "../../data/skin/".$cfg['tplSkin']."/img/".$qrCfg['logoImg'];

### qrcode
$qrcount = $db->fetch("select count(*) from ".GD_QRCODE." where qr_type='goods' and contsNo='$goodsno'");

if($qrCfg['useGoods']=='y' && $qrcount[0]>0){
	require "../../lib/qrcode.class.php";
	$QRCode = Core::loader('QRCode');
	$qrdata['qrcode'] = $QRCode->get_GoodsViewTag($goodsno, "etc_view");
}else{
	$qrdata['qrcode'] = null;
}

//�̸�����/�ٿ�ε�� ������
$qrFullData = "d=".$qrdata['qr_string']."&o=".$qrdata['qr_string']."&s=".$qrdata['qr_size']."&v=".$qrdata['qr_version'];

if(substr($qrdata['qr_string'],0,6) == "MECARD"){
	$qrdata['qr_string'] = str_replace("MECARD:","",$qrdata['qr_string']);
	$arr = explode(';' , $qrdata['qr_string']); // . �� �����ڷ��Ͽ� ���ڿ��� �и�, �迭�� ����,,,
	$no = sizeof($arr);
	 
	for ($i=0 ; $i<$no ; $i++) {
		$var = strpos($arr[$i], ":");
		$var_str1 = substr($arr[$i], "0",$var);
		$var_str2 = substr($arr[$i], "0",$var+1);
		$var_rst = str_replace($var_str2 ,"",$arr[$i]);
		$qrdata[$var_str1] = $var_rst;
	}
}


?>
<style type="text/css">
img {border:none;}
</style>
<script type="text/javascript">
	function func_preview(){
		

		var frm = document.frm1;
		var tempTarget = frm.target;

		if(frm.qr_name.value == ""){
			alert("�ڵ� �̸��� �����ϴ�.");
			frm.qr_name.focus();
			return;
		}

		if(frm.useType[1].checked == true){		
			if(frm.N.value == ""){
				alert("�̸��� �Է��Ͽ� �ֽʽÿ�.");
				frm.N.focus();
				return;
			}else if(frm.TEL.value == ""){
				alert("��ȭ��ȣ�� �Է��Ͽ� �ֽʽÿ�.");
				frm.TEL.focus();
				return;
			}else{
				frm.contents.value =  "MECARD:N:" + frm.N.value +";TEL:" + frm.TEL.value +";EMAIL:" + frm.EMAIL.value  + ";URL:" + frm.URL.value+";ADR:" + frm.ADR.value;
			}
		}else{
			if(frm.tmp_contents.value == ""){
				alert("������ �Է��Ͽ� �ֽʽÿ�.");
				return;
			}
			frm.contents.value =  frm.tmp_contents.value;
		}	

		document.getElementById("act1frame").style.display = "block";
		frm.d.value = encodeURI(frm.contents.value);
		frm.o.value = encodeURI(frm.contents.value);
		frm.s.value = frm.qr_size.value;
		frm.target  = "act1";
		frm.action = "../../lib/qrcodeImgMaker.php";
		frm.submit();

		frm.target = tempTarget;

	}

	function qr_save(){
		var frm = document.frm1;

		if(frm.qr_name.value == ""){
			alert("�ڵ� �̸��� �����ϴ�.");
			frm.qr_name.focus();
			return;
		}

		if(frm.useType[1].checked == true){		
			if(frm.N.value == ""){
				alert("�̸��� �Է��Ͽ� �ֽʽÿ�.");
				frm.N.focus();
				return;
			}else if(frm.TEL.value == ""){
				alert("��ȭ��ȣ�� �Է��Ͽ� �ֽʽÿ�.");
				frm.TEL.focus();
				return;
			}else{
				frm.contents.value =  "MECARD:N:" + frm.N.value +";TEL:" + frm.TEL.value +";EMAIL:" + frm.EMAIL.value + ";URL:" + frm.URL.value+";ADR:" + frm.ADR.value;
			}
		}else{
			if(frm.tmp_contents.value == ""){
				alert("������ �Է��Ͽ� �ֽʽÿ�.");
				frm.tmp_contents.focus();
				return;
			}
			frm.contents.value =  frm.tmp_contents.value;
		}

		frm.action = "indb.php";
		frm.submit();
	}

	function func_useType(val){
		var frm = document.frm1;
		document.getElementById("act1frame").style.display = "none";
		all_clear();

		if(frm.useType[1].checked == true){
			document.getElementById("divURL").style.display = "none";
			document.getElementById("divMcard").style.display = "block";
			frm.qr_version.value = "12";
		}else{
			document.getElementById("divURL").style.display = "block";
			document.getElementById("divMcard").style.display = "none";
			frm.qr_version.value = "5";			
		}
	}
	
	function all_clear(){
		var frm = document.frm1;

		frm.d.value = "";
		frm.o.value = "";

		frm.qr_name.value = "";
		frm.contents.value = "";
		frm.tmp_contents.value = "";
		frm.N.value = "";
		frm.TEL.value = "";
		frm.EMAIL.value = "";
		frm.URL.value = "";
		frm.ADR.value = "";
	}

</script>

<div style="width:800">
<form name="frm1" method="post"/>
<input type=hidden name=returnUrl value="qr_edit.php">
<input type="hidden" name="qr_type" value="etc">
<input type="hidden" name="contents">
<input type="hidden" name="sno" value="<?=$sno?>">
<input type="hidden" name="d" value="<?=$edata?>">
<input type="hidden" name="s" value="">
<input type="hidden" name="e" value="M">
<input type="hidden" name="v" value=''>
<input type="hidden" name="n" value=''>
<input type="hidden" name="m" value=''>
<input type="hidden" name="p" value=''>
<input type="hidden" name="o" value="<?=$edata?>">
<input type="hidden" name="useLogo" value="<?=$useLogo?>">
<input type="hidden" name="degree" value='<?=$qrCfg['degree']?>'>
<input type="hidden" name="logoImg" value='<?=$qrCfg['logoImg']?>'>
<input type="hidden" name="logoLocation" value='<?=$qrCfg['logoLocation']?>'>
<div class="title title_top">QR �ڵ� �����(URL) <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=15')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div>
		<label class="noline"><input type="radio" name="useType" value="url"  <?php echo $checked['useType']['url'];?> onclick="func_useType('url')"/>URL/TEXT</label>
		<label class="noline"><input type="radio" name="useType" value="mcard" <?php echo $checked['useType']['mcard'];?>  onclick="func_useType('mcard')"/>����(����ó)</label>
		<label class="noline"><img src='/shop/admin/img/btn_freeview.gif' style='vertical-align:middle' onclick="func_preview()" style="cursor:hand"></label>
		<? if(!empty($sno)){ ?>
			<label class="noline">
			<?
				$QRCode = new QRCode();
				echo  $QRCode->get_GoodsViewTag($sno, "etc_down", $qrFullData);
			?>
			</label>
			<label class="noline"><font class="extext">������ ����Ǿ� �ִ� �ڵ尡 �ٿ�ε�˴ϴ�.</font></label>
		<? } ?>
</div>
<div>
	<table border="0" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
	<tr>
	<td>	
		<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
		<col class="cellC"><col class="cellL">
		<tr>
			<td>QR �ڵ� �̸�</td>
			<td>
			<input type="text" name="qr_name" value="<?=$qrdata['qr_name']?>"/>
			</td>
		</tr>
		<tr>
			<td>�ΰ� �̹��� ���</td>
			<td class="noline">
			<label><input type="radio" name="useLogo" value="y"  <?php echo $checked['useLogo']['y'];?>/>���</label><label><input type="radio" name="useLogo" value="n" <?php echo $checked['useLogo']['n'];?> />������</label>
			</td>
		</tr>
		<tr>
			<td>QR �ڵ� ũ��</td>
			<td>
			<select name="qr_size">
			<? for($i=1;$i<9;$i++){ ?>
				<option value="<?=$i?>" <?php echo $checked['qr_size'][$i];?>/><?=$i?>
			<? } ?>
			</select>&nbsp;<font class="extext">1 (90pix) ~ 8 (405pix) : 1���� �� 45pix ����<font class="extext">
			</td>
		</tr>
		<tr>
			<td>QR �ڵ� ���е�</td>
			<td>
			<select name="qr_version">
			<? for($i=1;$i<13;$i++){ ?>
				<option value="<?=$i?>" <?php echo $checked['qr_version'][$i];?>/><?=$i?>
			<? } ?>
			</select>&nbsp;<font class="extext">������ ���� ��� ���е��� �÷��ּ���.(�ڵ尡 Ŀ������ �ֽ��ϴ�.)</font>
			</td>
		</tr>
		</table>

		<div style="height:10"></div>

		<div id="divURL" <?=$divURLVew?>>
		<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
		<col class="cellC"><col class="cellL">
		<tr>
			<td>����(URL)</td>
			<td><textarea cols='60' rows='10' name="tmp_contents"><?=$qrdata['qr_string']?></textarea>
			<div id="textHelf" <?=$divtextHelf?>><font class="extext">���е� 12���� �ִ� 285byte �Է°���(�ѱ�95�� ����/���� 285��)</font></div></td>
		</tr>
		</table>
		</div>

		<div id="divMcard" <?=$divMcardVew?>>
			<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse;height:280" width="100%">
			<col class="cellC"><col class="cellL">
			<tr>
				<td>�̸�</td>
				<td>
				<input type="text" name="N" value="<?=$qrdata['N']?>"/> ex) ��ǥ ȫ�浿(����)</td>
			</tr>
			<tr>
				<td>��ȭ��ȣ</td>
				<td>
				<input type="text" name="TEL" value="<?=$qrdata['TEL']?>"/>
				</td>
			</tr>
			<tr>
				<td>�̸���</td>
				<td>
				<input type="text" name="EMAIL" value="<?=$qrdata['EMAIL']?>" style="width:250px"/>
				</td>
			</tr>
			<tr>
				<td>Ȩ������</td>
				<td>
				<input type="text" name="URL" value="<?=$qrdata['URL']?>" style="width:250px"/>
				</td>
			</tr>
			<tr>
				<td>�ּ�</td>
				<td>
				<input type="text" name="ADR" value="<?=$qrdata['ADR']?>" style="width:250px"/>
				</td>
			</tr>
			</table>
			</div>
	</td>
	<td>		
		<div id="act1frame" style="display:none"><iframe name="act1" id="act1" marginheight='0' marginwidth='0' frameBorder='0'  height='100%' scrolling='yes' allowTransparency='true' align="center"></iframe><div>
	</td>
	</tr>
	</table>
<div>
<div class=button>
<a href="javascript:qr_save()"><img src="../img/btn_save.gif"></a>
<a href="qr_list.php?page=<?=$_GET[page] ?>"><img src="../img/btn_cancel.gif"></a>
</div>
<p/>
</div>
</form>
</div>
<div id=MSG01>


<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr><td>��ǰ�������� �̺�Ʈ ������ �ܿ� ������ ����� QR�ڵ带 �űԷ� ������ �� �ֽ��ϴ�.</td></tr>
<tr><td>1)URL: ����ư ������ QR�ڵ� �νĽ� ���������� ����˴ϴ�.</td></tr>
<tr><td>2)TEXT: �Է��� �ؽ�Ʈ ������ ����Ʈ ���� ��������, �ִ� 450byte���� �Է� �����մϴ�.</td></tr>
<tr><td>3) ����(����ó): ����� ����ó ���� ������ ����Ʈ ���� �������ϴ�.</td></tr>

<tr><td>-QR Code�̸�: ������ QR�ڵ��� �̸��� ����մϴ�.</td></tr>
<tr><td>-�ΰ��̹���: ���θ��� �ΰ� �̹��� ���Կ��θ� �����մϴ�.</td></tr>
<tr><td>-QR Codeũ��: �����ϴ� �ڵ��� �������̸�, Ȱ�뵵�� ���� ũ�⸦ ������ �� �ֽ��ϴ�.</td></tr>
<tr><td>-QR Code���е�:  ������ �ڵ��� ���е��� ������ �� �ֽ��ϴ�. </td></tr>
<tr><td> ��ǰ url�Ӹ� �ƴ϶� ������ ���뵵 ����� �� �ֱ� ������ �����Ͱ� ���� ��� ũ�⸦ �����ؾ� �մϴ�. </td></tr>
<tr><td>�Ϲ����� ����Ʈ url������ ���� ������� 5�Դϴ�.</td></tr>
<tr><td>-����(URL): QR�ڵ忡 ����� ��ũ ���� �Ǵ� �ؽ�Ʈ ���� ������ ����մϴ�. </td></tr>

</table>
</div>
<script>cssRound('MSG01')</script>

<SCRIPT LANGUAGE="JavaScript">
<!--
<?if($qrdata['qr_string']!=""){?>
		func_preview();
<?}?>
document.getElementById("act1").style.height='100%' ;
//-->
</SCRIPT>

<? include "../_footer.php"; ?>
