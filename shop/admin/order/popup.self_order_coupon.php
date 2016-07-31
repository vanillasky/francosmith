<?
include "../_header.popup.php";
include "../../lib/page.class.php";
include "../../lib/cart.class.php";

## 쿠폰 설정 정보
@include "../../conf/config.pay.php";
@include "../../conf/coupon.php";

if(!$cfgCoupon['use_yn']) $cfgCoupon['use_yn'] = 0;	// 쿠폰 사용여부(0:사용하지않음 1:사용)
if(!$cfgCoupon['range']) $cfgCoupon['range'] = 0;	// 중복할인여부(0:쿠폰할인, 회원할인 동시사용  1:회원할인만 사용 2: 쿠폰할인만 사용)
if(!$cfgCoupon['double']) $cfgCoupon['double'] = 0;	// 쿠폰 사용제한(0:한 주문에 여러 쿠폰 사용가능   1:한 주문에 한개 쿠폰만 사용)

## 회원할인 적용여부
if(!$cfgCoupon['use_yn'] || ($cfgCoupon['range'] != '2' && $cfgCoupon['use_yn']))$ableDc = true;
else $ableDc = false;

## 쿠폰 적용여부
if($cfgCoupon['range'] != '1' && $cfgCoupon['use_yn'])$ableCoupon = true;
else $ableCoupon = false;

if($_GET['tmpMemID']) {
	$tmpSess = $_SESSION['sess'];
	list($sess['m_no'], $sess['m_id'], $sess['level'], $sess['groupsno']) = $db->fetch("SELECT m.m_no, m.m_id, m.level, g.sno FROM ".GD_MEMBER." AS m LEFT JOIN ".GD_MEMBER_GRP." AS g ON m.level = g.level WHERE m.m_id = '".$_GET['tmpMemID']."'"); // 주문자의 회원정보를 세션 변수에 저장
	$_SESSION['sess'] = $sess;
}
else msg("회원 아이디 값이 전달되지 않았습니다.", "close");

$Cart = Core::loader('Cart', $_COOKIE[gd_isDirect]);
$Goods = Core::loader('Goods');
$coupon_price = Core::loader('coupon_price');
$coupon_price->set_config($cfgCoupon);

if($Cart -> item)foreach($Cart -> item as $v) {
	if($abledc) $dc = getDcPrice($v[price],$Cart->dc);
	else $dc = 0;
	$arCategory = $Goods->get_goods_category($v['goodsno']);
	$coupon_price->set_item($v['goodsno'],$v['price'],$v['ea'],$arCategory,$v['opt'][0],$v['opt'][1],$v['addopt'],$v['goodsnm']);
	$goodsPrice += ($v['price'] + $v['addprice']) * $v['ea'];
}

$coupon_price->get_goods_coupon('order');
if($coupon_price->arCoupon)foreach($coupon_price->arCoupon as $data){
	if($data['excPrice'] && $data['excPrice'] > $goodsPrice) continue;
	if($data['pay_limit'] == "limited" && $data['limit_amount'] && $goodsPrice < $data['limit_amount']) continue;
	$data['apr']=0;
	if($data['sale']) $data['apr'] = @array_sum($data['sale']);
	if($data['reserve']) $data['apr'] = @array_sum($data['reserve']);
	$data['pay_method'] = $data['payMethod'];
	$loop[] = $data;
}
$cart->dc = $member['dc']."%";
if(!$sess['m_no'])	msg("회원만 쿠폰사용이 가능합니다!", "close");
if(!$ableCoupon)	msg("쿠폰사용이 불가 합니다!", "close");

$_SESSION['sess'] = $tmpSess;
$sess = $tmpSess;
?>

<script language=javascript>

var arCoupon = new Array();
<? for($i = 0, $imax = count($loop); $i < $imax; $i++) { ?>
arCoupon[<?=$i?>] = new Array('<?=$loop[$i]['apr']?>','<?=$loop[$i]['ability']?>','<?=$loop[$i]['sno']?>','<?=$loop[$i]['pay_method']?>');
<? } ?>

