<?

@include "../../conf/daumCpc.cfg.php";

$location = "다음 쇼핑하우 > 쇼핑하우 설정하기";
include "../_header.php";

if(!$daumCpc['useYN']) $daumCpc['useYN']='N';
if($daumCpc['useYN']) $checked['useYN'][$daumCpc['useYN']]='checked';
?>
<script type="text/javascript">
function copy_txt(val){
	window.clipboardData.setData('Text', val);
}
function check_use(){
	var conf = document.getElementById('configration');
	var chk = document.getElementsByName('useYn');
	var obj = document.getElementsByName('daumCpc[useYN]')[0];
	if(chk[0].checked == true){
		conf.disabled = false;
		obj.value='Y';
	}else{
		conf.disabled = true;
		obj.value='N';
	}
}
function review_init(){
	document.form.mode.value = 'review_init';
	document.form.submit();
	document.form.mode.value = 'daumCpc';
}
window.onload = function(){
	check_use();
}
</script>
<div style="width:800px">
<div class="title title_top">쇼핑하우 설정하기 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=18')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table class="tb" border="0">
<col class="cellC"><col class="cellL">
<?
@include "../../conf/fieldset.php";
list($grpnm,$grpdc) = $db->fetch("select grpnm,dc from ".GD_MEMBER_GRP." where level='".$joinset[grp]."'");
$url = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/partner/daumCpc.php";
$allUrl = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/partner/daum_all.php";
$sumUrl = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/partner/daum_some.php";
$reviewUrl = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/partner/daum_review.php";
?>
<tr>
	<td>사용 설정</td>
	<td class="noline">
		<input type="radio" name="useYn" value="Y" onclick="check_use();" <?=$checked['useYN']['Y']?>> 사용 &nbsp;<input type="radio" name="useYn" value="N" onclick="check_use();" <?=$checked['useYN']['N']?>> 미사용
	</td>
</tr>
</table>
<p/>
<form name="form" method="post" action="indb.php" target="ifrmHidden" >
<input type="hidden" name="mode" value="daumCpc">
<input type="hidden" name="daumCpc[useYN]" value="<?=$daumCpc['useYN']?>">
<div id="configration">
<table class="tb" border="0">
<col class="cellC"><col class="cellL">
<tr>
	<td>상품가격 설정</td>
	<td class="noline">
	<div><b><?=$grpnm?></b> 할인율은 <b><?=number_format($grpdc)?>%</b>가 상품가격에 적용되어 쇼핑하우에 노출 됩니다.</div>
	<div class="extext" style="padding:5 0 0 0">쇼핑하우에에 노출되는 상품가격은 적용된 쿠폰과 가입시 회원그룹의 할인율이 적용된 가격이 됩니다.</div>
	<div class="extext" style="padding:1 0 0 0">가입시 회원그룹 설정은 <a href="../member/fieldset.php" class="extext" style="font-weight:bold">회원관리 > 회원가입관리</a>에서 변경 가능합니다.</div>
	<div class="extext" style="padding:1 0 0 0">회원그룹의 할인율 변경은 <a href="../member/group.php" class="extext" style="font-weight:bold">회원관리 > 회원그룹관리 </a>에서 변경 가능합니다.</div>
	</td>
</tr>
<tr>
	<td>다음쇼핑하우<br/>무이자할부정보</td>
	<td>
	<input type="text" name="daumCpc[nv_pcard]" value="<?=$daumCpc['nv_pcard']?>" class="lline">
	<div class="extext" style="padding:5 0 0 0">예) 삼성2~3/롯데3/현대6</div>
	</td>
</tr>
<tr>
	<td>다음쇼핑하우<br />상품명 머릿말 설정</td>
	<td>
	<div><input type=text name="daumCpc[goodshead]" value="<?=$daumCpc['goodshead']?>" class="lline"></div>
	<div class="extext" style="padding:5 0 0 0">* 상품명 머리말 설정을 위한 치환코드</div>
	<div class="extext" style="padding:1 0 0 0">- 머리말 상품에 입력된 "제조사"를 넣고 싶을 때 : {_maker}</div>
	<div class="extext" style="padding:1 0 0 0">- 머리말 상품에 입력된 "브랜드"를 넣고 싶을 때 : {_brand}</div>
	</td>
</tr>
</table>
<p/>
<table width=100% cellpadding=0 cellspacing="0">
<col class="cellC"><col style="padding:5px 10px;line-height:140%">
<tr class="rndbg">
	<th colspan="2" align="center">상품 DB URL</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>
