<?

if (strpos(basename($_SERVER['HTTP_REFERER']), 'codi.banner.php') !== false || strpos(basename($_GET['returnUrl']), 'codi.banner.php') !== false){
	include "../_header.popup.php";
	$popupWin = true;
}
else {
	$location = "�����ΰ��� > �ΰ�/��� ����";
	include "../_header.php";
}

# �ΰ�/�����ġ ��������
if ( file_exists( $tmp = dirname(__FILE__) . "/../../conf/config.todayshop.banner_".$cfg['tplSkinTodayWork'].".php" ) ) @include $tmp;
else @include dirname(__FILE__) . "/../../conf/config.banner.php";

if(!$b_loccd['90']) $b_loccd['90']	= "���ηΰ�";
if(!$b_loccd['91']) $b_loccd['91']	= "�ϴܷΰ�";
if(!$b_loccd['92']) $b_loccd['92']	= "���Ϸΰ�";
if(!$b_loccd['93']) $b_loccd['93']	= "�ΰ���ġ�Է�";
if(!$b_loccd['94']) $b_loccd['94']	= "�ΰ���ġ�Է�";
if(!$b_loccd['95']) $b_loccd['95']	= "�ΰ���ġ�Է�";

$returnUrl = ($_GET[returnUrl]) ? $_GET[returnUrl] : $_SERVER[HTTP_REFERER];

$parseUrl = parse_url( $returnUrl );
$listUrl = ( $returnUrl ? $parseUrl[query] : $_SERVER['QUERY_STRING'] );
$listUrl = ($popupWin === true ? 'codi.banner.php?' : 'codi.banner.php?') . preg_replace( "'(mode|sno)=[^&]*(&|)'is", '', $listUrl );

if (!$_GET[mode]) $_GET[mode] = "register";

if ($_GET[mode]=="modify"){
	$data = $db->fetch("select * from ".GD_BANNER." where sno='" . $_GET['sno'] . "'",1);

	# WebFTP ����
	include dirname(__FILE__) . "/../design/webftp/webftp.class.php";
	$webftp = new webftp;
	$webftp->ftp_path = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $cfg['rootDir'] . '/data/skin_today/' . $cfg['tplSkinTodayWork']; # ��Ų���
}
?>
<SCRIPT language=javascript><!--
/*-------------------------------------
 ������ üũ
 fobj : form object
-------------------------------------*/
function fm_save( fobj ){

	if ( fobj.mode.value!="modify" && fobj['img_up'].value == "" ){

		alert( "�ΰ�/����̹����� �Էµ��� �ʾҽ��ϴ�." );
		fobj['img_up'].focus();
		return false;
	}

	if (!chkForm(fobj)) return false;
}
//--></SCRIPT>



<div id=goods_form>

<form method=post action="indb.codi.banner.php" enctype="multipart/form-data" onsubmit="return fm_save(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$_GET[sno]?>">
<input type=hidden name=returnUrl value="<?=$returnUrl?>">

<div class="title title_top">�ΰ�/�������<span>�����ּҸ� ��� ���Ϸ��� "nolink" ��� �Է�, �Ǵ� ��������� �μ���. &nbsp;&nbsp;&nbsp;<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=6')"><img src="../img/btn_bn_manual.gif" align=absmiddle></a></span></div>
<? include "../proc/warning_disk_msg.php"; # not_delete  ?>
<?=$workSkinTodayStr?>
<table class=tb>
<col class=cellC><col class=cellL>

<tr>
	<td nowrap>�ΰ�/�����ġ ����</td>
	<td nowrap>
	<SELECT NAME="loccd" required label="�ΰ�/�����ġ">
	<option value="">�� �ΰ�/�����ġ�� �����ϼ���.</option>
	<optgroup label="-- �ΰ���ġ --"></optgroup>
	<?
	# �ΰ��
	foreach ( $b_loccd as $lKey => $lVal ){
		if( $lKey < 90 ) continue;
	?>
	<option value="<?=$lKey?>" <?=$lKey==$data['loccd']?" selected":""?>><?=$lVal?></option>
	<?}?>
	<optgroup label="-- ������ġ --"></optgroup>
	<?
	# ���ʿ�
	foreach ( $b_loccd as $k => $v ){
		if( $k >= 90 ) continue;
	?>
	<option value="<?=$k?>" <?=$k==$data['loccd']?" selected":""?>><?=$v?></option>
	<?}?>
	</SELECT>
	<a href="javascript:popupLayer('../todayshop/codi.banner.loccd.php',780,600);"><img src="../img/btn_bangroup.gif" border=0 align=absmiddle></a> <font class=extext>���� ����� ����� �����ġ�� ���� ����Ҵٸ� �����ġ�� ��������</font>
	</td>
</tr>
<tr>
	<td nowrap>�����ּ�(��ũ)</td>
	<td nowrap>
	<input name="linkaddr" type="text" value="<?=$data['linkaddr']?>" style="width:300;" class="line">
	<select name="target">
	  <option value="">�� Ÿ���� �����ϼ���.</option>
	  <option value="_blank" <?if($data['target'] == "_blank") echo"selected";?>>��â</option>
	  <option value="" <?if($data['target'] == "") echo"selected";?>>����â</option>
	</select>
	</td>
