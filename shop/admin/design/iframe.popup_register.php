<?

$scriptLoad='<script src="../design/codi/_codi.js"></script>';
include "../_header.popup.php";

if($_GET['file']) $_GET['design_file']	= "popup/" . $_GET['file'];

include_once dirname(__FILE__) . "/codi/code.class.php";
$codi = new codi;

if($_GET['design_file']) $data_dir	= $codi->get_dirinfo( $dirpath = dirname( $_GET['design_file'] ) );	# Directory Data
if($_GET['design_file']) $data_file	= $codi->get_fileinfo( $_GET['design_file'] );						# File Data

$data_dir['inc']	= 'file';
$data_file['real_linkurl'] = '../../' . $data_file['linkurl'];

$file_name	= substr($data_file['name'],0,-4);
?>

<form method="post" name="fm" action="../design/codi/indb.php?design_file=<?=$_GET['design_file']?>" onsubmit="return chkForm( this );" enctype="multipart/form-data">
<input type="hidden" name="mode" value="popupConf" />

<div class="title title_top">�����˾�â ���<span>���� �˾�â�� ���� ������ �߰� �����Ͻ� �� �ֽ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=7')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<?=$workSkinStr?>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�˾�����</td>
	<td><input type="text" name="text" size="60" value="<?=$data_file['text']?>" class="line"></td>
</tr>
<tr>
	<td>�˾����ϸ�</td>
	<td>
	<? if($_GET['file']){?>
	<input type="hidden" name="name" value="<?=$file_name?>"><?=$_GET['file']?>
	<?}else{?>
	<font class=ver811><input type="text" name="name" value="<?=$file_name?>" style="ime-mode:disabled;">.htm</font> �� ����ϰ����ϴ� �˾�ȭ���� ȭ�ϸ��� �����ž��մϴ�.(������ ����)
	<?}?>
	</td>
</tr>
<tr>
	<td>��¿���</td>
	<td class="noline">
	<input type="radio" name="popup_use" value="Y" <?=( $data_file['popup_use'] == 'Y' ? 'checked' : '' );?> />���
	<input type="radio" name="popup_use" value="N" <?=( $data_file['popup_use'] != 'Y' ? 'checked' : '' );?> />�����
	</td>
</tr>
<tr>
	<td>â��ġ</td>
	<td>
	��ܿ��� : <input type="text" name="popup_spotw" size="6" class="right line" value="<?=$data_file['popup_spotw']?>" onkeydown="onlynumber();" /> <font class=ver811>pixel</font><br />
	�������� : <input type="text" name="popup_spoth" size="6" class="right line" value="<?=$data_file['popup_spoth']?>" onkeydown="onlynumber();" /> <font class=ver811>pixel</font>
	</td>
</tr>
<tr>
	<td>âũ��</td>
	<td>
	����ũ�� : <input type="text" name="popup_sizew" size="6" class="right line" value="<?=$data_file['popup_sizew']?>" onkeydown="onlynumber();" /> <font class=ver811>pixel</font><br />
	����ũ�� : <input type="text" name="popup_sizeh" size="6" class="right line" value="<?=$data_file['popup_sizeh']?>" onkeydown="onlynumber();" /> <font class=ver811>pixel</font>
	</td>
</tr>

<tr>
	<td>Ư���Ⱓ����<br>������ ����</td>
	<td>

	<div class="noline"><input type="radio" name="popup_dt2tm" value="Y" <?=( $data_file['popup_dt2tm'] == 'Y' ? 'checked' : '' );?> /> Ư���Ⱓ���� �˾�â�� �����ϴ�.</div>
	<div>
	������ : <input type="text" name="popup_sdt_tg" size="10" maxlength="8" class="line" value="<?=( $data_file['popup_dt2tm'] == 'Y' ? $data_file['popup_sdt'] : '' )?>" onkeydown="onlynumber();" onclick="calendar(event);" />
	���۽ð� : <input type="text" name="popup_stime_tg" size="6" maxlength="4" class="line" value="<?=( $data_file['popup_dt2tm'] == 'Y' ? $data_file['popup_stime'] : '' )?>" onkeydown="onlynumber();" /> <font color=627dce class=ver8>ex) 20080415</font>
	<div>
	</div>
	������ : <input type="text" name="popup_edt_tg" size="10" maxlength="8" class="line" value="<?=( $data_file['popup_dt2tm'] == 'Y' ? $data_file['popup_edt'] : '' )?>" onkeydown="onlynumber();" onclick="calendar(event);" />
	����ð� : <input type="text" name="popup_etime_tg" size="6" maxlength="4" class="line" value="<?=( $data_file['popup_dt2tm'] == 'Y' ? $data_file['popup_etime'] : '' )?>" onkeydown="onlynumber();" /> <font color=627dce class=ver8>ex) ����6��: 0600, ��12��: 2400</font>
	</div>
	</td>
