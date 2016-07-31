<?

include "../../lib/page.class.php";
@include "../../conf/phone.php";

list ($total) = $db->fetch("select count(*) from ".GD_TODAYSHOP_GOODS_REVIEW); # 총 레코드수

### 변수할당
if (!$_GET['page_num']) $_GET['page_num'] = 10; # 페이지 레코드수
$selected['page_num'][$_GET['page_num']] = "selected";

$orderby = ($_GET['sort']) ? $_GET['sort'] : "ask desc"; # 정렬 쿼리
$selected['sort'][$orderby]			= "selected";
$selected['skey'][$_GET['skey']]	= "selected";

### 검색조건
if ($_GET['cate']){
	$category	= array_notnull($_GET['cate']);
	$category	= $category[count($category)-1];

	if ($category){
		$subtable = " left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
		$subwhere[] = "category like '".$category."%'";
	}
}

if ($_GET['skey'] && $_GET['sword']){
	if ($_GET['skey']== 'goodnm' ||  $_GET['skey']== 'all'){
		$tmp = array();
		$res = $db->query("select goodsno from ".GD_GOODS." where goodsnm like '%".$_GET['sword']."%'");
		while ($data=$db->fetch($res))$tmp[] = $data['goodsno'];
		if ( is_array( $tmp ) && count($tmp) ) $goodnm_where = "a.goodsno in(" . implode( ",", $tmp ) . ")";
		else $goodnm_where = "0";
	}

	if ($_GET['skey']== 'all') $subwhere[] = "( concat( subject, contents, ifnull(m_id, ''), ifnull(a.name, '') ) like '%".$_GET['sword']."%' or ".$goodnm_where." )";
	else if ($_GET['skey']== 'goodnm') $subwhere[] = $goodnm_where;
	else if ($_GET['skey']== 'm_id') $subwhere[] = "concat( ifnull(m_id, ''), ifnull(a.name, '') ) like '%".$_GET['sword']."%'";
	else $subwhere[] = "".$_GET['skey']." like '%".$_GET['sword']."%'";
}

if ($_GET['sregdt'][0] && $_GET['sregdt'][1]) $subwhere[] = "a.regdt between date_format(".$_GET['sregdt'][0].",'%Y-%m-%d 00:00:00') and date_format(".$_GET['sregdt'][1].",'%Y-%m-%d 23:59:59')";

if (count($subwhere))
{
	$parent = array();
	$res = $db->query( "select parent from ".GD_TODAYSHOP_GOODS_REVIEW." a left join ".GD_MEMBER." b on a.m_no=b.m_no ".$subtable." where " . implode(" and ", $subwhere) );
	while ( $row = $db->fetch( $res ) ) $parent[] = $row['parent'];
	$parent = array_unique ($parent);
	if ( count( $parent ) ) $where[] = "parent in ('" . implode( "','", $parent ) . "')";
	else $where[] = "0";
}

### 목록
$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->field = "distinct sno, parent, goodsno, subject, contents, point, regdt, name, m_no, emoney";
$db_table = GD_TODAYSHOP_GOODS_REVIEW;
$pg->setQuery($db_table,$where,( $orderby == 'ask desc' ? "parent desc, ( case when parent=sno then 0 else 1 end ) asc, regdt desc" : $orderby ));
$pg->exec();

$res = $db->query($pg->query);

### 환경설정
include "../../conf/config.php";
$checked['todayshopReviewWriteAuth'][$cfg['todayshopReviewWriteAuth']] = "checked";
$checked['todayshopReviewUse'][$cfg['todayshopReviewUse']] = "checked";

?>
<?getjskPc080();?>
<script src="../../lib/js/todayshopCategoryBox.js"></script>

<form method="post" action="../todayshop/goods_review_indb.php?mode=set" name="fmSet">
<div class="title title_top">상품후기게시판 설정 <span>리스트 갯수 제한 및 글쓰기에 대한 권한을 설정하실 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19')"><img src="../img/btn_q.gif" align="absmiddle" hspace="2" /></a></div>
<table class="tb">
<col class="cellC" /><col class="cellL" />
<tr>
	<td>사용여부 설정</td>
	<td class="noline">

		<label><input type="radio" name="todayshopReviewUse" value="0" <?=$checked['todayshopReviewUse'][0]?>/>사용함</label>
		<label><input type="radio" name="todayshopReviewUse" value="1" <?=$checked['todayshopReviewUse'][1]?>/>사용안함</label>
		<span class="small"><font class="extext">상품후기 게시판 사용여부를 설정합니다.</font></span>

	</td>
