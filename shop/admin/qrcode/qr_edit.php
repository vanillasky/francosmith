<?
$location = "QR Code 관리 > QR Code 만들기";
include "../_header.php";
require_once("../../lib/qrcode.class.php");

$strPath = "../../conf/qr.cfg.php";
if(file_exists($strPath)) {
	require $strPath;
}

$sno = $_REQUEST['sno'];
$qr_type = $_REQUEST['qr_type'];
if($qr_type == "")$qr_type= 'etc';

if(!empty($sno)){
	### qrcode
	$qrdata = $db->fetch("select * from ".GD_QRCODE." where qr_type='etc' and sno=$sno");
}

if(!$qrdata['qr_size'])$qrdata['qr_size']='3';
if(!$qrdata['qr_version'])$qrdata['qr_version']='5';
if(!$qrdata['useLogo']){
	$qrdata['useLogo']=$qrCfg['useLogo'];
}

if(substr($qrdata['qr_string'],0,6) == "MECARD"){
	$qrdata['useType']	= "mcard";
	$divURLVew			= "style='display:none'";
	$divMcardVew		= "style='display:block'";
	if(empty($sno)){
		$qrdata['qr_size']		= "2";
		$qrdata['qr_version'] = "16";
	}
}else if(substr($qrdata['qr_string'],0,4) == "http"){
	$divURLVew			= "style='display:block'";
	$divMcardVew		= "style='display:none'";
	$qrdata['useType'] = "url";
}else{
	$divURLVew			= "style='display:block'";
	$divMcardVew		= "style='display:none'";
	$qrdata['useType'] = "url";
}
$checked['qr_size'][$qrdata['qr_size']] = "selected";
$checked['qr_version'][$qrdata['qr_version']] = "selected";
$checked['useLogo'][$qrdata['useLogo']] = "checked";
$checked['useType'][$qrdata['useType']] = "checked";
$checked['logoLocation'][$qrCfg['logoLocation']] = "checked";

$logoPath = "../../data/skin/".$cfg['tplSkin']."/img/".$qrCfg['logoImg'];

### qrcode
$qrcount = $db->fetch("select count(*) from ".GD_QRCODE." where qr_type='goods' and contsNo='$goodsno'");

if($qrCfg['useGoods']=='y' && $qrcount[0]>0){
	require "../../lib/qrcode.class.php";
	$QRCode = Core::loader('QRCode');
	$qrdata['qrcode'] = $QRCode->get_GoodsViewTag($goodsno, "etc_view");
}else{
	$qrdata['qrcode'] = null;
}

//미리보기/다운로드용 데이터
$qrFullData = "d=".$qrdata['qr_string']."&o=".$qrdata['qr_string']."&s=".$qrdata['qr_size']."&v=".$qrdata['qr_version'];

if(substr($qrdata['qr_string'],0,6) == "MECARD"){
	$qrdata['qr_string'] = str_replace("MECARD:","",$qrdata['qr_string']);
	$arr = explode(';' , $qrdata['qr_string']); // . 를 구분자로하여 문자열을 분리, 배열로 리턴,,,
	$no = sizeof($arr);
	 
	for ($i=0 ; $i<$no ; $i++) {
		$var = strpos($arr[$i], ":");
		$var_str1 = substr($arr[$i], "0",$var);
		$var_str2 = substr($arr[$i], "0",$var+1);
		$var_rst = str_replace($var_str2 ,"",$arr[$i]);
		$qrdata[$var_str1] = $var_rst;
	}
}


