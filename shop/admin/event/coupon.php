<?
$location = "����������� > ��������Ʈ";
include "../_header.php";
include "../../lib/page.class.php";

$db_table = "".GD_COUPON." a left join ".GD_COUPON_APPLY." b on a.couponcd=b.couponcd";

$pg = new Page($_GET[page]);
$pg -> field = "a.*,count(b.sno) cnt";

if($_GET[goodstype] == null)$_GET[goodstype]='a';
$checked[goodstype][$_GET[goodstype]] = " checked";
$checked[c_screen][$_GET[c_screen]] = " checked";

$selected[skey][$_GET[skey]] = ' selected';
$selected[dtkind][$_GET[dtkind]] = " selected";
$selected[gkey][$_GET[gkey]] = "selected";

if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}

if($_GET[goodstype] != null && $_GET[goodstype] != 'a'){
	$where[] = "goodstype='$_GET[goodstype]'";
	if($_GET[goodstype]){
		if($category){
			$db_table .= " left join ".GD_COUPON_CATEGORY." d on a.couponcd=d.couponcd";
			$where1[] = "d.category like '$category%'";
		}
		if($_GET[gword]){
			if($_GET[gkey] != 'goodsno'){
				$res = $db->query("select goodsno from ".GD_GOODS." where $_GET[gkey] like '%$_GET[gword]%'");
				while($data = $db->fetch($res)) $arr[] = $data[goodsno];
				if($arr) $where1[] = "c.goodsno in (".implode(',',$arr).")";
			}else{
				$where1[] = "c.goodsno = '$_GET[gword]'";
			}
			$db_table .= " left join ".GD_COUPON_GOODSNO." c on a.couponcd = c.couponcd";
		}
		if($where1) $where[] = "(".implode(' OR ',$where1).")";
	}
}

if ($_GET[sword]){
	$t_skey = ($_GET[skey]=="all") ? "concat( a.couponcd,coupon )" : $_GET[skey];
	$where[] = "$t_skey like '%$_GET[sword]%'";
}

if( $_GET[ability] ){
	foreach($_GET[ability] as $v) $checked[ability][$v] = " checked";
	$where[] = "ability in (".implode(',',$_GET[ability]).")";
}
if( $_GET[coupontype] ){
	foreach($_GET[coupontype] as $v) $checked[coupontype][$v] = " checked";
	$where[] = "coupontype in (".implode(',',$_GET[coupontype]).")";
}

if($_GET[regdt][0] && $_GET[regdt][1]){
	$tmpwhere = "{$_GET[dtkind]} between date_format({$_GET[regdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[regdt][1]},'%Y-%m-%d 23:59:59')";
	if($_GET[dtkind] == 'sddate') $tmpwhere = "(sdate <= date_format(".$_GET[regdt][0].",'%Y-%m-%d 00:00:00') AND edate >= date_format(".$_GET[regdt][1].",'%Y-%m-%d 00:00:00') AND priodtype='0') OR (priodtype='1' )";
	/*AND ADDDATE(regdt,INTERVAL sdate DAY) between date_format(".$_GET[regdt][0].",'%Y-%m-%d 00:00:00') and date_format(".$_GET[regdt][1].",'%Y-%m-%d 23:59:59'))*/
	if($tmpwhere) $where[] = $tmpwhere;
}

if($_GET['c_screen']) {
	$where[] = "a.c_screen='".$_GET['c_screen']."'";
}

if(count($where))
	$pg->cntQuery = "select count(distinct a.couponcd) from $db_table where " . implode(" and ", $where);
else
	$pg->cntQuery = "select count(distinct a.couponcd) from $db_table";

$pg->setQuery($db_table,$where,"regdt desc",'group by a.couponcd');
$pg->exec();

$res = $db->query($pg->query);
?>
<script language=javascript>
function delCoupon(couponcd){
	var f = document.forms[0];
	if(confirm('������ �����Ͻø� �߱������鵵 ���� ������ϴ�.\n���� �� ���� ���������� �����Ͻ� �� �����ϴ�.\n���� �����Ͻðڽ��ϱ�?')){
		f.mode.value = 'delete';
		f.couponcd.value = couponcd;
		f.method = 'post';
		f.action = "indb.coupon.php";
		f.submit();
	}
}

function chkGoodsType(){
	if(document.getElementsByName("goodstype")[2].checked) document.getElementById('category_id').style.display='block';
	else document.getElementById('category_id').style.display='none';
}