</tr>
<tr>
	<td>리스트 갯수</td>
	<td><input type="text" name="todayshopReviewListCnt" value="<?=$cfg['todayshopReviewListCnt']?>" size="6" class="rline" onkeydown="onlynumber();" /> 개</td>
</tr>
<tr>
	<td>글쓰기 권한</td>
	<td class="noline">
	<input type="radio" name="todayshopReviewWriteAuth" value="" <?=$checked['todayshopReviewWriteAuth']['']?> /> 회원만 가능&nbsp;&nbsp;
	<input type="radio" name="todayshopReviewWriteAuth" value="free" <?=$checked['todayshopReviewWriteAuth']['free']?> /> 비회원도 가능
	</td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_save3.gif" /></div>
</form>

<div style="padding-top:5px"></div>


<form name="frmList">
<input type="hidden" name="sort" value="<?=$_GET['sort']?>" />
<input type="hidden" name="page_num" value="<?=$_GET['page_num']?>" />
<div class="title title_top">상품후기관리<span>고객들이 남긴 상품후기를 살펴보실 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19')"><img src="../img/btn_q.gif" align="absmiddle" hspace="2" /></a></div>
<table class="tb" />
<col class="cellC" /><col class="cellL" />
<tr>
	<td>분류선택</td>
	<td><script>new todayshopCategoryBox('cate[]',4,'<?=$category?>','','frmList');</script></td>
</tr>
<tr>
	<td>키워드검색전송</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected['skey']['all']?>> 통합검색 </option>
	<option value="subject" <?=$selected['skey']['subject']?>> 제목 </option>
	<option value="contents" <?=$selected['skey']['contents']?>> 후기 </option>
	<option value="m_id" <?=$selected['skey']['m_id']?>> 작성자 </option>
	<option value="goodnm" <?=$selected['skey']['goodnm']?>> 상품명 </option>
	</select>
	<input type="text" class="line" name="sword" value="<?=$_GET['sword']?>" />
	</td>
</tr>
<tr>
	<td>등록일</td>
	<td>
	<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][0]?>" onclick="calendar(event);" onkeydown="onlynumber();" class="line" /> -
	<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][1]?>" onclick="calendar(event);" onkeydown="onlynumber();" class="line" />
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
	</td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>
</form>



<table width="100%">
<tr>
	<td class="pageInfo"><font class="ver8">
	총 <b><?=number_format($total)?></b>개, 검색 <b><?=number_format($pg->recode['total'])?></b>개, <b><?=number_format($pg->page[now])?></b> of <?=number_format($pg->page['total'])?> Pages
	</td>
	<td align=right>
	<!--<select onchange="frmList.sort.value=this.value; frmList.submit();">
	<option value="regdt desc" <?=$selected[sort]['regdt desc']?>>- 등록일 정렬↑</option>
	<option value="regdt asc" <?=$selected[sort]['regdt asc']?>>- 등록일 정렬↓</option>
	<option value="point desc" <?=$selected[sort]['point desc']?>>- 평점 정렬↑</option>
	<option value="point asc" <?=$selected[sort]['point asc']?>>- 평점 정렬↓</option>
	<optgroup label="------------"></optgroup>
	<option value="subject desc" <?=$selected[sort]['subject desc']?>>- 제목 정렬↑</option>
	<option value="subject asc" <?=$selected[sort]['subject asc']?>>- 제목 정렬↓</option>
	</select>&nbsp;-->
	<select onchange="frmList.page_num.value=this.value; frmList.submit();">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>개 출력
	<? } ?>
	</select>
	</td>
</tr>
</table>
<!-- 가짜 bar - SMS 전송시 스크립트 오류로 인해 가짜 bar 를 표시 -->
<div id="sms_bar" style="width:0;height:10px;display:none"></div>
<form method="post" action="" name="fmList">
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg">
	<th width="60">번호</th>
	<th width="70">이미지</th>
	<th>상품명/제목</th>
	<th width="80">작성자</th>
	<th width="80">작성일</th>
	<th width="80">평점</th>
	<th width="80">적립금</th>
	<th width="50">답변</th>
	<th width="50">수정</th>
	<th width="50">삭제</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>
