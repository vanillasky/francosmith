<?
if ( $_GET['design_file'] == 'default' || substr( $_GET['design_file'], 0, 8 ) == 'outline/' ) $location = "디자인관리 > 전체레이아웃 디자인";
else if ( $_GET['design_file'] == 'main/index.htm' ) $location = "디자인관리 > 메인페이지 디자인";
else $location = "디자인관리 > 기타페이지 디자인";

$scriptLoad='<script src="../design/codi/_codi.js"></script>';
include "../_header.popup.php";

## 개인정보취급약관 제어
if($_GET[design_file] == "service/_private.txt" && !file_exists("../../data/skin/".$cfg['tplSkinWork']."/service/_private.txt")) $_GET[design_file] = "service/private.htm";
?>

<? if ( $_GET['design_file'] == 'default' || $_GET['design_file'] == 'main/index.htm' || substr( $_GET['design_file'], 0, 8 ) == 'outline/' ){ ?>
	<? if ( $_GET['design_file'] == 'default' || substr( $_GET['design_file'], 0, 8 ) == 'outline/' ){ ?>
	<div class="title title_top">전체레이아웃 설정<span>내 쇼핑몰의 전체레이아웃을 설정합니다</span></div>
	<? } else if ( $_GET['design_file'] == 'main/index.htm' ){ ?>
	<div class="title title_top">메인페이지 디자인<span>메인페이지 디자인을 수정합니다</span>  <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=9')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
	<? } ?>
<? } else { ?>
<div class="title title_top">서브페이지 디자인<span>서브페이지들의 디자인을 수정합니다</span>  <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=10')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<? } ?>
<?=$workSkinStr?>

<?php
	// 약관/개인정보 설정 안내
	$termsTxtFileArray = array(
		'service/_private.txt'		, 
		'service/_private1.txt'		, 
		'service/_private2.txt'		, 
		'service/_private3.txt'		, 
		'service/_private_non.txt'	,
		'proc/_agreement.txt'
	);
	
	if(in_array($_GET[design_file], $termsTxtFileArray)){
?>
	<table cellpadding="0" cellspacing="0" border="0" style="border: 2px solid #dddddd;" width="100%">
	<tr>
		<td style="font-size:15px; padding: 5px 5px 15px 5px; color: #0080FF; font-weight: bold;">* 새로운 이용약관, 개인정보취급방침 등 설정 기능 안내</td>
	</tr>
	<tr>
		<td style="padding: 5px 5px 5px 5px;">이용약관, 개인정보취급방침 등 쇼핑몰 운영 정책에 관련된 안내 사항을 간편하게 등록할 수 있는 기능이 배포되었습니다. 현재와 같이 디자인관리의 각 페이지에 HTML 형태로 내용을 입력하여 사용하여도 무방하지만 새로운 기능을 이용하면 이후 스킨 교체와 관계없이 입력된 내용을 그대로 사용할 수 있으므로 가급적 새로운 기능을 이용해 주시기 바랍니다. <a href="javascript:;" onclick="javascript:parent.document.location.href='../basic/terms.php';" style="color: #0080FF;"><u>[기본설정 > 약관/개인정보 설정 바로가기]</u></a>
		</td>
	</tr>
	<tr>
		<td style="padding: 5px 5px 5px 5px;">※ <span style="color: red; font-weight: bold;">2014년 07월 31일 이전 제작 무료 스킨</span>을 사용하시는 경우 <span style="font-weight: bold; text-decoration: underline;">반드시 스킨패치</span>를 적용해야 기능 사용이 가능합니다. <a href="http://www.godo.co.kr/customer_center/patch.php?sno=2064" target="_blank" style="color: #0080FF;"><u>[패치 바로가기]</u></a></td>
	</tr>
	</table>
<?php
	}
?>

<?
	// 레이아웃 형태 알림 이미지
	$todayshop = Core::loader('todayshop');
	if ($todayshop->cfg['shopMode'] == "todayshop") {
?>
	<img src="../img/todayshop/bn_ly01.gif" style="margin-top:5px; margin-bottom:10px;" />
<?
	} //
{ // Design Codi 메인
	@include_once dirname(__FILE__) . "/codi/main.php";
}
?>

<script>
table_design_load();
setHeight_ifrmCodi();
</script>