?>
<style type="text/css">
img {border:none;}
</style>
<script type="text/javascript">
	function func_preview(){
		

		var frm = document.frm1;
		var tempTarget = frm.target;

		if(frm.qr_name.value == ""){
			alert("코드 이름이 없습니다.");
			frm.qr_name.focus();
			return;
		}

		if(frm.useType[1].checked == true){		
			if(frm.N.value == ""){
				alert("이름을 입력하여 주십시오.");
				frm.N.focus();
				return;
			}else if(frm.TEL.value == ""){
				alert("전화번호를 입력하여 주십시오.");
				frm.TEL.focus();
				return;
			}else{
				frm.contents.value =  "MECARD:N:" + frm.N.value +";TEL:" + frm.TEL.value +";EMAIL:" + frm.EMAIL.value  + ";URL:" + frm.URL.value+";ADR:" + frm.ADR.value;
			}
		}else{
			if(frm.tmp_contents.value == ""){
				alert("내용을 입력하여 주십시오.");
				return;
			}
			frm.contents.value =  frm.tmp_contents.value;
		}	

		document.getElementById("act1frame").style.display = "block";
		frm.d.value = encodeURI(frm.contents.value);
		frm.o.value = encodeURI(frm.contents.value);
		frm.s.value = frm.qr_size.value;
		frm.target  = "act1";
		frm.action = "../../lib/qrcodeImgMaker.php";
		frm.submit();

		frm.target = tempTarget;

	}

	function qr_save(){
		var frm = document.frm1;

		if(frm.qr_name.value == ""){
			alert("코드 이름이 없습니다.");
			frm.qr_name.focus();
			return;
		}

		if(frm.useType[1].checked == true){		
			if(frm.N.value == ""){
				alert("이름을 입력하여 주십시오.");
				frm.N.focus();
				return;
			}else if(frm.TEL.value == ""){
				alert("전화번호를 입력하여 주십시오.");
				frm.TEL.focus();
				return;
			}else{
				frm.contents.value =  "MECARD:N:" + frm.N.value +";TEL:" + frm.TEL.value +";EMAIL:" + frm.EMAIL.value + ";URL:" + frm.URL.value+";ADR:" + frm.ADR.value;
			}
		}else{
			if(frm.tmp_contents.value == ""){
				alert("내용을 입력하여 주십시오.");
				frm.tmp_contents.focus();
				return;
			}
			frm.contents.value =  frm.tmp_contents.value;
		}

		frm.action = "indb.php";
		frm.submit();
	}

	function func_useType(val){
		var frm = document.frm1;
		document.getElementById("act1frame").style.display = "none";
		all_clear();

		if(frm.useType[1].checked == true){
			document.getElementById("divURL").style.display = "none";
			document.getElementById("divMcard").style.display = "block";
			frm.qr_version.value = "12";
		}else{
			document.getElementById("divURL").style.display = "block";
			document.getElementById("divMcard").style.display = "none";
			frm.qr_version.value = "5";			
		}
	}
	
	function all_clear(){
		var frm = document.frm1;

		frm.d.value = "";
		frm.o.value = "";

		frm.qr_name.value = "";
		frm.contents.value = "";
		frm.tmp_contents.value = "";
		frm.N.value = "";
		frm.TEL.value = "";
		frm.EMAIL.value = "";
		frm.URL.value = "";
		frm.ADR.value = "";
	}

</script>

<div style="width:800">
<form name="frm1" method="post"/>
<input type=hidden name=returnUrl value="qr_edit.php">
<input type="hidden" name="qr_type" value="etc">
<input type="hidden" name="contents">
<input type="hidden" name="sno" value="<?=$sno?>">
<input type="hidden" name="d" value="<?=$edata?>">
<input type="hidden" name="s" value="">
<input type="hidden" name="e" value="M">
<input type="hidden" name="v" value=''>
<input type="hidden" name="n" value=''>
<input type="hidden" name="m" value=''>
<input type="hidden" name="p" value=''>
<input type="hidden" name="o" value="<?=$edata?>">
<input type="hidden" name="useLogo" value="<?=$useLogo?>">
<input type="hidden" name="degree" value='<?=$qrCfg['degree']?>'>
<input type="hidden" name="logoImg" value='<?=$qrCfg['logoImg']?>'>
<input type="hidden" name="logoLocation" value='<?=$qrCfg['logoLocation']?>'>
<div class="title title_top">QR 코드 만들기(URL) <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=15')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div>
		<label class="noline"><input type="radio" name="useType" value="url"  <?php echo $checked['useType']['url'];?> onclick="func_useType('url')"/>URL/TEXT</label>
		<label class="noline"><input type="radio" name="useType" value="mcard" <?php echo $checked['useType']['mcard'];?>  onclick="func_useType('mcard')"/>명함(연락처)</label>
		<label class="noline"><img src='/shop/admin/img/btn_freeview.gif' style='vertical-align:middle' onclick="func_preview()" style="cursor:hand"></label>
		<? if(!empty($sno)){ ?>
			<label class="noline">
			<?
				$QRCode = new QRCode();
				echo  $QRCode->get_GoodsViewTag($sno, "etc_down", $qrFullData);
			?>
			</label>
			<label class="noline"><font class="extext">서버에 저장되어 있는 코드가 다운로드됩니다.</font></label>
		<? } ?>
