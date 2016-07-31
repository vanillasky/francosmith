<?

include "../../lib/page.class.php";
@include "../../conf/phone.php";
include "../../conf/config.php";

list ($total) = $db->fetch("select count(*) from ".GD_GOODS_QNA); # 총 레코드수

### 변수할당
if (!$_GET['page_num']) $_GET['page_num'] = 20; # 페이지 레코드수
$selected['page_num'][$_GET['page_num']] = "selected";
$cfg['qnaFavoriteReplyUse'] = (!$cfg['qnaFavoriteReplyUse']) ? "n" : $cfg['qnaFavoriteReplyUse'];

$orderby = ($_GET['sort']) ? $_GET['sort'] : "notice desc, parent desc, ( case when parent=sno then 0 else 1 end ) asc, regdt desc"; # 정렬 쿼리
$selected['sort'][$orderby]			= "selected";
$selected['skey'][$_GET['skey']]	= "selected";
$selected["qnaFavoriteReplyNo"][$cfg['qnaFavoriteReplyNo']]		= "selected";
$checked["qnaFavoriteReplyUse"][$cfg['qnaFavoriteReplyUse']]	= "checked";

### 검색조건
if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];

	if ($category){
		$subtable = " left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";

		// 상품분류 연결방식 전환 여부에 따른 처리
		$subwhere[]	= getCategoryLinkQuery('c.category', $category, 'where');
	}
}

if ($_GET['skey'] && $_GET[sword]){
	if ($_GET['skey']== 'goodnm' ||  $_GET['skey']== 'all'){
		$tmp = array();
		$res = $db->query("select goodsno from ".GD_GOODS." where goodsnm like '%$_GET[sword]%'");
		while ($data=$db->fetch($res))$tmp[] = $data[goodsno];
		if ( is_array( $tmp ) && count($tmp) ) $goodnm_where = "a.goodsno in(" . implode( ",", $tmp ) . ")";
		else $goodnm_where = "0";
	}

	if ($_GET['skey']== 'all') $subwhere[] = "( concat( subject, contents, ifnull(m_id, ''), ifnull(a.name, '') ) like '%$_GET[sword]%' or $goodnm_where )";
	else if ($_GET['skey']== 'goodnm') $subwhere[] = $goodnm_where;
	else if ($_GET['skey']== 'm_id') $subwhere[] = "concat( ifnull(m_id, ''), ifnull(a.name, '') ) like '%$_GET[sword]%'";
	else $subwhere[] = $_GET['skey']." like '%$_GET[sword]%'";
}

if ($_GET[sregdt][0] && $_GET[sregdt][1]) $subwhere[] = "a.regdt between date_format({$_GET[sregdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[sregdt][1]},'%Y-%m-%d 23:59:59')";

if (count($subwhere))
{
	$parent = array();
	$res = $db->query( "select parent from ".GD_GOODS_QNA." a left join ".GD_MEMBER." b on a.m_no=b.m_no {$subtable} where " . implode(" and ", $subwhere) );
	while ( $row = $db->fetch( $res ) ) $parent[] = $row['parent'];
	$parent = array_unique ($parent);
	if ( count( $parent ) ) $t_where[] = "parent in ('" . implode( "','", $parent ) . "')";
	else $t_where[] = "parent in ('0')";
}

### 목록
$pg = new Page($_GET[page],$_GET['page_num']);
$pg->field = "distinct a.sno, a.parent, a.goodsno, a.subject, a.contents, a.regdt, a.name, a.m_no, a.secret, a.notice";
$db_table = GD_GOODS_QNA." AS a LEFT JOIN ".GD_MEMBER." AS m ON (a.m_no = m.m_no) ";
$pg->setQuery($db_table,$t_where,"notice desc, ".$orderby);
$pg->exec();

$res = $db->query($pg->query);

$replyQuery = "SELECT sno, subject FROM ".GD_GOODS_FAVORITE_REPLY." WHERE customerType = 'qna' ORDER BY regdt DESC";
$replyRes = $db->query($replyQuery);
$replyTotal = $db->count_($replyRes);
?>

<form name=frmList>
<input type="hidden" name="sort" value="<?=$_GET['sort']?>">
<input type="hidden" name="page_num" value="<?=$_GET['page_num']?>">
<div class="title title_top">상품문의관리 <span>고객들이 질문한 상품문의를 살펴보실 수 있습니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>분류선택</td>
	<td colspan="3"><script>new categoryBox('cate[]',4,'<?=$category?>','','frmList');</script></td>