<tr>
	<td>다음 쇼핑하우<br>상품 DB URL</td>
	<td>
	<div style="clear:both">
		<div style="color:#57a300;float:left;">[전체상품]</div>
		<div class="ver8" style="float:left;width:500px;padding:2"><?php echo $allUrl;?></div>
		<div style="float:left;"><a href="../../partner/daum_all.php" target=_blank><img src="../img/btn_naver_view.gif" align="absmiddle"></a></div>
	</div>
	<div style="clear:both">
		<div style="color:#57a300;float:left;">[전체상품(구)]</div>
		<div class="ver8" style="float:left;width:479px;padding:2"><?php echo $url;?></div>
		<div class="ver8" style="float:left;"><a href="../../partner/daumCpc.php" target=_blank><img src="../img/btn_naver_view.gif" align="absmiddle"></a></div>
	</div>
	<div style="clear:both">
		<div style="color:#57a300;float:left;">[요약상품]</div>
		<div class="ver8" style="float:left;width:500px;padding:2"><?php echo $sumUrl;?></div>
		<div class="ver8" style="float:left;"><a href="../../partner/daum_some.php" target=_blank><img src="../img/btn_naver_view.gif" align="absmiddle"></a></div>
	</div>
	<div style="clear:both">
		<div style="color:#57a300;float:left;">[상품평]</div>
		<div class="ver8" style="float:left;width:512px;padding:2"><?php echo $reviewUrl;?></div>
		<div class="ver8" style="float:left;"><a href="../../partner/daum_review.php" target=_blank><img src="../img/btn_naver_view.gif" align="absmiddle"></a></div>
	</div>
	</td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
</table>
</div>

<table class="tb" border="0">
<col class="cellC"><col class="cellL">
<tr>
	<td>상품평 수집 초기화</td>
	<td class="noline">
		<div><a href="javascript:review_init();"><img src="../img/btn_review_init.png" align="absmiddle"></a></div>
		<div class="extext" style="padding:5 0 0 0">기존에 등록되었던 상품평이 다음쇼핑하우에 노출되지 않을 때 상품평 수집 초기화를 시켜보시기 바랍니다.</div>
		<div class="extext" style="padding:5 0 0 0">상품평 수집 초기화 시 익일 새벽에 전체 상품평이 업데이트 됩니다.</div>
	</td>
</tr>
</table>

<div style="padding-top:10px;">
<div class="red" style="border: solid #dce1e1 4px; padding: 10px;">※ 주의!: gif포맷으로 된 상품이미지는 다음 쇼핑하우(쇼핑하우 정책에 의해)에 전송이 되지 않습니다.
<div class="red" style="padding-left:50">jpg 등 다른 이미지포맷으로 상품이미지를 변경 후 쇼핑하우 연동을 해주시기 바랍니다.</div></div>
</div>

<div class=button>
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
</form>
<p/>
<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">다음쇼핑하우 무이자할부정보란?: 각 카드사별 무이자정보를 입력하실 수 있습니다. 예) 삼성2~3/롯데3/현대6</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">변경된 무이자할부정보는 지식쇼핑 업데이트 주기에 따라 지식쇼핑에 반영되어집니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">다음쇼핑하우에 노출되는 상품정보는 다시 등록하시는 것이 아닙니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">현재 운영중인 쇼핑몰의 상품정보를 다음쇼핑하우가 매일 새벽에 자동으로 가져갑니다.</td></tr>
</table>
<br/>
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">다음쇼핑하우에서 상품검색이 많이 될 수 있도록 상품명 머리말 설정을 활용하세요!</td></tr>
<tr><td style="padding-left:10">예시 1) 상품명 머리말 설정 : 공란</td></tr>
<tr>
	<td style="padding-left:10">
	<table style='border:1px solid #ffffff;width:400' class="small_ex">
	<col align="center" width="60"><col align="center" width="50"><col align="center" width="50"><col>
	<tr>
		<td>상품명</td>
		<td>제조사</td>
		<td>브랜드</td>
		<td>노출 상품명</td>
	</tr>
	<tr>
		<td>여자청바지</td>
		<td>스웨덴</td>
		<td>폴로</td>
		<td>여자청바지</td>
	</tr>
	</table>
	</td>
</tr>
<tr><td style="padding-left:10">예시 2) 상품명 머리말 설정 : [무료배송 / {_maker} / {_brand}]</td></tr>
<tr>
	<td style="padding-left:10">
	<table style='border:1px solid #ffffff;width:400' class="small_ex">
	<col align="center" width="60"><col align="center" width="50"><col align="center" width="50"><col>
	<tr>
		<td>상품명</td>
		<td>제조사</td>
		<td>브랜드</td>
		<td>노출 상품명</td>
	</tr>
	<tr>
		<td>여자청바지</td>
		<td>스웨덴</td>
		<td>폴로</td>
		<td>[무료배송 / 수에덴 / 폴로] 여자청바지</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</div>
<script>cssRound('MSG01')</script>
</div>
<? include "../_footer.php"; ?>