<?

include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".GD_MEMBER_QNA.""); # 총 레코드수

### 변수할당
$itemcds = codeitem( 'question' ); # 질문유형

if (!$_GET[page_num]) $_GET[page_num] = 20; # 페이지 레코드수
$selected[page_num][$_GET[page_num]] = "selected";
$cfg['memberQnaFavoriteReplyUse'] = (!$cfg['memberQnaFavoriteReplyUse']) ? "n" : $cfg['memberQnaFavoriteReplyUse'];

$orderby = ($_GET[sort]) ? $_GET[sort] : "parent desc, sno asc"; # 정렬 쿼리
$selected[sort][$orderby] = "selected";

$selected[skey][$_GET[skey]] = "selected";
$selected[sitemcd][$_GET[sitemcd]] = "selected";
$selected["memberQnaFavoriteReplyNo"][$cfg['memberQnaFavoriteReplyNo']]	= "selected";
$checked["memberQnaFavoriteReplyUse"][$cfg['memberQnaFavoriteReplyUse']]	= "checked";

### 검색조건
if ($_GET[skey] && $_GET[sword])
{
	if ($_GET[skey]== 'all') $subwhere[] = "concat( subject, contents, ifnull(m_id, '') ) like '%$_GET[sword]%'";
	else $subwhere[] = "$_GET[skey] like '%$_GET[sword]%'";
}
if ($_GET[sitemcd] <> '' && $_GET[sitemcd] <> 'all') $subwhere[] = "itemcd='" . $_GET[sitemcd] . "'"; # 분류검색
if ($_GET[sregdt][0] && $_GET[sregdt][1]) $subwhere[] = "a.regdt between date_format({$_GET[sregdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[sregdt][1]},'%Y-%m-%d 23:59:59')";

if (count($subwhere))
{
	$parent = array();
	$res = $db->query( "select parent from ".GD_MEMBER_QNA." a left join ".GD_MEMBER." b on a.m_no=b.m_no ".$subtable." where " . implode(" and ", $subwhere) );
	while ( $row = $db->fetch( $res ) ) $parent[] = $row['parent'];
	$parent = array_unique ($parent);
	if ( count( $parent ) ) $where[] = "a.parent in ('" . implode( "','", $parent ) . "')";
	else $where[] = "a.parent in ('0')";
}

### 목록
$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "distinct sno, parent, itemcd, ordno, subject, contents, regdt, m_no, notice ";
$db_table = GD_MEMBER_QNA." AS a";
$pg->setQuery($db_table,$where,"notice desc, ".$orderby);
$pg->exec();

$res = $db->query($pg->query);

$replyQuery = "SELECT sno, subject FROM ".GD_GOODS_FAVORITE_REPLY." WHERE customerType = 'memberQna' ORDER BY regdt DESC";
$replyRes = $db->query($replyQuery);
$replyTotal = $db->count_($replyRes);
?>
<?getjskPc080();?>

<form name=frmList>
<input type="hidden" name="sort" id="sort" value="<?=$_GET['sort']?>" />
<input type="hidden" name="page_num" value="<?=$_GET['page_num']?>" />
<? if ($m_no){ ?><input type="hidden" name="m_no" value="<?=$m_no?>" /><? } ?>
<div class="title title_top">1:1 문의관리<span>1:1문의를 통해 들어온 고객문의를 관리할 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>키워드검색전송</td>
	<td colspan="3">
	<select name="skey">
	<option value="all" <?=$selected[skey]['all']?>> 통합검색 </option>
	<option value="subject" <?=$selected[skey]['subject']?>> 제목 </option>
	<option value="contents" <?=$selected[skey]['contents']?>> 문의내용 </option>
	<option value="m_id" <?=$selected[skey]['m_id']?>> 작성자 </option>
	</select> <input type="text" NAME="sword" value="<?=$_GET['sword']?>" class=line>
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
<tr>
	<td>질문유형</td>
	<td>
		<select name="sitemcd">
		<option value="all" <?=$selected[sitemcd]['all']?>> - 전체 - </option>
		<?foreach ( codeitem('question') as $k => $v ){?>
		<option value='<?=$k?>' <?=$selected[sitemcd][$k]?>><?=$v?></option>
		<?}?>
		</select>
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>
</form>