</tr>
<tr>
	<td>키워드검색전송</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected['skey']['all']?>> 통합검색 </option>
	<option value="subject" <?=$selected['skey']['subject']?>> 제목 </option>
	<option value="contents" <?=$selected['skey']['contents']?>> 문의내용 </option>
	<option value="m_id" <?=$selected['skey']['m_id']?>> 작성자 </option>
	<option value="goodnm" <?=$selected['skey']['goodnm']?>> 상품명 </option>
	</select> <input type="text" class=line NAME="sword" value="<?=$_GET['sword']?>">
	</td>
</tr>
<tr>
	<td>등록일</td>
	<td colspan="3">
	<input type=text name=sregdt[] value="<?=$_GET[sregdt][0]?>" onclick="calendar(event)" class=line> -
	<input type=text name=sregdt[] value="<?=$_GET[sregdt][1]?>" onclick="calendar(event)" class=line>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>
</form>

<div style="padding-top:5px"></div>

<form name="fmSet" method="post" action="../board/customer_indb.php?mode=replySet">
<input type="hidden" name="sort" value="<?=$_GET['sort']?>">
<input type="hidden" name="page_num" value="<?=$_GET['page_num']?>">
<div class="title title_top">자주쓰는 답변 <span>답변쓰기 작성시에 미리 입력해놓은 자주쓰는 답변을 자동/수동으로 불러올 수 있습니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=4')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>
<table class="tb" style="margin-bottom:10px;">
<col class="cellC" width="15%"><col class="cellL">
<tr>
	<td>자주쓰는 답변 설정</td>
	<td class="noline">
		<div>
			<input type="radio" name="qnaFavoriteReplyUse" id="qnaFavoriteReplyUse_n" value="n" <?=$checked["qnaFavoriteReplyUse"]['n']?> onclick="checkReplyUse('n')" /> 수동
			<input type="radio" name="qnaFavoriteReplyUse" id="qnaFavoriteReplyUse_y" value="y" <?=$checked["qnaFavoriteReplyUse"]['y']?> onclick="checkReplyUse('y')" /> 자동
			<select name="qnaFavoriteReplyNo" id="qnaFavoriteReplyNo" style="margin:0px 10px;">
<? if(!$replyTotal) { ?>
				<option value="">자주쓰는 답변을 입력해 주세요.</option>
<? } ?>
<? while($replyData=$db->fetch($replyRes)) { ?>
				<option value="<?=$replyData['sno']?>" <?= $selected["qnaFavoriteReplyNo"][$replyData['sno']]?>><?=strcut($replyData['subject'], 40)?></option>
<? } ?>
			</select>
			<a href="javascript:popup2('../board/customer_reply.php?type=qna',800,800,1)"><img src="../img/icon_repeatqna.gif" align="absmiddle" /></a><input type="image" src="../img/btn_save2.gif" border="0" align="absmiddle" style="margin-left:20px;" /><br />
		</div>
		<div class="extext" style="margin:5px 0px;">* <b>자동</b>을 선택하시면 설정한 답변이 답변쓰기 팝업 창에 자동으로 입력되어 열립니다.</div>
	</td>
</tr>
</table>
</form>

<div style="padding-top:5px"></div>

<table width=100%>
<tr>
	<td class=pageInfo><font class=ver8>
	총 <b><?=number_format($total)?></b>개, 검색 <b><?=number_format($pg->recode[total])?></b>개, <b><?=number_format($pg->page[now])?></b> of <?=number_format($pg->page[total])?> Pages
	</td>
	<td align=right>
	<select onchange="frmList.sort.value=this.value; frmList.submit();">
	<option value="regdt desc" <?=$selected['sort']['regdt desc']?>>- 등록일 정렬↑</option>
	<option value="regdt asc" <?=$selected['sort']['regdt asc']?>>- 등록일 정렬↓</option>
    <optgroup label="------------"></optgroup>
	<option value="subject desc" <?=$selected['sort']['subject desc']?>>- 제목 정렬↑</option>
	<option value="subject asc" <?=$selected['sort']['subject asc']?>>- 제목 정렬↓</option>
	</select>&nbsp;
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

<form method="post" action="" name="fmList">
<table width=100% cellpadding=0 cellspacing=0>
<tr><td class=rnd colspan=11></td></tr>
<tr class=rndbg>
	<th width="60" onclick="javascript:PubAllSordes( 'reflect', fmList['confirmyn'] );" style="cursor:pointer;">선택</th>
	<th width="60">번호</th>
	<th width="70">이미지</th>
	<th>상품명/제목</th>
	<th width="80">작성자</th>
	<th width="80">작성일</th>
	<th width="50">답변</th>
	<th width="50">수정</th>
	<th width="50">삭제</th>
</tr>
<tr><td class=rnd colspan=11></td></tr>
</table>