</table>

<?
while ($data=$db->fetch($res)){

	if ( empty($data['m_no']) ) $data['m_id'] = $data['name']; // 비회원명
	else {
		list( $data[m_id],$data[phone],$data[mobile] ) = $db->fetch("select m_id,phone,mobile from ".GD_MEMBER." where m_no='$data[m_no]'" );
	}

	if ( $data['parent']==$data['sno'] ){ // 원글
		$query = "select b.goodsnm,b.img_s,c.price, d.tgsno
		from
			".GD_GOODS." b
			left join ".GD_GOODS_OPTION." c on b.goodsno=c.goodsno and link and go_is_deleted <> '1'
			left join ".GD_TODAYSHOP_GOODS." d on b.goodsno=d.goodsno

		where
			b.goodsno = '" . $data['goodsno'] . "'";
		list( $data['goodsnm'], $data['img_s'], $data['price'], $data['tgsno'] ) = $db->fetch($query);

		list( $data['replecnt'] ) = $db->fetch("select count(*) from ".GD_TODAYSHOP_GOODS_REVIEW." where sno != parent and parent='".$data['sno']."'");
	}
?>
<?if ( $data['parent']==$data['sno'] ){ ?>
<div style="border-top-width:1px; border-top-style:solid; border-top-color:#DCD8D6;">
<table width="100%" cellpadding="0" cellspacing="0" onclick="view_content(this, event);" class="hand">
<tr><td height="4" colspan="10"></td></tr>
<tr height="25" align="center" onmouseover="this.style.background='#F7F7F7'" onmouseout="this.style.background=''">
	<td width="60"><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td width="70"><a href="../../todayshop/today_goods.php?tgsno=<?=$data['tgsno']?>" target="_blank"><?=goodsimg($data['img_s'],40,"style='border:1px solid #efefef'",1)?></a></td>
	<td align="left" style="line-height:17px">
	<div style="color:#999999"><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',825,800)" style="color:#0074BA;" class="small">[ <?=$data['goodsnm']?> ]</a></div>
	<font color="#333333"><?=$data['subject']?></font><font class="ver8" color="#FF6709">(<?=$data['replecnt']?>)</font>
	</td>
	<td width="80">
	<?
	if($data['m_no']){
	?>
	<div><span id="navig" name="navig" m_id="<?=$data[m_id]?>" m_no="<?=$data[m_no]?>"><img src="../img/icon_crmlist.gif" /></span><?getlinkPc080($data['phone'],'phone')?><?getlinkPc080($data['mobile'],'mobile')?></div><div><span style="color:#616161;" class=ver8><?=$data[m_id]?></span></div>
	<?}else{?>
	<?=$data['m_id']?>
	<?}?>
	</td>
	<td width="80"><font class="ver8" color="#333333"><?=substr($data['regdt'],0,10)?></font></td>
	<td width="80" align="left"><font class="ver8" color="#ef6d00"><span style="margin-left:10px;"><?=str_repeat( "★", $data['point'] )?></span></td>
	<td width="80" align="right"><font class="ver8" color="#ef6d00"><span style="margin-right:10px;"><?=number_format($data['emoney'])?> 원</span></td>
	<td width="50"><a href="javascript:popupLayer('../todayshop/goods_review_register.php?mode=reply&sno=<?echo($data['sno'])?>')"><img src="../img/i_reply.gif" /></a></td>
	<td width="50"><a href="javascript:popupLayer('../todayshop/goods_review_register.php?mode=modify&sno=<?echo($data['sno'])?>')"><img src="../img/i_edit.gif" /></a></td>
	<td width="50" class="noline"><input type="checkbox" name="confirmyn" value="<?=$data['sno']?>"></td>
</tr>
<tr><td height="4" colspan="10"></td></tr>
</table>
<div style="display:none;padding:5px 10px 10px 130px;"><font color="484848"><?=nl2br($data['contents'])?></font></div>
</div>
<?} else if ( $data['sno'] != $data['parent'] ){ // 답글?>
<div style="border-top-width:1px; border-top-style:dotted; border-top-color:#DCD8D6;">
<table width="100%" cellpadding="0" cellspacing="0" onclick="view_content(this, event);" class="hand">
<tr><td height="4" colspan="10"></td></tr>
<tr height="25" align="center" onmouseover="this.style.background='#F7F7F7'" onmouseout="this.style.background=''">
	<td width="60"><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td width="70"><img src="../img/btn_reply.gif" /></td>
	<td align="left" style="line-height:17px"><font color="#333333"><?=$data['subject']?></font></td>
	<td width="80"><?=$data['m_id']?></td>
	<td width="80"><font class="ver8" color="#333333"><?=substr($data['regdt'],0,10)?></font></td>
	<td width="80"></td>
	<td width="80"></td>
	<td width="50"></td>
	<td width="50"><a href="javascript:popupLayer('../todayshop/goods_review_register.php?mode=modify&sno=<?echo($data['sno'])?>')"><img src="../img/i_edit.gif" /></a></td>
	<td width="50" class="noline"><input type="checkbox" name="confirmyn" value="<?=$data['sno']?>"></td>
</tr>
<tr><td height="4" colspan="10"></td></tr>
</table>
<div style="display:none;padding:5px 10px 10px 130px;"><font color="484848"><?=nl2br($data['contents'])?></font></div>
</div>
<? } ?>
<? } ?>
<div style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCD8D6;width:100%;height:1px;font-size:0px;"></div>
<input type="hidden" name="nolist">
</form>

