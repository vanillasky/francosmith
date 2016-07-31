<?
$location = "택배연동 서비스 > 우체국택배 예약하기(2단계)";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";
include "../../lib/godopost.class.php";

$godopost = new godopost();

if(!$godopost->linked) {
	msg("우체국택배 연동을 신청하셔야 사용 하실 수 있습니다");
	go("post_admin.php");
	exit;
}




if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$page = ((int)$_GET['page']?(int)$_GET['page']:1);

$query = "
	select
		dv.dvcode,
		dv.ordno,
		dv.goodsinfo,
		o.nameOrder,
		o.settleprice,
		o.step,
		o.step2,
		o.orddt
	from
		(
			select 
				dvcode,
				ordno,
				group_concat(concat_ws(',',cast(goodsno as char),cast(ea as char),goodsnm) SEPARATOR '\n\n') as goodsinfo
			from 
				gd_order_item 
			where 
				dvno='100' 
			group by 
				dvcode 
			order by 
				dvcode asc
		) as dv
		left join gd_godopost_reserved as r on dv.dvcode=r.deliverycode
		left join gd_order as o on dv.ordno=o.ordno
	where
		isnull(r.deliverycode)
";
$result = $db->_select_page(10,$page,$query);

foreach($result['record'] as $k=>$v) {
	$ar_line = explode("\n\n",$v['goodsinfo']);
	$ar_goods = array();
	foreach($ar_line as $each_line) {
		preg_match('/^([0-9]+),([0-9]+),(.+)$/',$each_line,$matches);
		$ar_goods[]=array(
			'goodsno'=>$matches[1],
			'ea'=>$matches[2],
			'goodsnm'=>$matches[3]
		);
	}
	unset($result['record'][$k]['goodsinfo']);
	$result['record'][$k]['goods']=$ar_goods;
}




?>
<script language='javascript'> 
/*** 레이어 팝업창 띄우기 ***/
function customPopupLayer(s,w,h)
{
	if (!w) w = 600;
	if (!h) h = 400;

	var pixelBorder = 3;
	var titleHeight = 12;
	w += pixelBorder * 2;
	h += pixelBorder * 2 + titleHeight;

	var bodyW = document.body.clientWidth;
	var bodyH = document.body.clientHeight;

	var posX = (bodyW - w) / 2;
	var posY = (bodyH - h) / 2;

	hiddenSelectBox('hidden');

	/*** 백그라운드 레이어 ***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = 0;
		top = 0;
		width = "100%";
		height = document.body.scrollHeight;
		backgroundColor = "#000000";
		filter = "Alpha(Opacity=80)";
		opacity = "0.5";
	}
	obj.id = "objPopupLayerBg";
	document.body.appendChild(obj);

	/*** 내용프레임 레이어 ***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = posX + document.body.scrollLeft;
		top = posY + document.body.scrollTop;
		width = w;
		height = h;
		backgroundColor = "#ffffff";
		border = "3px solid #000000";
	}
	obj.id = "objPopupLayer";
	document.body.appendChild(obj);

	/*** 타이틀바 레이어 ***/
	var bottom = document.createElement("div");
	with (bottom.style){
		position = "absolute";
		width = w - pixelBorder * 2;
		height = titleHeight;
		left = 0;
		top = h - titleHeight - pixelBorder * 3;
		padding = "4px 0 0 0";
		textAlign = "center";
		backgroundColor = "#000000";
		color = "#ffffff";
		font = "bold 8pt tahoma; letter-spacing:0px";
		
	}
	bottom.innerHTML = "<a href='javascript:closeLayer()' class='white'>X close</a>";
	obj.appendChild(bottom);

	/*** 아이프레임 ***/
	try {
		var ifrm = document.createElement("<iframe name='processLayerForm'></iframe>");
	}
	catch (e1) {
		obj.innerHTML += '<iframe name="processLayerForm"></iframe>';
		var ifrm = obj.childNodes[obj.childNodes.length-1];
	}
	with (ifrm.style){
		width = w - 6;
		height = h - pixelBorder * 2 - titleHeight - 3;
		//border = "3 solid #000000";
	}
	ifrm.frameBorder = 0;
	ifrm.src = s;
	//ifrm.className = "scroll";
	obj.appendChild(ifrm);
}

function checkAll() {
	var ar_checkbox = $$(".sel_checkbox");
	if(ar_checkbox[0]) {
		var action=!ar_checkbox[0].checked;
	}

	ar_checkbox.each(function(item){
		item.checked=action;
	});

	var ar_checkbox = $$(".sel_checkbox");
	var checked_number=0;
	ar_checkbox.each(function(item){
		if(item.checked) checked_number++;
	});
	$('checked_number').innerHTML=checked_number;
}


