<?
/**
	2011-01-13 by x-ta-c

	무통장 거래건 中 미입금 주문건을 조회하여, 고객에게 입금요청 SMS를 발송 또는 입금확인 처리를 할 수 있다.
 */

$location = "주문관리 > 주문리스트";
include "../_header.php";
@include "../../conf/config.pay.php";
include "../../lib/page.class.php";
@include "../../conf/phone.php";

// 현재 날짜 -> 주문일로 부터 경과일 수를 계산하기 위함.
$today = mktime(0,0,0,date('m'), date('d'), date('Y'));

$_GET[dtkind] = 'orddt'; # 처리일 => 주문일자로 고정
$checked[dtkind][$_GET[dtkind]] = $checked[settlekind][$_GET[settlekind]] = $checked[escrowyn][$_GET[escrowyn]] = $checked[eggyn][$_GET[eggyn]] = $checked[mobilepay][$_GET[mobilepay]] = $checked[sugi][$_GET[sugi]] = "checked";

$selected[skey][$_GET[skey]] = "selected";
$selected[sgkey][$_GET[sgkey]] = "selected";

$db_table = "".GD_ORDER." a left join ".GD_MEMBER." b on a.m_no=b.m_no";
$orderby = "a.ordno desc";


/**
	미입금내역은 무통장거래시에만 존재하므로 고정.
 */
$where[] = "settlekind = 'a'";

if ($_GET[sword]){
	$_GET[sword] = trim($_GET[sword]);
	$t_skey = ($_GET[skey]=="all") ? "concat( a.ordno, nameOrder, nameReceiver, bankSender, ifnull(m_id,'') )" : $_GET[skey];
	$where[] = "$t_skey like '%$_GET[sword]%'";
}
if ($_GET[sgword]){
	$_GET[sgword] = trim($_GET[sgword]);
	$where[] = "{$_GET[sgkey]} like '%$_GET[sgword]%'";
	$db_table .= " left join ".GD_ORDER_ITEM." c on a.ordno=c.ordno";
	$tmp_query = "group by a.ordno";
}
if ($_GET[regdt][0]){
	if (!$_GET[regdt][1]) $_GET[regdt][1] = date("Ymd");
	$where[] = "{$_GET[dtkind]} between date_format({$_GET[regdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[regdt][1]},'%Y-%m-%d 23:59:59')";
}
if ($_GET[sugi] == "online") $where[] = "a.inflow != 'sugi'";
elseif ($_GET[sugi] == "sugi") $where[] = "a.inflow = 'sugi'";

/**
	접수처리건 외에는 리스팅될 필요가 없음.
 */
$where2[] = "(step=0 and step2 = 0)";


if ($_GET[cbyn] == 'Y'){
	$checked[cbyn] = "checked";
	$where[] = "cbyn = 'Y'";
}

if ($_GET['aboutcoupon'] == '1'){
	$checked['aboutcoupon'] = "checked";
	$where[] = "a.about_coupon_flag = '1'";
}

if($_GET[chk_inflow]){
	foreach ($_GET[chk_inflow] as $v){
		$checked[chk_inflow][$v] = "checked";
		if ( $v == 'naver_price' ) $where3[] = "inflow in ('naver_elec', 'naver_bea', 'naver_milk')";
		else $where3[] = "inflow='$v'";
	}
}

if ($where2) $where[] = "(".implode(" or ",$where2).")";
if ($where3) $where[] = "(".implode(" or ",$where3).")";

if ($_GET[escrowyn]) $where[] = "escrowyn='$_GET[escrowyn]'";
if ($_GET[eggyn]) $where[] = "eggyn='$_GET[eggyn]'";
if ($_GET[mobilepay]) $where[] = "mobilepay='$_GET[mobilepay]'";

if(!$cfg['orderPageNum'])$cfg['orderPageNum'] = 15;
$pg = new Page($_GET[page],$cfg['orderPageNum']);

$pg->field = "b.*,a.*";
$pg->cntQuery = sprintf("select count(distinct a.ordno) from %s where %s", $db_table, implode(' and ', $where));
$pg->setQuery($db_table,$where,$orderby,$tmp_query);
$pg->exec();
$res = $db->query($pg->query);
?>

