<?
	include "../_header.popup.php";

	if($_POST['action'] == 'ok') {

		require_once("../../lib/qfile.class.php");
		$qfile = new qfile();

		$cfg['cartTabUse']			= isset($_POST['cartTabUse']) ? $_POST['cartTabUse'] : 'n';
		$cfg['cartTabTpl']			= isset($_POST['cartTabTpl']) ? $_POST['cartTabTpl'] : '01';

		$cfg = array_map("stripslashes",$cfg);
		$cfg = array_map("addslashes",$cfg);

		$qfile->open( $path = dirname(__FILE__) . "/../../conf/config.php");
		$qfile->write("<?\n\n" );
		$qfile->write("\$cfg = array(\n" );

		foreach ( $cfg as $k => $v ){

			if ( $v === true ) $qfile->write("'$k'\t\t\t=> true,\n" );
			else if ( $v === false ) $qfile->write("'$k'\t\t\t=> false,\n" );
			else $qfile->write("'$k'\t\t\t=> '$v',\n" );
		}

		$qfile->write(");\n\n" );
		$qfile->write("?>" );
		$qfile->close();
		@chMod( $path, 0757 );


		echo "
		<script>
		alert('저장되었습니다');
		self.location.replace ('".$_SERVER['PHP_SELF']."');
		</script>
		";
		exit;
	}
?>
<script>


</script>
<form id="frmCartTab" action="<?=$_SERVER['PHP_SELF']?>" method="post">
<input type="hidden" name="action" value="ok">

<div class="title title_top">쇼핑카트탭 사용 설정<span> 쇼핑카트탭 사용여부를 설정합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=19')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellC">
<col class="cellL">
<tr>
	<td>쇼핑카트탭 사용 설정</td>
	<td>
		<input type="radio" name="cartTabUse" style="border:0px" <?=$cfg['cartTabUse'] == 'y' ? 'checked' : '' ?> value="y"> 사용
		<input type="radio" name="cartTabUse" style="border:0px" <?=$cfg['cartTabUse'] != 'y' ? 'checked' : '' ?> value="n"> 사용안함
	</td>
</tr>
</table>

<br>
<div class="title title_top">쇼핑카트탭 디자인타입 설정<span> 쇼핑카트탭 디자인타입 형식을 설정합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=19')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellC">
<col class="cellL">
<tr>
	<td>템플릿 디자인 선택</td>
	<td>
	<table cellpadding="5">
	<? for ($i=1;$i<=6;$i++) { ?>
	<tr><td class="noline"><input type="radio" name="cartTabTpl" value="<?=sprintf('%02s',$i)?>" <?=$cfg['cartTabTpl'] == sprintf('%02s',$i) ? 'checked' : '' ?>></td><td><img src="../img/cart_tab_preview_<?=sprintf('%02s',$i)?>.gif"></td></tr>
	<? } ?>
	</table>
	</td>
</tr>
</table>

	<div class="button">
		<input type=image src="../img/btn_register.gif">
		<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
	</div>

</form>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">쇼핑몰 하단에 '오늘본상품' '관심상품' '장바구니' 에 담긴 상품목록을 페이지 이동없이 한눈에 보여주면서 빠르게 결제로 연결해 주는 기능입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">쇼핑카트탭은 인트로 화면을 제외한 쇼핑몰 모든 화면에 노출됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">언제든 탭을 열어 페이지별로 담긴 상품목록을 확인 할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">쇼핑카트탭 사용 설정시 전체 레이아웃의 하단 부분이 탭의 닫혔을 때의 크기만큼 가려질 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">쇼핑카트탭이 닫혔을경우(탭 상단부분은 항상 보여짐)를 고려하여 '전체 레이아웃 설정 > 하단디자인' 에서 하단 여백을 조정하여 주세요.</td></tr>
</table>
</div>

<script>
cssRound('MSG01')
table_design_load();
setHeight_ifrmCodi();
</script>