<?

include "../_header.popup.php";

if ($_GET[mode]=="modify" || $_GET[mode]=="reply"){
	$data = $db->fetch("select * from ".GD_MEMBER_QNA." where sno='" . $_GET['sno'] . "'",1);

	if ( $_GET[mode]=="reply" ){
		$data['subject'] = '';
		$data['contents'] = '';
		$data['regdt'] = date( 'Y-m-d H:i:s' );
		$data['ip'] = $_SERVER['REMOTE_ADDR'];
	}
	else {
		list( $data['m_id'] ) = $db->fetch("select m_id from ".GD_MEMBER." where m_no='" . $data['m_no'] . "'");
		$data['subject'] = htmlspecialchars( $data['subject'] );
		$data['contents'] = htmlspecialchars( $data['contents'] );
		$data[mobile]	= explode("-",$data[mobile]);
	}

	if( $_GET[mode] == 'reply' || ( $_GET['sno'] != '' && $data['parent'] != '' && $_GET['sno'] != $data['parent'] ) ){
		$formtype = 'reply'; // �Է��׸� ����
		$data = array_merge( $data, $db->fetch("select itemcd, ordno, email, mailling, mobile, sms from ".GD_MEMBER_QNA." where sno='" . $data['parent'] . "'") );
	}
}
?>
<form name=form method=post action="<?=$sitelink->link('admin/board/member_qna_indb.php','ssl')?>" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$_GET[sno]?>">
<input type=hidden name=goodsno value="<?=$data[goodsno]?>">

<div class="title title_top">1:1 ���� <?=( $_GET[mode] == "modify" ? '����' : '�亯' )?><span></span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�ۼ���</td>
	<td><font class=ver8><b>
<?
if ( $_GET[mode]=="reply" ){
	echo '<select name="m_no">';

	$res = $db->query( "select m_no, m_id, name from ".GD_MEMBER." where m_id!='godomall' and level = 100 order by m_id" );
	while( $row = $db->fetch( $res ) ){
		echo '<option value="' . $row[m_no] . '">' . $row[m_id] . ' [' . $row[name] . ']</option>';
	}

	echo '</select>';
}
else {
	echo $data[m_id];
}
?>
	</td>
</tr>
<tr>
	<td>�ۼ���</td>
	<td><font class=ver8><?=$data[regdt]?> &nbsp;&nbsp;&nbsp; ( <?=$data[ip]?> )</td>
</tr>

