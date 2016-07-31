<?

@include "../../conf/auctionos.php";
@include "../../conf/fieldset.php";

$location = "어바웃 > 어바웃 설정";
include "../_header.php";

if(!$partner['auctionshopid'])$partner['auctionshopid'] = "GODO".$godo[sno];
$fexist = file_exists('../../data/auctionos/godo/'.$partner['auctionshopid'].'/auctionos.php');
$fexist2 = file_exists('../../data/auctionos/godo/'.$partner['auctionshopid'].'/auctionos2.php');

$useYn = $partner['useYn'];
$checked['useYn'][$useYn] = "checked";
?>
<div class="title title_top">어바웃 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=20')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name=form method=post action="indb.php">
<input type=hidden name=mode value="auctionos">

<table class="tb" border="0">
<col class="cellC"><col class="cellL">
<input type="hidden" name="partner[auctionshopid]" value="<?=$partner['auctionshopid']?>" class="lline">
<tr>
	<td>사용여부</td>
	<td class="noline">
	<label><input type="radio" name="useYn" value="y" <?php echo $checked['useYn']['y'];?>/>사용</label><label><input type="radio" name="useYn" value="n" <?php echo $checked['useYn']['n'];?> <?php echo $checked['useYn'][''];?> />사용안함</label>
	</td>
</tr>
<tr>
	<td>무이자할부정보</td>
	<td><input type="text" name="partner[nv_pcard]" value="<?=$partner[nv_pcard]?>" class=lline><div class="extext" style="padding-top:5px;">예) 카드명,무이자개월수  [공백없이 입력.  복수카드가 있는 경우, 무이자개월수가 가장긴 카드로 표시]</div></td>

</tr>
<tr>
	<td>상품명 머릿말 설정</td>
	<td>
	<div><input type="text" name="partner[goodshead]" value="<?=$partner[goodshead]?>" class="lline"></div>
	<div class="extext" style="padding-top:5px;">* 상품명 머리말 설정을 위한 치환코드</div>
	<div class="extext">- 머리말 상품에 입력된 "제조사"를 넣고 싶을 때 : {_maker}</div>
	<div class="extext">- 머리말 상품에 입력된 "브랜드"를 넣고 싶을 때 : {_brand}</div>
	</td>
</tr>
<?
list($grpnm,$grpdc) = $db->fetch("select grpnm,dc from ".GD_MEMBER_GRP." where level='".$joinset[grp]."'");
?>
<tr>
	<td>상품가격 설정</td>
	<td>
	<div class="noline"><b><?=$grpnm?></b> 할인율은 <b><?=$grpdc?>%</b>가 상품가격에 적용되어 어바웃에 노출 됩니다. <input type="image" src="../img/btn_naver_install.gif" align="absmiddle" border="0"></div>
	<div class="extext">어바웃에 노출되는 상품가격은 적용된 쿠폰과 가입시 회원그룹의 할인율이 적용된 가격이 됩니다.</div>
	<div class="extext">가입시 회원그룹 설정은 <a href="../member/fieldset.php" class="extext" style="font-weight:bold">회원관리 > 회원가입관리</a>에서 변경 가능합니다.</div>
	<div class="extext">회원그룹의 할인율 변경은 <a href="../member/group.php" class="extext" style="font-weight:bold">회원관리 > 회원그룹관리 </a>에서 변경 가능합니다.</div>
	</td>
</tr>
</table>
</form>