<? if ($crm_view != true) { ?>
<form name="fmSet" method="post" action="../board/customer_indb.php?mode=replySet">
<input type="hidden" name="sort" value="<?=$_GET['sort']?>">
<input type="hidden" name="page_num" value="<?=$_GET['page_num']?>">
<div class="title title_top">자주쓰는 답변 <span>답변쓰기 작성시에 미리 입력해놓은 자주쓰는 답변을 자동/수동으로 불러올 수 있습니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=6')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>
<table class="tb" style="margin-bottom:10px;">
<col class="cellC"><col class="cellL">
<tr>
	<td>자주쓰는 답변 설정</td>
	<td class="noline">
		<div>
			<input type="radio" name="memberQnaFavoriteReplyUse" id="memberQnaFavoriteReplyUse_n" value="n" onclick="checkReplyUse('n')" <?=$checked["memberQnaFavoriteReplyUse"]['n']?> /> 수동
			<input type="radio" name="memberQnaFavoriteReplyUse" id="memberQnaFavoriteReplyUse_y" value="y" onclick="checkReplyUse('y')" <?=$checked["memberQnaFavoriteReplyUse"]['y']?> /> 자동
			<select name="memberQnaFavoriteReplyNo" id="memberQnaFavoriteReplyNo" style="margin:0px 10px;">
<? if(!$replyTotal) { ?>
				<option value="">자주쓰는 답변을 입력해 주세요.</option>
<? } ?>
<? while($replyData=$db->fetch($replyRes)) { ?>
				<option value="<?=$replyData['sno']?>" <?= $selected["memberQnaFavoriteReplyNo"][$replyData['sno']]?>><?=strcut($replyData['subject'], 40)?></option>
<? } ?>
			</select>
			<a href="javascript:popup2('../board/customer_reply.php?type=memberQna',800,800,1)"><img src="../img/icon_repeatqna.gif" align="absmiddle" /></a><input type="image" src="../img/btn_save2.gif" border="0" align="absmiddle" style="margin-left:20px;" /><br />
		</div>
		<div class="extext" style="margin:5px 0px;">* <b>자동</b>을 선택하시면 설정한 답변이 답변쓰기 팝업 창에 자동으로 입력되어 열립니다.</div>
	</td>
</tr>
</table>
</form>
<? } ?>

<div style="padding-top:5px"></div>

<table width=100%>
<tr>
	<td class=pageInfo><font class=ver8>
	총 <b><?=number_format($total)?></b>개, 검색 <b><?=number_format($pg->recode[total])?></b>개, <b><?=number_format($pg->page[now])?></b> of <?=number_format($pg->page[total])?> Pages
	</td>
	<td align=right>
	<select name="sort" onchange="$('sort').value=this.value; frmList.submit();">
	<option value="parent desc, sno asc" <?=$selected[sort]['parent desc, sno asc']?>>- 기본 정렬</option>
	<optgroup label="----------------"></optgroup>
	<option value="regdt desc" <?=$selected[sort]['regdt desc']?>>- 등록일 정렬↑</option>
	<option value="regdt asc" <?=$selected[sort]['regdt asc']?>>- 등록일 정렬↓</option>
    <optgroup label="----------------"></optgroup>
	<option value="subject desc" <?=$selected[sort]['subject desc']?>>- 제목 정렬↑</option>
	<option value="subject asc" <?=$selected[sort]['subject asc']?>>- 제목 정렬↓</option>
	</select>&nbsp;
	<select name=page_num onchange="frmList.page_num.value=this.value; frmList.submit()">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>개 출력
	<? } ?>
	</select>
	</td>
</tr>
</table>