</div>
<div>
	<table border="0" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
	<tr>
	<td>	
		<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
		<col class="cellC"><col class="cellL">
		<tr>
			<td>QR 코드 이름</td>
			<td>
			<input type="text" name="qr_name" value="<?=$qrdata['qr_name']?>"/>
			</td>
		</tr>
		<tr>
			<td>로고 이미지 사용</td>
			<td class="noline">
			<label><input type="radio" name="useLogo" value="y"  <?php echo $checked['useLogo']['y'];?>/>사용</label><label><input type="radio" name="useLogo" value="n" <?php echo $checked['useLogo']['n'];?> />사용안함</label>
			</td>
		</tr>
		<tr>
			<td>QR 코드 크기</td>
			<td>
			<select name="qr_size">
			<? for($i=1;$i<9;$i++){ ?>
				<option value="<?=$i?>" <?php echo $checked['qr_size'][$i];?>/><?=$i?>
			<? } ?>
			</select>&nbsp;<font class="extext">1 (90pix) ~ 8 (405pix) : 1레벨 당 45pix 증가<font class="extext">
			</td>
		</tr>
		<tr>
			<td>QR 코드 정밀도</td>
			<td>
			<select name="qr_version">
			<? for($i=1;$i<13;$i++){ ?>
				<option value="<?=$i?>" <?php echo $checked['qr_version'][$i];?>/><?=$i?>
			<? } ?>
			</select>&nbsp;<font class="extext">내용이 많을 경우 정밀도를 올려주세요.(코드가 커질수도 있습니다.)</font>
			</td>
		</tr>
		</table>

		<div style="height:10"></div>

		<div id="divURL" <?=$divURLVew?>>
		<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
		<col class="cellC"><col class="cellL">
		<tr>
			<td>내용(URL)</td>
			<td><textarea cols='60' rows='10' name="tmp_contents"><?=$qrdata['qr_string']?></textarea>
			<div id="textHelf" <?=$divtextHelf?>><font class="extext">정밀도 12기준 최대 285byte 입력가능(한글95자 영문/숫자 285자)</font></div></td>
		</tr>
		</table>
		</div>

		<div id="divMcard" <?=$divMcardVew?>>
			<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse;height:280" width="100%">
			<col class="cellC"><col class="cellL">
			<tr>
				<td>이름</td>
				<td>
				<input type="text" name="N" value="<?=$qrdata['N']?>"/> ex) 대표 홍길동(고도몰)</td>
			</tr>
			<tr>
				<td>전화번호</td>
				<td>
				<input type="text" name="TEL" value="<?=$qrdata['TEL']?>"/>
				</td>
			</tr>
			<tr>
				<td>이메일</td>
				<td>
				<input type="text" name="EMAIL" value="<?=$qrdata['EMAIL']?>" style="width:250px"/>
				</td>
			</tr>
			<tr>
				<td>홈페이지</td>
				<td>
				<input type="text" name="URL" value="<?=$qrdata['URL']?>" style="width:250px"/>
				</td>
			</tr>
			<tr>
				<td>주소</td>
				<td>
				<input type="text" name="ADR" value="<?=$qrdata['ADR']?>" style="width:250px"/>
				</td>
			</tr>
			</table>
			</div>
	</td>
	<td>		
		<div id="act1frame" style="display:none"><iframe name="act1" id="act1" marginheight='0' marginwidth='0' frameBorder='0'  height='100%' scrolling='yes' allowTransparency='true' align="center"></iframe><div>
	</td>
	</tr>
	</table>
<div>
<div class=button>
<a href="javascript:qr_save()"><img src="../img/btn_save.gif"></a>
<a href="qr_list.php?page=<?=$_GET[page] ?>"><img src="../img/btn_cancel.gif"></a>
</div>
<p/>
</div>
</form>
</div>
<div id=MSG01>


<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr><td>상품페이지와 이벤트 페이지 외에 영역에 사용할 QR코드를 신규로 생성할 수 있습니다.</td></tr>
<tr><td>1)URL: 스마튼 폰으로 QR코드 인식시 웹페이지로 연결됩니다.</td></tr>
<tr><td>2)TEXT: 입력한 텍스트 내용이 스마트 폰에 보여지며, 최대 450byte까지 입력 가능합니다.</td></tr>
<tr><td>3) 명함(연락처): 등록한 연락처 정보 내용이 스마트 폰에 보여집니다.</td></tr>

<tr><td>-QR Code이름: 생성한 QR코드의 이름을 등록합니다.</td></tr>
<tr><td>-로고이미지: 쇼핑몰의 로고 이미지 삽입여부를 설정합니다.</td></tr>
<tr><td>-QR Code크기: 생성하는 코드의 사이즈이며, 활용도에 따라 크기를 조절할 수 있습니다.</td></tr>
<tr><td>-QR Code정밀도:  생성한 코드의 정밀도를 조절할 수 있습니다. </td></tr>
<tr><td> 상품 url뿐만 아니라 컨텐츠 내용도 등록할 수 있기 때문에 데이터가 많을 경우 크기를 변경해야 합니다. </td></tr>
<tr><td>일반적인 사이트 url정보의 권장 사이즈는 5입니다.</td></tr>
<tr><td>-내용(URL): QR코드에 연결될 링크 정보 또는 텍스트 등의 정보를 등록합니다. </td></tr>

</table>
</div>
<script>cssRound('MSG01')</script>

<SCRIPT LANGUAGE="JavaScript">
<!--
<?if($qrdata['qr_string']!=""){?>
		func_preview();
<?}?>
document.getElementById("act1").style.height='100%' ;
//-->
</SCRIPT>

<? include "../_footer.php"; ?>
