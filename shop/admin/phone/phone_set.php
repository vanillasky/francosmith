<?
$location = "이나무폰 설정 > 이나무폰 신청 및 설정";
include "../_header.php";

@include "../../conf/phone.php";
$mode = "setting";
$set = $set['phone'];


if($set['pc080_id']) $checked['register']['1'] = "checked";
else $checked['register']['0'] = "checked";

$settingyn = 0;
if($set['user_id'] && $set['coop_id'] && $set['pc080_id'])$settingyn = 1;

if(!$settingyn){
	msg("이나무폰 서비스는 종료되었습니다.","phone_guide.php");
}
?>
<script>
function chkdown(){
	var f = document.forms[0];
	if( f.settingyn.value != '1' ){
		alert('바르게 신청되지 않았습니다.!');
		return;
	}
	popup("../../partner/pc080/download.php?mode=1",500,200);
}
function chkregister(){
	document.getElementById('layerid0').style.display =	document.getElementById('layerid1').style.display = 'none';
	if( document.getElementsByName('mode')[0].checked == true ){
		document.getElementById('layerid0').style.display = 'block';
		document.getElementById('download_id').style.display = 'none';
	}
	if( document.getElementsByName('mode')[1].checked == true ){
		document.getElementById('layerid1').style.display = 'block';
		document.getElementById('download_id').style.display = 'block';
	}
}
</script>
<div class="title title_top">이나무폰 신청 및 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=28')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div style="font-weight:bold">1. 이나무폰 서비스 신청 하거나 설정값이 올바르지 않아 서비스가 정상운영되지 않을시 설정값을 조정하십시요!</div>
<div style="font:0;height:5"></div>
<form method="post" action="indb.php" onsubmit="return chkForm(this)">
<input type="hidden" name="settingyn" value="<?=$settingyn?>">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>신청여부</td>
	<td>
	<input type="radio" value="register" name="mode" class="null" <?=$checked['register']['0']?> onclick="chkregister()" <?if($settingyn){?>disabled<?}?> />서비스 등록 신청 전 이거나 새로 신청합니다.
	<input type="radio" value="setting" name="mode" class="null" <?=$checked['register']['1']?> onclick="chkregister()" <?if(!$settingyn){?>disabled<?}?> />서비스 등록 완료 후 설정합니다.
	</td>
</tr>
<tr>
	<td><div id="layerid0" style="display:none">이메일</div><div id="layerid1" style="display:none">이나무폰 아이디</div></td>
	<td>
	<input type="text" name="email" value="<?=$set['email']?>" class="line" size="40" required>
	<div class=small><font class=extext>(100자 이내 입력) 수신 불가한 E메일주소 사용 시, 이나무폰(PC080) 이용이 중지 될 수 있습니다. </font></div>
	</td>
</tr>
<tr>
	<td>이름</td>
	<td>
	<input type="text" name="user_name" value="<?=$set['user_name']?>" class="line" size="40" required>
	</td>
</tr>
<tr>
	<td>전화번호</td>
	<td>
	<input type="text" name="tel" value="<?=$set['user_tel']?>" class="line" label="전화번호" required>
	<span class=small><font class=extext>(-없이 숫자로만 입력)</font></span>
	</td>
</tr>
<tr>
	<td>비밀번호</td>
	<td>
	<input type="password" name="pwd" value="<?=$set['pwd']?>" class="line" size='12' maxlength='12' label="비밀번호" required>
	<span class=small><font class=extext>(4~12자 입력)</font></span>
	</td>
</tr>
</table>
<div style="font:0;height:5"></div>
<div style="background-color:#f6f6f6;text-align:center;height:30px;padding-top:7px;color:red;">2011년 11월 30일 이후 서비스 신규 가입이 종료됩니다. <a href="phone_guide.php"><font color="#0000ff" style="font-weight:bold;">자세히 보기</font></a></div>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>
<div style="font:0;height:5"></div>
<div align="center"><input type="image" src="../img/btn_register.gif" />
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a></div>
</form>

<div id="download_id" style="display:none">
<p />
<div style="font-weight:bold">2. 위의 설정 값이 채워졌을 경우 아래의 다운로드 버튼을 클릭하여 메신저 폰을 다운로드 받고 설치합니다. </div>
<div style="font:0;height:5"></div>
<table width="100%" border="0" cellpadding="10" cellspacing="0" style="border:1px #dddddd solid;">
<tr>
	<td align="center" bgcolor="#f6f6f6" style="font:16pt tahoma;"><img src="../img/icon_down.gif" border="0" align="absmiddle"><b>download</b></td>
</tr>
<tr>
	<td align="center"><a href="javascript:chkdown();">다운로드</a></td>
</tr>
</table>
<p />
<div style="font-weight:bold">3. 쇼핑몰에 이나무폰메신저 전화하기버튼(베너)을 생성해줍니다. </div>
<div style="font:0;height:5"></div>
<table width="100%" border="0" cellpadding="5" cellspacing="0" style="border:1px #dddddd solid;">
<tr>
	<td align="left" style="padding:10 10 10 10">
	<div>디자인 관리에서 이나무폰 아이콘을 추가하고 싶으신 곳에 <b>아래의 '출력 아이콘 설정'에 설정되어있는 아이콘치환코드</b>를 입력합니다. <font class=extext>- 예) { =dataIconPhone(0) }</font></div>
	<table class="null" cellPadding="2" border="1" borderColor="#e6e6e6">
	<col class=cellC align="center"><col style="padding-left:5" align="center"><col style="padding-left:5" align="center"><col style="padding-left:5" align="center">
	<tr>
		<td>ON아이콘</td>
		<td><img src="<?=$cfg[rootDir]?>/data/skin/<?=$cfg[tplSkin]?>/img/banner/banner_phone.gif"><div>banner_phone.gif</div></td>
		<td><img src="<?=$cfg[rootDir]?>/data/skin/<?=$cfg[tplSkin]?>/img/banner/banner_phone1.gif"><div>banner_phone1.gif</div></td>
		<td><img src="<?=$cfg[rootDir]?>/data/skin/<?=$cfg[tplSkin]?>/img/banner/banner_phone2.gif"><div>banner_phone2.gif</div></td>
	</tr>
	<tr>
		<td>OFF아이콘</td>
		<td><img src="<?=$cfg[rootDir]?>/data/skin/<?=$cfg[tplSkin]?>/img/banner/banner_phone_off.gif"><div>banner_phone_off.gif</div></td>
		<td><img src="<?=$cfg[rootDir]?>/data/skin/<?=$cfg[tplSkin]?>/img/banner/banner_phone1_off.gif"><div>banner_phone1_off.gif</div></td>
		<td><img src="<?=$cfg[rootDir]?>/data/skin/<?=$cfg[tplSkin]?>/img/banner/banner_phone2_off.gif"><div>banner_phone2_off.gif</div></td>
	</tr>
	<tr>
		<td>치환코드</td>
		<td>{ =dataIconPhone(0) }</td>
		<td>{ =dataIconPhone(1) }</td>
		<td>{ =dataIconPhone(2) }</td>
	</tr>

	</table>
	<div><b>※이미지 변경은 사용중이신 스킨의 img/banner/폴더에서 해당파일을 찾아 수정하실수 있습니다.</div>
	</td>
</tr>
</table>
</div>
<script>chkregister();</script>
<? include "../_footer.php"; ?>
