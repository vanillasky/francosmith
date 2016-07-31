<?
include "../lib/library.php";
@include "../conf/config.pay.php";
include "../conf/config.php";
@include "../conf/fieldset.php";

$yesterdayDate = date("Y-m-d",strtotime("-1 day"));

$table_order = GD_ORDER;
$table_item = GD_ORDER_ITEM;
$table_review = GD_GOODS_REVIEW;

$query_total = "select count(distinct goodsno) as prodcnt, IFNULL(sum(price*ea),0) as totalamount
				from $table_order a join $table_item b on a.ordno = b.ordno
				where b.istep < 40
				and a.orddt between '".$yesterdayDate." 00:00:00' and '".$yesterdayDate." 23:59:59'
				and a.inflow = 'naver' ";
list($prodcnt, $totalamount) = $db->fetch($query_total); 

$query_prod =  "select b.goodsno, sum(b.ea) as sellcnt, sum(b.price*ea) as sellamount, 0 as reviewcnt
				from $table_order a join $table_item b on a.ordno = b.ordno
				where b.istep < 40
				and a.orddt between '".$yesterdayDate." 00:00:00' and '".$yesterdayDate." 23:59:59'
				and a.inflow = 'naver' 
				group by b.goodsno having sellcnt > 0";
$res = $db->query($query_prod);
# 여기에서 일단 기존 파일을 삭제하고 다시 쓴다.


echo("<<<mstart>>>".chr(10));
echo( $totalamount."|".$prodcnt."|".$yesterdayDate.chr(10)); 
echo("<<<mend>>>".chr(10));
echo("<<<pstart>>>".chr(10));
while ($v=$db->fetch($res)){
	$goodsno = $v['goodsno']; 
	$sellcnt = $v['sellcnt']; 
	$sellamount = $v['sellamount'];
	# 몰상품ID|판매수량|판매금액
	echo( $goodsno."|".$sellcnt."|".$sellamount.chr(10));
}
echo("<<<pend>>>".chr(10));
flush();
?>