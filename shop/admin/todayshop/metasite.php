<?
$location = "투데이샵 > 소셜메타사이트 설정 ";
@include "../_header.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}

$tsCfg = $todayShop->cfg;
$tsCfg['metasite'] = unserialize( $tsCfg['metasite'] );

$social_meta = &load_class('social_meta','social_meta');


?>
<div class="title title_top">소셜 메타사이트 설정<span>제휴된 소셜 메타사이트의 EP(Engine Page)를 확인 할 수 있습니다</span></div>
<table border=4 bordercolor=#dce1e1 style="border-collapse:collapse" width=750>
<tr><td style="padding:7 10 10 10">
<div style="padding-top:5"><b>※ 메타사이트 연동시 유의사항.</b></div>
<div style="padding-top:7"><font class=g9 color=666666>① 연동방법</font></a></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>상점에서 판매하고 있는 상품을 메타사이트에 노출하기 위해서는 아래에 제공되는 메타사이트 중 원하는 사이트와 제휴를 진행하셔야 합니다.</font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>입점이 완료된 후에 상품정보 연동을 하실 수 있습니다.</font></div>

<div style="padding-top:5"><font class=g9 color=666666>② 상품 연동시 체크사항</div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>상품 로고 이미지가 필수 값으로 성정되어 있는 업체가 있기 때문에 로고이미지를 등록해 주셔야 합니다.</font></div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>상품등록시 메타사이트 연동 정보 부분에 상호명, 주소, 연락처, 카테고리 정보를 등록해주셔야 합니다.</font></div>

<div style="padding-top:5"><font class=g9 color=666666>③ 이미지 호스팅 관련</div>
<div style="padding-top:5;padding-left:15px;"><font class=small1 color=#444444>메타사이트 사용시 상점의 상품 이미지가 외부 사이트에 노출되기 때문에 쇼핑몰의 대용량 이미지를 안정적으로 노출할 수 있도록 이미지 호스팅을 반드시 사용하셔야 합니다.</font></div>
</table>
<br>
<form name=form method=post action="indb.metasite.php" target="ifrmHidden" enctype="multipart/form-data">

	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>로고 이미지</td>
		<td>
			<input type=file name=logo style="width:300px">  (jpg 형식의 이미지만 등록 가능)
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
			<div style="margin-top:5px;">※ <font class="small1" color="#444444">메타사이트 연동시 상점의 로고 이미지가 필수 값으로 설정되어 있는 업체가 있기 때문에 상점의 로고 이미지를 등록해 주시길 바랍니다.</font></div>

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
		<td align=center><b>사용여부</b></td>
		<td align=center><b>소셜 메타사이트</b></td>
		<td align=center><b>주소 URL</b></td>
		<td align=center><b>미리보기</b></td>
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">투데이샵에 등록한 상품을 메타사이트에 전송하여 홍보할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">위의 리스트된 사이트로 연동되며, 상품 등록시 메타사이트 연동정보를 입력하셔야 사용할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">메타사이트 연동정보를 입력하지 않은 상품은 메타사이트에 노출되지 않습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">위의 사이트중 입점하고자 하는 사이트에서 입점신청을 하신 후 사용해주세요.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
<? include "../_footer.php"; ?>