<?
set_time_limit(0);
include "../lib.php";
@include "../../conf/config.pay.php";
@include "../../conf/orderXls.php";


header("Content-Type: application/vnd.ms-excel; charset=euc-kr");
header("Content-Disposition: attachment; filename=GDorder_".$_POST[mode]."_".date("YmdHi").".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");



if ($_POST[mode]=="goods"){
	$query = str_replace("left join ".GD_ORDER_ITEM." c on a.ordno=c.ordno","",$query);
	$query = str_replace("from ".GD_ORDER." a",",d.*,c.*, a.memo as order_memo from ".GD_ORDER." a left join ".GD_ORDER_ITEM." c on a.ordno=c.ordno left join ".GD_GOODS." d on c.goodsno=d.goodsno",$query);
	$query = str_replace("concat( ordno,","concat( a.ordno,",$query);
	if(!preg_match('/a.dyn/',$query)) $query = str_replace("dyn,","a.dyn,",$query);
	if(!preg_match('/d.goodsnm/',$query)) $query = str_replace("goodsnm","d.goodsnm",$query);
}

$query = stripslashes($_POST[query]);

if(!$orderXls)$orderXls = $default[orderXls];
else $orderXls = getdefault('orderXls');
foreach($orderXls as $tmp) if($tmp[1]=='goodsnm' && $tmp[3]=='checked')$addfield['goodsnm']=1;

if(!$orderGoodsXls)$orderGoodsXls = $default[orderGoodsXls];
else $orderGoodsXls = getdefault('orderGoodsXls');


foreach($orderXls as $key=>$value)if($value[3]=="")unset($orderXls[$key]);
foreach($orderGoodsXls as $key=>$value)if($value[3]=="")unset($orderGoodsXls[$key]);



$orderXls[] = array('ÄíÆù¹øÈ£','sendcode');
$orderXls[] = array('ÄíÆù¼ö·®','ea');

$res = $db->query($query);
?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<style>td {mso-number-format:"@"}</style>
<? if ($_POST[mode]=="goods"){ ?>
<?
$query = "select brandnm from ".GD_GOODS_BRAND." where sno = '$data[brandno]'";
list($data[brandnm]) =  $db->fetch($query);

$query = "select supply from ".GD_GOODS_OPTION." where sno = '$data[brandno]' and opt1='$data[opt1]' and opt2='$data[opt2]' and go_is_deleted <> '1' limit 1";
list($data[supply]) =  $db->fetch($query);
?>
<table border=1>
<tr bgcolor=#f7f7f7>
<?
	foreach($orderGoodsXls as $k => $v)	echo("<th>$v[0]</th>");
?>
</tr>
<? while ($data=$db->fetch($res)){ ?>
<tr>
	<?
	$data[no] = $data[opt] = $data[sprice] = $data[deliveryno] = $data[deliverycode] = "";
	if(!$data[dvno]) $data[dvno] = "";
	$data[no] = ++$idx;
	if($data[opt1])$data[opt] .= "[".$data[opt1];
	if($data[opt2])$data[opt] .= "/".$data[opt2];
	if($data[opt])$data[opt] .= "]";
	if($data[addopt]) $data[opt] .= "<div>[".str_replace("^","] [",$data[addopt])."]</div>";
	$data[settlekind] = $r_settlekind[$data[settlekind]];
	$data[step] = $r_istep[$data[istep]];
	$data[deliveryno] = $data[dvno];
	$data[deliverycode] = $data[dvcode];
	$data[sprice]=$data[prn_settleprice];
	if($data[deli_msg])$data[deli_type] = $data['deli_msg'];
	$data['deli_type'] = str_replace('ÈÄºÒ','ÂøºÒ',$data['deli_type']);

	foreach($orderGoodsXls as $k => $v)  echo("<td>".strip_tags($data[$v[1]])."</td>");
	?>
</tr>
<? } ?>
</table>

<? } else {?>

<table border=1>
<tr bgcolor=#f7f7f7>
	<?
	foreach($orderXls as $k => $v)	echo("<th>$v[0]</th>");
?>
</tr>
<?
	while ($data=$db->fetch($res)){
		if($addfield['goodsnm']){
			$itemcnt=0;
			$query = "select goodsnm from ".GD_ORDER_ITEM." where ordno='$data[ordno]'";
			$res_item = $db->query($query);
			while($item = $db->fetch($res_item)){
				if($itemcnt == 0)$data[goodsnm] = $item['goodsnm'];
				$itemcnt++;
			}
			if($itemcnt > 1)  $data[goodsnm].= "¿Ü ".($itemcnt-1)."°Ç";
		}
?>
<tr>
	<?
	if(!$data[deliveryno]) $data[deliveryno] = "";
	$data[no] = $data[opt] = $data[sprice] = "";
	$data[no] = ++$idx;
	$data[settlekind] = $r_settlekind[$data[settlekind]];
	$step = getStepMsg($data[step],$data[step2],$data[ordno]);
	if(strlen($step) > 10) $step = substr($step,10);
	$data[step] = $step;
	$data[order_memo] = $data[memo];
	$data[settleprice]=$data[prn_settleprice];
	list($dcnt) = $db->fetch("select count(*) from gd_order_item where ordno='$data[ordno]' and deli_msg != ''");
	if($data['deli_msg']  == "°³º° ÂøºÒ ¹è¼Ûºñ") $data['deli_type'] = "°³º° ÂøºÒ";
	if($data['deli_type'] == "¼±ºÒ" && $dcnt > 0) $data['deli_type'] .= "(°³º° ÂøºÒ)";
	$data['deli_type'] = str_replace('ÈÄºÒ','ÂøºÒ',$data['deli_type']);

	foreach($orderXls as $k => $v) echo("<td>".strip_tags($data[$v[1]])."</td>");
	?>
</tr>
<? } ?>
</table>
<? } ?>