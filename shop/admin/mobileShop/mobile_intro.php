<?
$location = "����ϼ� �����ΰ��� > ����ϼ� �����ΰ���";
include "../_header.php";
include "../../conf/config.mobileShop.php";

// �Ǹ�Ȯ�� ���� ��� ����
@include dirname(__FILE__)."/../../conf/fieldset.php";
if($realname['id'] != '' && $realname['useyn'] == 'y') $use_realname = true;
else $use_realname = false;
if($ipin['id'] != '' && $ipin['useyn'] == 'y') $use_ipin = true;
else $use_ipin = false;
if($ipin['nice_useyn'] == 'y' && $ipin['nice_minoryn'] == 'y') $use_niceipin = true;
else $use_niceipin = false;

//�޴�������Ȯ�� ���� ����� �ÿ��� ������Ʈ�� ��밡���ϰԲ� �߰� 2013-07-26
$hpauth = Core::loader('Hpauth');
$hpauthRequestData = $hpauth->getAdultRequestData();

if($hpauthRequestData['useyn'] =='y') $use_hpauth = true;
else $use_hpauth = false;
if($use_realname || $use_ipin || $use_niceipin || $use_hpauth) $adultro_ready = true;
else $adultro_ready = false;

if ( !$_GET['mode'] ) $_GET['mode'] = "mod_intro";
?>

<script language="javascript"><!--


function fnToggleIntroForm(b) {
	$$('input[name="custom_landingpageMobile"]').each(function(el){
		el.writeAttribute({disabled: !b});
		<? if(!$adultro_ready) { ?>if(el.value == "2") el.writeAttribute({disabled: true});<? } ?>
	});
}

function fnDesignIntroTemplate(t) {

	var url = false;

	switch (t) {
	case 1:			// ���� ��Ʈ��
		url = './iframe.codi.php?design_file=intro/intro.htm';
		break;
	case 2:			// ����
		url = './iframe.codi.php?design_file=intro/intro_adult.htm';
		break;
	case 3:			// ȸ��
		url = './iframe.codi.php?design_file=intro/intro_member.htm';
		break;
	}

	if (url != false)
	{
		var win = popup_return( url, 'INTRODESIGN', 900, 650, 100, 100, 1 );
		win.focus();
	}
	return;
}

window.onload = function() {
	fnToggleIntroForm(<?=$cfg['introUseYNMobile'] == 'Y' ? 'true' : 'false'?>);

}
--></script>


<form name="fm" method="post" action="indb.php" onsubmit="return chkForm(this)">
<input type="hidden" name="mode" value="<?=$_GET['mode']?>" />
<input type=hidden name=tplSkinMobileWork value="<?=$cfg['tplSkinMobileWork']?>">
<div class="title title_top">��Ʈ��/������ ����<span>��Ʈ�������� �Ǵ� �������������� ����� �� �ֽ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=4')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>��뿩��</td>
	<td class="noline" width=80%>
	<input type="radio" name="introUseYNMobile" value="Y" <?=( $cfg['introUseYNMobile'] == 'Y' ? 'checked' : '' )?> required label="��뿩��" onClick="fnToggleIntroForm(true);" /> ���
	<input type="radio" name="introUseYNMobile" value="N" <?=( $cfg['introUseYNMobile'] != 'Y' ? 'checked' : '' )?> required label="��뿩��" onClick="fnToggleIntroForm(false);" /> ������
	</td>
</tr>
</table>

<table border="0" cellpadding="0">
<tr><td height=15 colspan=2></td></tr>
<tr>
	<td><font class=extext>��Ʈ��/�������������� �ּҴ� <font class=ver7 color="#627dce"><b>http://<font class=small1><b>������</b></font>/m/intro/intro.php</b></font> �Դϴ�</td>
	<td width=10></td>
	<td><a href="/m/intro/intro.php?tplSkin=<?=$cfg['tplSkinMobileWork']?>" target="_blank"><img src="../img/btn_m_intro.gif"></a></td>
</tr>
<tr><td height=1 colspan=5></td></tr>
<tr>
	<td><font class=extext>���θ� ������������ �ּҴ� <font class=ver7 color="#627dce"><b>http://<font class=small1><b>������</b></font>/m/index.php </b></font> �Դϴ�</td>
	<td width=10></td>
	<td><a href="/m/index.php?tplSkin=<?=$cfg['tplSkinMobileWork']?>" target="_blank"><img src="../img/btn_m_mainpage.gif"></a></td>
