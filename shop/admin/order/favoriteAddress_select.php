<?
	include "../_header.popup.php";
	include "../../conf/config.pay.php";
	include "../../lib/page.class.php";

	$page = ($_GET['page']) ? $_GET['page'] : 1;
	$page_num = 10;
	$orderby = "regdt DESC";
	$db_table = GD_FAVORITE_ADDRESS;

	list($total) = $db->fetch("SELECT COUNT(*) FROM ".$db_table);

	if($_GET['fa_group']) $where[] = "fa_group = '".$_GET['fa_group']."'";

	if($_GET['skey'] && $_GET['sval']) {
		if($_GET['skey'] == 'all') $where[] = "(CONCAT(fa_name, fa_address, fa_road_address, fa_address_sub, fa_phone, fa_mobile, fa_memo) LIKE '%".$_GET['sval']."%')";
		else if($_GET['skey'] == 'fa_address') $where[] = "(CONCAT(fa_address, fa_road_address, fa_address_sub) LIKE '%".$_GET['sval']."%')";
		else $where[] = $_GET['skey']." LIKE '%".$_GET['sval']."%'";
	}

	$pg = new Page($page, $_GET['page_num']);
	$pg->field = "fa_no, fa_group, fa_name, fa_email, fa_zipcode, fa_zonecode, fa_address, fa_road_address, fa_address_sub, fa_phone, fa_mobile, fa_memo";
	$pg->setQuery($db_table, $where, $orderby);
	$pg->exec();

	$res = $db->query($pg->query);

	$qr = "SELECT fa_group FROM $db_table GROUP BY fa_group ORDER BY fa_group ASC";
	$rs = $db->query($qr);
?>
<style type="text/css">
	body { padding:0px; margin:5px; }
	.hiddenArea { display:none; }
	#fa_memoBox { z-index:1000; display:none; position:absolute; top:0; left:0; width:500px; padding:10px; -moz-opacity:.90; filter:alpha(opacity=90); opacity:.90; line-height:140%; background:#FFFFFF; color:#000000; border:1px #000000 solid; }
</style>
<script language="JavaScript">
	var nmr = '<?=$_GET['nmr']?>';
	var eml = '<?=$_GET['eml']?>';
	var zcd1 = '<?=$_GET['zcd1']?>';
	var zcd2 = '<?=$_GET['zcd2']?>';
	var zonecode = '<?=$_GET['zonecode']?>';
	var ad1 = '<?=$_GET['ad1']?>';
	var road_address = '<?=$_GET['road_address']?>';
	var div_road_address = '<?=$_GET['div_road_address']?>';
	var div_road_address_sub = '<?=$_GET['div_road_address_sub']?>';
	var ad2 = '<?=$_GET['ad2']?>';
	var phn1 = '<?=$_GET['phn1']?>';
	var phn2 = '<?=$_GET['phn2']?>';
	var phn3 = '<?=$_GET['phn3']?>';
	var mb1 = '<?=$_GET['mb1']?>';
	var mb2 = '<?=$_GET['mb2']?>';
	var mb3 = '<?=$_GET['mb3']?>';

	function applyAddress(no) {
		if(nmr) {
			opener.document.getElementById(nmr).value = document.getElementById('nameReceiver_' + no).innerHTML;
		}

		if(eml) {
			opener.document.getElementById(eml).value = document.getElementById('email_' + no).innerHTML;
		}

		if(zcd1 && zcd2) {
			tempZcd = document.getElementById('zipcode_' + no).innerHTML.split("-");

			opener.document.getElementById(zcd1).value = tempZcd[0];
			opener.document.getElementById(zcd2).value = tempZcd[1];
		}
		
		if(zonecode){
			tempZonecode = document.getElementById('zonecode_' + no).innerHTML;
			opener.document.getElementById(zonecode).value = tempZonecode;
		}

		if(ad1 && ad2) {
			if(ad1 == ad2) {
				opener.document.getElementById(ad1).value = document.getElementById('address_' + no).innerHTML + " " + document.getElementById('address_sub_' + no).innerHTML;
				if(document.getElementById('road_address_' + no).innerHTML != "") {
					opener.document.getElementById(road_address).value = document.getElementById('road_address_' + no).innerHTML + " " + document.getElementById('address_sub_' + no).innerHTML;
				} else {
					opener.document.getElementById(road_address).value = "";
				}
			}
			else {
				opener.document.getElementById(ad1).value = document.getElementById('address_' + no).innerHTML;
				opener.document.getElementById(ad2).value = document.getElementById('address_sub_' + no).innerHTML;

				if(document.getElementById('road_address_' + no).innerHTML != "") {
					opener.document.getElementById(road_address).value = document.getElementById('road_address_' + no).innerHTML;
					opener.document.getElementById(div_road_address).innerHTML = document.getElementById('div_road_address_' + no).innerHTML;
					opener.document.getElementById(div_road_address_sub).innerHTML = document.getElementById('address_sub_' + no).innerHTML;
				} else {
					opener.document.getElementById(road_address).value = "";
					opener.document.getElementById(div_road_address).innerHTML = "";
					opener.document.getElementById(div_road_address_sub).innerHTML = "";
				}

			}
		}

		if(phn1 && phn2 && phn3) {
			tempPhn = document.getElementById('phone_' + no).innerHTML.split("-");

			opener.document.getElementById(phn1).value = tempPhn[0];
			opener.document.getElementById(phn2).value = tempPhn[1];
			opener.document.getElementById(phn3).value = tempPhn[2];
		}

		if(mb1 && mb2 && mb3) {
			tempMb = document.getElementById('mobile_' + no).innerHTML.split("-");

			opener.document.getElementById(mb1).value = tempMb[0];
			opener.document.getElementById(mb2).value = tempMb[1];
			opener.document.getElementById(mb3).value = tempMb[2];
		}

		opener.setPayInfo();

		self.close();
	}

	function tooltipShow(obj) {

		var tooltip = document.getElementById('fa_memoBox');
		tooltip.innerText = obj.getAttribute('tooltip');

		var pos_x = event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
		var pos_y = event.clientY + document.body.scrollTop + document.documentElement.scrollTop;

		tooltip.style.top = (pos_y + 10) + 'px';
		tooltip.style.left = (pos_x - 510) + 'px';
		tooltip.style.display = 'block';
	}

	function tooltipHide(obj) {
		var tooltip = document.getElementById('fa_memoBox');
		tooltip.innerText = '';
		tooltip.style.display = 'none';
	}
