<?php
$location = '기본관리 > 이용/탈퇴안내 설정';
include '../_header.php';

$guideFilePath = dirname(__FILE__) . '/../../conf/guide/';
$guideTitleArr		= array(1 => '이용안내',			2 => '탈퇴안내'			);
$guideIncludeArr	= array(1 => '_guide.operate.php',	2 => '_guide.secede.php');
$guideButtons		= "
	<input type='image' src='../img/btn_register.gif' />
	<a href='javascript:history.back();'><img src='../img/btn_cancel.gif' /></a>
";
if(get_magic_quotes_gpc()) $guide = array_map('stripslashes', $guide);
?>
<style>
a:hover										{ font-weight: bold; color:#2E64FE; text-decoration:underline;}
textarea									{ width: 1000px; height: 300px; padding: 20px 0 0 20px;}
.guideTable									{ width: 360px; height: 50px; }
.guideTable .guideTableSmall				{ width: 180px; height: 50px; }
.guideTableBorder							{ border: 1px #D8D8D8 solid; }
.guideBorderBottomZero						{ border-bottom-width: 0px; }
.guideBorderRightZero						{ border-right-width: 0px; }
.guideTrHeight								{ height: 30px; }
.guidePadding								{ padding: 5px 0 5px 5px; }
.guidePaddingLeft							{ padding-left: 10px; }
.guideFontColorRed							{ color: #FF0000; }
.guideFontColorSky							{ color: #0080FF; }
.guideBgColorSGray							{ background-color: #A4A4A4; }
.guideBgColorGray							{ background-color: #F2F2F2; }
.guideBgColorWhite							{ background-color: #FFFFFF; }
.guideFontWeightBold						{ font-weight: bold; }
.guideTextUnderline							{ text-decoration:underline; }
</style>

<form name="form" method="post" action="indb.php" target="ifrmHidden">
<input type="hidden" name="mode" value="guide" />

<div class="title title_top">이용/탈퇴안내 설정<span>쇼핑몰의 이용안내 페이지에 표시됩니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>


<table cellpadding="0" cellspacing="0" class="guideTable center" border="0">
<tr>
	<?php 
	$subClass = 'guideBorderRightZero';
	for($i=1; $i<=2; $i++){
		if($i == 2) $subClass = '';
	?>
	<td class="guideBorderBottomZero guideTableBorder <?php echo $subClass; ?>">
		<table cellpadding="0" cellspacing="0" border="0" class="guideTableSmall hand" id="guideTab<?php echo $i; ?>" onclick="javascript:guideTab('<?php echo $i; ?>');">
		<tr>
			<td class="center"><?php echo $guideTitleArr[$i]; ?></td>
			<td>▼</td>
		</tr>
		</table>
	</td>
	<?php } ?>
</tr>
</table>

<?php for($i=1; $i<=2; $i++){ ?>
<div id="guideForm<?php echo $i; ?>">
	<?php include $guideIncludeArr[$i]; ?>
</div>
<?php } ?>

</form>

<script>
function guideTab(idx)
{
	var guideForm;
	var guideTab;

	for(var i=1; i<=2; i++){
		guideForm	= document.getElementById('guideForm' + i);
		guideTab	= document.getElementById('guideTab' + i);
		
		if(idx == i) {
			guideForm.style.display			= '';
			guideTab.style.backgroundColor	= '#FFFFFF';
		} else {
			guideForm.style.display			= 'none';
			guideTab.style.backgroundColor	= '#F2F2F2';
		}
	}
}
guideTab('1');
cssRound('MSG01');
cssRound('MSG02');
</script>
<?php include '../_footer.php'; ?>