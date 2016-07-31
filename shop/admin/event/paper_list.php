<?

$location = "페이퍼쿠폰관리  > 페이퍼쿠폰리스트";
include "../_header.php";

$page = ((int)$_GET['page']?(int)$_GET['page']:1);

$query = ' SELECT aa.*, bb.* ';
$query .= '  FROM gd_offline_coupon AS aa LEFT JOIN ';
$query .= '	(SELECT c.coupon_sno, SUM(c.total_cnt) AS total_cnt, IFNULL(SUM(c.use_cnt), 0) AS use_cnt';
$query .= '	  FROM ( ';
$query .= '		SELECT a.coupon_sno,  COUNT(a.sno) AS total_cnt, COUNT(b.ordno) AS use_cnt ';
$query .= '		  FROM gd_offline_download AS a LEFT JOIN gd_coupon_order AS b ON (a.sno = b.downloadsno AND a.m_no = b.m_no) ';
$query .= '		GROUP BY a.coupon_sno ';
$query .= '	) c ';
$query .= '	GROUP BY c.coupon_sno) AS bb ON (aa.sno = bb.coupon_sno)';
$result = $db->_select_page(10,$page,$query);

$arCouponType = array('sale'=>'할인','save'=>'적립');
$arStatus = array('pre'=>'사용전','done'=>'사용','disuse'=>'중지');
?>
<script type="text/javascript">
document.observe("dom:loaded", function() {
	cssRound('MSG01');
});
</script>
<div>
<div class="title title_top">페이퍼쿠폰 리스트 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=18')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table width="100%" cellpadding="0" cellspacing="0">
<tr class="rndbg">
	<th width="50">번호</th>
	<th>쿠폰명</th>
	<th width="100">시작/종료일</th>
	<th width="100">등록/사용 회원보기</th>
	<th width="100">종류</th>
	<th width="100">쿠폰금액</th>
	<th width="100">상태</th>
	<th width="60">중지/재시작</th>
	<th width="60">수정</th>
	<th width="60">삭제</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>
<?if($result['record']) foreach($result['record'] as $k=>$data): ?>
<tr><td height="5" colspan="10"></td></tr>
<tr height="50">
	<td align="center"><?=$data['_no']?></td>
	<td><?=$data['coupon_name']?></td>
	<td align="center">
	<div><?=$data['start_year']?>-<?=$data['start_mon']?>-<?=$data['start_day']?> <?=$data['start_time']?>:00</div>
	<div><?=$data['end_year']?>-<?=$data['end_mon']?>-<?=$data['end_day']?> <?=$data['end_time']?>:59</div>
	</td>
	<td align="center"><a href="javascript:popup('popup.coupon_user_paper.php?sno=<?=$data['sno']?>',650,850)"><font color="00899d"><?= (is_null($data['total_cnt']) ? 0 : $data['total_cnt'])?>명중 <?= (is_null($data['use_cnt']) ? 0 : $data['use_cnt'])?>명 사용</font></a></td>
	<td align="center"><?=$arCouponType[$data['coupon_type']]?></td>
	<td align="center"><?=number_format($data['coupon_price'])?><?=$data['currency']?></td>
	<td align="center"><?=$arStatus[$data['status']]?></td>
	<td align="center">
	<?if($data['status']!='disuse'):?>
	<a href="indb.paper.php?mode=disuse&sno=<?=$data['sno']?>" target="ifrmHidden" onclick="return confirm('중지시 이미 사용된 쿠폰을 제외한 모든 쿠폰사용이 중지됩니다.')"><img src="../img/i_stop.gif" alt="중지" /></a>
	<?else:?>
	<a href="indb.paper.php?mode=disuse&sno=<?=$data['sno']?>" target="ifrmHidden" onclick="return confirm('사용시 쿠폰이 다시 사용가능한 상태로 변경됩니다.')"><img src="../img/i_restart.gif" alt="재시작" /></a>
	<?endif;?>
	</td>
	<td align="center">
	<a class="extext" href="paper_register.php?mode=modify&sno=<?=$data['sno']?>"><img src="../img/i_edit.gif" alt="수정"></a>
	</td>
	<td align="center">
	<a href="indb.paper.php?mode=delete&sno=<?=$data['sno']?>" target="ifrmHidden" onclick="return confirm('이 쿠폰과 관련된 모든 정보가 삭제됩니다.\n정말로 삭제하시겠습니까?')"><img src="../img/i_del.gif" alt="삭제" /></a>
	</td>
</tr>
<tr><td colspan="10" class="rndline"></td></tr>
<? endforeach; ?>
</table>

<? $pageNavi = &$result['page']; ?>
<div align="center" class="pageNavi ver8">
	<? if($pageNavi['prev']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['prev'])?>">◀ </a>
	<? endif; ?>
	<? if($pageNavi['page'])foreach($pageNavi['page'] as $v): ?>
		<? if($v==$pageNavi['nowpage']): ?>
			<a href="?<?=getvalue_chg('page',$v)?>"><?=$v?></a>
		<? else: ?>
			<a href="?<?=getvalue_chg('page',$v)?>">[<?=$v?>]</a>
		<? endif; ?>
	<? endforeach; ?>
	<? if($pageNavi['next']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['next'])?>">▶</a>
	<? endif; ?>
</div>

</div>

<div style="padding-top:15px"></div>

</div>
<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex" style="padding-top:0px;">
	<tr>
		<td>
		<div><img src="../img/icon_list.gif" align="absmiddle">중지는 쿠폰을 사용할 수 없는 상태로 변경 합니다. 이미 사용된 쿠폰인 경우 삭제가 불가능 하며 더 이상 사용을 못하게할 경우 중지 기능을 이용하세요.</div>
		<div><img src="../img/icon_list.gif" align="absmiddle">등록/사용 회원보기의 내용을 클릭하시면 페이퍼쿠폰 등록및 사용한 회원목록을 확인하실 수 있습니다.</div>
		</td>
	</tr>
	</table>
</div>


<? include "../_footer.php"; ?>