</script>
<div id="fa_memoBox"></div>
<div style="margin-bottom:7px; font-weight:bold; font-size:14px; font-family:dotum;"><img src="../img/titledot.gif" align="absbottom" style="margin-right:5px;" />자주 쓰는 주소 <span class="extext">자주 사용하는 주소를 검색하여 등록합니다.</span></div>

<div style="margin:10px;">
<form name="findForm" style="margin:0px; padding:0px;">
<input type="hidden" name="mode" id="mode" value="selectAddress" />
<input type="hidden" name="nmr" value="<?=$_GET['nmr']?>" />
<input type="hidden" name="eml" value="<?=$_GET['eml']?>" />
<input type="hidden" name="zcd1" value="<?=$_GET['zcd1']?>" />
<input type="hidden" name="zcd2" value="<?=$_GET['zcd2']?>" />
<input type="hidden" name="zonecode" value="<?=$_GET['zonecode']?>" />
<input type="hidden" name="ad1" value="<?=$_GET['ad1']?>" />
<input type="hidden" name="ad2" value="<?=$_GET['ad2']?>" />
<input type="hidden" name="road_address" value="<?=$_GET['road_address']?>" />
<input type="hidden" name="div_road_address" value="<?=$_GET['div_road_address']?>" />
<input type="hidden" name="div_road_address_sub" value="<?=$_GET['div_road_address_sub']?>" />
<input type="hidden" name="phn1" value="<?=$_GET['phn1']?>" />
<input type="hidden" name="phn2" value="<?=$_GET['phn2']?>" />
<input type="hidden" name="phn3" value="<?=$_GET['phn3']?>" />
<input type="hidden" name="mb1" value="<?=$_GET['mb1']?>" />
<input type="hidden" name="mb2" value="<?=$_GET['mb2']?>" />
<input type="hidden" name="mb3" value="<?=$_GET['mb3']?>" />
<select name="fa_group" id="fa_group">
	<option value="">그룹</option>
