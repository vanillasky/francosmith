<?

if ((isset($_SERVER['HTTP_REFERER']) && strpos(basename($_SERVER['HTTP_REFERER']), 'popup.banner.php') !== false) || (isset($_GET['returnUrl']) && strpos(basename($_GET['returnUrl']), 'popup.banner.php') !== false)) {
	include "../_header.popup.php";
	$popupWin = true;
}
else {
	$location = "�����ΰ��� > �ΰ�/��� ����";
	include "../_header.php";
}
# �ΰ�/�����ġ ��������
if ( file_exists( $tmp = dirname(__FILE__) . "/../../conf/config.banner_".$cfg['tplSkinWork'].".php" ) ) @include $tmp;
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
$listUrl = ($popupWin === true ? 'popup.banner.php?' : 'design_banner.php?') . preg_replace( "'(mode|sno)=[^&]*(&|)'is", '', $listUrl );

if (!$_GET[mode]) $_GET[mode] = "register";

# WebFTP ����
include dirname(__FILE__) . "/webftp/webftp.class.php";
$webftp = new webftp;
$webftp->ftp_path = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $cfg['rootDir'] . '/data/skin/' . $cfg['tplSkinWork']; # ��Ų���

$bannereditorchk = '';
$_GET['mode'] = add_slashes($_GET['mode']);
$_GET['sno'] = add_slashes($_GET['sno']);
$_GET['chgcode'] = add_slashes($_GET['chgcode']);
$_GET['iname'] = add_slashes($_GET['iname']);
$_POST['godoimg'] = add_slashes($_POST['godoimg']);
if ($_GET['mode']=="modify"){
	$modetext = '����';
	if($_GET['sno']){
		$data = $db->fetch("select * from ".GD_BANNER." where sno='" . $_GET['sno'] . "'",1);
	} else if($_GET['chgcode']){
		$data = $db->fetch("select * from ".GD_BANNER." where loccd='" . $_GET['chgcode'] . "' and img = '" . $_GET['iname'] . "'",1);
	}
	$data['imgchg'] = $_POST['godoimg'];
	$imgbannereditor = 'http://bannereditor.godo.co.kr/bannereditor/editor_down/'.$data['imgchg'];
	if(imgage_check($imgbannereditor)){
		$imgchgsizeset = ImgSizeSet($imgbannereditor,300,100);
		$bannereditorchk = 'Y';
	}
} else if($_GET['mode'] == "register"){
	$modetext = '���';
	$data['img'] = '';
	$data['imgchg'] = $_POST['godoimg'];
	$imgbannereditor = 'http://bannereditor.godo.co.kr/bannereditor/editor_down/'.$data['imgchg'];
	if(imgage_check($imgbannereditor)){
		$imgchgsizeset = ImgSizeSet($imgbannereditor,300,100);
		$bannereditorchk = 'Y';
	}
}
?>
<SCRIPT language=javascript><!--
/*-------------------------------------
 ������ üũ
 fobj : form object
-------------------------------------*/
function fm_save( fobj ){
	var bannereditorchk = '<?=$bannereditorchk?>';
	var chkname = '';
	if(bannereditorchk == 'Y'){
		chkname = 'imgchg';
	} else {
		chkname = 'img_up';
	}
	if ( fobj.mode.value!="modify" ){
		if (  fobj[chkname].value == "" ){
			alert( "�ΰ�/����̹����� �Էµ��� �ʾҽ��ϴ�." );
			fobj[chkname].focus();
			return false;
		}
	}

	if (!chkForm(fobj)) return false;
}
//--></SCRIPT>



<div id=goods_form>

<form method=post action="design_banner_indb.php" enctype="multipart/form-data" onsubmit="return fm_save(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$data[sno]?>">
<input type=hidden name=returnUrl value="<?=$returnUrl?>">
<input type=hidden name=bannereditorchk value="<?=$bannereditorchk?>">

<div class="title title_top">�ΰ�/�������<span>�����ּҸ� ��� ���Ϸ��� "nolink" ��� �Է�, �Ǵ� ��������� �μ���. &nbsp;&nbsp;&nbsp;<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=6')"><img src="../img/btn_bn_manual.gif" align=absmiddle></a></span></div>
<? include "../proc/warning_disk_msg.php"; # not_delete  ?>
<?=$workSkinStr?>
<table class=tb>
<col class=cellC><col class=cellL>

<tr>
	<td nowrap>�ΰ�/�����ġ ����</td>
	<td nowrap>
	<SELECT name="loccd" fld_esssential label="�ΰ�/�����ġ">
	<option value="">�� �ΰ�/�����ġ�� �����ϼ���.</option>
	<optgroup label="-- �ΰ���ġ --">
	<?
	# �ΰ��
	foreach ( $b_loccd as $lKey => $lVal ){
		if( $lKey < 90 ) continue;
	?>
	<option value="<?=$lKey?>" <?=$lKey==$data['loccd']?" selected":""?>><?=$lVal?></option>
	<?}?>
	</optgroup>

	<optgroup label="-- ������ġ --">
	<?
	# ���ʿ�
	foreach ( $b_loccd as $k => $v ){
		if( $k >= 90 ) continue;
	?>
	<option value="<?=$k?>" <?=$k==$data['loccd']?" selected":""?>><?=$v?></option>
	<?}?>
	</optgroup>
	</SELECT>
	<a href="javascript:popupLayer('../design/design_banner_loccd.php',780,600);"><img src="../img/btn_bangroup.gif" border=0 align=absmiddle></a> <font class=extext>���� ����� ����� �����ġ�� ���� ����Ҵٸ� �����ġ�� ��������</font>
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
	<?if($bannereditorchk != 'Y'){?>
	<input type="file" name="img_up" class="line">
	<a href="javascript:popup_bannereditor('<?=$data['sno']?>'); self.close();"><img src="../img/btn_editdesign.gif" border="0" alt="��ʿ�����" align="absmiddle"></a>
	<? } ?>
	<input type="hidden" name="img" value="<?=$data['img']?>"><input type="hidden" name="imgchg" value="<?=$data['imgchg']?>">
	<table>
		<? if ( $data['img'] != '' ){?>
		<tr>
			<td colspan="2">[ ���� �� �̹��� ]</td>
		</tr>
		<tr>
			<td><?=$webftp->confirmImage( "../../data/skin/" . $cfg['tplSkinWork'] . "/img/banner/" . $data['img'],300,100,"0")?></td>
			<td><? if ( $data['img'] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="img_del" value="Y">����</span><? } ?> &nbsp;<a href="javascript:webftpinfo( '<?=( $data['img'] != '' ? '/data/skin/' . $cfg['tplSkinWork'] . '/img/banner/' . $data['img'] : '' )?>' );"><img src="../img/codi/icon_imgsizeview.gif" border="0" alt="�̹��� ����" align="absmiddle"></a></td>
		</tr>
		<? } ?>
		<? if ( $data['imgchg'] != '' ){?>
		<tr>
			<td colspan="2">[ <STRONG>���� �� �̹���</STRONG> - <STRONG style="color: #ff0000;">�ݵ�� "<?=$modetext?>" ��ư�� Ŭ���ؾ� ����˴ϴ�.</STRONG> ]</td>
		</tr>
		<tr>
			<td colspan="2"><img src="<?=$imgbannereditor?>" width="<?=$imgchgsizeset[0]?>" height="<?=$imgchgsizeset[1]?>"></td>
		</tr>
		<? } ?>
	</table>
	</td>
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
