<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: 로고/배너 종류 관리
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/
include "../_header.popup.php";


if ( !$_GET['mode'] ) $_GET['mode'] = "modify_loccd";

switch ( $_GET['mode'] ){
	case "modify_loccd":
		if ( file_exists( $tmp = dirname(__FILE__) . "/../../conf/config.banner_".$cfg['tplSkinWork'].".php" ) ) @include $tmp;
		else @include dirname(__FILE__) . "/../../conf/config.banner.php";

		if(!$b_loccd['90']) $b_loccd['90']	= "메인로고";
		if(!$b_loccd['91']) $b_loccd['91']	= "하단로고";
		if(!$b_loccd['92']) $b_loccd['92']	= "메일로고";
		if(!$b_loccd['93']) $b_loccd['93']	= "로고위치입력";
		if(!$b_loccd['94']) $b_loccd['94']	= "로고위치입력";
		if(!$b_loccd['95']) $b_loccd['95']	= "로고위치입력";

		break;
}

$tmps = array_fill(1, 200, 0);
for ( $i = 90; $i <= 95; $i++ ){
	unset($tmps[$i]);
}
$nums = array_keys($tmps);
$half = count($nums) / 2;
?>


<form name="fm" method=post action="design_banner_indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET['mode']?>">

<div class="title title_top">로고/배너위치관리<span>등록할 로고/배너들에 맞도록 미리 로고/배너위치를 입력해놓으세요. 예) 메인상단큰배너, 메인중앙배너1, 고객센터배너4 -> 요렇게 넣으세요. </span><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<?=$workSkinStr?>
<table class=tb style="float:left;">
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<? for ( $i = 90; $i <= 92; $i++ ){ $j = $i + 3; ?>
<tr>
	<td>로고코드 <?=$i?></td>
	<td><input type=text name="loccd[<?=$i?>]" value="<?=( $b_loccd[$i] ? $b_loccd[$i] : '로고위치입력' );?>" class="line" style="width:200;"></td>
	<td>로고코드 <?=$j?></td>
	<td><input type=text name="loccd[<?=$j?>]" value="<?=( $b_loccd[$j] ? $b_loccd[$j] : '로고위치입력' );?>" class="line" style="width:200;"></td>
</tr>
<? } ?>
<? for ( $k = 0; $k < $half; $k++ ){ $i = $nums[$k]; $j = $nums[$k + $half]; ?>
<tr>
	<td>배너코드 <?=$i?></td>
	<td><input type=text name="loccd[<?=$i?>]" value="<?=( $b_loccd[$i] ? $b_loccd[$i] : '배너위치입력' );?>" class="line" style="width:200;"></td>
	<td>배너코드 <?=$j?></td>
	<td><input type=text name="loccd[<?=$j?>]" value="<?=( $b_loccd[$j] ? $b_loccd[$j] : '배너위치입력' );?>" class="line" style="width:200;"></td>
</tr>
<? } ?>
</table>

<div style="padding:20px" align=center class=noline>
<input type=image src="../img/btn_register.gif">
</div>

</form>


<script>
table_design_load();
</script>