<?
$i = 0;
while ($data=$db->fetch($res)){

	if ( empty($data[m_no]) ) $data[m_id] = $data[name]; // 비회원명
	else {
		list( $data[m_id],$data[phone],$data[mobile],$data[dormant_regDate] ) = $db->fetch("select m_id,phone,mobile,dormant_regDate from ".GD_MEMBER." where m_no='$data[m_no]'" );
	}

	$data['secretIcon'] = 0;
	if($data['secret']&& $cfg['qnaSecret'])$data['secretIcon'] = "1";

	if ( $data[sno] == $data[parent] ){ // 질문

		$query = "select b.goodsnm,b.img_s,c.price
		from
			".GD_GOODS." b
			left join ".GD_GOODS_OPTION." c on b.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
		where
			b.goodsno = '" . $data[goodsno] . "'";
		list( $data[goodsnm], $data[img_s], $data[price] ) = $db->fetch($query);

		list( $data[replecnt] ) = $db->fetch("select count(*) from ".GD_GOODS_QNA." where sno != parent and parent='$data[sno]'");
	}
	?>

	<?if ( $data[sno] == $data[parent] ){ // 질문?>
<div style="border-top-width:1; border-top-style:solid; border-top-color:#DCD8D6;">
<table width=100% cellpadding=0 cellspacing=0 onclick="view_content(this, event);" class=hand>
<tr><td height=4 colspan=11></td></tr>
<tr height=25 align="center" onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="">
	<td width="60"><input type=checkbox name=confirmyn value="<?=$data['sno']?>" style="border:0"></td>
	<td width="60"><font class=ver8 color=616161><?=$pg->idx--?></td>
	<td width="70">
	<?php if($data['notice']){?>
	공지
	<?php }else{?>
	<a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,"style='border:1 solid #efefef'",1)?></a>
	<?php }?>
	</td>
	<td align="left" style="line-height:17px">
	<?php	if(!$data['notice'] && $data['goodsno']!=0){?><div style="color:#999999"><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)" style="color:#0074BA;" class=small>[ <?=$data[goodsnm]?> ]</a></div><?php }?>
	<font color=333333><?=$data[subject]?></font> <font class=ver8 color=FF6709>(<?=$data[replecnt]?>)</font> <?if($data['secretIcon']){?><img src="../img/icn_secret.gif" border=0><?}?>
	</td>
	<td width="80">
		<? if($data['m_no'] && $data['m_id']){ ?>
			<?php if($data['dormant_regDate'] == '0000-00-00 00:00:00'){ ?>
				<div><span id="navig" name="navig" m_id="<?=$data[m_id]?>" m_no="<?=$data[m_no]?>"><img src="../img/icon_crmlist.gif" /></span><?getlinkPc080($data['phone'],'phone')?><?getlinkPc080($data['mobile'],'mobile')?></div><div><span style="color:#616161;" class=ver8><?=$data[m_id]?></span></div>
			<?php } else { ?>
				<div style="color:#616161;" class="ver8"><?php echo $data[m_id]; ?><br />(휴면회원)</div>
			<?php } ?>
		<?}else{?>
			<?=$data['m_id']?>
		<?}?>
	</td>
	<td width="80"><font class=ver8 color=333333><?=substr($data[regdt],0,10)?></font></td>
	<td width="50"><?php	if(!$data['notice']){?><a href="javascript:popup2('../board/customer_register.php?mode=qnaReply&sno=<?echo($data['sno'])?>',800,800,1)"><img src="../img/i_reply.gif"></a><?php }?></td>
	<td width="50">
	<?php if(!$data['notice']){?>
	<a href="javascript:popup2('../board/goods_qna_register.php?mode=modify&sno=<?echo($data['sno'])?>',800,800)"><img src="../img/i_edit.gif"></a>
	<?php }else{?>
	<a href="javascript:popup2('../board/goods_qna_notice.php?mode=noticeModify&sno=<?echo($data['sno'])?>',800,600)"><img src="../img/i_edit.gif"></a>
	<?php }?>
	</td>
	<td width="50" class="noline"><a href="javascript:act_delete_case(<?= $i++?>)"><img src="../img/i_del.gif"></a></td>
</tr>
<tr><td height=4 colspan=11></td></tr>
</table>
<div style="display:none;padding:5 10 10 130;"><font color=484848 class="editorArea"><?=($data[contents])?></font></div>
</div>

	<?} else if ( $data[sno] != $data[parent] ){ // 답글?>
<div style="border-top-width:1; border-top-style:dotted; border-top-color:#DCD8D6;">
<table width=100% cellpadding=0 cellspacing=0 onclick="view_content(this, event);" class=hand>
<tr><td height=4 colspan=11></td></tr>
<tr height=25 align="center" onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="">
	<td width="60"><input type=checkbox name=confirmyn value="<?=$data['sno']?>" style="border:0"></td>
	<td width="60"><font class=ver8 color=616161><?=$pg->idx--?></td>
	<td width="70"><img src="../img/btn_reply.gif"></td>
	<td align="left" style="line-height:17px"><font color=333333><?=$data[subject]?></font></td>
	<td width="80"><?=$data[m_id]?></td>
	<td width="80"><font class=ver8 color=333333><?=substr($data[regdt],0,10)?></font></td>
	<td width="50"></td>
	<td width="50"><a href="javascript:popup2('../board/goods_qna_register.php?mode=modify&sno=<?echo($data['sno'])?>',800,800)"><img src="../img/i_edit.gif"></a></td>
	<td width="50" class="noline"><a href="javascript:act_delete_case(<?= $i++?>)"><img src="../img/i_del.gif"></a></td>
</tr>
<tr><td height=4 colspan=11></td></tr>
</table>
<div style="display:none;padding:5 10 10 130;"><font color=484848 class="editorArea"><?=($data[contents])?></font></div>
</div>
	<? } ?>

