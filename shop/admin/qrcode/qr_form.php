<?
$location = "QR �ڵ� ���� > QR �ڵ� ����";
include "../_header.php";

$strPath = "../../conf/qr.cfg.php";
if(file_exists($strPath)) {
	require $strPath;
}

if(!$qrCfg['useGoods'])$qrCfg['useGoods']='n';
if(!$qrCfg['useEvent'])$qrCfg['useEvent']='n';
if(!$qrCfg['useLogo'])$qrCfg['useLogo']='n';

if(!$qrCfg['logoImg'])$qrCfg['logoImg']='';
if(!$qrCfg['degree'])$qrCfg['degree']='';
if(!$qrCfg['logoLocation'])$qrCfg['logoLocation']='';

$checked['useGoods'][$qrCfg['useGoods']] = "checked";
$checked['useEvent'][$qrCfg['useEvent']] = "checked";
$checked['useLogo'][$qrCfg['useLogo']] = "checked";
$checked['qr_style'][$qrCfg['qr_style']] = "checked";

$checked['logoLocation'][$qrCfg['logoLocation']] = "checked";

$logoPath = "../../data/skin/".$cfg['tplSkin']."/img/".$qrCfg['logoImg'];

$qrCfg['degree'] = number_format($qrCfg['degree']);
?>
<style type="text/css">
img {border:none;}
</style>
<script type="text/javascript">
</script>
<div style="width:800">
<form method="post" action="incfg.php" onsubmit="return chkForm(this)"  enctype="multipart/form-data"/>
<input type=hidden name=returnUrl value="qr_form.php">
<div class="title title_top">QR �ڵ� ����/���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=15')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">

<tr height="30">
	<td>��ǰ ���� QR ���</td>
	<td class="noline">
	<label><input type="radio" name="useGoods" value="y"  <?php echo $checked['useGoods']['y'];?>/>���</label><label><input type="radio" name="useGoods" value="n" <?php echo $checked['useGoods']['n'];?> />������</label>
	<div style="padding-top:5;" class="small1 extext">
	<div>��ǰ ���� ȭ�鿡 ��ǰ �ּ� ������ ���� qr�ڵ尡 �������ϴ�.</div>
	</div>
	</td>
</tr>

<tr height="30">
	<td>�̺�Ʈ QR ���</td>
	<td class="noline">
	<label><input type="radio" name="useEvent" value="y"  <?php echo $checked['useEvent']['y'];?> />���</label><label><input type="radio" name="useEvent" value="n" <?php echo $checked['useEvent']['n'];?>  onclick="logoChk();"/>������</label>
	<div style="padding-top:5;" class="small1 extext">
	<div>�̺�Ʈ �������� �̺�Ʈ �ּ� ������ ���� qr�ڵ尡 ���� �˴ϴ�.</div>
	</div>
	</td>
</tr>
<tr height="30">
	<td>QR ���� ����</td>
	<td class="noline">
	<label><input type="radio" name="qr_style" value=""  <?php echo $checked['qr_style'][''];?> />QR�̹���</label><label><input type="radio" name="qr_style" value="btn" <?php echo $checked['qr_style']['btn'];?>  onclick="logoChk();"/>QR�̹��� + ���� ��ư</label>
	</td>
</tr>
<tr>
	<td>�ΰ� �̹��� ���</td>
	<td class="noline">
	<label><input type="radio" name="useLogo" value="y"  <?php echo $checked['useLogo']['y'];?>/>���</label><label><input type="radio" name="useLogo" value="n" <?php echo $checked['useLogo']['n'];?> />������</label>
	</td>
</tr>
<tr>
	<td>�ΰ� �̹��� ���</td>
	<td class="noline">
	<div style="padding:2 0 0 0">
	<? if(!empty($qrCfg['logoImg'])){ ?>
	<img src="<?=$logoPath?>" border="0" align ="absbottom"/>
	<? } ?>
	<input type="file" name="logoImg"/> <span class="small1 extext">(��������� 100 x 20)</span>
	</div>
	</td>
</tr>
<tr>
	<td>�ΰ� ��ġ</td>
	<td class="noline">
	<div style="padding:0 0 2 0">
	<label><input type="radio" name="logoLocation" value="top" <?php echo $checked['logoLocation']['top'];?>>��
	<input type="radio" name="logoLocation" value="bottom" <?php echo $checked['logoLocation']['bottom'];?>>��
	<input type="radio" name="logoLocation" value="left" <?php echo $checked['logoLocation']['left'];?>>��
	<input type="radio" name="logoLocation" value="right" <?php echo $checked['logoLocation']['right'];?>>��</label>
	</div>
	</td>
</tr>
<tr>
	<td>�ΰ� ����</td>
	<td>
	<input type="text" name="degree" size="3" value="<?=$qrCfg['degree']?>"/> % (0�� ����� ���� �����մϴ�.)
	</td>
</tr>
</table>
<div class=button>
<input type="image" src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
<p/>
</div>
</form>
</div>
<div style="padding-top:10px"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>*�ñ��� �ذ�

 <tr><td>�� ��ǰ���� QR���: <br>
 - ��������� ������ ��� ��ǰ �� �������� QR�ڵ带 ������ �� �ִ� ����� �����˴ϴ�.<br>
                           - ��ǰ �������������  ��QR Code���⡯����� �߰� �Ǿ� �� ��ǰ�� ���� ���θ� ������ �� �ֽ��ϴ�.</td></tr>
 <tr><td>�� �̺�Ʈ QR��� :  �̺�Ʈ�������� �����Ͽ� Ȱ���� �� �ֽ��ϴ�.��������� ������ �̺�Ʈ ������������� ��뼳���� �� �� �ֽ��ϴ�.</td></tr>

 <tr><td>�� �ΰ��̹������ : ����� QR�ڵ忡 ���θ� �ΰ� �߰� �� �� �ֽ��ϴ�. ��ǰ�������� �̺�Ʈ �������� ����Ǵ� QR�ڵ忡 ���Ե˴ϴ�.</td></tr>

 <tr><td>�� �ΰ��̹��� ��� : QR�ڵ忡 ������ �̹����� ����մϴ�. gif�̹��� 100*20 ������ ����� �����մϴ�.</td></tr>

<tr><td>�� �ΰ���ġ : ����� �ΰ��� ���� ��ġ�� �����մϴ�.</td></tr>

<tr><td>�� �ΰ����� : ����� �̹����� ������ �����Ͽ� ������ �� �ֽ��ϴ�. </td></tr>

</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<? include "../_footer.php"; ?>