function chk_settlekind(mod) {
	var settlekind = opener.document.getElementsByName('settlekind');
	if(mod){
		for(var j=0;j<settlekind.length;j++){
			if(settlekind[j].value == 'a'){
				settlekind[j].disabled = false;
				settlekind[j].checked = true;
			}else{
				settlekind[j].disabled = true;
			}
		}
		prn_msg();
	}else{
		for(var j=0;j<settlekind.length;j++){
			settlekind[j].disabled = false;
		}
	}
}
function prn_msg(){
	var obj = document.getElementById('coupon_msg');
	if(obj.style.display!='block'){
		obj.style.display = "block";
		setTimeout("prn_msg()",3000);
	}else{
		obj.style.display = "none";
	}
}
function calcuCoupon(obj, typ)
{
	var chk = document.getElementsByName(obj.name);
	var apply_coupon = opener.document.getElementById('apply_coupon');
	var del_coupon = opener.document.getElementById('del_coupon');
	var emoney = opener.document.getElementById('emoney');
	var coupon_price = 0; var coupon_emoney = 0; var sno = '';
	var dc = 0; var abi = 0;
	var chkCash = false;

	// 중복 사용 체크
	var dup = '<?=$cfg['emoney']['useduplicate']?>';
	if(parseInt(emoney.value) > 0 && !dup) {
		alert('적립금과 쿠폰 사용이 중복적용되지 않습니다.');
		obj.checked = false;
		return false;
	}
	apply_coupon.innerHTML = '';
	for(i=0;i<chk.length;i++) {
		if(chk[i].checked){
			if(arCoupon[i][3] == 'cash' || typ =='1') {
				chkCash = true;
			}
			dc = arCoupon[i][0];
			abi = arCoupon[i][1];
			sno = arCoupon[i][2];
			if(abi == 0)coupon_price += parseInt(dc);
			else coupon_emoney += parseInt(dc);
			apply_coupon.innerHTML += "<input type='hidden' name='apply_coupon[]' value='"+ sno +"'>";
		}
	}
	chk_settlekind(chkCash);
	document.getElementById('coupon_price').innerHTML = comma(coupon_price);
	document.getElementById('coupon_emoney').innerHTML = comma(coupon_emoney);

	if((coupon_price || coupon_emoney) && '2' == '<?=$cfgCoupon['range']?>')opener.document.getElementById('memberdc').innerHTML = 0;

	opener.document.orderForm.coupon.value = comma(coupon_price);
	opener.document.orderForm.coupon_emoney.value = comma(coupon_emoney);

	opener.setPayInfo();

	del_coupon.style.visibility = "";
}

function view_goods(idx,max){
	var obj = document.getElementById('goodsnm_'+idx);
	if(obj.style.display=="block") obj.style.display="none";
	else  obj.style.display="block";
	for(var i=0;i<max;i++){
		if(i != idx) document.getElementById('goodsnm_'+i).style.display="none";
	}
}
</script>
<style>
.applyGoods { display:none;position:relative }
.applyGoods div { position:absolute;left:-180;background-color:#ffffff;width:400px;border:3px solid #000000;padding:5px 5px 5px 5px }
.msg { display:none;position:relative }
.msg div { position:absolute;left:-180;top:-50;background-color:#ffffff;width:400px;border:3px solid #000000;padding:5px 5px 5px 5px }
</style>

<form name="frmList">

<div class="title title_top">쿠폰적용하기<span>COUPON</span></div>

<table height="100%" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF">
<tr>
	<td align="center" valign="top">
		<div class="msg" id="coupon_msg">
		<div>무통장 입금 사용시 가능한 쿠폰을 선택하였습니다.<br/>결제수단은 무통장입금만 이용하실 수 있습니다.</div>
		</div>
		<div style="height:15; font-size:0pt"></div>
		<div style="float:right;padding-right:15px;"><b>쿠폰 할인액 : <span id="coupon_price">0</span>원</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>쿠폰 적립액 : <span id="coupon_emoney">0</span>원</b></div>
		<table width="100%" border="2" bordercolor="#D6D6D6" frame="hsides" rules="rows" style="border-collapse:collapse;" cellpadding="4" id="tb">
			<col width="30" align="center"><col width="100"><col width="150" align="center"><col width="110" align="center"><col width="50" align="center"><col width="100" align="center">
			<tr bgcolor="#F0F0F0" height="23" class="input_txt">
				<th>선택</th>
				<th>쿠폰</th>
				<th>적용상품</th>
				<th>종료일</th>
				<th>기능</th>
				<th>할인/적립</th>
			</tr>
<? for($i = 0, $imax = count($loop); $i < $imax; $i++) { ?>
			<tr height="40">
				<td class="noline"><input type="<?=($cfgCoupon['double']) ? "checkbox" : "radio"?>" name='coupon[]' onclick="calcuCoupon(this,'<?=$loop[$i]['payMethod']?>')" value='<?=$loop[$i]['couponcd']?>'></td>
				<td><?=$loop[$i]['coupon'].(($loop[$i]['payMethod'] == 1) ? '<div class="small red">무통장 입금으로 결제시에만 적용됩니다.</div>' : '')?></td>
				<td>
					<?=($loop[$i]['goodsnms']) ? '<div><button onclick="view_goods('.$i.', '.count($loop).');">적용상품보기</button></div>' : '-'?>
					<div class="applyGoods" id="goodsnm_<?=$i?>">
						<div><? for($j = 0, $jmax = count($loop[$i]['goodsnms']); $j < $jmax; $j++) echo $loop[$i]['goodsnms'][$j]."<br/>"; ?></div>
					</div>
				</td>
				<td><?=($loop[$i]['priodtype'] == 1) ? " 발급 후 ".$loop[$i]['sdate'] : substr($loop[$i]['edate'], 0, 10);?></td>
				<td><?=$r_couponAbility[$loop[$i]['ability']]?></td>
				<td><?=(substr($loop[$i]['price'], -1) != '%') ? number_format($loop[$i]['price']).'원' : number_format($loop[$i]['price']).'%'?><br/>(<?=number_format($loop[$i]['apr'])?>원)</td>
			</tr>
<? } ?>
		</table>
		<div style="padding:10px" align=center><a href="javascript:window.close()"><img src="../img/btn_close_s.gif"></a></div>
	</td>
</tr>
</table>
</body>
</html>
