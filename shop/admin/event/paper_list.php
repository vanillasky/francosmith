<?

$location = "��������������  > ��������������Ʈ";
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

$arCouponType = array('sale'=>'����','save'=>'����');
$arStatus = array('pre'=>'�����','done'=>'���','disuse'=>'����');
?>
<script type="text/javascript">
document.observe("dom:loaded", function() {
	cssRound('MSG01');
});
</script>
<div>
<div class="title title_top">���������� ����Ʈ <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=18')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table width="100%" cellpadding="0" cellspacing="0">
<tr class="rndbg">
	<th width="50">��ȣ</th>
	<th>������</th>
	<th width="100">����/������</th>
	<th width="100">���/��� ȸ������</th>
	<th width="100">����</th>
	<th width="100">�����ݾ�</th>
	<th width="100">����</th>
	<th width="60">����/�����</th>
	<th width="60">����</th>
	<th width="60">����</th>
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
	<td align="center"><a href="javascript:popup('popup.coupon_user_paper.php?sno=<?=$data['sno']?>',650,850)"><font color="00899d"><?= (is_null($data['total_cnt']) ? 0 : $data['total_cnt'])?>���� <?= (is_null($data['use_cnt']) ? 0 : $data['use_cnt'])?>�� ���</font></a></td>
	<td align="center"><?=$arCouponType[$data['coupon_type']]?></td>
	<td align="center"><?=number_format($data['coupon_price'])?><?=$data['currency']?></td>
	<td align="center"><?=$arStatus[$data['status']]?></td>
	<td align="center">
	<?if($data['status']!='disuse'):?>
	<a href="indb.paper.php?mode=disuse&sno=<?=$data['sno']?>" target="ifrmHidden" onclick="return confirm('������ �̹� ���� ������ ������ ��� ��������� �����˴ϴ�.')"><img src="../img/i_stop.gif" alt="����" /></a>
	<?else:?>
	<a href="indb.paper.php?mode=disuse&sno=<?=$data['sno']?>" target="ifrmHidden" onclick="return confirm('���� ������ �ٽ� ��밡���� ���·� ����˴ϴ�.')"><img src="../img/i_restart.gif" alt="�����" /></a>
	<?endif;?>
	</td>
	<td align="center">
	<a class="extext" href="paper_register.php?mode=modify&sno=<?=$data['sno']?>"><img src="../img/i_edit.gif" alt="����"></a>
	</td>
	<td align="center">
	<a href="indb.paper.php?mode=delete&sno=<?=$data['sno']?>" target="ifrmHidden" onclick="return confirm('�� ������ ���õ� ��� ������ �����˴ϴ�.\n������ �����Ͻðڽ��ϱ�?')"><img src="../img/i_del.gif" alt="����" /></a>
	</td>
</tr>
<tr><td colspan="10" class="rndline"></td></tr>
<? endforeach; ?>
</table>

<? $pageNavi = &$result['page']; ?>
<div align="center" class="pageNavi ver8">
	<? if($pageNavi['prev']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['prev'])?>">�� </a>
	<? endif; ?>
	<? if($pageNavi['page'])foreach($pageNavi['page'] as $v): ?>
		<? if($v==$pageNavi['nowpage']): ?>
			<a href="?<?=getvalue_chg('page',$v)?>"><?=$v?></a>
		<? else: ?>
			<a href="?<?=getvalue_chg('page',$v)?>">[<?=$v?>]</a>
		<? endif; ?>
	<? endforeach; ?>
	<? if($pageNavi['next']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['next'])?>">��</a>
	<? endif; ?>
</div>

</div>

<div style="padding-top:15px"></div>

</div>
<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex" style="padding-top:0px;">
	<tr>
		<td>
		<div><img src="../img/icon_list.gif" align="absmiddle">������ ������ ����� �� ���� ���·� ���� �մϴ�. �̹� ���� ������ ��� ������ �Ұ��� �ϸ� �� �̻� ����� ���ϰ��� ��� ���� ����� �̿��ϼ���.</div>
		<div><img src="../img/icon_list.gif" align="absmiddle">���/��� ȸ�������� ������ Ŭ���Ͻø� ���������� ��Ϲ� ����� ȸ������� Ȯ���Ͻ� �� �ֽ��ϴ�.</div>
		</td>
	</tr>
	</table>
</div>


<? include "../_footer.php"; ?>