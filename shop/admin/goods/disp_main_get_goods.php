<?
include "../lib.php";
include "../lib.skin.php"; // $strSQLWhere 변수가 이 파일에서 설정됨
include "../../conf/design.main.php";
@include "../../conf/design_main.".$cfg['tplSkinWork'].".php";

$query = "
select
	distinct a.mode,a.goodsno,b.goodsnm,b.img_s,c.price
from
	".GD_GOODS_DISPLAY." a,
	".GD_GOODS." b,
	".GD_GOODS_OPTION." c
where
	a.goodsno=b.goodsno
	and a.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
	and a.mode = '".$_POST['mode']."'
	".$strSQLWhere."
order by sort
";

$res = $db->query($query);
while ($data=$db->fetch($res,1)) {

	$data['price'] = number_format($data['price']);
	$data['goodsimg'] = goodsimg($data[img_s], '40,40','',1);


	$loop[] = $data;
}

echo gd_json_encode($loop);
?>