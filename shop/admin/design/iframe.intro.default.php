<?
$scriptLoad='<script src="../design/codi/_codi.js"></script>';
include "../_header.popup.php";
?>

<script language="javascript"><!--
function intro_sample_view(){

	var no = document.getElementById('intro_sample').value;
	var tag = "<img src='<?=$cfg['rootDir']?>/data/skin/<?=$cfg['tplSkinWork']?>/img/main/coming_" + no + ".gif' border='0'>";

	var txt = tag;
	txt = txt.replace( /\</, '&lt;' );
	txt = txt.replace( /\>/, '&gt;' );

	document.getElementById('intro_tag').innerHTML = txt;
	document.getElementById('intro_img').innerHTML = tag;
	setHeight_ifrmCodi();
}
--></script>


<form name="fm" method="post" action="../design/indb.php" onsubmit="return chkForm(this)">
<input type="hidden" name="mode" value="intro_save" />
<input type=hidden name=tplSkinWork value="<?=$cfg['tplSkinWork']?>">

<div class="title title_top">��Ʈ��/������ ������ ������<span>�Ϲ����� ��Ʈ��/������ �������� �������� �����մϴ�.</span></div>

<?=$workSkinStr?>


<!--<div style="margin:10px 0 10px 0;"><font class=extext>������ �������� ������ '<a href="/shop/main/intro.php" target="_blank"><font class=ver7 color="#0074BA"><b><u>http://�����θ�</u></b></font></a>' �� Ŭ���ϼ���.</div>
<div style="margin:10px 0 10px 0;"><font class=extext>������������ ������ '<a href="/shop/main/index.php" target="_blank"><font class=ver7 color="#0074BA"><b><u>http://�����θ�/shop/main/index.php</u></b></font></a>' �� Ŭ���ϼ���.</div>-->

<?=gen_design_history_tag('skin', $cfg['tplSkinWork'], 'main/intro.htm'); ?>

<?
{ // �������ڵ���

	$tmp = array();
	$tmp['t_name']		= 'content';
	$tmp['t_width']		= '99%';
	$tmp['t_rows']		= 20;
	$tmp['t_property']	= ' required label="HTML �ҽ�"';
	$tmp['tplFile']		= "/main/intro.htm";

	echo "<script>DCTM.write('{$tmp['t_name']}', '{$tmp['t_width']}', '{$tmp['t_rows']}', '{$tmp['t_property']}', '{$tmp['tplFile']}');</script>";
}
?>

<div style="padding-top:20px;"></div>


<table class="tb">
<col class="cellC"><col>
<tr>
	<td valign="top">
	<select id="intro_sample" onchange="intro_sample_view()">
	<option value="01">������ ���� 1</option>
	<option value="02">������ ���� 2</option>
	<option value="03">������ ���� 3</option>
	<option value="04">������ ���� 4</option>
	<option value="05">������ ���� 5</option>
	</select>
	</td>
	<td>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td id="intro_tag" style="padding:10px;background-color:#0071BB;color:#FFFFFF;"></td>
			</tr>
			<tr>
				<td id="intro_img" style="padding:10px;"></td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;&nbsp;<font class="small" color="#6d6d6d">�� �Ķ��ڽ����� �ڵ�κ��� �����ؼ� ����ϼ���.</font></td>
			</tr>
		</table>
	</td>
</tr>
</table>

<input type="hidden" name="skin_file" value="<?=$tmp['tplFile']?>"/>
<input type="hidden" name="gd_preview" value=""/>
<div style="padding:20px" align="center">
<a onclick="preview()" style="cursor:pointer"><img src="../img/codi/btn_editview.gif" /></a>
<input type="image" src="../img/btn_register.gif" class="null" />
</div>

</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class=small_ex>
<tr><td>��Ʈ�� ������ �Ǵ� ������ �������� ������ �� �ֽ��ϴ�.</td></tr>
<tr><td>�����̹��� 5���� �����ص帳�ϴ�. �� �Ķ��ڽ����� �ҽ��� ���� �� �����Ϳ� �־� Ȱ���ϼ���.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>�������� ��Ʈ�� �������� ������ ���� 2������ �����˴ϴ�.</td></tr>
<tr><td>�� ���� ������ ������ ���� �Ǵ� ȸ���� ���� ������ ��Ʈ�� ������</td></tr>
<tr><td>&nbsp;- ���� �Ǵ� ȸ���� ������ ������ ����Ʈ�� ���˴ϴ�. ������ ������ �� �ִ� ����Ȯ�� �������񽺸� ��û�ϰ� �̿��Ͽ� �ּ���.</td></tr>
<tr><td>�� ���� ������ ������ ȸ���� ���� ������ ��Ʈ�� ������</td></tr>
<tr><td>&nbsp;- ȸ���� ������ ������ ����Ʈ�� ���Ǹ�, ��ǰ ���Ŵ� ȸ���� �����մϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<script>
// �����ΰ��� ��Ʈ�� �̸�����
function preview() {
	DCPV.design_preview = window.open('about:blank');
	var fobj = document.fm;
	var ori_target = fobj.target;

	try {
		if (DCTM.editor_type == "codemirror" && DCTM.textarea_view_id == DCTM.textarea_merge_body) {
			DCTC.ed1.setValue(DCTC.merge_ed.editor().getValue());
		}
	}
	catch(e) {}

	fobj.gd_preview.value = '1';
	fobj.target = "ifrmHidden";
	fobj.submit();

	fobj.target = ori_target;
	fobj.gd_preview.value = '';
}

// �����ΰ��� ��Ʈ�� �̸����� �ݹ��Լ�
function preview_popup() {
	var fobj = document.fm;
	var skin_file = fobj.skin_file.value.substring(1);
	DCPV.preview_popup("../../" + skin_file.replace(/\.htm/gi, ".php") + "?tplSkin=" + fobj.tplSkinWork.value, skin_file);
}

intro_sample_view();
table_design_load();
setHeight_ifrmCodi();
</script>