</tr>
<tr>
	<td nowrap>�̹���</td>
	<td nowrap>
	<input type="file" name="img_up" class="line"><input type="hidden" name="img" value="<?=$data['img']?>">
	<a href="javascript:webftpinfo( '<?=( $data['img'] != '' ? '/data/skin_today/' . $cfg['tplSkinTodayWork'] . '/img/banner/' . $data['img'] : '' )?>' );"><img src="../img/codi/icon_imgsizeview.gif" border="0" alt="�̹��� ����" align="absmiddle"></a>
	<? if ( $data['img'] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="img_del" value="Y">����</span><? } ?>
	<? if ( $data['img'] != '' ){ echo '<div style="margin-top:3px;">' . $webftp->confirmImage( "../../data/skin_today/" . $cfg['tplSkinTodayWork'] . "/img/banner/" . $data['img'],300,100,"0") . '</div>'; }?> </td>
</tr>
</table>

<div style="padding: 3 0 0 132"><font class=extext>* �̹��� ���ϸ��� �ݵ�� <b>������ ���ڸ� ����</b>�մϴ�. <b>�ѱ�</b>�� ���� �ȵ˴ϴ�! ��) <font class=ver811 color=627dce><b>abc.jpg</b> �Ǵ� <b>abc123.gif</b> �Ǵ� <b>123.jpg</b></font></font></div>
<div style="padding: 3 0 0 132" class="extext">* ��, �÷��� ��ʸ� ����� ��� �̹���Ȯ���ڴ� <b>jpg</b>�θ� ����ϼž� �մϴ�.</div>
<div class=button>
<input type=image src="../img/btn_<?=$_GET[mode]?>.gif">
<a href="<?=$listUrl?>"><img src='../img/btn_list.gif'></a>
</div>

</form>
</div>


<table cellpadding=0 cellspacing=0 bgcolor=fafafa width=100%>
<tr><td style="padding: 5 15 15 15; text-align: justify">
<div><font color=EA0095><b>***</b> �� �����ּҶ��� �ּҸ� �Է��� �� �����Ͻ� ��! (<b>�ʵ�</b>) <b>***</b></font></div>
<font class=small1 color=555555>
<div style="padding-top:8">- �� ���θ��� �ƴ� <font color=0098a2>�ٸ� ����Ʈ�� �̵�</font> �� ������ <font class=ver811 color=333333><b>http://www.naver.com</b></font> �̷��� �ݵ�� ��� �ּҸ� �־�� �մϴ�. �̰��� <font color=0098a2>������</font>��� �մϴ�.</div>
<div style="padding-top:5">- �׷��� <font color=0098a2>�� ���θ� �ȿ� �ִ� �������� �̵�</font> �� ������ ������ �ϴ� �������� �ּҿ��� <font color=0098a2>�������� ������ ������ �ּҸ� ����</font>�Ͽ� �ֽ��ϴ�. �̰��� <font color=0098a2>�����</font>�Դϴ�.</div>
<div style="padding-top:5">- ���� ��� ��ʸ� ������ <font color=0098a2>ȸ��Ұ�������</font>�� ������ �� ��, �� �ּҴ� <font class=ver811 color=333333><b>http://www.test.co.kr/shop/service/company.php</b></font> �� �ǰ�,</div>
<div style="padding:3 0 0 8">���⼭ �������� ������ �������ּҴ� <font class=ver811 color=333333><b>/shop/service/company.php</b></font> �� �˴ϴ�. <font color=0098a2>�� �κи� �����ּҶ��� �Է�</font>�ϸ� �˴ϴ�.</div>
<div style="padding-top:5">- �ϳ��� ���� ���, �ΰ�/���Ŭ���� Ư���� <font color=0098a2>ī�װ�������</font>�� �̵��ϰ��� �ϸ�, �Է��� �ּҴ� <font class=ver811 color=333333><b>/shop/goods/goods_list.php?category=001</b></font> �̷��� �˴ϴ�. </div>
<div style="padding-top:5">- �ٽ� �����ϸ�, <font color=0098a2>�� ���θ��� �ٸ� �������� �̵�</font>�Ҷ����� �ݵ�� <font color=0098a2>�����</font>, �� <font color=0098a2>�������� ������ �������ּҸ� ����</font>�ؼ� �Է��ϼ���! </div>
<div style="padding-top:5">- <font color=0098a2>�ٸ� ����Ʈ</font>�� ������ <font color=0098a2>��� �ּ� (������)</font> �� �ְ�, <font color=0098a2>�� ���θ�������</font>�� �̵��Ϸ��� �� <font color=0098a2>�������� ������ �������ּ� (�����)</font> �� �����ؼ� �Է��ϼ���.</div>
<div style="padding-top:5">- �̷��� �����ο� ����θ� �����Ͽ� ��ũ�ּҸ� �ִ� ����� �ҽ����� HTML �ڵ����� ��ũ�� �� ������ ���������Դϴ�.</div>
</td></tr></table>


<div style="padding-top:5"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>�̹��� ���ϸ��� �ѱ����ϸ��� �ȵǸ�, �ݵ�� ����/���ڷ� �̷������ �մϴ�. ��) abc123.jpg</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>




<SCRIPT LANGUAGE="JavaScript" SRC="../proc/warning_disk_js.php"><!-- not_delete --></SCRIPT>
<?
if ($popupWin === true){
	echo '<script>table_design_load();</script>';
}
else {
	include "../_footer.php";
}
?>