function boxClick() {
	var ar_checkbox = $$(".sel_checkbox");
	var checked_number=0;
	ar_checkbox.each(function(item){
		if(item.checked) checked_number++;
	});
	$('checked_number').innerHTML=checked_number;
}


function reserve_order() {
	if(document.fmList.ps_method[0].checked) {
		var ar_checkbox = $$(".sel_checkbox");
		var checked_number=0;
		ar_checkbox.each(function(item){
			if(item.checked) checked_number++;
		});
		if(checked_number==0) {
			alert('선택된 주문이 없습니다');
		}
		else {
			customPopupLayer('about:blank',780,500);
			document.fmList.submit();
		}
	}
	else {
		<? if($result['page']['totalcount']): ?>
			customPopupLayer('about:blank',780,500);
			document.fmList.submit();
		<? else: ?>
			alert('송장번호 미발급 주문이 없습니다');
		<? endif; ?>
	}

	
}

function popupGodoPostManualConfirm(ordno) {
	popupLayer('popup.godopost.manualconfirm.php');
}
</script>
<div class="title title_top">우체국택배 예약하기<span>우체국택배 송장번호를 발급받은 물품에 대해서 배송을 예약합니다</span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=12')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a>
</div>

<br>


<form name="fmList" method="post" action="indb.godopost.reserve.php" target='processLayerForm'>
<input type="hidden" name="mode" value="order_reserve">
<input type="hidden" name="regdt[0]" value="<?=$regdt[0]?>">
<input type="hidden" name="regdt[1]" value="<?=$regdt[1]?>">


<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="8"></td></tr>
<tr class="rndbg">
	<th><span onclick="checkAll()" style="cursor:pointer">선택</span></th>
	<th>송장번호</th>
	<th>주문번호</th>
	<th>상품명</th>
	<th>주문자명</th>
	<th>결제가</th>
	<th>주문상태</th>
</tr>
<tr><td class="rnd" colspan="8"></td></tr>

<col align="center" width="40"/>
<col align="center" width="110" />
<col align="center" width="110" />
<col align="left" />
<col align="center" width="70" />
<col align="center" width="90" />
<col align="center" width="80" />

<? foreach($result['record'] as $k=>$data): ?>
<?
$data['orddt'] = date("Y-m-d H:i",strtotime($data['orddt']));
if(count($data['goods'])>1) {
	$data['goodsnm']=$data['goods'][0]['goodsnm'].' 외 '.(count($data['goods'])-1).'건';
}
else {
	$data['goodsnm']=$data['goods'][0]['goodsnm'];
}
?>


<tr><td height="4" colspan="8"></td></tr>
<tr height="25">
	<td class="noline"><input type="checkbox" name="sel_dvcode[]" value="<?=$data['dvcode']?>" class="sel_checkbox" onclick="boxClick(this)"></td>
	<td><font class="ver81" color="#616161"><?=$data['dvcode']?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['ordno']?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['goodsnm']?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['nameOrder']?></font></td>
	<td><font class="ver81" color="#616161"><?=number_format($data['settleprice'])?>원</font></td>
	<td><font class="ver81" color="#616161"><?=getStepMsg($data['step'],$data['step2'])?></font></td>
</tr>


<? endforeach; ?>

<tr><td height="4"></td></tr>
<tr><td colspan="8" class="rndline"></td></tr>

</table>


<? $pageNavi = &$result['page']; ?>
<div align="center" class="pageNavi ver8">
	<? if($pageNavi['prev']): ?> 
		<a href="?<?=getvalue_chg('page',$pageNavi['prev'])?>">◀ </a>
	<? endif; ?>
	<? foreach($pageNavi['page'] as $v): ?>
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




<div class="noline" style="border:1px solid #cccccc;padding:10px">

<table>
<tr>
<td>
	<input type="radio" name="ps_method" value="selected" checked> 
	선택된 <span id="checked_number">0</span>개의 송장번호에 대해서 우체국택배에 예약을 합니다<br>
	<input type="radio" name="ps_method" value="searched"> 
	검색된 주문 <?=$result['page']['totalcount']?>건에 대해서 우체국택배에 일괄예약을 합니다
</td>
<td style="padding-left:30px">
	<img src="../img/btn_postoffic_reserve.gif" style="cursor:pointer" onclick="reserve_order()">

</td>
</table>

</div>




</form>



<? include "../_footer.php"; ?>