function setCouponType() {
	var c_screen = document.getElementsByName('c_screen');
	var c_screen_val = "";
	for(var i=0; i< c_screen.length; i++) {
		
		if(c_screen[i].checked == true) {
			c_screen_val = c_screen[i].value;
		}
	}

	var c_coupon_type = document.getElementsByName('coupontype[]');

	if(c_screen_val == 'm') {
		
		for(var i=0; i< c_coupon_type.length; i++) {
		
			if(c_coupon_type[i].value == '2' || c_coupon_type[i].value == '3') {
				c_coupon_type[i].disabled= true;
			}
		}
	}
	else {

		for(var i=0; i< c_coupon_type.length; i++) {
		
			c_coupon_type[i].disabled= false;
		}

	}
}
document.observe('dom:loaded', function(){
	setCouponType();
});

</script>
<div class="title title_top">��������Ʈ<span>������ �߱޵� ������ �����ϰų� ������ �߱��մϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=13')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
<td>
<form>
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=couponcd value="">
<table class=tb>
<col class=cellC><col class=cellL style="width:250"><col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>�����˻� (����)</td>
	<td>
	<select name=skey>
		<option value="all"> = ���հ˻� =
		<option value="couponcd" <?=$selected[skey][couponcd]?>> ������ȣ
		<option value="coupon" <?=$selected[skey][coupon]?>> ������
	</select>
	<input type=text name=sword value="<?=$_GET[sword]?>" class=line>
	</td>
	<td><font class=small1>�������</td>
	<td><font class=small1 color=555555>
		<?
		foreach($r_couponAbility as $k => $v){
		?>
		<input class=null type=checkbox name='ability[]' value='<?=$k?>' <?=$checked[ability][$k]?>><?=$v?>
		<?}?>
	</td>
</tr>

<tr>
	<td><font class=small1>�����ǰ����</td>
	<td colspan=3>
		<div style="padding:5,0,0,0"><font class=small1 color=555555>
			<input type=radio name=goodstype value='a' onclick="chkGoodsType();" class=null <?=$checked[goodstype][a]?>> ��ü
			<input type=radio name=goodstype value='0' onclick="chkGoodsType();" class=null <?=$checked[goodstype][0]?>> ��ü��ǰ
			<input type=radio name=goodstype value='1' onclick="chkGoodsType();" class=null <?=$checked[goodstype][1]?>> Ư�� ��ǰ �� Ư�� ī�װ�</font>
		</div>
		<div style='display:none;' id='category_id'>
			<div style="padding:5,0,5,0">�з����� : <script>new categoryBox('cate[]',4,'<?=$category?>','');</script></div>
			<div style="padding:0,0,5,0"><select name=gkey>
			<option value="goodsnm" <?=$selected[gkey][goodsnm]?>>��ǰ��
			<option value="goodsno" <?=$selected[gkey][goodsno]?>>������ȣ
			<option value="goodscd" <?=$selected[gkey][goodscd]?>>��ǰ�ڵ�
			<option value="keyword" <?=$selected[gkey][keyword]?>>����˻���
			</select>
			<input type=text name=gword class=lline style="width:200" value="<?=$_GET[gword]?>"></div>
		</div>

	</td>
</tr>
<tr>
	<td><font class=small1>�����߱޹��</td>
	<td colspan=3><font class=small1 color=555555>
		<div class="noline"> 
			<label><input type="radio" name="c_screen" value="" <?=$checked[c_screen]['']?> onClick="javascript:setCouponType();" /> ��ü</label>
			<label><input type="radio" name="c_screen" value="m" <?=$checked[c_screen]['m']?> onClick="javascript:setCouponType();" /> ���������</label>
		</div>
			
		<?
		foreach($r_couponType as $k => $v){
		?>
		<input class=null type=checkbox name='coupontype[]' value='<?=$k?>' <?=$checked[coupontype][$k]?>><?=$v?>
		<?}?>
	</td>
</tr>
<tr>
	<td><font class=small1>����������/�Ⱓ</td>
	<td colspan=3>
		<span class="noline small1" style="color:5C5C5C; margin-right:20px;">
		<select name=dtkind>
			<option value="a.regdt" <?=$selected[dtkind]['regdt']?>>������
			<option value="sddate" <?=$selected[dtkind]['sddate']?>>����Ⱓ
		</select>
		</span>
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
</table>
<div class="button_top" style="float:center"><input type=image src="../img/btn_search2.gif"></div>
</form>
</td>
</tr>
</table>



<div align=right style="padding:10 5 3 0">�� <font class=small1 color=333333>��ڰ� ȸ������ �߱��ϴ� ������ �Ʒ� <font color=EA0095><b>ȸ���߱��ϱ�</b></font> ��ư�� ���� ȸ������ ������ �߱��ϼ���.</font></div>


