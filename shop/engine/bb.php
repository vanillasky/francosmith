<?
include "../lib/library.php";
include "../lib/page.class.php";
include "../conf/config.php";
include "../conf/engine.php";
include "../conf/config.pay.php";
@include "../conf/fieldset.php";

function img_url($url,$src){
	if(preg_match('/http:\/\//',$src))$img_url = $src;
	else $img_url = $url.'/data/goods/'.$src;
	return $img_url;
}

$url = "http://".$_SERVER['HTTP_HOST'].$cfg[rootDir];

if(!$bb[chk]){
	exit;
}

### �⺻ ȸ�� ������
if($joinset[grp] != ''){
	list($mdc) = $db->fetch("select dc from gd_member_grp where level='".$joinset[grp]."' limit 1");
}

### ��ǰ ����Ʈ
$db_table = "
".GD_GOODS_LINK." a
left join ".GD_GOODS." b on a.goodsno=b.goodsno
left join ".GD_GOODS_OPTION." c on a.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
left join ".GD_GOODS_BRAND." d on b.brandno=d.sno
";

$where[] = "open";

$pg = new Page($_GET[page],1000);
$pg->cntQuery = "select count(distinct a.goodsno) from ".GD_GOODS_LINK." a,".GD_GOODS." b where a.goodsno=b.goodsno and open";
$pg->field = "
a.goodsno,b.*,
c.price,
c.reserve,
c.opt1,
c.opt2,
c.consumer,
".getCategoryLinkQuery('a.category', null, 'max').",
d.brandnm
";
$pg->setQuery($db_table,$where,$_GET[sort],'group by a.goodsno');
$pg->exec();
?>
<pre>
&lt;&lt;&lt;total&gt;&gt;&gt;<?=chr(10)?>
	<<�ѻ�ǰ��>><?=number_format($pg->recode[total])?><?=chr(10)?>
	<<����������>><?=Core::helper('Date')->format(G_CONST_NOW)?><?=chr(10)?>
	<<����/�߰���ǰ��>><?=number_format($pg->recode[total])?><?=chr(10)?>
&lt;&lt;&lt;/total&gt;&gt;&gt;<?=chr(10)?>
<?
$goodsModel = Clib_Application::getModelClass('goods');

$res = $db->query($pg->query);
while ($data=$db->fetch($res)){

	// �Ǹ� ����(�Ⱓ �� ����)�� ��� ����
	if (! $goodsModel->setData($data)->canSales()) continue;

	$query ="select subject
			from ".GD_GOODS_DISPLAY." a
				, ".GD_EVENT." b
			where a.goodsno='$data[goodsno]'
				and substring(a.mode,1,1) = 'e'
				and trim(substring(a.mode,2))=trim(b.sno)
				and b.sdate <= '".date('Ymd',time())."'
				and b.edate >= '".date('Ymd',time())."'";
	$r1 = $db->query($query);

	$event = "";
	while($data1 = $db->fetch($r1)) $event .= ",".$data1[subject];
	if($event)$event = substr($event,1);

	$img_arr = explode("|",$data['img_s']);
	$img_url1 = img_url($url,$img_arr[0]);
	$img_arr = explode("|",$data['img_m']);
	$img_url2 = img_url($url,$img_arr[0]);

	### ��۷�
	$param = array(
		'mode' => '1',
		'deliPoli' => 0,
		'price' => $data[price],
		'goodsno' => $data[goodsno],
		'goods_delivery' => $data[goods_delivery],
		'delivery_type' => $data[delivery_type]
	);
	$tmp = getDeliveryMode($param);
	$delivery = $tmp['price']+0;

	### �Ｎ�������� ��ȿ�� �˻�
	list($data[coupon],$data[coupon_emoney]) = getCouponInfo($data[goodsno],$data[price]);
	$data[reserve] += $data[coupon_emoney];

	### ȸ������
	$dcprice = 0;
	if($mdc)$dcprice = getDcprice($data[price],$mdc.'%');

	$t = strlen($data[category]);

	$catenm="";
	if($t){
		for($i=3;$i<=$t;$i+=3) $catenm .= "@".getCatename(substr($data[category],0,$i));
		$catenm = substr($catenm,1);
	}

	$goods_url = "http://{$_SERVER['HTTP_HOST']}{$cfg[rootDir]}/goods/goods_view.php?goodsno=".$data[goodsno]."&category=".$data[category]."&inflow=".$bb[gubun];
	$p = $pg->page['navi'];
	$p = str_replace("<b>","<a href='".$_SERVER[PHP_SELF]."?page=1'>",$p);
	$p = str_replace("class=navi","",$p);
	$p = str_replace("[","",$p);
	$p = str_replace("]","",$p);
	$p = str_replace("</b>","</a>",$p);
	$data[goodsnm] = str_replace(array('��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��'),'',$data[goodsnm]);
?>
&lt;&lt;&lt;product&gt;&gt;&gt;<?=chr(10)?>
   <<<��ǰ���̵�>>><?=$data[goodsno]?><?=chr(10)?>
   <<<��ǰ��>>><?=$data[goodsnm]?><?=chr(10)?>
   <<<��ǰ�з���>>><?=$catenm?><?=chr(10)?>
   <<<������>>><?=$data[maker]?><?=chr(10)?>
   <<<�����>>><?=substr($data[launchdt],0,7)?><?=chr(10)?>
   <<<�귣��>>><?=$data[brandnm]?><?=chr(10)?>
   <<<������>>><?=$data[origin]?><?=chr(10)?>
   <<<��ǰURL>>><?=$goods_url?><?=chr(10)?>
   <<<��ǰ�̹���URL>>><?=$img_url1?><?=chr(10)?>
   <<<��ǰū�̹���URL>>><?=$img_url2?><?=chr(10)?>
   <<<�ǸŰ�>>><?=($data[price] - $dcprice)?><?=chr(10)?>
   <<<��۷�>>><?=$delivery?><?=chr(10)?>
   <<<��۱Ⱓ>>><?=chr(10)?>
   <<<��������>>><?=$data[coupon]?><?=chr(10)?>
   <<<������>>><?=$data[reserve]?><?=chr(10)?>
   <<<�������Һ�>>><?=$card[cardfree]?><?=chr(10)?>
   <<<�̺�Ʈ>>><?=$event?><?=chr(10)?>
&lt;&lt;&lt;/product&gt;&gt;&gt;<?=chr(10)?>
<?
}
?>
<?=$p?>
</pre>
