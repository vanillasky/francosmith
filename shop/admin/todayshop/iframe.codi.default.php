<?
	include "../_header.popup.php";
	$popupWin = true;


if ( !$_GET['mode'] ) $_GET['mode'] = "mod_default";

switch ( $_GET['mode'] ){
	case "mod_default":
		$checked['subCategory'][$cfg['subCategory']] = "checked";
		$checked['copyProtect'][$cfg['copyProtect']] = "checked";
		if(!$cfg['shopMainGoodsConf']) $cfg['shopMainGoodsConf'] = "T";
		$checked['shopMainGoodsConf'][$cfg['shopMainGoodsConf']] = "checked";
	break;
}

{ // ��Ų ���丮 ����

	$baseSkin = array( 'today' , 'goodday');	// 2011-07-11 goodday �߰�
	$tmp = array( 'b' => array(), 'u' => array() );

	$skinDir = dirname(__FILE__) . "/../../data/skin_today/";
	$odir = @opendir( $skinDir );

	while (false !== ($rdir = readdir($odir))) {
		// ���丮������ üũ
		if(is_dir($skinDir . $rdir)){
			if ( !ereg( "\.$", $rdir ) && in_array( $rdir, $baseSkin ) ) $tmp['b'][] = $rdir;
			else if ( !ereg( "\.$", $rdir ) && !in_array( $rdir, $baseSkin ) ) $tmp['u'][] = $rdir;
		}
	}

	sort ( $tmp['b'] );
	sort ( $tmp['u'] );

	$skins = array_merge($tmp['b'], $tmp['u']);
	unset( $tmp );
}
?>
<script language="javascript">
<!--
function shopSize(){

	var FObj = document.fm;

	{ // ���λ��� ������
		var shopLineSize = 0;

		if ( FObj.shopLineColorL.value != '' ) shopLineSize++;
		//if ( FObj.shopLineColorC.value != '' ) shopLineSize++;	// 2011-07-11 ������ ���ܵǸ鼭 ���ʿ��� �κ�
		if ( FObj.shopLineColorR.value != '' ) shopLineSize++;

		document.getElementById('shopLineSize').innerHTML = shopLineSize;
	}

	{ // ��ü ������
		document.getElementById('shopSize').innerHTML = eval( FObj.shopOuterSize.value ) + shopLineSize;
	}

	{ // ���� ������
		document.getElementById('shopBodySize').innerHTML = eval( FObj.shopOuterSize.value ) /*- eval( FObj.shopSideSize.value )*/;	// 2011-07-11 ������ ���ܵǸ鼭 ���ʿ��� �κ�
	}
}

function selectSkinDelete(tplSkinToday){
	if(confirm(tplSkinToday + "��Ų�� ������ ���� �Ͻðڽ��ϱ�? ������ ������ �Ұ����մϴ�.")){
		location.href="./indb.skin.php?mode=skinDel&tplSkinToday="+tplSkinToday;
	}
}

function selectSkinCopy(tplSkinToday){
	if(confirm("��Ų�̸��� " + tplSkinToday + "_C �� �����Ǿ� ���簡 ���� �˴ϴ�. Ȯ�ι�ư�� �����ø� ���簡 ���� �˴ϴ�.")){
		location.href="./indb.skin.php?mode=skinCopy&tplSkinToday="+tplSkinToday;
	}
}

window.onload = shopSize;
//-->
</script>

<div class="title title_top">������ ��Ų����<span>������ �⺻������ �����ϼ���</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=2')"><img src="../img/btn_q.gif" align="absmiddle" hspace="2" /></a></div>

<div style="padding-top:5px"></div>

<!-------------- ��Ų���� ���� --------------->
<table cellpadding="0" cellspacing="0" border="0" background="../img/codi/bg_skin_form_center.gif">
<tr>
	<td height="16" colspan="2"><img src="../img/codi/bg_skin_form_top.gif" align="absmiddle" /></td>
