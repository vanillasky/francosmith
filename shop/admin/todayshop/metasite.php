<?
$location = "�����̼� > �Ҽȸ�Ÿ����Ʈ ���� ";
@include "../_header.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' ���� ��û�ȳ��� ���� �����ͷ� �������ֽñ� �ٶ��ϴ�.', -1);
}

$tsCfg = $todayShop->cfg;
$tsCfg['metasite'] = unserialize( $tsCfg['metasite'] );

$social_meta = &load_class('social_meta','social_meta');


?>
<div class="title title_top">�Ҽ� ��Ÿ����Ʈ ����<span>���޵� �Ҽ� ��Ÿ����Ʈ�� EP(Engine Page)�� Ȯ�� �� �� �ֽ��ϴ�</span></div>
<table border=4 bordercolor=#dce1e1 style="border-collapse:collapse" width=750>
<tr><td style="padding:7 10 10 10">
<div style="padding-top:5"><b>�� ��Ÿ����Ʈ ������ ���ǻ���.</b></div>
<div style="padding-top:7"><font class=g9 color=666666>�� �������</font></a></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>�������� �Ǹ��ϰ� �ִ� ��ǰ�� ��Ÿ����Ʈ�� �����ϱ� ���ؼ��� �Ʒ��� �����Ǵ� ��Ÿ����Ʈ �� ���ϴ� ����Ʈ�� ���޸� �����ϼž� �մϴ�.</font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>������ �Ϸ�� �Ŀ� ��ǰ���� ������ �Ͻ� �� �ֽ��ϴ�.</font></div>

<div style="padding-top:5"><font class=g9 color=666666>�� ��ǰ ������ üũ����</div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>��ǰ �ΰ� �̹����� �ʼ� ������ �����Ǿ� �ִ� ��ü�� �ֱ� ������ �ΰ��̹����� ����� �ּž� �մϴ�.</font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>��ǰ��Ͻ� ��Ÿ����Ʈ ���� ���� �κп� ��ȣ��, �ּ�, ����ó, ī�װ� ������ ������ּž� �մϴ�.</font></div>

<div style="padding-top:5"><font class=g9 color=666666>�� �̹��� ȣ���� ����</div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>��Ÿ����Ʈ ���� ������ ��ǰ �̹����� �ܺ� ����Ʈ�� ����Ǳ� ������ ���θ��� ��뷮 �̹����� ���������� ������ �� �ֵ��� �̹��� ȣ������ �ݵ�� ����ϼž� �մϴ�.</font></div>
</table>
<br>
<form name=form method=post action="indb.metasite.php" target="ifrmHidden" enctype="multipart/form-data">

	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>�ΰ� �̹���</td>
		<td>
			<input type=file name=logo style="width:300px">  (jpg ������ �̹����� ��� ����)
			<?
			$todayshop_logo = $_SERVER['DOCUMENT_ROOT'].'/shop/data/todayshop/todayshop_logo.jpg';
			if (is_file($todayshop_logo)) {
				$_img_url = str_replace($_SERVER['DOCUMENT_ROOT'],'http://'.$_SERVER['SERVER_NAME'],$todayshop_logo);
			?>
			<IMG
			style="BORDER-BOTTOM: #cccccc 1px solid; BORDER-LEFT: #cccccc 1px solid; BORDER-TOP: #cccccc 1px solid; BORDER-RIGHT: #cccccc 1px solid"
			class=hand onclick="popupImg('<?=$_img_url?>','../')"
			onerror="this.style.display='none'"
			src="<?=$_img_url?>" width=20>
			<? } ?>
			<div style="margin-top:5px;">�� <font class="small1" color="#444444">��Ÿ����Ʈ ������ ������ �ΰ� �̹����� �ʼ� ������ �����Ǿ� �ִ� ��ü�� �ֱ� ������ ������ �ΰ� �̹����� ����� �ֽñ� �ٶ��ϴ�.</font></div>

		</td>
	</tr>
	</table>
	<p/>
<table class=tb>
<colgroup>
	<col width="50" />
	<col width="150" />
	<col width="" />
	<col width="100" />
</colgroup>
	<tr class=rndbg>
		<td align=center><b>��뿩��</b></td>
		<td align=center><b>�Ҽ� ��Ÿ����Ʈ</b></td>
		<td align=center><b>�ּ� URL</b></td>
		<td align=center><b>�̸�����</b></td>
	</tr>
	<?
		foreach ($social_meta->sites as $key => $data) {
			$endpoint = 'http://'.($cfg['shopUrl'] != '' ? $cfg['shopUrl'] : $_SERVER['SERVER_NAME']).($cfg['rootDir'] != '' ? $cfg['rootDir'] : '/shop').'/partner/social.php?meta='.$key;
		?>

	<tr class=cellL height=30>
		<td align=center><input type=checkbox class=null name=metasite[<?=$key?>] value='1' <?=($tsCfg['metasite'][$key])?"checked":""?>></td>
		<td style="padding-left:10"><a href="<?=$data['url']?>" target="_blank"><font color=444444><?=$data['name']?></a></td>
		<td><?=$endpoint?></td>
		<td align=center><img src="../img/btn_naver_view.gif" onClick="window.open('<?=$endpoint?>');" class="hand"></td>
	</tr>
	<? } ?>

</table>



<div class="button">
<input type=image src="../img/btn_register.gif">
</div>
</form>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�����̼��� ����� ��ǰ�� ��Ÿ����Ʈ�� �����Ͽ� ȫ���� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ����Ʈ�� ����Ʈ�� �����Ǹ�, ��ǰ ��Ͻ� ��Ÿ����Ʈ ���������� �Է��ϼž� ����� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��Ÿ����Ʈ ���������� �Է����� ���� ��ǰ�� ��Ÿ����Ʈ�� ������� �ʽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ����Ʈ�� �����ϰ��� �ϴ� ����Ʈ���� ������û�� �Ͻ� �� ������ּ���.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
<? include "../_footer.php"; ?>