<? if ( $formtype != 'reply' ){ // �亯�� �ƴ� ��쿡�� ��� ?>
<tr>
	<td>��������</td>
	<td><select name="itemcd" required fld_esssential label="��������">
	<option value="">���㳻���� �����ϼ���.</option>
	<? foreach( codeitem( 'question' ) as $key => $value ){ ?>
	<option value="<?=$key?>" <?=( $data[itemcd] == $key ? 'selected' : '' )?>><?=$value?></option>
	<? } ?>
	</select></td>
</tr>
<tr>
	<td>�ֹ���ȣ</td>
	<td>
	<input type=text name=ordno style="width:25%" value="<?=$data[ordno]?>"> <a href="javascript:order_open();"><img src="../img/btn_ordersearch.gif" border=0 align=absmiddle></a>

	</td>
</tr>
<tr>
	<td>�̸���</td>
	<td><input type=text name=email value="<?=$data[email]?>" size=30>
	<span class=noline style="padding-left:10px"><input type=checkbox name=mailling <?=( $data[mailling] == 'y' ? 'checked' : '' )?>>�޽��ϴ�</span>
	</td>
</tr>
<tr>
	<td>���ڸ޽���</td>
	<td>
	<input type=text name=mobile[] value="<?=$data[mobile][0]?>" size=4 maxlength=4> -
	<input type=text name=mobile[] value="<?=$data[mobile][1]?>" size=4 maxlength=4> -
	<input type=text name=mobile[] value="<?=$data[mobile][2]?>" size=4 maxlength=4>
	<span class=noline style="padding-left:10px"><input type=checkbox name=sms <?=( $data[sms] == 'y' ? 'checked' : '' )?>>�޽��ϴ�</span>
	</td>
</tr>
<? } else { ?>
<tr>
	<td>��������</td>
	<td><? foreach( codeitem( 'question' ) as $key => $value ){ echo ( $data[itemcd] == $key ? $value : '' ); } ?></td>
</tr>
<tr>
	<td>�ֹ���ȣ</td>
	<td><?=$data[ordno]?></td>
</tr>
<tr>
	<td>�̸���</td>
	<td style="padding-top:3px; padding-bottom:3px;">
	<?=$data[email]?><span style="padding-left:10px"><?=( $data[mailling] == 'y' ? '"�亯�� �������ּ���"' : '' )?></span>

<? if ( $_GET['mode'] != 'reply' ){ ?>
	<div style="padding-top:3px"></div>
	<font class=ver8><b><?echo( str_replace( ".", "", $data[maildt] ) > 0 ? '<span class="txt_Color2">' . $data[maildt] . '</span>' : '<span class="txt_Color3">�̹߼�</span>' )?></b></font>
	<?=str_repeat('&nbsp;',2);?>
	<img onclick="document.ifrmHidden.location.href='member_qna_indb.php?mode=mailsend&sno=<?=$data[sno]?>';" alt="�������� �����ϱ�" src="../img/btn_mailing.gif" align="absmiddle" style="cursor:pointer;">
<? } ?>

	<div class=noline style="margin-top:3px"><input type="checkbox" name="mailyn" value="Y" <?=( $data[mailling] == 'y' ? 'checked' : '' )?>> <font class=ver81 color=444444>������ �亯������ ���ÿ� �����Ͻðڽ��ϱ�?</font></div>
	</td>
</tr>
<tr>
	<td>���ڸ޽���</td>
	<td style="padding-top:3px; padding-bottom:3px;">
	<?=$data[mobile]?><span style="padding-left:10px"><?=( $data[sms] == 'y' ? '"�亯�� �������ּ���"' : '' )?></span>

<? if ( $_GET['mode'] != 'reply' ){ ?>
	<div style="padding-top:3px"></div>
	<font class=ver8><b><?echo( str_replace( ".", "", $data[smsdt] ) > 0 ? '<span class="txt_Color2">' . $data[smsdt] . '</span>' : '<span class="txt_Color3">�̹߼�</span>' )?></b></font>
	<?=str_repeat('&nbsp;',2);?>
	<img onclick="document.ifrmHidden.location.href='member_qna_indb.php?mode=smssend&sno=<?=$data[sno]?>';" alt="�������� �����ϱ�" src="../img/btn_mailing.gif" align="absmiddle" style="cursor:pointer;">
<? } ?>

	<div class=noline style="margin-top:3px"><input type="checkbox" name="smsyn" value="Y"<?=( $data[sms] == 'y' ? 'checked' : '' )?>> <font class=ver81 color=444444>������ �亯SMS�� ���ÿ� �����Ͻðڽ��ϱ�?</font></div>
	</td>
</tr>
<? } ?>
<tr>
	<td>����</td>
	<td><input type="text" name="subject" value="<?=$data['subject']?>" style="width:90%;" required fld_esssential  label="����" class=line></td>
</tr>
<tr>
	<td>����</td>
	<td>

<? if ( $formtype != 'reply' ){ // �亯�� �ƴ� ��쿡�� ��� ?>
	<textarea name="contents" cols=60 rows=20 style="width:90%;" required fld_esssential label="����" class=tline><?=$data['contents']?></textarea>
<? } else { ?>
	<textarea name="contents" cols=60 rows=14 style="width:90%;" required fld_esssential label="����" class=tline><?=$data['contents']?></textarea>
<? } ?>

	</td>
</tr>
</table>

<div class="button_popup">
<input type=image src="../img/btn_confirm_s.gif">
<a href="javascript:window.close()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>


<iframe id=ifm_order frameborder=0 scrolling=no style="display:none; background-color:#ffffff; border-style:solid; border-width:1; border-color:#000000;"></iframe>
<script language="javascript">
function order_open(){
	var divEl = document.getElementById('ifm_order');
	divEl.style.display = "block";
	divEl.style.left = 20;
	divEl.style.top = 165;
	divEl.style.width = 560;
	divEl.style.height = 280;
	divEl.style.position = "absolute";
	if( divEl.src == '' ) divEl.src = "member_qna_order.php?m_no=<?=$data[m_no]?>";
}

function order_close(){
	var divEl = document.getElementById('ifm_order');
	divEl.style.display = "none";
}

function order_put( ordno ){
	form.ordno.value = ordno;
	order_close();
}
</script>


<script>table_design_load();</script>