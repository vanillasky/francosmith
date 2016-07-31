<?

$location = "상품관리 > 이미지호스팅 일괄전환";
$scriptLoad = '<script src="../imgHostReplace.js"></script>';
include "../_header.php";
include "../../lib/page.class.php";
include "../../lib/imgHostReplace.class.php";
$imgHost = new imgHost($_SESSION['ftpConf']);

list ($total) = $db->fetch("select count(*) from gd_goods");

if (!$_GET[page_num]) $_GET[page_num] = 10;
$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
$checked[open][$_GET[open]] = "checked";
$checked[isToday][$_GET[isToday]] = "checked";

if ($_GET[sCate]){
	$sCategory = array_notnull($_GET[sCate]);
	$sCategory = $sCategory[count($sCategory)-1];
}

if ($_GET[indicate] == 'search'){
	$orderby = "a.goodsno desc";

	if ($_GET[cate]){
		$category = array_notnull($_GET[cate]);
		$category = $category[count($category)-1];
	}

	$db_table = "
	gd_goods a
	left join gd_goods_option b on a.goodsno=b.goodsno and link and go_is_deleted <> '1'
	";

	if ($category){
		$db_table .= "left join gd_goods_link c on a.goodsno=c.goodsno";

		// 상품분류 연결방식 전환 여부에 따른 처리
		$where[]	= getCategoryLinkQuery('c.category', $category, 'where');
	}
	if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
	if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);

	$pg = new Page($_GET[page], $_GET[page_num]);
	$pg->field = "distinct a.goodsno,a.goodsnm,a.img_s,a.open,a.regdt,a.longdesc,a.totstock,b.*";
	$pg->setQuery($db_table,$where,$orderby);
	$pg->exec();

	$res = $db->query($pg->query);
}

?>

<script><!--
function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}
--></script>

<div class="title title_top">이미지호스팅 일괄전환<span>내 쇼핑몰의 상품설명이미지를 이미지호스팅으로 일괄전환합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=20')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;color:#777777;" id="goodsInfoBox">
<div><font color="#EA0095"><b>필독! 이미지호스팅 일괄전환이란?</b></font></div>
<div style="padding-top:2">오픈마켓에 입점한 운영자는 반드시 이미지호스팅을 사용해야 합니다.</div>
<div style="padding-top:2">내 상점에 등록한 상품수가 많을 경우 하나하나 이미지호스팅으로 수정하는 시간이 많이 걸리게 됩니다.</div>
<div style="padding-top:2">아래 기능은 내 쇼핑몰에 올려진 상품설명이미지를 이미지호스팅으로 빠르게 전환해주는 기능입니다.</div>
<div style="padding-top:2">이 기능을 사용하려면 이미지호스팅이 신청되어 있어야 합니다. <a href="http://hosting.godo.co.kr/imghosting/service_info.php" target=_blank><img src="../img/btn_imghost_infoview.gif" align=absmiddle></a> 를 참조하세요!</div>

</div>


<!-- 상품출력조건 : start -->
<form name=frmList onsubmit="return chkForm(this)">
<input type="hidden" name="indicate" value="search">

<div style="padding:10 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">①</font> 먼저 아래에서 이미지호스팅으로 전환할 상품을 검색합니다.</b></font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>분류선택</td>
	<td>
	<script>new categoryBox('cate[]',4,'<?=$category?>');</script>
	<span class="noline" style="margin-left:10px;"><input type=image src="../img/btn_search_s.gif" align="absmiddle"></span>
	</td>
</tr>
<tr>
	<td>검색어</td>
	<td>
	<select name=skey>
	<option value="goodsnm" <?=$selected[skey][goodsnm]?>>상품명
	<option value="a.goodsno" <?=$selected[skey]['a.goodsno']?>>고유번호
	<option value="goodscd" <?=$selected[skey][goodscd]?>>상품코드
	<option value="keyword" <?=$selected[skey][keyword]?>>유사검색어
	</select>
	<input type=text name=sword class=lline value="<?=$_GET[sword]?>">
	</td>
</tr>
<tr>
	<td>상품출력여부</td>
	<td class=noline>
	<input type=radio name=open value="" <?=$checked[open]['']?>>전체
	<input type=radio name=open value="11" <?=$checked[open][11]?>>출력상품
	<input type=radio name=open value="10" <?=$checked[open][10]?>>미출력상품
	</td>
</tr>
</table>

<div style="margin-top:20px;">
	<div style="float:left;" class="pageInfo ver8">총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode[total]?></b>개, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</div>
	<div style="float:right;">
		<img src="../img/sname_output.gif" align=absmiddle>
		<select name=page_num onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>개 출력
		<? } ?>
		</select>
	</div>
</div>
</form>
<!-- 상품출력조건 : end -->

<form name="fmList" method="post" onsubmit="return ( imgHost.submit(this) ? false : false );">
<input type=hidden name=mode>
<input type=hidden name=category value="<?=$category?>">

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr height=35 bgcolor=4a3f38>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>선택</b></a></th>
	<th><font class=small1 color=white><b>번호</b></th>
	<th></th>
	<th></th>
	<th><font class=small1 color=white><b>상품명</b></th>
	<th style="padding-top:3"><font class=small1 color=white><b>전환이 필요한<br> 이미지갯수</b></th>
	<th><font class=small1 color=white><b>등록일</b></th>
	<th><font class=small1 color=white><b>가격</b></th>
	<th><font class=small1 color=white><b>재고</b></th>
	<th><font class=small1 color=white><b>진열</b></th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=50><col width=40><col width=10><col><col width=120><col width=60><col width=80><col width=55 span=2>
