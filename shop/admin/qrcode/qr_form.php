<?
$location = "QR 코드 관리 > QR 코드 설정";
include "../_header.php";

$strPath = "../../conf/qr.cfg.php";
if(file_exists($strPath)) {
	require $strPath;
}

if(!$qrCfg['useGoods'])$qrCfg['useGoods']='n';
if(!$qrCfg['useEvent'])$qrCfg['useEvent']='n';
if(!$qrCfg['useLogo'])$qrCfg['useLogo']='n';

if(!$qrCfg['logoImg'])$qrCfg['logoImg']='';
if(!$qrCfg['degree'])$qrCfg['degree']='';
if(!$qrCfg['logoLocation'])$qrCfg['logoLocation']='';

$checked['useGoods'][$qrCfg['useGoods']] = "checked";
$checked['useEvent'][$qrCfg['useEvent']] = "checked";
$checked['useLogo'][$qrCfg['useLogo']] = "checked";
$checked['qr_style'][$qrCfg['qr_style']] = "checked";

$checked['logoLocation'][$qrCfg['logoLocation']] = "checked";

$logoPath = "../../data/skin/".$cfg['tplSkin']."/img/".$qrCfg['logoImg'];

$qrCfg['degree'] = number_format($qrCfg['degree']);
?>
<style type="text/css">
img {border:none;}
</style>
<script type="text/javascript">
</script>
<div style="width:800">
<form method="post" action="incfg.php" onsubmit="return chkForm(this)"  enctype="multipart/form-data"/>
<input type=hidden name=returnUrl value="qr_form.php">
<div class="title title_top">QR 코드 설정/관리 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=15')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">

<tr height="30">
	<td>상품 정보 QR 사용</td>
	<td class="noline">
	<label><input type="radio" name="useGoods" value="y"  <?php echo $checked['useGoods']['y'];?>/>사용</label><label><input type="radio" name="useGoods" value="n" <?php echo $checked['useGoods']['n'];?> />사용안함</label>
	<div style="padding-top:5;" class="small1 extext">
	<div>상품 보기 화면에 상품 주소 정보를 담은 qr코드가 보여집니다.</div>
	</div>
	</td>
</tr>

<tr height="30">
	<td>이벤트 QR 사용</td>
	<td class="noline">
	<label><input type="radio" name="useEvent" value="y"  <?php echo $checked['useEvent']['y'];?> />사용</label><label><input type="radio" name="useEvent" value="n" <?php echo $checked['useEvent']['n'];?>  onclick="logoChk();"/>사용안함</label>
	<div style="padding-top:5;" class="small1 extext">
	<div>이벤트 페이지에 이벤트 주소 정보를 담은 qr코드가 삽입 됩니다.</div>
	</div>
	</td>
</tr>
<tr height="30">
	<td>QR 노출 형태</td>
	<td class="noline">
	<label><input type="radio" name="qr_style" value=""  <?php echo $checked['qr_style'][''];?> />QR이미지</label><label><input type="radio" name="qr_style" value="btn" <?php echo $checked['qr_style']['btn'];?>  onclick="logoChk();"/>QR이미지 + 저장 버튼</label>
	</td>
</tr>
<tr>
	<td>로고 이미지 사용</td>
	<td class="noline">
	<label><input type="radio" name="useLogo" value="y"  <?php echo $checked['useLogo']['y'];?>/>사용</label><label><input type="radio" name="useLogo" value="n" <?php echo $checked['useLogo']['n'];?> />사용안함</label>
	</td>
</tr>
<tr>
	<td>로고 이미지 등록</td>
	<td class="noline">
	<div style="padding:2 0 0 0">
	<? if(!empty($qrCfg['logoImg'])){ ?>
	<img src="<?=$logoPath?>" border="0" align ="absbottom"/>
	<? } ?>
	<input type="file" name="logoImg"/> <span class="small1 extext">(권장사이즈 100 x 20)</span>
	</div>
	</td>
</tr>
<tr>
	<td>로고 위치</td>
	<td class="noline">
	<div style="padding:0 0 2 0">
	<label><input type="radio" name="logoLocation" value="top" <?php echo $checked['logoLocation']['top'];?>>상
	<input type="radio" name="logoLocation" value="bottom" <?php echo $checked['logoLocation']['bottom'];?>>하
	<input type="radio" name="logoLocation" value="left" <?php echo $checked['logoLocation']['left'];?>>좌
	<input type="radio" name="logoLocation" value="right" <?php echo $checked['logoLocation']['right'];?>>우</label>
	</div>
	</td>
</tr>
<tr>
	<td>로고 투명도</td>
	<td>
	<input type="text" name="degree" size="3" value="<?=$qrCfg['degree']?>"/> % (0에 가까울 수록 투명합니다.)
	</td>
</tr>
</table>
<div class=button>
<input type="image" src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
<p/>
</div>
</form>
</div>
<div style="padding-top:10px"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>*궁금증 해결

 <tr><td>① 상품정보 QR사용: <br>
 - 사용함으로 설정할 경우 상품 상세 페이지에 QR코드를 노출할 수 있는 기능이 설정됩니다.<br>
                           - 상품 등록페이지에서  ‘QR Code노출’기능이 추가 되어 각 상품별 노출 여부를 설정할 수 있습니다.</td></tr>
 <tr><td>② 이벤트 QR사용 :  이벤트페이지에 적용하여 활용할 수 있습니다.사용함으로 설정시 이벤트 등록페이지에서 사용설정을 할 수 있습니다.</td></tr>

 <tr><td>③ 로고이미지사용 : 등록한 QR코드에 쇼핑몰 로고를 추가 할 수 있습니다. 상품페이지와 이벤트 페이지에 적용되는 QR코드에 삽입됩니다.</td></tr>

 <tr><td>④ 로고이미지 등록 : QR코드에 삽입할 이미지를 등록합니다. gif이미지 100*20 사이즈 등록을 권장합니다.</td></tr>

<tr><td>⑤ 로고위치 : 등록한 로고의 삽입 위치를 선택합니다.</td></tr>

<tr><td>⑥ 로고투명도 : 등록한 이미지의 투명도를 조절하여 노출할 수 있습니다. </td></tr>

</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<? include "../_footer.php"; ?>