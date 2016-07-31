<?

$location = "인터파크 오픈스타일 입점 > 클레임요청내역";
include "../_header.php";
include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".INPK_CLAIM_ITEM." a left join ".INPK_CLAIM." b on a.clmsno=b.clmsno where b.step='r'");

### 변수할당
$clm_tpnms = array( '출고전주문취소', '반품', '교환', '출고전품절주문취소' );
$clm_statnms = array( '요청', '요청철회', '승인', '거부', '요청완료' );

if (!$_GET['page_num']) $_GET['page_num'] = 20; # 페이지 레코드수
$selected['page_num'][$_GET['page_num']] = "selected";

$orderby = ($_GET['sort']) ? $_GET['sort'] : "a.latedt desc"; # 정렬 쿼리
$selected['sort'][$orderby] = "selected";

$selected['skey'][$_GET['skey']] = "selected";
$selected['sgkey'][$_GET['sgkey']] = "selected";

if (!$_GET['dtkind']) $_GET['dtkind'] = 'latedt'; # 처리일
$checked['dtkind'][$_GET['dtkind']] = "checked";

### 목록
$db_table = "
".INPK_CLAIM_ITEM." a
left join ".INPK_CLAIM." b on a.clmsno=b.clmsno
";

$where[] = "b.step='r'";
if ($_GET['sword']){
	$_GET['sword'] = trim($_GET['sword']);
	$t_skey = ($_GET['skey']=="all") ? "concat(b.ordno, clm_rsn_tpnm, clm_rsn_dtl)" : $_GET['skey'];
	$t_skey = ($_GET['skey']=="clm_rsn_tpnm") ? "concat(clm_rsn_tpnm, clm_rsn_dtl)" : $t_skey;
	$where[] = "$t_skey like '%{$_GET['sword']}%'";
}
if ($_GET['sgword']){
	$_GET['sgword'] = trim($_GET['sgword']);
	$where[] = "{$_GET['sgkey']} like '%{$_GET['sgword']}%'";
	$db_table .= " left join ".GD_ORDER_ITEM." c on a.item_sno=c.sno";
}
if ($_GET['clm_tpnm']){
	$where[] = "clm_tpnm in ('".implode("','",$_GET['clm_tpnm'])."')";
	foreach ($_GET['clm_tpnm'] as $v) $checked['clm_tpnm'][$v] = "checked";
}
if ($_GET['clm_statnm']){
	$where[] = "clm_statnm in ('".implode("','",$_GET['clm_statnm'])."')";
	foreach ($_GET['clm_statnm'] as $v) $checked['clm_statnm'][$v] = "checked";
}
if ($_GET['regdt'][0]){
	if (!$_GET['regdt'][1]) $_GET['regdt'][1] = date("Ymd");
	$where[] = "{$_GET[dtkind]} between date_format({$_GET['regdt'][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET['regdt'][1]},'%Y-%m-%d 23:59:59')";
}

$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "*";
$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);

?>

<div class="title title_top">클레임요청내역<span>인터파크로부터 접수된 클레임요청(출고전주문취소/반품/교환) 내역입니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=24')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;" id="goodsInfoBox">
<div><font color="#EA0095"><b>필독!</b></font></div>
<div style="padding-top:2">"배송중" 처리는 안하고 <font color=EA0095>물리적 배송만 한 상태에서 주문취소요청을 승인하면</font> 이후 배송 프로세스를 진행할 수 없게 됩니다.</div>
<div style="padding-top:2">이에 대해서는 판매자에게 책임이 전가되므로 <font color=0074BA>주문취소요청에 대해 승인하기전 배송여부를 반드시 체크</font>하시기 바랍니다.</font></div>
</div>


<!-- 검색조건 : start -->
<form name=frmList onsubmit="return chkForm(this)">

