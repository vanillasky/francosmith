<?
$noDemoMsg = $indexLog = 1;
include "../_header.php";
@include "../conf/config.pay.php";

$query = "
select * from
	".GD_GOODS." a
	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
where
	a.goodsno='$_GET[goodsno]'
";

$data = $db->fetch($query);
list( $data[brand] ) = $db->fetch("select brandnm from ".GD_GOODS_BRAND." where sno='$data[brandno]'");
$data[r_img] = explode("|",$data[img_l]);

### 옵션이미지
$query = "select opt1img from ".GD_GOODS_OPTION." where goodsno='".$_GET[goodsno]."' and go_is_deleted <> '1' and go_is_display = '1' group by opt1";
$res = $db->query($query);
while($tmp = $db->fetch($res)){
	if($tmp[opt1img])$data[r_img][] = $tmp[opt1img];
}

$data[t_img] = array_map("toThumb",$data[r_img]);
$data[shortdesc] = nl2br($data[shortdesc]);

if(!$data['use_emoney']){
	if( !$set['emoney']['chk_goods_emoney'] ){
		if( $set['emoney']['goods_emoney'] ) $data['reserve'] = getDcprice($data['price'],$set['emoney']['goods_emoney'].'%');
	}else{
		$data['reserve']	= $set['emoney']['goods_emoney'];
	}
}

$tpl->assign($data);
$tpl->print_('tpl');

?>