<?

include "../_header.popup.php";

if ($_GET[mode]=="modify" || $_GET[mode]=="reply"){
	$data = $db->fetch("SELECT QA.*,MB.m_id FROM ".GD_GOODS_QNA." AS QA LEFT JOIN ".GD_MEMBER." AS MB ON QA.m_no = MB.m_no where QA.sno='" . $_GET['sno'] . "'",1);
	$checked['secret'] = "";
	if($data['secret'])$checked['secret'] = " checked";
	if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
		$data = validation::xssCleanArray($data, array(
				validation::DEFAULT_KEY => 'html',
				'subject' => array('html', 'ent_quotes'),
				'contents' => array('html', 'ent_quotes'),
		));
	}
	if ( $_GET[mode]=="reply" ){
		$data['subject'] = '';
		$data['contents'] = '';
		$data['regdt'] = date( 'Y-m-d H:i:s' );
		$data['ip'] = $_SERVER['REMOTE_ADDR'];
	}
	else {
		list( $data['m_id'] ) = $db->fetch("select m_id from ".GD_MEMBER." where m_no='" . $data['m_no'] . "'");
		if ( empty($data[m_no]) ) $data[m_id] = $data[name]; // ��ȸ����
		$data['subject'] = htmlspecialchars( $data['subject'] );
		$data['contents'] = htmlspecialchars( $data['contents'] );
	}

	$query = "select b.goodsnm,b.img_s,c.price
	from
		".GD_GOODS." b
		left join ".GD_GOODS_OPTION." c on b.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
	where
		b.goodsno = '" . $data[goodsno] . "'";
	list( $data[goodsnm], $data[img_s], $data[price] ) = $db->fetch($query);
}
?>
<script>
function fnOpenSMSSelector(el) {

	if (el.checked == true)
	{
		var fname = 'form';
		var fld = 'sms';
		var mode = 'popup';
		var mobile = document.form.phone.value;

		popup('../member/sms_selector.php?mode='+mode+'&fname='+fname+'&fld='+fld+'&mobile='+mobile,800,600);
	}
	else {
		document.form.sms.value = '';
		document.getElementById('sms_ready').style.display = 'none';
	}
}
function fnToggleSms(s) {

	var f = document.form;

	if (f.sms.value != '' && f.snd_sms.checked == true) {
		document.getElementById('sms_ready').style.display = 'inline';
	}

}
</script>
<form name=form method=post action="goods_qna_indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$_GET[sno]?>">
<input type=hidden name=goodsno value="<?=$data[goodsno]?>">
<input type=hidden name='page' value="<?=$_GET[page]?>">
<input type=hidden name='sms' value="">
<div class="title title_top">��ǰ���ǿ� ���� <?=( $_GET[mode] == "modify" ? '����' : '�亯' )?><span></span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=26>
	<td>��ǰ</td>
	<td>
	<div style="float:left"><?=goodsimg($data[img_s],40,"style='border:1 solid #efefef;margin-right:10px;'",1)?></div>
	<div style="float:left;color:#0074BA;" class=def><?=$data[goodsnm]?></div>
	</td>
</tr>
<tr>
	<td>�ۼ���</td>
	<td>
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
	<td><font class=ver8><?=$data[regdt]?> &nbsp;&nbsp;IP: (<?=$data[ip]?>)</td>
</tr>
<?if($cfg['qnaSecret']){?>
<tr>
	<td>��б�</td>
	<td class="noline"><input type="checkbox" name="secret" value="1"<?=$checked['secret']?>> ��б�</td>
</tr>
<?}?>

<? if ($data['rcv_email']) { ?>
<tr>
	<td>�̸���</td>
	<td><label class="noline"><input type="checkbox" name="snd_email" value="1"> ������ �亯������ ���ÿ� �����Ͻðڽ��ϱ�?</label></td>
</tr>
<? } ?>

<tr>
	<td>����</td>
	<td><input type="text" name="subject" value="<?=$data['subject']?>" style="width:90%;" class=line></td>
</tr>
<tr>
	<td>����</td>
	<td>
	<textarea name=contents style="width:550px;height:350px" type=editor required label="����"><?=$data['contents']?></textarea>
	<script src="../../lib/meditor/mini_editor.js"></script>
	<script>mini_editor("../../lib/meditor/")</script>
	</td>
</tr>
<? if ($data['rcv_sms']) { ?>
<tr>
	<td>���ڸ޽���</td>
	<td><label class="noline"><input type="checkbox" name="snd_sms" value="1" onClick="fnOpenSMSSelector(this);"> üũ�� <?=$data['name']?>[<?=$data['m_id'] ? $data['m_id'] : '��ȸ��' ?>] �Կ��� SMS ����</label>
	<img src="../img/icon_sms_ready.gif" align="absmiddle" id="sms_ready" style="display:none;">
	<input type="hidden" name="phone" value="<?=$data['phone']?>">
	</td>
</tr>
<? } ?>
</table>

<div class="button_popup">
<input type=image src="../img/btn_confirm_s.gif">
<a href="javascript:window.close()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>

<script>
linecss();
table_design_load();
</script>