<table class=tb>
<col class=cellC><col class=cellL style="width:250">
<col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>검색 (통합)</td>
	<td>
	<select name=skey>
	<option value="all"> = 통합검색 =
	<option value="b.ordno" <?=$selected['skey']['b.ordno']?>> 주문번호
	<option value="clm_rsn_tpnm" <?=$selected['skey']['clm_rsn_tpnm']?>>요청사유
	</select>
	<input type=text name=sword value="<?=$_GET['sword']?>">
	</td>
	<td><font class=small1>상품검색 (선택)</td>
	<td>
	<select name=sgkey>
	<option value="goodsnm" <?=$selected['sgkey']['goodsnm']?>> 상품명
	<option value="brandnm" <?=$selected['sgkey']['brandnm']?>> 브랜드
	<option value="maker" <?=$selected['sgkey']['maker']?>> 제조사
	<option value="a.goodsno" <?=$selected['sgkey']['a.goodsno']?>>고유번호
	</select>
	<input type=text name=sgword value="<?=$_GET['sgword']?>">
	</td>
</tr>
<tr>
	<td>클레임요청구분</td>
	<td class=noline colspan="3">
	<? foreach ($clm_tpnms as $v){ ?>
	<input type=checkbox name="clm_tpnm[]" value="<?=$v?>" <?=$checked['clm_tpnm'][$v]?>><?=$v?>
	<? } ?>
	</td>
</tr>
<tr>
	<td>요청상태</td>
	<td class=noline colspan="3">
	<? foreach ($clm_statnms as $v){ ?>
	<input type=checkbox name="clm_statnm[]" value="<?=$v?>" <?=$checked['clm_statnm'][$v]?>><?=$v?>
	<? } ?>
	</td>
</tr>
<tr>
	<td><font class=small1>처리일자</td>
	<td colspan=3>
	<span class="noline small1" style="color:5C5C5C; margin-right:20px;">
	<input type=radio name=dtkind value="latedt" <?=$checked['dtkind']['latedt']?>>처리일
	<input type=radio name=dtkind value="clm_dt" <?=$checked['dtkind']['clm_dt']?>>요청일
	</span>
	<input type=text name=regdt[] value="<?=$_GET['regdt'][0]?>" onclick="calendar()" size=12> -
	<input type=text name=regdt[] value="<?=$_GET['regdt'][1]?>" onclick="calendar()" size=12>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<table width=100%>
<tr>
	<td class=pageInfo><font class=ver8>
	총 <b><?=number_format($total)?></b>개, 검색 <b><?=number_format($pg->recode[total])?></b>개, <b><?=number_format($pg->page[now])?></b> of <?=number_format($pg->page[total])?> Pages
	</td>
	<td align=right>
	<select name="sort" onchange="this.form.submit();">
	<option value="latedt desc" <?=$selected[sort]['latedt desc']?>>- 처리일 정렬↑</option>
	<option value="latedt asc" <?=$selected[sort]['latedt asc']?>>- 처리일 정렬↓</option>
	<option value="clm_dt desc" <?=$selected[sort]['clm_dt desc']?>>- 요청일 정렬↑</option>
	<option value="clm_dt asc" <?=$selected[sort]['clm_dt asc']?>>- 요청일 정렬↓</option>
    <optgroup label="------------"></optgroup>
	<option value="ordno desc" <?=$selected[sort]['ordno desc']?>>- 주문번호 정렬↑</option>
	<option value="ordno asc" <?=$selected[sort]['ordno asc']?>>- 주문번호 정렬↓</option>
	</select>&nbsp;
	<select name=page_num onchange="this.form.submit()">
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

</form>
<!-- 검색조건 : end -->


<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th><font class=small1><b>번호</th>
	<th><font class=small1><b>클레임요청구분</th>
	<th><font class=small1><b>주문번호</th>
	<th><font class=small1><b>상품명</th>
	<th><font class=small1><b>요청수량</th>
	<th><font class=small1><b>요청사유</th>
	<th><font class=small1><b>요청일</th>
	<th><font class=small1><b>처리일</th>
	<th><font class=small1><b>요청상태</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=120><col width=120><col><col width=60><col width=100><col width=65 span=2><col width=80>