<div id="MSG02">
<table cellpadding="1" cellspacing="0" border=0 class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">무이자할부정보란?: 각 카드사별 무이자정보를 입력하실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">무이자할부정보의 카드사간 구문은 %로 하며,전체 카드사는 전카드로 표기합니다. 예) 삼성:3%현대:6%국민:12</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">무이자할부정보를 입력/저장후 아래 상품DB URL의 수동 업데이트를 실정행하면 상품DB URL 정보 중 무이자 정보가 필드인 pcard필드의 정보가 변경됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">변경된 무이자할부정보는 옥션어바웃 업데이트 주기에 따라 반영되어집니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">옥션 어바웃에 노출되는 상품정보는 다시 등록하시는 것이 아닙니다.</td></tr>
<tr><td style="padding-left:10">현재 운영중인 쇼핑몰의 상품정보를 옥션 어바웃이 주기에 따라  가져갑니다.</td></tr>
</table>
<br/>
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">옥션 어바웃에서 상품검색이 많이 될 수 있도록 상품명 머리말 설정을 활용하세요!</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">특수문자를 머리말로 사용하셔도 특수 문자는 노출되지 않습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">예시 1) 상품명 머리말 설정 : 공란</td></tr>
<tr>
	<td style="padding-left:10">
	<table style='border:1px solid #ffffff;width:400' class="small_ex">
	<col align="center" width="60"><col align="center" width="50"><col align="center" width="50"><col>
	<tr>
		<td>상품명</td>
		<td>제조사</td>
		<td>브랜드</td>
		<td>옥션 어바웃 노출 상품명</td>
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">예시 2) 상품명 머리말 설정 : 무료배송 {_maker} {_brand}</td></tr>
<tr>
	<td style="padding-left:10">
	<table style='border:1px solid #ffffff;width:400' class="small_ex">
	<col align="center" width="60"><col align="center" width="50"><col align="center" width="50"><col>
	<tr>
		<td>상품명</td>
		<td>제조사</td>
		<td>브랜드</td>
		<td>옥션 어바웃 노출 상품명</td>
	</tr>
	<tr>
		<td>여자청바지</td>
		<td>스웨덴</td>
		<td>폴로</td>
		<td>무료배송 수에덴 폴로 여자청바지</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<script>cssRound('MSG02')</script>
</div>

<p>

<table width="100%" cellpadding="0" cellspacing="0">
<col class="cellC"><col style="padding:5px 10px;line-height:140%">
<tr class="rndbg">
	<th>업체</th>
	<th>상품 DB URL [페이지 미리보기]</th>
	<th>최근 업데이트일시</th>
	<th>업데이트</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>
<!--
<tr>
	<td>옥션 오픈쇼핑<br>상품DB URL페이지</td>
	<td>
	<div><font color="#57a300">[전체상품]</font> <?if( file_exists('../../conf/engine/auctionos_all.php') && $fexist ){?><a href="../../data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos.php?mode=all" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos.php?mode=all</font> <img src="../img/btn_naver_view.gif" align="absmiddle"></a><?}else{?>업데이트필요<?}?></font></div>
	<div><font color="#57a300">[요약상품]</font> <?if(file_exists('../../conf/engine/auctionos_summary.php') && $fexist){?><a href="../../data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos.php?mode=summary" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos.php?mode=summary</font> <img src="../img/btn_naver_view.gif" align="absmiddle"></a><?}else{?>업데이트필요<?}?></font></div>
	<div><font color="#57a300">[신규상품]</font> <?if($fexist){?><a href="../../data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos.php?mode=new" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos.php?mode=new</font> <img src="../img/btn_naver_view.gif" align="absmiddle"></a><?}else{?>업데이트필요<?}?></font></div>
	</td>
	<td align="center"><font class="ver81">
		<?if(file_exists('../../conf/engine/auctionos_all.php'))echo date('Y-m-d h:i:s',filectime ( '../../conf/engine/auctionos_all.php'));?>
	</td>
	<td align="center">
		<a href="../../partner/auctionos.engine.php?mode=all" target='ifrmHidden'><img src="../img/btn_price_update.gif"></a>
	</td>
</tr>
-->
<tr><td colspan="12" class="rndline"></td></tr>
<tr>
	<td>어바웃<br>상품DB URL페이지</td>
	<td>
	<div><font color="#57a300">[전체상품]</font> <?if( file_exists('../../conf/engine/auctionos2_all.php') && $fexist2 ){?><a href="../../data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos2.php?mode=all" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos2.php?mode=all</font> <img src="../img/btn_naver_view.gif" align="absmiddle"></a><?}else{?>업데이트필요<?}?></font></div>
	<div><font color="#57a300">[요약상품]</font> <?if(file_exists('../../conf/engine/auctionos2_summary.php') && $fexist2){?><a href="../../data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos2.php?mode=summary" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos2.php?mode=summary</font> <img src="../img/btn_naver_view.gif" align="absmiddle"></a><?}else{?>업데이트필요<?}?></font></div>
	<div><font color="#57a300">[신규상품]</font> <?if($fexist2){?><a href="../../data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos2.php?mode=new" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/data/auctionos/godo/<?=$partner['auctionshopid']?>/auctionos2.php?mode=new</font> <img src="../img/btn_naver_view.gif" align="absmiddle"></a><?}else{?>업데이트필요<?}?></font></div>
	</td>
	<td align="center"><font class="ver81">
		<?if(file_exists('../../conf/engine/auctionos2_all.php'))echo date('Y-m-d h:i:s',filectime ( '../../conf/engine/auctionos2_all.php'));?>
	</td>
	<td align="center">
		<? if ($godo[ecCode]=="self_enamoo_season" && $partner['useYn'] != 'y'){?>
		<img src="../img/btn_price_update.gif" style="cursor:hand;" onclick="javascript:alert('사용여부를 사용으로 설정해주시기 바랍니다.');">
		<? }else{ ?>
		<a href="../../partner/auctionos2.engine.php?mode=all" target='ifrmHidden'><img src="../img/btn_price_update.gif"></a>
		<? } ?>
	</td>