<script>
function fnRequestBanking() {

	var f = document.frmList;

	if (f.processType.value == 'sms')
	{
		f.mode.value = 'requestSMS';
	}
	else if (f.processType.value == 'confirm') {
		f.mode.value = 'chgAllBanking';
	}
	else {
		alert('처리 방법을 선택해 주세요.');
		return;
	}

	// 처리할 주문건 체크.
	var cnt = 0, chk = document.getElementsByName('chk[]');

	for (var i =0;i<chk.length ;i++)
		if (chk[i].checked == true) cnt++;

	if (cnt == 0) {
		alert('처리할 주문건을 선택해 주세요.');
		return;
	}

	f.submit();
}

function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFA1" : row.getAttribute('bg');
}

function chkBoxAll(El,mode)
{
	if (!El || !El.length) return;
	for (i=0;i<El.length;i++){
		if (El[i].disabled) continue;
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		iciSelect(El[i]);
	}
}
</script>

<div class="title title_top" style="position:relative;padding-bottom:15px">입금대기 리스트<span>무통장 거래건의 입금대기자 명단을 확인하고 주문상태를 변경합니다</span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=14')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a>
</div>
<form>
<input type=hidden name=mode value="<?=$_GET[mode]?>">

<table class=tb>
<col class=cellC><col class=cellL style="width:250">
<col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>주문검색 (통합)</td>
	<td>
	<select name=skey>
	<option value="all"> = 통합검색 =
	<option value="a.ordno" <?=$selected[skey][a.ordno]?>> 주문번호
	<option value="nameOrder" <?=$selected[skey][nameOrder]?>> 주문자명
	<option value="nameReceiver" <?=$selected[skey][nameReceiver]?>> 수령자명
	<option value="bankSender" <?=$selected[skey][bankSender]?>> 입금자명
	<option value="m_id" <?=$selected[skey][m_id]?>> 아이디
	</select>
	<input type=text name=sword value="<?=$_GET[sword]?>" class=line>
	</td>
	<td><font class=small1>상품검색 (선택)</td>
	<td>
	<select name=sgkey>
	<option value="goodsnm" <?=$selected[sgkey][goodsnm]?>> 상품명
	<option value="brandnm" <?=$selected[sgkey][brandnm]?>> 브랜드
	<option value="maker" <?=$selected[sgkey][maker]?>> 제조사
	</select>
	<input type=text name=sgword value="<?=$_GET[sgword]?>" class=line>
	</td>
</tr>
<tr>
	<td><font class=small1>접수유형</td>
	<td colspan=3 class=noline><font class=small1 color=5C5C5C>
	<input type=radio name=sugi value="" <?=$checked[sugi]['']?>>전체
	<input type=radio name=sugi value="online" <?=$checked[sugi]['online']?>>온라인접수
	<input type=radio name=sugi value="sugi" <?=$checked[sugi]['sugi']?>>수기접수
	</td>
</tr>
<tr>
	<td><font class=small1>주문일</td>
	<td colspan=3>
	<input type=text name=regdt[] value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" size=12 class=line> -
	<input type=text name=regdt[] value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" size=12 class=line>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td><font class=small1>모바일샵</td>
	<td colspan=3 class=noline><font class=small1 color=5C5C5C>
	<input type=radio name=mobilepay value="" <?=$checked[mobilepay]['']?>>전체
	<input type=radio name=mobilepay value="n" <?=$checked[mobilepay]['n']?>>일반결제
	<input type=radio name=mobilepay value="y" <?=$checked[mobilepay]['y']?>>모바일샵결제
	</td>
</tr>
<tr>
	<td><font class=small1>에스크로</td>
	<td class=noline><font class=small1 color=5C5C5C>
	<input type=radio name=escrowyn value="" <?=$checked[escrowyn]['']?>>전체
	<input type=radio name=escrowyn value="n" <?=$checked[escrowyn]['n']?>>일반결제
	<input type=radio name=escrowyn value="y" <?=$checked[escrowyn]['y']?>>에스크로 <img src="../img/btn_escrow.gif" align=absmiddle>
	</td>
	<td><font class=small1>전자보증보험 <a href="../basic/egg.intro.php"><img src="../img/btn_question.gif"></a></td>
	<td class=noline><font class=small1 color=5C5C5C>
	<input type=radio name=eggyn value="" <?=$checked[eggyn]['']?>>전체
	<input type=radio name=eggyn value="n" <?=$checked[eggyn]['n']?>>미발급
	<input type=radio name=eggyn value="f" <?=$checked[eggyn]['f']?>>발급실패
	<input type=radio name=eggyn value="y" <?=$checked[eggyn]['y']?>>발급완료 <img src="../img/icon_guar_order.gif">
	</td>
