<?
/*------------------------------------------------------------------------------
�� Copyright 2005, Flyfox All right reserved.
@���ϳ���: �������ڵ��� > ���̾ƿ� > ��ü���̾ƿ�
@��������/������/������:
------------------------------------------------------------------------------*/
?>

<div style="padding-top:10;"></div>

<form method="post" name="fm" action="../todayshop/codi/indb.php?mode=save&design_file=<?=$_GET['design_file']?>" onsubmit="return chkForm( this );" enctype="multipart/form-data">


<?

if ($todayShop->cfg['shopMode'] == "todayshop") {
@include_once dirname(__FILE__) . "/_codi_map.php";
?>


<div style="margin:17px 0;">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>��ü����</td>
	<td>
		<div>
			��ü�� ������ <input type="text" name="outbg_color" value="<?=$data_file['outbg_color']?>" maxlength="6" style="width:100;" class="line"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable.gif" border="0" alt="����ǥ ����" align="absmiddle"></a>
		</div>
		<div>
			��ü�� ����̹���
			<input type="file" name="outbg_img_up" size="30" class="line"><input type="hidden" name="outbg_img" value="<?=$data_file['outbg_img']?>">
			<a href="javascript:webftpinfo( '<?=( $data_file['outbg_img'] != '' ? '/data/skin_today/' . $cfg['tplSkinTodayWork'] . '/img/codi/' . $data_file['outbg_img'] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="�̹��� ����" align="absmiddle"></a>
			<? if ( $data_file['outbg_img'] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="outbg_img_del" value="Y">����</span><? } ?>
		</div>
	</td>
</tr>
</table>
</div>


<div align=center class=noline>
	<input type=image src="../img/btn_save.gif" alt="�����ϱ�">&nbsp;&nbsp;
</div>

</form>


<!-- header/footer ���ϼ��� -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<TR>
	<TD><img src="../img/codi_main_05.gif"></TD>
</TR>
<tr>
	<td style="border:10px solid #EEEEEE; padding:5 5 5 5">
	<TABLE cellpadding=7 cellspacing=0 border=0>
	<TR>
		<TD colspan=2><div>��Ų�ҽ����� �ܰ��� ũ�� ���ΰ� �ִ� ��� ������ϰ� �ϴ� ǲ������ �ΰ��� �ֽ��ϴ�.</div>
		<div style="padding-top:4px">�� �ΰ��� ������ ũ�� ������ ���� ���� �ܰ������Դϴ�.</div>
		<div style="padding-top:4px">�ܰ��� �����ϽǶ����� �����Ͻñ� �ٶ��ϴ�. �Ʒ� �ΰ��� �����Դϴ�.</div>
		<div style="padding-top:10px">
		�Ʒ��� outline/header/standard.htm, outline/footer/standard.htm �� ���� ������ <span style="color:#FF3000;">�����̼� ���� ������ ����</span>�Դϴ�.<br>
		�Ϲ� ���θ� ������ ������ outline/header.htm, outline/footer.htm �Դϴ�.<br>
		������ ������ ��Ȯ�ϰ� �����Ͽ� �۾��� �ֽñ� �ٶ��ϴ�.<br>
		</div>
		</TD>
	</TR>
	<tr>
		<td width=420><img src="../img/codi_main_06.gif"></td>
		<td width=400 align=left>
		<TABLE>
		<TR>
			<TD style="padding-bottom:30"><A HREF="iframe.codi.php?design_file=outline/header/standard.htm"><img src="../img/btn_ts_header.gif"></A></TD>
		</TR>
		<TR>
			<TD valign=top><A HREF="iframe.codi.php?design_file=outline/footer/standard.htm"><img src="../img/btn_ts_footer.gif"></A></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	</TABLE>
	</td>
</tr>
</TABLE>
<!-- header/footer ���ϼ��� �� -->
<?
}
?>