<? } ?>
<div style="border-bottom-width:1; border-bottom-style:solid; border-bottom-color:#DCD8D6;width:100%;height:1px;font-size:0px;"></div>
<INPUT TYPE="hidden" style="width:300" NAME="nolist">
</form>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div style="float:left">
<img src="../img/btn_allselect_s.gif" alt="전체선택"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'select', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_allreselect_s.gif" alt="선택반전"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'reflect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_alldeselect_s.gif" alt="선택해제"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'deselect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_alldelet_s.gif" alt="선택삭제" border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javaScript:act_delete();"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
</div>
<div style="float:right"><img src="../img/btn_notice_s.gif" alt="공지글등록" border="0" align='absmiddle' style="cursor:hand" onclick="javascript:popup2('../board/goods_qna_notice.php?mode=noticeRegist',800,600)"> <a href="javascript:go_excel()"><img src="../img/btn_download_s.gif" align='absmiddle' /></a></div>
<div style="clear:both;padding-top:15px"></div>

<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>문의제목을 클릭하면 글내용이 열리며, 다시 제목을 클릭하면 내용이 닫히게됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>상품이미지를 클릭</font>하면 새창과 함께 상품상세페이지로 이동합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>작성자를 클릭하시면 회원정보와 함께 회원주문내역 등을 보실 수 있습니다.</td></tr>
</table>
</div>
<script language="javascript">
var preContent;

//2012-01-16 dn 익스플로러 10 스크립트 오류 수정
function view_content(obj, e)
{
	var eventTarget = e.srcElement || e.target;
	if ( eventTarget.tagName == 'A' || eventTarget.tagName == 'IMG' || eventTarget.tagName == 'INPUT' ) return;

	var div = obj.parentNode;

	for (var i=1, m=div.childNodes.length;i<m;i++) {
		if (div.childNodes[i].nodeType != 1) continue;	// text node.
		else if (obj == div.childNodes[ i ]) continue;

		obj = div.childNodes[ i ];
		break;
	}

	if (preContent && obj!=preContent){
		obj.style.display = "block";
		preContent.style.display = "none";
	}
	else if (preContent && obj==preContent) preContent.style.display = ( preContent.style.display == "none" ? "block" : "none" );
	else if (preContent == null ) obj.style.display = "block";

	preContent = obj;
}

function go_excel(){
	if(confirm("검색된 리스트에 대한 게시물을 다운로드 하시겠습니까?")){
		location.href = "../board/goods_qna_excel_list.php?<?=$_SERVER['QUERY_STRING']?>";
	}
}

function checkReplyUse(useSet) {
	if(useSet == "y") document.getElementById('qnaFavoriteReplyNo').disabled = false;
	else document.getElementById('qnaFavoriteReplyNo').disabled = true;
}

function act_delete_case(idx){
	if(confirm("원본글을 삭제하시면, 답변글도 같이 삭제됩니다.\n\n삭제시 정보는 복구되지 않습니다.")) {
		fmList.nolist.value = fmList['confirmyn'][idx].value;
		fmList.action = "../board/goods_qna_indb.php?mode=delete" ;
		fmList.submit() ;
	}
}

function act_delete(){

	if ( PubChkSelect( fmList['confirmyn'] ) == false ){
		alert( "삭제하실 내역을 선택하여 주십시요." );
		return;
	}

	if ( confirm( "원본글을삭제하시면 답변도 같이 삭제되어집니다.\n선택한 아이템을 정말 삭제하시겠습니까?\n삭제 후 복구할 수 없습니다." ) == false ) return;

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
	fmList.action = "../board/goods_qna_indb.php?mode=delete" ;
	fmList.submit() ;
}

window.onload = function() {
	cssRound('MSG01');
	UNM.inner();
	if(document.getElementById('qnaFavoriteReplyUse_n').checked) document.getElementById('qnaFavoriteReplyNo').disabled = true;
};
</script>