<?
while (is_resource($res) && $data=$db->fetch($res)){
	$stock = $data['totstock'];
	$cnt = $imgHost->imgStatus($data['longdesc']);
?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[goodsno]?>" onclick="iciSelect(this)"></td>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td style="border:1px #e9e9e9 solid;"><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a></td>
	<td></td>
	<td><a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><font class=small1 color=0074BA><?=$data[goodsnm]?></a></td>
	<td align=center><font class="ver81" color="#444444" id="in_<?=$data[goodsno]?>" style="font-weight:bold; font-size:16pt;"><?=number_format($cnt['in'])?></font></td>
	<td align=center><font class="ver81" color="#444444"><?=substr($data[regdt],0,10)?></td>
	<td align=center>
	<font color="#4B4B4B"><font class=ver81 color="#444444"><b><?=number_format($data[price])?></b></font>
	<div style="padding-top:2px"></div>
	<img src="../img/good_icon_point.gif" align=absmiddle><font class=ver8><?=number_format($data[reserve])?></font>
	</td>
	<td align=center><font class="ver81" color="#444444"><?=number_format($stock)?></td>
	<td align=center><img src="../img/icn_<?=$data[open]?>.gif"></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<!-- 실행 : start -->
<div style="padding:20 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">②</font> 위 상품리스트에서 선택한 상품의 상품설명이미지를 이미지호스팅으로 전환합니다.</b></font></div>
<div class="noline" style="padding:0 0 5 5">

	<div style="padding-left:210px;"><input type=image src="../img/btn_confirm.gif" align=top></div>
</div>
<!-- 실행 : end -->

</form>

<div style="padding-top:30"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품설명이미지란?
<ul style="margin:0px 0px 0px 34px;">
<li>상품등록시 '상품설명'란에 올려진 이미지를 의미합니다.</li>
</ul>
</td></tr>
<tr><td height=4></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">이미지호스팅이란?
<ul style="margin:0px 0px 0px 34px;">
<li>오픈마켓 등 접속자가 많은 외부사이트에서 사용이 가능하도록 안정적인 이미지 전용서버를 제공하는 오픈마켓용 전용 서비스입니다.</li>
<li>이 기능을 이용하시려면 이미지호스팅을 먼저 신청하셔야 합니다. <a href="http://hosting.godo.co.kr/imghosting/service_info.php" target=_blank><img src="../img/btn_imghost_infoview.gif" align=absmiddle></a> 를 참조하세요!</li>
<li>이미지호스팅은 이미지 전송 링크주소(도메인 URL)와 이미지관리 접속주소(FTP)가 다릅니다.<br>도메인 URL : FTPID.godohosting.com / FTP 주소 : ftp.FTPID.godohosting.com</li>
</ul>
</td></tr>
<tr><td height=4></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">전환이 필요한 이미지 갯수
<ul style="margin:0px 0px 0px 34px;">
<li>상품설명이미지 중에서 <u>이미지호스팅으로 전환이 필요한 이미지 갯수</u>를 보여줍니다.</li>
</ul>
</td></tr>
<tr><td height=4></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">이용방법/순서
<ol type="1" style="margin:0px 0px 0px 40px;">
<li><u>상품선택하기</u> : 상품을 검색한 후 상품별 전환이 필요한 이미지갯수를 참고하여 이미지호스팅으로 전환할 상품을 선택합니다.</li>
<div style="padding-top:2"></div>
<li><u>실행요청하기</u> : [확인] 버튼을 클릭하여 실행을 요청합니다.</li>
<div style="padding-top:2"></div>
<li><u>FTP 입력하기</u> : 이미지호스팅 FTP 계정정보를 입력합니다.</li>
<div style="padding-top:2"></div>
<li><u>이미지전송</u> : 상품설명이미지 중에서 전환이 필요한 이미지가 이미지호스팅으로 전송됩니다.<br>
+ <b>'/goods_상점명' 폴더</b>로 전송됩니다.<br>
+ 동일한 이미지명이 이미지호스팅에 존재하면 <b>덮어쓰기</b>가 됩니다.<br>
+ <span class=color_ffe><b>원본이미지는 다른 곳에서도 사용하고 있을 수 있으므로 자동 삭제되지 않습니다.</b></span><br>
&nbsp; '디자인관리 > webFTP이미지관리 > data > editor'에서 이미지체크 후 삭제관리하세요.
</li>
<div style="padding-top:2"></div>
<li><u>이미지 주소 변경</u> : 상품설명이미지의 링크주소가 오픈마켓이 가능한 이미지호스팅의 링크주소로 변경됩니다.</li>
<div style="padding-top:2"></div>
<li><u>실행종료</u> : 실행이 종료되면 [close] 버튼을 클릭합니다.</li>
</ol>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script language="javascript"><!--
imgHost.ftp = <?=(session_is_registered('ftpConf') ? 'true' : 'null')?>;
--></script>

<? include "../_footer.php"; ?>