</tr>

<tr>
	<td>Ư���Ⱓ����<br>Ư���ð����� ����</td>
	<td>
	<div class="noline"><input type="radio" name="popup_dt2tm" value="" <?=( $data_file['popup_dt2tm'] == '' ? 'checked' : '' );?> /> Ư���Ⱓ���� Ư���� �ð����� �˾�â�� �����ϴ�.</div>
	<div>
	������ : <input type="text" name="popup_sdt" size="10" maxlength="8" class="line" value="<?=( $data_file['popup_dt2tm'] == '' ? $data_file['popup_sdt'] : '' )?>" onkeydown="onlynumber();" onclick="calendar(event);" />
	������ : <input type="text" name="popup_edt" size="10" maxlength="8" class="line" value="<?=( $data_file['popup_dt2tm'] == '' ?$data_file['popup_edt'] : '' )?>" onkeydown="onlynumber();" onclick="calendar(event);" /> <font color=627dce class=ver8>ex) 20080415</font>
	</div>
	<div>
	���½ð� : <input type="text" name="popup_stime" size="6" maxlength="4" class="line" value="<?=( $data_file['popup_dt2tm'] == '' ?$data_file['popup_stime'] : '' )?>" onkeydown="onlynumber();" />
	Ŭ����ð� : <input type="text" name="popup_etime" size="6" maxlength="4" class="line" value="<?=( $data_file['popup_dt2tm'] == '' ?$data_file['popup_etime'] : '' )?>" onkeydown="onlynumber();" /> <font color=627dce class=ver8>ex) ����6��: 0600, ��12��: 2400</font>
	</div>
	</td>
</tr>
<tr>
	<td>âŸ��</td>
	<td class="noline">
	<input type="radio" name="popup_type" value="" <?=( $data_file['popup_type'] == '' ? 'checked' : '' );?> />�Ϲ� �������˾�â
	<input type="radio" name="popup_type" value="layerMove" <?=( $data_file['popup_type'] == 'layerMove' ? 'checked' : '' );?> />�̵����̾�
	<input type="radio" name="popup_type" value="layer" <?=( $data_file['popup_type'] == 'layer' ? 'checked' : '' );?> />�������̾�
	</td>
</tr>
</table>

<div style="padding-top:10px;"></div>

<?=gen_design_history_tag('skin', $cfg['tplSkinWork'], $_GET['design_file']); ?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
<tr valign="top">
	<td>
<?
{ // �������ڵ���

	$tmp = array();
	$tmp['t_name']		= 'content';
	$tmp['t_width']		= '100%';
	$tmp['t_rows']		= 20;
	$tmp['t_property']	= ' fld_esssential label="HTML �ҽ�"';
	$tmp['tplFile']		= "/" . $_GET['design_file'];

	echo "<script>DCTM.write('{$tmp['t_name']}', '{$tmp['t_width']}', '{$tmp['t_rows']}', '{$tmp['t_property']}', '{$tmp['tplFile']}');</script>";
}
?>
	</td>
</tr>
</table>

<div style="padding:20px" align="center" class="noline">
<a onclick="preview()" style="cursor:pointer"><img src="../img/codi/btn_editview.gif" /></a>
<input type="image" src="../img/btn_save.gif" alt="�����ϱ�" />
<a href="iframe.popup_list.php"><img src="../img/btn_list.gif" border="0" /></a>
</div>

</form>

<div id="codi_replacecode"><script>DCRM.write('<?=$_GET['design_file']?>');</script></div>

<script>
// �̸�����
function preview() {
	DCPV.design_preview = window.open('about:blank');
	var fobj = document.fm;
	var ori_action = fobj.action;
	var ori_target = fobj.target;

	try {
		if (DCTM.editor_type == "codemirror" && DCTM.textarea_view_id == DCTM.textarea_merge_body) {
			DCTC.ed1.setValue(DCTC.merge_ed.editor().getValue());
		}
	}
	catch(e) {}

	fobj.action = ori_action + "&gd_preview=1";
	fobj.target = "ifrmHidden";
	fobj.submit();

	fobj.action = ori_action;
	fobj.target = ori_target;
}

// �̸����� �˾�
function preview_popup() {
	var url = "../../../main/html.php?htmid=<?=$_GET['design_file']?>";
	DCPV.preview_popup(url, "<?=$_GET['design_file']?>");
}

table_design_load();
setHeight_ifrmCodi();
</script>