</tr>
<tr><td height=15 colspan=2></td></tr>
</table>



<table class=tb>
<tr>
	<td class=cellC style="width:60%;height:30px;">��Ʈ�� ������ ��� ����</td>
	<td class=cellC style="width:25%;">���� ������ �湮 ����</td>
	<td class=cellC style="width:15%;">������ ������</td>
</tr>
<tr>
	<td class="noline">
		<label><input type="radio" name="custom_landingpageMobile" value="1" <?=$cfg['custom_landingpageMobile'] == 1 ? 'checked' : ''?> />������ �湮�� ������ ���� �Ϲ����� ��Ʈ�� ������</label>
	</td>
	<td>��ü</td>
	<td><a href="javascript:void(0);" onClick="fnDesignIntroTemplate(1);"><img src="../img/btn_view_intro2.gif"></a></td>
</tr>
<tr>
	<td class="noline">
		<label><input type="radio" name="custom_landingpageMobile" value="2" <?=$cfg['custom_landingpageMobile'] == 2 ? 'checked' : ''?> <?=!$adultro_ready ? 'disabled' : ''?> />���� ���� �� ��Ʈ�� ������</label><br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(���������� ����Ȯ�� ��������(�� : ������ ��)�� �̿��Ͽ� �ּ���.) <img src="../img/<?=(!$adultro_ready ? 'btn_nouse.gif' : 'btn_on_func.gif') ?>" align="absmiddle">
	</td>
	<td>����</td>
	<td><a href="javascript:void(0);" onClick="fnDesignIntroTemplate(2);"><img src="../img/btn_view_intro2.gif"></a></td>
</tr>
<tr>
	<td class="noline"><label><input type="radio" name="custom_landingpageMobile" value="3" <?=$cfg['custom_landingpageMobile'] == 3 ? 'checked' : ''?> />���������� ������ ȸ���� ���� ������ ��Ʈ�� ������</label></td>
	<td>ȸ��</td>
	<td><a href="javascript:void(0);" onClick="fnDesignIntroTemplate(3);"><img src="../img/btn_view_intro2.gif"></a></td>
</tr>
</table>

<p/>

<!--<div style="margin:10px 0 10px 0;"><font class=extext>������ �������� ������ '<a href="/shop/main/intro.php" target="_blank"><font class=ver7 color="#0074BA"><b><u>http://�����θ�</u></b></font></a>' �� Ŭ���ϼ���.</div>
<div style="margin:10px 0 10px 0;"><font class=extext>������������ ������ '<a href="/shop/main/index.php" target="_blank"><font class=ver7 color="#0074BA"><b><u>http://�����θ�/shop/main/index.php</u></b></font></a>' �� Ŭ���ϼ���.</div>-->




<div style="padding:20px" align="center">
<input type="image" src="../img/btn_register.gif" class="null" />
</div>

</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class=small_ex>
<tr><td>����ϼ����� ����� ��Ʈ�� ������ �Ǵ� ������ �������� ������ �� �ֽ��ϴ�.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>�������� ��Ʈ�� �������� ������ ���� 2������ �����˴ϴ�.</td></tr>
<tr><td>�� ���� ������ ������ ���θ� ���� ������ ��Ʈ�� ������</td></tr>
<tr><td>&nbsp;- ���θ� ������ ������ ����Ʈ�� ���˴ϴ�.</td></tr>
<tr>
	<td>&nbsp;- ������ ������ �� �ִ� ����Ȯ�� �������񽺸� ��û�ϰ� �̿��Ͽ� �ּ���. <a href="../member/adm_member_auth.hpauthDream.php" class=small_ex>[�޴��� ����Ȯ�� ����]</a> <a href="../member/ipin_new.php" class=small_ex>[������ ����]</a></td></tr>
<tr><td>�� ���� ������ ������ ȸ���� ���� ������ ��Ʈ�� ������</td></tr>
<tr><td>&nbsp;- ȸ���� ������ ������ ����Ʈ�� ���Ǹ�, ��ǰ ���Ŵ� ȸ���� �����մϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<script>
table_design_load();
setHeight_ifrmCodi();
</script>

<? include "../_footer.php"; ?>