</tr>
<tr>
	<td><font class=small1>제휴처주문 <a href="../naver/naver.php"><img src="../img/btn_question.gif"></a></td>
	<td colspan=3 class=noline><font class=small1 color=5C5C5C>
	<input type=checkbox name=chk_inflow[] value="naver" <?=$checked[chk_inflow][naver]?>><img src="../img/inflow_naver.gif" align=absmiddle> 네이버 쇼핑&nbsp;
	<input type=checkbox name=chk_inflow[] value="yahoo_fss" <?=$checked[chk_inflow][yahoo_fss]?>><img src="../img/inflow_yahoo_fss.gif" align=absmiddle> 야후패션소호&nbsp;
	<input type=checkbox name=chk_inflow[] value="interpark" <?=$checked[chk_inflow][interpark]?>><img src="../img/inflow_interpark.gif" align=absmiddle> 인터파크샵플러스&nbsp;
	<input type=checkbox name=chk_inflow[] value="openstyle" <?=$checked[chk_inflow][openstyle]?>><img src="../img/inflow_interpark.gif" align=absmiddle> 인터파크오픈스타일&nbsp;
	<input type=checkbox name=chk_inflow[] value="openstyleOutlink" <?=$checked[chk_inflow][openstyleOutlink]?>><img src="../img/inflow_interpark.gif" align=absmiddle> 인터파크오픈스타일아웃링크<br>
	<input type=checkbox name=chk_inflow[] value="naver_price" <?=$checked[chk_inflow][naver_price]?>><img src="../img/inflow_naver_price.gif" align=absmiddle> 네이버가격비교&nbsp;
	<input type=checkbox name=chk_inflow[] value="danawa" <?=$checked[chk_inflow][danawa]?>><img src="../img/inflow_danawa.gif" align=absmiddle> 다나와&nbsp;
	<input type=checkbox name=chk_inflow[] value="mm" <?=$checked[chk_inflow][mm]?>><img src="../img/inflow_mm.gif" align=absmiddle> 마이마진&nbsp;
	<input type=checkbox name=chk_inflow[] value="bb" <?=$checked[chk_inflow][bb]?>><img src="../img/inflow_bb.gif" align=absmiddle> 베스트바이어&nbsp;
	<input type=checkbox name=chk_inflow[] value="omi" <?=$checked[chk_inflow][omi]?>><img src="../img/inflow_omi.gif" align=absmiddle> 오미&nbsp;
	<input type=checkbox name=chk_inflow[] value="enuri" <?=$checked[chk_inflow][enuri]?>><img src="../img/inflow_enuri.gif" align=absmiddle> 에누리&nbsp;
	<input type=checkbox name=chk_inflow[] value="yahoo" <?=$checked[chk_inflow][yahoo]?>><img src="../img/inflow_yahoo.gif" align=absmiddle> 야후가격비교&nbsp;
	<input type=checkbox name=chk_inflow[] value="yahooysp" <?=$checked[chk_inflow][yahooysp]?>><img src="../img/inflow_yahooysp.gif" align=absmiddle> 야후전문몰<br />
	<input type=checkbox name=chk_inflow[] value="auctionos" <?=$checked[chk_inflow][auctionos]?>><img src="../img/inflow_auctionos.gif" align=absmiddle> 옥션어바웃&nbsp;
	<input type=checkbox name=chk_inflow[] value="daumCpc" <?=$checked['chk_inflow']['daumCpc']?>><img src="../img/inflow_daumCpc.gif" align="absmiddle"> 다음쇼핑하우&nbsp;
	<input type=checkbox name=chk_inflow[] value="cywordScrap" <?=$checked['chk_inflow']['cywordScrap']?>><img src="../img/inflow_cywordScrap.gif" align="absmiddle"> 싸이월드스크랩&nbsp;
	<input type=checkbox name=chk_inflow[] value="naverCheckout" <?=$checked['chk_inflow']['naverCheckout']?>><img src="../img/inflow_naverCheckout.gif" align="absmiddle"> 네이버체크아웃
	</td>