<div align="center" class="pageNavi"><font class="ver8"><?=$pg->page['navi']?></font></div>

<div>
<img src="../img/btn_allselect_s.gif" alt="전체선택"  border="0" align="absmiddle" style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javascript:PubAllSordes( 'select', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?> />
<img src="../img/btn_allreselect_s.gif" alt="선택반전"  border="0" align="absmiddle" style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javascript:PubAllSordes( 'reflect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?> />
<img src="../img/btn_alldeselect_s.gif" alt="선택해제"  border="0" align="absmiddle" style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javascript:PubAllSordes( 'deselect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?> />
<img src="../img/btn_alldelet_s.gif" alt="선택삭제" border="0" align="absmiddle" style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javaScript:act_delete();"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?> />
</div>

<div style="padding-top:15px"></div>

<div id="MSG01">
<table cellpadding="2" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />후기제목을 클릭하면 글내용이 열리며, 다시 제목을 클릭하면 내용이 닫히게됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />상품이미지를 클릭하면 새창과 함께 상품상세페이지로 이동합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />작성자를 클릭하시면 회원정보와 함께 회원주문내역 등을 보실 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script language="javascript">
var preContent;

function view_content(obj, e)
{
	if ( document.all && ( e.srcElement.tagName == 'A' || e.srcElement.tagName == 'IMG' || e.srcElement.tagName == 'INPUT' ) ) return;
	else if ( !document.all && ( e.target.tagName == 'A' || e.target.tagName == 'IMG' || e.srcElement.tagName == 'INPUT' ) ) return;

	var div = obj.parentNode;

	if ( document.all ) obj = div.childNodes[ 1 ]; else obj = div.childNodes[ 3 ]; // 모질라 경우 줄내림도 #text 임

	if (preContent && obj!=preContent){
		obj.style.display = "block";
		preContent.style.display = "none";
	}
	else if (preContent && obj==preContent) preContent.style.display = ( preContent.style.display == "none" ? "block" : "none" );
	else if (preContent == null ) obj.style.display = "block";

	preContent = obj;
}
</script>



<script language="javascript">
<!--
/*-------------------------------------
 삭제
-------------------------------------*/
function act_delete(){

	if ( PubChkSelect( fmList['confirmyn'] ) == false ){
		alert( "삭제하실 내역을 선택하여 주십시요." );
		return;
	}

	if ( confirm( "선택한 아이템을 정말 삭제하시겠습니까?\n삭제 후 복구할 수 없습니다." ) == false ) return;

	var idx = 0;
	var codes = new Array();
	var count = fmList['confirmyn'].length;

	if ( count == undefined ) codes[ idx++ ] = fmList['confirmyn'].value;
	else {

		for ( i = 0; i < count ; i++ ){
			if ( fmList['confirmyn'][i].checked ) codes[ idx++ ] = fmList['confirmyn'][i].value;
		}
	}

	fmList.nolist.value = codes.join( ";" );
	fmList.action = "../todayshop/goods_review_indb.php?mode=delete" ;
	fmList.submit() ;
}
//-->
</SCRIPT>

<script>window.onload = function(){ UNM.inner();};</script>