<form method="post" action="" name="fmList">
<table width=100% cellpadding=0 cellspacing=0>
<tr><td class=rnd colspan=11></td></tr>
<tr class=rndbg>
	<th width="60" onclick="javascript:PubAllSordes( 'reflect', fmList['confirmyn'] );" style="cursor:pointer">선택</th>
	<th width="60">번호</th>
	<th>제목</th>
	<th width="80">질문유형</th>
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

	list( $data[m_id], $data[dormant_regDate] ) = $db->fetch("select m_id, dormant_regDate from ".GD_MEMBER." where m_no='$data[m_no]'" );

	if ( $data[sno] == $data[parent] ){ // 질문
		$data[itemcd] = $itemcds[ $data[itemcd] ];
		list( $data[replecnt] ) = $db->fetch("select count(*) from ".GD_MEMBER_QNA." where sno != parent and parent='$data[sno]'");
		list( $data[ordercnt] ) = $db->fetch("select count(*) from ".GD_ORDER." where ordno='$data[ordno]'" );
	}

	$pg->idx--;

	$m_id = '';
	if($data[m_id]){
		if($data[dormant_regDate] == '0000-00-00 00:00:00'){
			$m_id = "<span id='navig' name='navig' m_id='".$data[m_id]."' m_no='".$data[m_no]."'><font color=0074BA class=ver8><strong>".$data[m_id]."</strong></font></span>";
		}
		else {
			$m_id = "<font color='#0074BA' class='ver8'><strong>" . $data['m_id'] . "</strong><br />(휴면회원)</span>";
		}
	}
	else {
		$m_id = '';
	}
	?>

	<?if ( $data[sno] == $data[parent] ){ // 질문 or 공지?>
<div style="border-top-width:1; border-top-style:solid; border-top-color:#DCD8D6;">
<table width=100% cellpadding=0 cellspacing=0 onclick="view_content(this, event);" class=hand>
<tr><td height=4 colspan=11></td></tr>
<tr height=25 align="center" onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="">
	<td width="60" class="noline"><input type=checkbox name=confirmyn value="<?=$data['sno']?>"></td>
	<td width="60"><font class=ver8 color=616161><?=($data['notice']) ? '공지' : $pg->idx?></td>
	<td align="left" style="line-height:17px"><font color=333333><?=$data[subject]?></font> <font class=ver8 color=FF6709>(<?=$data[replecnt]?>)</font></td>
	<td width="80" align="center"><font class=small color=444444><?=$data[itemcd]?></font></td>
	<td width="80"><?php echo $m_id; ?></td>
	<td width="80"><font class=ver8 color=333333><?=substr($data[regdt],0,10)?></font></td>
	<td width="50">
	<? if(!$data['notice']){?>
	<a href="javascript:popup2('../board/customer_register.php?mode=memberQnaReply&sno=<?echo($data['sno'])?>',800,800,1)"><img src="../img/i_reply.gif"></a>
	<? } ?>
	</td>
	<td width="50"><a href="javascript:popup2('../board/member_qna_register.php?mode=modify&sno=<?echo($data['sno'])?>',800,800)"><img src="../img/i_edit.gif"></a></td>
	<td width="50" class="noline"><a href="javascript:act_delete_case(<?= $i++?>)"><img src="../img/i_del.gif"></a></td>
</tr>
<tr><td height=4 colspan=11></td></tr>
</table>
<div style="display:none;padding:5 10 10 63;">
<?if( $data['ordno'] != '0' && $data[ordercnt] == 1 ){?>
<div><font class=small color=444444>[주문번호 <a href="javascript:popup('../order/popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><font class=ver81 color=0074BA><?=$data['ordno']?></font></a><a href="javascript:popup('../order/popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/btn_vieworder.gif" border=0 align=absmiddle hspace=2></a>]</font></div>
<?} else if( $data['ordno'] != '0' && $data[ordercnt] == 0 ){?>
<div><font class=small color=444444>[주문번호 <a href="javascript:alert('주문 데이타가 존재하지 않습니다.')"><font class=ver81 color=0074BA><?=$data['ordno']?></font></a>]</font></div>
<?}?>
<div><font color=484848><?=nl2br($data[contents])?></font></div>
</div>
</div>

	<?} else if ( $data[sno] != $data[parent] ){ // 답글?>
<div style="border-top-width:1; border-top-style:dotted; border-top-color:#DCD8D6;">
<table width=100% cellpadding=0 cellspacing=0 onclick="view_content(this, event);" class=hand>
<tr><td height=4 colspan=11></td></tr>
<tr height=25 align="center" onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="">
	<td width="60" class="noline"><input type=checkbox name=confirmyn value="<?=$data['sno']?>"></td>
	<td width="60"><font class=ver8 color=616161><?=$pg->idx?></td>
	<td align="left" style="line-height:17px"><img src="../img/btn_reply.gif" border=0 align=absmiddle><font color=333333><?=$data[subject]?></font></td>
	<td width="80" align="left"></td>
	<td width="80"><?php echo $m_id; ?></td>
	<td width="80"><font class=ver8 color=333333><?=substr($data[regdt],0,10)?></font></td>
	<td width="50"></td>
	<td width="50"><a href="javascript:popup2('../board/member_qna_register.php?mode=modify&sno=<?echo($data['sno'])?>',800,800)"><img src="../img/i_edit.gif"></a></td>
	<td width="50" class="noline"><a href="javascript:act_delete_case(<?= $i++?>)"><img src="../img/i_del.gif"></a></td>
</tr>
<tr><td height=4 colspan=11></td></tr>
</table>
<div style="display:none;padding:5 10 10 97;"><font color=484848><?=nl2br($data[contents])?></font></div>
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
<div style="float:right">
	<? if ($crm_view != true) { ?>
	<img src="../img/btn_notice_s.gif" alt="공지글등록" border="0" align='absmiddle' style="cursor:hand" onclick="javascript:popup2('../board/member_qna_notice.php?mode=noticeRegist',800,600)">
	<a href="javascript:go_excel()"><img src="../img/btn_download_s.gif" align='absmiddle' /></a>
	<? } ?>
</div>

<div style="height:30px"></div>

<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>문의제목을 클릭하면 글내용이 열리며, 다시 제목을 클릭하면 내용이 닫히게됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>작성자를 클릭하시면 회원정보와 함께 회원주문내역 등을 보실 수 있습니다.</td></tr>
</table>
</div>
<script language="javascript">
var preContent;

//2012-01-16 dn 익스플로러 10 스크립트 오류 수정
function view_content(obj, e)
{
	if ( document.all && ( e.srcElement.tagName == 'A' || e.srcElement.tagName == 'IMG' || e.srcElement.tagName == 'INPUT' ) ) return;
	else if ( !document.all && ( e.target.tagName == 'A' || e.target.tagName == 'IMG' || e.srcElement.tagName == 'INPUT' ) ) return;

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
		location.href = "member_qna_excel_list.php?<?=$_SERVER['QUERY_STRING']?>";
	}
}

function checkReplyUse(useSet) {
	if(useSet == "y") document.getElementById('memberQnaFavoriteReplyNo').disabled = false;
	else document.getElementById('memberQnaFavoriteReplyNo').disabled = true;
}

function act_delete_case (idx){
	if(!confirm("원본글을 삭제 하시면, 답변글도 같이 삭제됩니다.\n삭제시 정보는 복구되지 않습니다.")) return;
	fmList.nolist.value = fmList['confirmyn'][idx].value;
	fmList.action = "../board/member_qna_indb.php?mode=delete" ;
	fmList.submit() ;
}

function act_delete(){

	if ( PubChkSelect( fmList['confirmyn'] ) == false ){
		alert( "삭제하실 내역을 선택하여 주십시요." );
		return;
	}

	if ( confirm( "원본글을 삭제 하시면, 답변글도 같이 삭제됩니다.\n삭제시 정보는 복구되지 않습니다." ) == false ) return;

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
	fmList.action = "../board/member_qna_indb.php?mode=delete" ;
	fmList.submit() ;
}

window.onload = function() {
	cssRound('MSG01');
	UNM.inner();
	if(document.getElementById('memberQnaFavoriteReplyUse_n').checked) document.getElementById('memberQnaFavoriteReplyNo').disabled = true;
}
</script>