</tr>
</table>
<div class="button_top">
<input type=image src="../img/btn_search2.gif">
</div>
</form>

<div style="padding-top:15px"></div>

<form name=frmList method=post action="indb.php">
<input type=hidden name=mode value="chgAllBanking">
<input type=hidden name=case value="1"><!-- 입금확인 -->

<table width=100% cellpadding=0 cellspacing=0 border=0>
<col width=25><col width=30><col width=100><col width=100><col width=150><col><col width=95><col width=50><col width=50><col><col width=55>
<tr><td class=rnd colspan=20></td></tr>
<tr class=rndbg>
	<th><a href="javascript:void(0)" onClick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class=white>선택</a></th>
	<th>번호</th>
	<th>주문일시</th>
	<th>경과일자</th>
	<th colspan=2>주문번호 (주문상품)</th>
	<th>주문자</th>
	<th>받는분</th>
	<th>결제</th>
	<th>금액</th>
	<th colspan=6>처리상태</th>
</tr>
<tr><td class=rnd colspan=20></td></tr>
<?
$idx_grp = 0;
$idx = $pg->idx; $pr = 1;
while ($data=$db->fetch($res)){
	unset($supply); unset($selected);
	$bgcolor = ($data[step2]) ? "#F0F4FF" : "#ffffff";
	$disabled = ($data[step2]) ? "disabled" : "";

	$stepMsg = $step = getStepMsg($data[step],$data[step2],$data[ordno]);

	if(strlen($step) > 10) $step = substr($step,10);

	list($cntDv) = $db->fetch("SELECT count(*) cntDv FROM gd_order_item WHERE ordno='$data[ordno]' and dvcode != '' and dvno != ''");

	if ( $data[deliverycode] || $cntDv ){
		$step = "<a href=\"javascript:popup('popup.delivery.php?ordno=$data[ordno]',650,500)\"><font color=0074BA><b><u>$step</u></b></font></a>";
	}

	if ($_GET[sgword]) {
        $_res = $db->query("select goodsnm, if({$_GET[sgkey]} LIKE '%{$_GET[sgword]}%', 0, 1) as resort from ".GD_ORDER_ITEM." where ordno='$data[ordno]' order by resort, sno");
        list($goodsnm) = $db->fetch($_res);
    }
	else {
        $_res = $db->query("select goodsnm from ".GD_ORDER_ITEM." where ordno='$data[ordno]' order by sno");
        list($goodsnm) = $db->fetch($_res);
    }

	$grp[settleprice][''] += $data[prn_settleprice];

	$passed = Core::helper('Date')->diff($data['orddt'],$today);	// xm, xy, xd, -xm, -xy, -xd
?>
<tr height=25 bgcolor="<?=$bgcolor?>" bg="<?=$bgcolor?>" align=center>
	<td class=noline><input type=checkbox name=chk[] value="<?=$data[ordno]?>" onclick="iciSelect(this)" required label=">선택사항이 없습니다" <?=$disabled?>></td>
	<td><font class=ver8 color=616161><?=$pr*$idx--?></font></td>
	<td><font class=ver81 color=616161><?=substr($data[orddt],0,-3)?></font></td>
	<td><font class=ver81 color=616161><?=$passed?></font></td>
	<td align=left>
	<? if ($data['inflow'] == "sugi"){ ?>
	<a href="view.php?ordno=<?=$data[ordno]?>"><font class=ver81 style="color:#ED6C0A"><b><?=$data[ordno]?></b><span class="small1">(수기)</span></font></a>
	<? } else { ?>
	<a href="view.php?ordno=<?=$data[ordno]?>"><font class=ver81 color=0074BA><b><?=$data[ordno]?></b></font></a>
	<? } ?>
	<a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align=absmiddle></a>
	</td>
	<td align=left>
	<div style="height:13px; overflow-y:hidden;">
		<? if ($data[oldordno]!=""){	?><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/icon_twice_order.gif"></a><? } ?>
		<? if ($data[escrowyn]=="y"){	?><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/btn_escrow.gif"></a><? } ?>
		<? if ($data[eggyn]=="y"){		?><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/icon_guar_order.gif"></a><? } ?>
		<? if ($data[inflow]!="" && $data[inflow]!="sugi"){		?><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/inflow_<?=$data[inflow]?>.gif" align=absmiddle></a><? } ?>
		<? if ($data[cashreceipt]!=""){	?><img src="../img/icon_cash_receipt.gif"><? } ?>
		<? if ($data[cbyn]=="Y"){		?><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/icon_okcashbag.gif" align=absmiddle></a><? } ?>
		<font class=small1 color=444444><?=$goodsnm?>
			<? if (($_cnt = $db->count_($_res))>1){ ?>외 <?=$_cnt-1?>건<? } ?>
		</font>
	</div>
	</td>
	<td>
		<?php if($data[m_id]){ ?>
			<?php if($data['dormant_regDate'] == '0000-00-00 00:00:00'){ ?>
				<span id="navig" name="navig" m_id="<?=$data['m_id']?>" m_no="<?=$data['m_no']?>"><span class="small1" style="color:#0074BA"><strong><?=$data['nameOrder']?></strong> (<?=$data[m_id]?>)</span></span>
			<?php } else { ?>
				<span class="small1" style="color:#0074BA"><strong><?=$data['nameOrder']?></strong>(<?=$data[m_id]?> / 휴면회원)</span>
			<?php } ?>
		<?php } else { ?>
			<span class="small1"><?=$data['nameOrder']?></span>
		<?php } ?>
	</td>
	<td><font class=small1 color=444444><?=$data[nameReceiver]?></td>
	<td class=small4><?=$r_settlekind[$data[settlekind]]?></td>
	<td class=ver81><b><?=number_format($data[prn_settleprice])?></b></td>
	<td class=small4 width=60><?=$step?></td>
</tr>
<tr><td colspan=20 bgcolor=E4E4E4></td></tr>
<?
	}
	$cnt = $pr * ($idx+1);
?>
<tr>
	<td>

	<a href="javascript:chkBoxAll(document.getElementsByName('chk[]'),'rev')"><img src="../img/btn_allchoice.gif" border=0></a>

	</td>
	<td align=right height=30 colspan=9 style=padding-right:8>합계: <!--(<?=$cnt?>건)--> <font class=ver9><b><?=number_format($grp[settleprice][$preStepMsg])?></font>원</b></td>
	<td colspan=3></td>
</tr>
<tr bgcolor=#f7f7f7 height=30>
	<td colspan=10 align=right style=padding-right:8>전체합계 : <span class=ver9><b><?=number_format(@array_sum($grp[settleprice]))?>원</b></span></td>
	<td colspan=3></td>
</tr>

<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>

</table>

<div class=pageNavi align=center><font class=ver8><?=$pg->page[navi]?></font></div>


선택한 주문건 : <select name="processType"><option value="">--------- 선택 ---------</option><option value="sms">입금요청 SMS 발송</option><option value="confirm">입금확인 처리</option></select>
<img src="../img/btn_confirm_mini.gif" border="0" onclick="fnRequestBanking();" class="hand" alt="확인" align="absmiddle">


</form>


<p>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">무통장으로 주문한 내역 중 입금대기 상태의 주문건에 대한 리스트입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">경과 일자를 확인 하신 후 입금요청 SMS(문자)를 발송하고자 하는 주문건을 선택해 주세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">선택하신 후 입금요청 SMS발송을 하시면 해당 고객에게 입금요청 정보가 전송됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">발송되는 SMS의 내용은 관리자페이지 > 회원/SMS EMAIL > SMS설정 > SMS자동발송/설정에서 "입금요청 발송" 영역에서 수정하실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">단,SMS자동발송/설정 메뉴에 있는 입금요청 발송내용 하단에 고객에게 자동발송을 체크하지 않은 경우에는 선택한 주문건에 SMS가 발송되지 않습니다.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">입금이 확인 된 주문건은 선택 하신 후 입금확인 상태로 변경하시면 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">입금 확인된 주문건은 주문리스트에서 확인하실 수 있습니다.</td></tr>


</table>
</div>
<script>cssRound('MSG01')</script>

<script>window.onload = function(){ UNM.inner();};</script>
<? @include dirname(__FILE__) . "/../interpark/_order_list.php"; // 인터파크_인클루드 ?>

<? include "../_footer.php"; ?>