<?
while (is_resource($res) && $data=$db->fetch($res))
{
	$gItem = $db->fetch("select goodsnm, opt1, opt2, addopt from ".GD_ORDER_ITEM." where sno='{$data['item_sno']}'");
	$goodsnm = $gItem['goodsnm'];
	if ($gItem['opt1']) $goodsnm .= "[{$gItem['opt1']}" . ($gItem['opt2'] ? "/{$gItem['opt2']}" : "") . "]";
	if ($gItem['addopt']) $goodsnm .= "<div>[" . str_replace("^","] [",$gItem[addopt]) . "]</div>";
?>
<tr><td height=4 colspan=12></td></tr>
<tr height=18>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></font></td>
	<td align=center><font class="small"><?=$data['clm_tpnm']?></font></td>
	<td>
	<a href="../order/view.php?ordno=<?=$data['ordno']?>"><font class=ver81 color=0074BA><b><?=$data['ordno']?></b></font></a>
	<a href="javascript:popup('../order/popup.order.php?ordno=<?=$data['ordno']?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align=absmiddle></a>
	</td>
	<td><font class=small><?=$goodsnm?></font></td>
	<td align=center><font class="ver81" color="#444444"><b><?=number_format($data['clm_qty'])?></b></font></td>

<? if ($data['clm_rsn_dtl'] == ''){ ?>
	<td align=center><font class="small" color="#444444"><?=$data['clm_rsn_tpnm']?></font></td>
<? } else { ?>
	<td align=center style="cursor:default" onmouseover="this.getElementsByTagName('div')[0].style.display='block';" onmouseout="this.getElementsByTagName('div')[0].style.display='none';">
	<font class="small" color="#0074BA"><?=$data['clm_rsn_tpnm']?></font>
	<div style="position:relative; display:none;">
		<div style="position:absolute; background-color:#eeeeee; border:solid 1px #dddddd; filter:Alpha(Opacity=90); opacity:0.9; padding:5; top:0px; width:200px; text-align:left;">
			<?=$data['clm_rsn_dtl']?>
		</div>
	</div>
	</td>
<? } ?>

	<td align=center><font class="small" color="#444444"><?=substr($data['clm_dt'],2,8)?></font></td>
	<td align=center><a href="javascript:popupLayer('popup.log.php?itmsno=<?=$data['itmsno']?>')"><font class="small" color="#0074BA"><u><?=($data['clm_statnm'] == '요청' ? '' : substr($data['latedt'],2,8))?></u></font></a></td>
	<td align=center>
<? if ($data['clm_statnm'] == '요청'){ ?>
	<a href="../order/view.php?ordno=<?=$data['ordno']?>"><font class=small color=#E97D00><u><b><?=$data['clm_statnm']?></b></u></font></a>
<? } else { ?>
	<font class=small color=0074BA><b><?=$data['clm_statnm']?></b></font>
<? } ?>
	</td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">클레임요청구분 안내
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>출고전주문취소 : 배송(배송중) 전에 구매자가 주문취소를 요청한 클레임입니다.</li>
<li>반품 : 배송(배송중) 후에 구매자가 반품을 요청한 클레임입니다.</li>
<li>교환 : 배송(배송중) 후에 구매자가 교환을 요청한 클레임입니다.</li>
<li>출고전품절주문취소 : 배송(배송중) 전에 판매자가 주문취소를 요청한 클레임입니다.</li>
</ol>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">요청상태 안내
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>요청 : 구매자가 클레임을 요청한 상태로, 판매자는 요청에 대해 <b>[승인]</b>하거나 <b>[거부]</b> 해야 합니다.</li>
<li>요청철회 : 구매자가 요청을 철회한 상태입니다.</li>
<li>승인 : 구매자 요청에 대해 판매자가 승인한 상태입니다.</li>
<li>거부 : 구매자 요청에 대해 판매자가 거부한 상태입니다.</li>
<li>요청완료 : 판매자가 출고전품절주문취소를 요청한 상태입니다.</li>
</ol>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">요청사유에 마우스를 올리시면 상세사유를 확인할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">처리일을 클릭하시면 인터파크 처리로그를 확인할 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>