<? while($gdata = $db->fetch($rs)) { ?>
	<option value="<?=$gdata['fa_group']?>" <?=($_GET['fa_group'] == $gdata['fa_group']) ? "selected" : ""?>><?=$gdata['fa_group']?></option>
<? } ?>
</select>
<select name="skey" id="skey">
	<option value="all">통합검색</option>
	<option value="fa_name" <?=($_GET['skey'] == "fa_name") ? "selected" : ""?>>이름</option>
	<option value="fa_address" <?=($_GET['skey'] == "fa_address") ? "selected" : ""?>>주소</option>
	<option value="fa_phone" <?=($_GET['skey'] == "fa_phone") ? "selected" : ""?>>연락처</option>
	<option value="fa_mobile" <?=($_GET['skey'] == "fa_mobile") ? "selected" : ""?>>휴대폰</option>
	<option value="fa_memo" <?=($_GET['skey'] == "fa_memo") ? "selected" : ""?>>메모</option>
</select>
<input type="text" name="sval" id="sval" value="<?=$_GET['sval']?>" />
<input type="image" src="../img/btn_search2.gif" align="absmiddle" style="border:0px;" />
</form>
</div>

<div style="color:#FF0000; font-size:11px; font-family:dotum;">* 검색된 주소의 성명을 클릭하면 주소정보가 등록됩니다.</div>

<div style="margin-top:10px;">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="8"></td></tr>
<tr class="rndbg">
	<th width="50">번호</th>
	<th width="80">그룹</th>
	<th width="80">성명</th>
	<th width="">주소</th>
	<th width="150">이메일</th>
	<th width="100">연락처</th>
	<th width="100">휴대폰</th>
	<th width="60">메모</th>
</tr>
<tr><td class="rnd" colspan="8"></td></tr>
<?
	while($data = $db->fetch($res)) {
		$data['tempZcd'] = explode("-", $data['fa_zipcode']);
		$data['tempPhone'] = explode("-", $data['fa_phone']);
?>
<div class="hiddenArea">
	<div id="nameReceiver_<?=$data['fa_no']?>"><?=$data['fa_name']?></div>
	<div id="email_<?=$data['fa_no']?>"><?=$data['fa_email']?></div>
	<div id="zipcode_<?=$data['fa_no']?>"><?=$data['fa_zipcode']?></div>
	<div id="zonecode_<?=$data['fa_no']?>"><?=$data['fa_zonecode']?></div>
	<div id="address_<?=$data['fa_no']?>"><?=$data['fa_address']?></div>
	<div id="road_address_<?=$data['fa_no']?>"><?=$data['fa_road_address']?></div>
	<div id="div_road_address_<?=$data['fa_no']?>"><?=$data['fa_road_address']?></div>
	<div id="address_sub_<?=$data['fa_no']?>"><?=$data['fa_address_sub']?></div>
	<div id="div_road_address_sub_<?=$data['fa_no']?>"><?=$data['fa_address_sub']?></div>
	<div id="phone_<?=$data['fa_no']?>"><?=$data['fa_phone']?></div>
	<div id="mobile_<?=$data['fa_no']?>"><?=$data['fa_mobile']?></div>
</div>
<tr height="30" align="center">
	<td><?=$pg->idx--?></td>
	<td><?=$data['fa_group']?></td>
	<td><a href="javascript:;" onclick="applyAddress('<?=$data['fa_no']?>')" style="color:#3482CA;text-decoration:underline;"><?=$data['fa_name']?></a></td>
	<td><?=$data['fa_zonecode']." (".$data['fa_zipcode'].") ".$data['fa_address']." ".$data['fa_address_sub']?><?if($data['fa_road_address']) { ?><div style="padding:5px 0 0 20px;font:12px dotum;color:#999;" id="div_road_address">[<?=$data['fa_road_address']." ".$data['fa_address_sub']?>]</div><? } ?></td>
	<td><?=$data['fa_email']?></td>
	<td><?=$data['fa_phone']?></td>
	<td><?=$data['fa_mobile']?></td>
	<? if($data['fa_memo']) { ?>
	<td style="cursor:pointer; color:#3482CA;" onmouseover="tooltipShow(this)" onmousemove="tooltipShow(this)" onmouseout="tooltipHide(this)" tooltip="<?=$data['fa_memo']?>">[보기]</td>
	<? } else { ?>
	<td>-</td>
	<? } ?>
</tr>
<tr><td colspan="8" style="height:1px; background:#DCD8D6;"></td></tr>
<?
	}
?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td align="center" height="35" style="padding-left:13px"><font class="ver8"><?=$pg->page['navi']?></font></td>
</tr>
</table>
</div>

</body>
</html>