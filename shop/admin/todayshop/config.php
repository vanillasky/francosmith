<?
$location = "투데이샵 > 기본설정관리";
include "../_header.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}
$tsCfg = $todayShop->cfg;

if(!$tsCfg['useTodayShop']) $tsCfg['useTodayShop'] = 'n';
$checked['useTodayShop'][$tsCfg['useTodayShop']] = 'checked';

if(!$tsCfg['shopMode']) $tsCfg['shopMode'] = 'regular';
$checked['shopMode'][$tsCfg['shopMode']] = 'checked';

if(!$tsCfg['useEncor']) $tsCfg['useEncor'] = 'n';
$checked['useEncor'][$tsCfg['useEncor']] = 'checked';

if(!$tsCfg['useGoodsTalk']) $tsCfg['useGoodsTalk'] = 'n';
$checked['useGoodsTalk'][$tsCfg['useGoodsTalk']] = 'checked';

if(!$tsCfg['useSMS']) $tsCfg['useSMS'] = 'n';
$checked['useSMS'][$tsCfg['useSMS']] = 'checked';

if(!$tsCfg['useReserve']) $tsCfg['useReserve'] = 'n';
$checked['useReserve'][$tsCfg['useReserve']] = 'checked';

?>
<style type="text/css">
img {border:none;}
</style>
<script type="text/javascript">
function enableForm(mode) {
	var fobj = document.frmConfig;
	var el = fobj.document.getElementsByTagName("INPUT");
	for(var i = 0; i < el.length; i++) {
		if (!(el[i].name == "useTodayShop" || el[i].type == "image")) el[i].disabled = !mode;
	}
}

function copy_txt(val){
	window.clipboardData.setData('Text', val);
}
</script>

<div style="width:100%">
	<form name="frmConfig" method="post" action="indb.config.php" target="ifrmHidden" />
		<div class="title title_top">설정관리 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=2')"><img src="../img/btn_q.gif"></a></div>
		<table class=tb>
		<col class=cellC><col class=cellL>
		<tr>
			<td>투데이샵 사용설정</td>
			<td class="noline">
				<label><input type="radio" name="useTodayShop" value="y" <?=$checked['useTodayShop']['y']?> onclick="enableForm(true)" />사용</label>
				<label><input type="radio" name="useTodayShop" value="n" <?=$checked['useTodayShop']['n']?> onclick="enableForm(false)" />미사용</label>
				<span class="small"><font class="extext">투데이샵 사용여부를 설정합니다.</font></span>
			</td>
		</tr>
		<tr>
			<td>쇼핑몰 메인화면설정</td>
			<td class="noline">
				<label><input type="radio" name="shopMode" value="regular" <?=$checked['shopMode']['regular']?> />일반쇼핑몰 메인 화면을 사용합니다.</label>
				<label><input type="radio" name="shopMode" value="todayshop" <?=$checked['shopMode']['todayshop']?> />투데이샵 메인 화면을 사용합니다.</label>
				<span class="small"><font class="extext">최초접속 페이지 화면을 설정합니다.</font></span>
				<div class="small">
					<div><font class="extext">일반쇼핑몰에서 배너링크를 사용하여 투데이샵을 사용할 경우 투데이샵 오늘의 상품 url을 복사하여 적용해 주시길 바랍니다.</font></div>
					<div><font class="extext">투데이샵 오늘의 상품 URL : http://<?=$_SERVER['HTTP_HOST'].$cfg['rootDir']?>/todayshop</font><img class="hand" src="../img/i_copy.gif" onclick="copy_txt('http://<?=$_SERVER['HTTP_HOST'].$cfg['rootDir']?>/todayshop')" alt="복사하기" align="absmiddle" /></div>
				</div>
			</td>
		</tr>
		<tr>
			<td>앵콜 기능 설정</td>
			<td class="noline">
				<label><input type="radio" name="useEncor" value="y" <?=$checked['useEncor']['y']?> />사용</label>
				<label><input type="radio" name="useEncor" value="n" <?=$checked['useEncor']['n']?> />미사용</label>
				<span class="small"><font class="extext">사용함으로 설정시 사용자페이지에 기능이 적용됩니다.</font></span>
			</td>
		</tr>
		<tr>
			<td>상품토크 설정</td>
			<td class="noline">
				<label><input type="radio" name="useGoodsTalk" value="y" <?=$checked['useGoodsTalk']['y']?> />사용</label>
				<label><input type="radio" name="useGoodsTalk" value="n" <?=$checked['useGoodsTalk']['n']?> />미사용</label>
				<span class="small"><font class="extext">상품토크 사용함으로 설정시 사용자페이지에 노출됩니다.</font></span>
			</td>
		</tr>
		<tr>
			<td>적립금 결제 사용</td>
			<td class="noline">
				<label><input type="radio" name="useReserve" value="y" <?=$checked['useReserve']['y']?> />사용</label>
				<label><input type="radio" name="useReserve" value="n" <?=$checked['useReserve']['n']?> />미사용</label>
				<span class="small"><font class="extext">투데이샵 상품 구매시 적립금 사용 여부를 설정합니다.</font></span>
			</td>
		</tr>
		</table>

		<div class="button">
			<input type=image src="../img/btn_register.gif">
			<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
		</div>

		<div style="padding-top:15px"></div>
	</form>