<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr class=rndbg>
	<th><font class=small1>������</th>
	<th><font class=small1>��������</th>
	<th><font class=small1>����������</th>
	<th><font class=small1>���</th>
	<th><font class=small1>���αݾ�(��)</th>
	<th><font class=small1>����Ⱓ</th>
	<th align=left style="padding-left:3"><font class=small1>�߱�/��ȸ(�߱޼�)</th>
	<th><font class=small1>����/����</th>
</tr>
<tr><td class=rnd colspan=11></td></tr>
<?
while ($data=$db->fetch($res)){
	$trBgColor = "#FFFFFF";
	$end_event = "";
	if(substr($data[edate],0,10) < date("Y-m-d", time()) && $data[priodtype] != 1 && $data[edate] !=""){
		$trBgColor = "#F3F3F3";
		$end_event = "<font class=small color=EA0095>(����)</font>";
	}
	$tt = '%';
	if(substr($data[price],-1) != '%') $tt = '��';

	if($data[priodtype] == 1)$data[priod] = "�߱� �� ".$data[sdate]." ��";
	else $data[priod] = $data[sdate]."<br>~".$data[edate];

	$applymsg = "<img src=../img/btn_coupon_mem_view.gif align=absmiddle> <font color=0074BA><b>(".number_format($data[cnt]).")</b></font>";
	switch ($data[coupontype]){
		case "0" :
				$applymsg = "<img src=../img/btn_coupon_mem_issue.gif align=absmiddle> <font color=EA0095><b>(".number_format($data[cnt]).")</b></font>";
				$apply = "<a href='coupon_apply.php?couponcd=".$data[couponcd]."'>".$applymsg."</a>";
			break;
		case "1" :
			$apply = "<a href='coupon_apply.php?couponcd=".$data[couponcd]."'>".$applymsg."</a>";
			break;
		case "2" :
			$apply = "<a href='coupon_apply.php?couponcd=".$data[couponcd]."'>".$applymsg."</a>";
			break;
		case "3" :
			$apply = "<a href='coupon_apply.php?couponcd=".$data[couponcd]."'>".$applymsg."</a>";
			break;
	}

?>
<tr height=35 bgcolor=<?=$trBgColor?>>
	<td align=center><font class=small1 color=555555><b><?=$data[coupon]?></b><?=$end_event?></td>
	<td align=center><? if($data[c_screen]== 'm') {?><font class=small1 color=EA0095>(���������)</font><br /><?}?><font class=small1 color=0074BA><b><?=$r_couponType[$data[coupontype]]?></b></td>
	<td align=center style="font-family: verdana;font-size:7pt;letter-spacing:-1"><font class=ver71 color=555555><?=$data[regdt]?></td>
	<td align=center><font class=small1 color=0074BA><b><?=$r_couponAbility[$data[ability]]?></td>
	<td align=center><font class=small color=EA0095><b><?=number_format($data[price])?><?=$tt?></td>
	<td align=center><font class=small color=555555><div><?=$data[priod]?></div>

	<td align=left style="padding-left:5"><font class=small1 color=555555><?=$apply?></td>
	<td align=center>
		<? if($data[c_screen]== 'm') {?>
		<font class=small1 color=555555><a href="coupon_mobile_register.php?couponcd=<?=$data[couponcd]?>"><img src="../img/i_edit.gif"></a> <a href="javascript:delCoupon(<?=$data[couponcd]?>)"><img src="../img/i_del.gif"></a></font>
		<?} else {?>
		<font class=small1 color=555555><a href="coupon_register.php?couponcd=<?=$data[couponcd]?>"><img src="../img/i_edit.gif"></a> <a href="javascript:delCoupon(<?=$data[couponcd]?>)"><img src="../img/i_del.gif"></a></font>
		<?}?>
	</td>
</tr>
<tr><td colspan=11 class=rndline></td></tr>
<? } ?>
</table>

<div class="pageNavi" align=center><font class=ver8><?=$pg->page[navi]?></div>

</form>
<div style="padding-top:15px"></div>

<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>��ù߱������� ���� '�߱��ϱ�'�� Ŭ���Ͽ� ���� ȸ������ �߱��ؾ� �մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>�ڵ����� �߱޵Ǵ� �������� '��ȸ�ϱ�'�� ������ �����߱޳���� �߱޹��� ȸ���� ��ȸ�� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01');chkGoodsType();</script>



<? include "../_footer.php"; ?>