</tr>
<tr>
	<td colspan="2" style="padding:5px 25px 5px 25px;vertical-align:top;">

	<!-- �����ϰ� �ִ� ��Ų -->
	<div style="padding-top:3px"><img src="../img/codi/bar_get_skin.gif" align="absmiddle" /></div>
	<table class="tb">
	<tr>
		<td height="20">

		<div id="skinBoxScroll" class="scroll">
		<table width="96%" cellpadding="0" cellspacing="0" border="0">
<?

	foreach ( $skins as $sKey => $sVal ){
		echo"<tr height=\"22\">".chr(10);

		/* ��Ų�� */
		echo"<td>";
		if($sVal == $cfg['tplSkinTodayWork']) echo"<b style='color:F54D01;'>";
		if($sVal == $cfg['tplSkinToday']) echo"<b style='color:5F8F1A;'>";
		if( in_array( $sVal, $baseSkin ) ){
			echo"�⺻��Ų";
		}else{
			echo"����ڽ�Ų";
		}
		echo" ( ".$sVal." )";
		if($sVal == $cfg['tplSkinTodayWork']) echo"</b>";
		echo"</td>".chr(10);

		/* �۾���Ų */
		echo"<td width=\"65\" style=\"padding:0px 3px 0px 3px\">";
		if($sVal == $cfg['tplSkinTodayWork']){
			echo"<img src=\"../img/codi/btn_work_skin_on.gif\" border=\"0\" align=\"absmiddle\" />";
		}else{
			echo"<a href=\"./indb.skin.php?mode=skinChangeWork&workSkinToday=".$sVal."\"><img src=\"../img/codi/btn_work_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}
		echo"</td>".chr(10);

		/* ��뽺Ų */
		echo"<td width=\"65\" style=\"padding:0px 20px 0px 3px\">";
		if($sVal == $cfg['tplSkinToday']){
			echo"<img src=\"../img/codi/btn_use_skin_on.gif\" border=\"0\" align=\"absmiddle\" />";
		}else{
			echo"<a href=\"./indb.skin.php?mode=skinChange&useSkinToday=".$sVal."\"><img src=\"../img/codi/btn_use_skin_off.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}
		echo"</td>".chr(10);

		/* �̸����� */
		echo"<td width=\"65\" style=\"padding:0px 3px 0px 3px\">".chr(10);
		echo"<a href=\"/?tplSkinToday=".$sVal."\" target=\"_blank\"><img src=\"../img/codi/btn_preview.gif\" border=\"0\" align=\"absmiddle\" /></a>".chr(10);
		echo"</td>".chr(10);

		/* �ٿ�ε� */
		echo"<td width=\"40\" style=\"padding:0px 3px 0px 3px\">";
		echo"<a href=\"./indb.skin.php?mode=skinDown&tplSkinToday=".$sVal."\"><img src=\"../img/codi/btn_down.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		echo"</td>".chr(10);

		/* ���� */
		echo"<td width=\"40\" style=\"padding:0px 3px 0px 3px\">";
		echo"<a href=\"javascript:selectSkinCopy('".$sVal."');\"><img src=\"../img/codi/btn_copy.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		echo"</td>".chr(10);

		/* ���� */
		echo"<td width=\"40\" style=\"padding:0px 0px 0px 3px\">";
		if($sVal != $cfg['tplSkinToday'] && $sVal != $cfg['tplSkinTodayWork']){
			echo"<a href=\"javascript:selectSkinDelete('".$sVal."');\"><img src=\"../img/codi/btn_delete.gif\" border=\"0\" align=\"absmiddle\" /></a>";
		}
		echo"</td>".chr(10);

		echo"</tr>".chr(10);
	}
?>
		</table>
		</div>

		</td>
	</tr>
	</table>
	<!-- �����ϰ� �ִ� ��Ų �� -->

	</td>
</tr>
<tr>
	<td colspan="2" style="padding:5px 25px 5px 25px;text-align:right;">
	<a href="javascript:popup2('codi.skin.upload.php',400,300,0);"><img src="../img/codi/btn_skin_upload.gif" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td colspan="2" style="padding:0px 25px 5px 25px;">
	<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="color:#c8ec50;font-weight:bold;">��뽺Ų :</span> ���õ� ��Ų�� ���� ���θ� ȭ�鿡 �������ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="color:#fec6ac;font-weight:bold;">�۾���Ų :</span> ���õ� ��Ų���� ������ �۾��� �ϰ� �˴ϴ�. �������� ���ÿ� ���� ��뽺Ų�� �۾���Ų�� �ٸ��ų� ������ �� �ֽ��ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">ȭ�麸�� :</span> �ش� ��Ų�� ���θ� ȭ���� ��â���� ���� �帳�ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">�ٿ� :</span> �ش� ��Ų�� �ٿ�ε� �޾Ƽ� ����� �� �ֽ��ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">���� :</span> �ش� ��Ų�� ����Ǿ� ��Ų�� �߰��˴ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><span style="font-weight:bold;">���� :</span> �ش� ��Ų�� �����Ǿ� ���ϴ�. (�⺻ ��Ų, ������� ��Ų, �۾����� ��Ų�� �������� �ʽ��ϴ�.)</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" />���� : �������� �������� �ʾҰų� ���۱ǿ� ���˵Ǵ� ��Ų�� ���ε� �Ǵ� ����ؼ��� �ȵǸ�, �׿� ���� å���� ���θ� ��ڿ��� �ֽ��ϴ�.</td></tr>
	</table>
	</div>
	<script>cssRound('MSG01')</script>
	</td>
</tr>
<tr>
	<td height="17" colspan="2"><img src="../img/codi/bg_skin_form_bottom.gif" align="absmiddle" /></td>
</tr>
</table><br />
<!-------------- ��Ų���� �� --------------->

<!-- ���� ������� ��Ų -->
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td height="20"><img src="../img/codi/bar_use_skin.gif" align="absmiddle" /></td>
</tr>
<tr>
	<td height="22" style="border-left:1px solid #78b72a;border-right:1px solid #78b72a;">
	<table cellpadding="0" cellspacing="6" border="0">
	<tr>
		<td width="100"><img src="../img/codi/icon_use_skin.gif" align="absmiddle" /></td>
		<td width="300" style="line-height:30px;">
			<b style="color:5F8F1A;"><?=( in_array( $cfg['tplSkinToday'], $baseSkin ) ? "�⺻��Ų" : "����ڽ�Ų" )?> (<?=$cfg['tplSkinToday']?>)</b><br />
			<a href="/?tplSkinToday=<?=$cfg['tplSkinToday']?>" target="_blank"><img src="../img/codi/btn_preview.gif" align="absmiddle" /></a>
		</td>
		<td style="line-height:30px;"><a href="./indb.skin.php?mode=skinChangeWork&workSkinToday=<?=$cfg['tplSkinToday']?>"><img src="../img/codi/btn_work_skin.gif" align="absmiddle" /></a></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height="4"><img src="../img/codi/bg_use_skin_bottom.gif" align="absmiddle" /></td>
</tr>
</table><br />
<!-- ���� ������� ��Ų �� -->

<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td height="20"><img src="../img/codi/bar_work_skin.gif" align="absmiddle" /></td>
</tr>
<tr>
	<td height="22" style="border-left:1px solid #f64c01;border-right:1px solid #f64c01;">

	<!-------------- ���� �۾����� ��Ų ���� --------------->
	<table cellpadding="0" cellspacing="6" border="0">
	<tr>
		<td width="100"><img src="../img/codi/icon_work_skin.gif" align="absmiddle" /></td>
		<td width="300" style="line-height:30px;">
			<b style="color:F54D01;"><?=( in_array( $cfg['tplSkinTodayWork'], $baseSkin ) ? "�⺻��Ų" : "����ڽ�Ų" )?> (<?=$cfg['tplSkinTodayWork']?>)</b><br />
			<a href="/?tplSkinToday=<?=$cfg['tplSkinTodayWork']?>" target="_blank"><img src="../img/codi/btn_preview.gif" align="absmiddle" /></a>
		</td>
		<td style="line-height:30px;"><a href="./indb.skin.php?mode=skinChange&useSkinToday=<?=$cfg['tplSkinTodayWork']?>"><img src="../img/codi/btn_use_skin.gif" align="absmiddle" /></a></td>
	</tr>
	</table>
	<!-------------- ���� �۾����� ��Ų �� --------------->

	</td>
</tr>
<tr>
	<td height="22" style="border-left:1px solid #f64c01;border-right:1px solid #f64c01; padding:0px 33px 0px 33px;">

	<div style="padding-top:20px"></div>

	<form name="fm" method="post" action="../todayshop/indb.skin.php" onsubmit="return chkForm(this)">
	<input type="hidden" name="mode" value="<?=$_GET['mode']?>">
	<input type="hidden" name="tplSkinToday" value="<?=$cfg['tplSkinToday']?>">
	<input type="hidden" name="tplSkinTodayWork" value="<?=$cfg['tplSkinTodayWork']?>">

	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td width="96"></td>
		<td>
		<table width="500" height="56" cellpadding="0" cellspacing="0" background="../img/back_skin_allsize.gif" border="0">
		<tr>
			<td width="90"></td>
			<td align="center" valign="top">��ü <span id="shopSize" style="font:10pt ����;color:#ff4e00;font-weight:bold;"><b>0</b></font></span> �ȼ� = �ܰ� <input type="text" name="shopOuterSize" style="width:50px" value="<?=$cfg['shopOuterSize']?>" class="cline" onkeyup="shopSize();" required label='�ܰ� ������'> �ȼ� + ���� <span id="shopLineSize" style="font:10pt ����;color:#ff4e00;font-weight:bold;">0</span> �ȼ�</td>
			<td width="90"></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	<!-------------- ��ü������ ���� �� --------------->

	<!-------------- ����/���� ���� ���� --------------->
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<!---------- ���ʶ��� ���� ------------>
		<td>
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td>���ʶ��λ���</td>
			<td align="right"><img src="../img/back_side_leftline.gif" /></td>
		</tr>
		<tr>
			<td colspan="2" style="padding-right:3px"><input type="text" name=shopLineColorL class="line" value="<?=$cfg['shopLineColorL']?>" maxlength="6" style="width:55;" onkeyup="shopSize();"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable_s.gif" alt="����ǥ ����" align="absmiddle" /></a></td>
		</tr>
		<tr><td colspan="2" style="padding-top:2px"><font class="extext">������ �Ⱦ�����<br />�������� �μ���</td></tr></table>
		</td>
		<!---------- ���ʶ��� �� ------------>

		<!------------------------- ����/����/������� ���� -------------------------->
		<td width="500" height="300" background="../img/back_skintodaysize_set.gif" valign="top">

		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td valign="top">
			<br>
			<!---------- ������� ���� ------------>
			<table cellpadding="0" cellspacing="0" border="0">
			<tr><td height=100 colspan="2"></td></tr>
			<tr>
				<td width="10"></td>
				<td background="../img/back_centersize_todayshop.gif" width="480" height="7" align="center">���� <span id="shopBodySize" style="font:10pt ����;color:#ff4e00;font-weight:bold;">0</span> �ȼ�</td>
			</tr>
			</table>
			<!---------- ������� �� ------------>
			</td>
		</tr>
		</table>

		</td>
		<!---------------------------- ���麻��/������� �� --------------------------->

		<!---------- �����ʶ��� ���� ------------>
		<td>
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td width="18"><img src="../img/back_side_rightline.gif" /></td>
			<td>�����ʶ��λ���</td>
		</tr>
		<tr>
			<td colspan="2" style="padding-left:5px"><input type="text" name="shopLineColorR" class="line" value="<?=$cfg['shopLineColorR']?>" maxlength="6" style="width:55;" onkeyup="shopSize();"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable_s.gif" alt="����ǥ ����" align="absmiddle" /></a></td>
		</tr>
		<tr><td colspan="2" style="padding:2px 0px 0px 5px"><font class="extext">������ �Ⱦ�����<br />�������� �μ���</td></tr>
		</table>
		</td>
		<!---------- �����ʶ��� �� ------------>

		<td></td>

	</tr>
	</table>
	<!-------------- ����/���� ���� �� --------------->

	<div style="padding-top:15"></div>

	<!-------------- ȭ�� ���� ���� --------------->
	<table width="690" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="center">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="noline" align="center">
			<div><img src="../img/todayshop_left_align.gif" /></div>
			<input type="radio" name="shopAlign" value="left" <?=( $cfg['shopAlign'] == 'left' ? 'checked' : '' )?> required label='���Ĺ��'>ȭ�� �������� �����ϱ�</td>
			<td width=40></td>
			<td class="noline" align="center">
			<div><img src="../img/todayshop_center_align.gif" /></div>
			<input type="radio" name="shopAlign" value="center" <?=( $cfg['shopAlign'] == 'center' ? 'checked' : '' )?> required label='���Ĺ��'>ȭ�� ����� �����ϱ�</td></tr>
		</table>
		</td>
	</tr>
	</table>
	<!-------------- ȭ�� ���� �� --------------->

	<!--div class="title">���λ�ǰ ���� ����<span>���ο� ����Ǵ� ��ǰ�����ϴ� ����� ������ �� �ֽ��ϴ�.</span></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>���λ�ǰ ���� ����</td>
		<td class="noline">
		<input type="radio" name="shopMainGoodsConf" value="E" <?=$checked['shopMainGoodsConf']['E']?>> ��Ų�� ����
		<input type="radio" name="shopMainGoodsConf" value="T" <?=$checked['shopMainGoodsConf']['T']?>> ���� ����
		<div style="padding:6px 0px 0px 25px"><font class="extext">��Ų�� ���� : ��Ų���� ������ ��ǰ������ ������������ ����˴ϴ�.</font></div>
		<div style="padding:3px 0px 0px 25px"><font class="extext">���� ���� : ��Ų�� ������� ����</font></div>
		<div style="padding:3px 0px 0px 25px"><font class="extext">�� ������ ��ǰ�� ��ǰ���� > ��ǰ�������� > <a href="../goods/disp_main.php" class="extext">[���������� ��ǰ����]</a> ���� Ȯ���ϰ� ������ �� �ֽ��ϴ�.</font></div>
		</td>
	</tr>
	</table-->

	<!--div class="title">ī�װ� �޴����̾� ����<span>ī�װ� �޴����̾� Ÿ���� �����ϼ���</span></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>�޴����̾� ����</td>
		<td class="noline">
		<input type="radio" name="subCategory" value="0" <?=$checked['subCategory'][0]?>> �޴����̾� ������
		<input type="radio" name="subCategory" value="1" <?=$checked['subCategory'][1]?>> �޴����̾� �����
		<input type="radio" name="subCategory" value="2" <?=$checked['subCategory'][2]?>> 1��/2�� ī�װ��� ��� ���
		<div style="padding:6px 0px 0px 25px"><font class="extext">ī�װ� �޴����̾�� 1�� ī�װ� �޴��� ���콺�� �ø��� ������ ���̾ �������� ����Դϴ�</font></div>
		</td>
	</tr>
	</table>

	<div class="title">���콺 ������ ��ư ����<span>����Ʈ���� ���콺�� ������ ��ư�� ���� �������� ���� ���� (�������)</span></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>���콺 ������ ����</td>
		<td class="noline">
		<input type="radio" name="copyProtect" value="0" <?=$checked['copyProtect'][0]?>> ���콺 ������ ��ư ���Ѿ���
		<input type="radio" name="copyProtect" value="1" <?=$checked['copyProtect'][1]?>> ���콺 ������ ��ư ����
		</td>
	</tr>
	</table>-->

	<table width="690" cellpadding="0" cellspacing="0" border="0">
	<tr><td height="20"></td></tr>
	<tr>
		<td align="center" class="noline"><input type="image" src="../img/btn_register.gif"></td>
	</tr>
	<tr><td height="20"></td></tr>
	</table>

	</form>
	</td>
</tr>
<tr>
	<td height="4"><img src="../img/codi/bg_work_skin_bottom.gif" align="absmiddle" /></td>
</tr>
</table><br />

<!-------------- �����ΰ��̵� ���� --------------->
<table width="690" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td align="center"><a href="javascript:popup('http://guide.godo.co.kr/guide/php/manual_easy_design.php',750,800)"><img src="../img/btn_go_easydesign.gif" /></a>&nbsp;<a href="javascript:popup('http://guide.godo.co.kr/guide/php/manual_high_design.php',750,800)"><img src="../img/btn_go_highdesign.gif" /></a></td>
</tr>
</table>
<!-------------- �����ΰ��̵� �� --------------->

<div style="padding-top:25px"></div>

<div style="padding-left:50px">
<!-------------- ��Ų �⺻���� �ȳ� ���� --------------->
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td width="7"></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" class="small">
	<tr><td><img src="../img/arrow_blue.gif" align="absmiddle" /><font color="000000"><b>������ �⺻���� ����</b></font></td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><font color="555555">��Ų�� ������ �� ���ϴ� ���������� �����۾��� �����Ͻø� �˴ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><font color="555555">���ξ��� �������Ͻ÷��� ���λ����� �������� �μ���. ������ ���� 0�ȼ��� �˴ϴ�.</td><tr>
	<tr><td height=20></td></tr>
	<tr><td><img src="../img/arrow_blue.gif" align="absmiddle" /><font color="000000"><b>�� ��Ų�� �⺻������ ������</b></font></td></tr>
	</table>

	<div style="padding-top:13px"></div>

	<!-------------- season2 / easy ���� --------------->
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="padding-left:8px">

		<? if ( array_search( 'today', $skins ) !== false ){ ?>
		<table cellpadding="0" cellspacing="0" class="small" width=260>
		<tr><td><font color="627dce"><b>today</b></font></td></tr>
		<tr><td><font color="555555">��ü 900 �ȼ� = �ܰ�900�ȼ� + ���λ��� 0 �ȼ�</td></tr>
		<tr><td><font color="555555">���λ��� =  ����/���/������ ��� ����</font></div></td></tr>
		</table>
		<? } ?>
		</td>

		<td width=50></td>

		<td>
		<? if ( array_search( 'goodday', $skins ) !== false ){ ?>
		<table cellpadding="0" cellspacing="0" class="small" width=260>
		<tr><td><font color="627dce"><b>goodday</b></font></td></tr>
		<tr><td><font color="555555">��ü 900 �ȼ� = �ܰ�900�ȼ� + ���λ��� 0 �ȼ�</td></tr>
		<tr><td><font color="555555">���λ��� =  ����/���/������ ��� ����</font></div></td></tr>
		</table>
		<? } ?>
		</td>
	</tr>
	</table>
	<!-------------- season2 / easy �� --------------->

	<div style="padding-top:13px"></div>

	</td>
</tr>
</table>

<!-------------- ��Ų �⺻���� �ȳ� �� --------------->
</div>

<div style="padding-top:20px"></div>

<table cellpadding="0" cellspacing="0">
<tr><td bgcolor=e4e4e4 height=1 width=750></td></tr>
</table>

<div style="padding-top:20px"></div>

<?
if ($popupWin === true){
	echo '<script>table_design_load();setHeight_ifrmCodi();</script>';
}
else {
	include "../_footer.php";
}
?>