</div>

<div style="clear:both;" id=MSG01>
	<table cellpadding=1 cellspacing=0 border=0 class="small_ex">
	<tr>
		<td>
			<div>투데이샵서비스를 신청한 고객은 일반쇼핑몰과 투데이샵을 모두 사용하실 수 있습니다.</div>
			<div>&nbsp;</div>
			<div>● 사용여부설정 : 투데이샵 서비스를 신청하신 고객은 기본관리에서 사용설정을 하실 수 있습니다.</div>
			<div>사용안함으로 설정할 경우 일반 쇼핑몰로만 운영하실 수 있습니다.</div>
			<div>&nbsp;</div>
			<div>● 쇼핑몰 메인화면 설정 : 투데이샵을 사용함으로 설정하시면 메인 쇼핑몰 설정을 함께 해주어야 합니다.</div>
			<div>메인화면 설정을 일반쇼핑몰로 할 경우 쇼핑몰 첫 화면이 일반쇼핑몰 메인이 보여지며, 투데이샵으로 설정할 경우 쇼핑몰 첫 화면에 투데이샵 메인이 노출됩니다.</div>
			<div>두 개의 쇼핑몰을 함께 사용하여 이벤트 페이지로 활용하실 수 있습니다. </div>
			<div>예) 일반 쇼핑몰 설정 후 배너, 링크 버튼을 적용하여 투데이샵으로 연결할 수 있습니다.</div>
			<div>&nbsp;</div>
			<div>● 앵콜기능 설정 : 앵콜기능은 이미 판매가 완료된 상품을 사용자(고객)가 재 구매의사가 있는 경우 앵콜신청을 할 수 있는 기능으로 이 기능을 사용설정 하시면 앵콜기능이 사용자 페이지에 나타납니다.</div>
			<div>&nbsp;</div>
			<div>● 상품토크설정 : 상품토크는 상품 페이지에 소비자와 소비자, 소비자와 판매자가 자유롭게 커뮤니케이션할 수 있는 댓글 기능입니다.</div>
			<div>이 기능을 필요로 하지 않은 경우 '사용안함'으로 설정하시면 노출되지 않습니다.</div>
			<div>&nbsp;</div>
			<div>일반 쇼핑몰을 메인으로 사용함으로 설정하셨다면!</div>
			<div>쇼핑몰 메인에 투데이샵 상품페이지와 연결되는 링크 배너영역을 만드세요.</div>
			<div>&nbsp;</div>
			<div>투데이샵 서비스 배너 만들기
			<div>포토샵에서 배너 이미지를 만드신 후 <a href="javascript:popup('../todayshop/codi.banner.php',980,700)"><font class="extext_l">디자인>로고/배너관리</font></a>에서 신규 배너를 등록하시면 됩니다.</div>
			<div>&nbsp;</div>
			<div>● 적립금 결제 사용 설정 :  투데이샵 상품 구매시에도 적립금을 사용할 수 있도록 설정하는 기능입니다.<br>
			사용함으로 설정시 쇼핑몰 기본 적립금 정책 또는 상품별로 직접 입력하여 사용할 수 있습니다.</div>
		</td>
	</tr>
	</table>
</div>

<script type="text/javascript">
	enableForm(<?=($tsCfg['useTodayShop'] == 'y')? 'true' : 'false'?>);
	cssRound('MSG01');
</script>
<? include "../_footer.php"; ?>