</tr>
<tr><td colspan="12" class="rndline"></td></tr>
</table>
<div class="small1" ><img src="../img/icon_list.gif" align="absmiddle"><b><font color="ff6600">상품정보 변경시나 상품 DB URL의 값이 없을 시에는 반드시 업데이트버튼을 눌러주세요</font></B></div>
<div style="padding-top:2"></div>
<table align="center">
<tr><td width="500">
 <div align="center" class="small1" style='padding-bottom:3'><font color="6d6d6d">업데이트가 진행되면 아래 바를 통해 진행율이 보이게 됩니다.<br>완료메시지가 출력될때까지 다른 동작을 삼가하여주십시요.</font></div>
		<div style="height:8px;font:0;background:#f7f7f7;border:2 solid #cccccc">
		<div id=progressbar style="height:8px;background:#FF4E00;width:0"></div>
 </div>
</td></tr>
</table>
<div align="center"><a href="https://amc.about.co.kr/" target="_blank"><img src="../img/about/btn_about_go.gif" border="0"></a></div>
<p>
<div id="MSG01">
<table cellpadding="2" cellspacing="0" border=0 class="small_ex">
<tr><td>
<div style="padding-top:6;"></div>
<img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe">상품DB URL이란?</span><BR>
&nbsp;&nbsp;운영中인 쇼핑몰의 상품데이타 정보가 옥션 어바웃에 노출되도록 하는<br>
&nbsp;&nbsp;"<B>상점의 상품정보 데이타가 한곳에 모여있는 페이지의 주소값</B>"입니다.<br>
&nbsp;&nbsp;MMC에 등록된 상품DB URL은 광고주의 쇼핑몰 상품을 자동으로 옥션 어바웃으로 가져오는 역할을 합니다.<br>
<br>
<div style="padding-top:6;"></div>
<img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe">페이지 미리보기란?</span><BR>
&nbsp;&nbsp;현재 생성된 상품DB URL페이지의 정보를 확인할 수 있습니다.
<br>
<div style="padding-top:6;"></div>
<img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe">업데이트란?</span><BR>
&nbsp;&nbsp;쇼핑몰 상품정보의 변경으로 지식쇼핑 상품정보 또한 업데이트가 필요로 하게 되며 이때 각각의 업데이트를 클릭하여 상품 DB URL 페이지에 대한 업데이트를 실행하시면<BR>
&nbsp;&nbsp;상품 DB URL 페이지가 업데이트가 되며 실제적으로는 옥션 어바웃의 업데이트 주기에 따라 옥션 어바웃의 상품정보가 업데이트 됩니다.<BR>
&nbsp;&nbsp;-순서: 쇼핑몰상품정보변경 ⇒ 업데이트 실행 ⇒ 옥션 어바웃 업데이트(옥션 어바웃 업데이트 주기에 따른 반영)
<br>
<div style="padding-top:6;"></div>
<img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe">옥션 어바웃 무이자할부정보란?</span><BR>
&nbsp;&nbsp;무이자할부정보를 입력/저장후 상품DB URL의 업데이트를 실행하면 상품DB URL 정보 중 무이자 정보가 필드인 pcard필드의 정보가 변경됩니다.<BR>
&nbsp;&nbsp;변경된 무이자할부정보는 옥션 어바웃 업데이트 주기에 따라 지식쇼핑에 반영되어집니다.
</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
<? include "../_footer.php"; ?>