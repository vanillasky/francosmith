<?
include "../_header.popup.php";
include "../../lib/page.class.php";

list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_PURCHASE); # 총 레코드수

if (!$_GET['page_num']) $_GET['page_num'] = 5;
$orderby = ($_GET['sort']) ? $_GET['sort'] : "regdt DESC"; # 정렬 쿼리

### 변수할당
$selected['skey'][$_GET['skey']]			= "selected";
$selected['page_num'][$_GET['page_num']]	= "selected";
$selected['sort'][$orderby]					= "selected";

### 목록
$db_table = GD_PURCHASE;
//if($_GET['ctrlType'] != "goods") $where[] = " comcd != '0000'";

if($_GET['sword']) {
	if($_GET['skey'] == "all") {
		$where[] = "CONCAT(comnm, ceonm, phone1, phone2) LIKE '%".$_GET['sword']."%'";
	}
	else if($_GET['skey'] == "phone") {
		$where[] = "CONCAT(phone1, phone2) LIKE '%".$_GET['sword']."%'";
	}
	else {
		$where[] = $_GET['skey']." LIKE '%".$_GET['sword']."%'";
	}
}

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->setQuery($db_table, $where, "ordgrade DESC, ".$orderby);
$pg->exec();
$res = $db->query($pg->query);

$qstr = "skey=".$_GET['skey']."&sword=".$_GET['sword']."&sort=".$_GET['sort']."&page_num=".$_GET['page_num']."&ctrlType=".$_GET['ctrlType']."&targetNo=".$_GET['targetNo'];

switch($_GET['ctrlType']) {
	case "url" :
		$funcCtrl = "urlPurchase()";
		break;
	case "goods" :
		$funcCtrl = "goodsPurchase(".$_GET['targetNo'].")";
		break;
	default :
		$funcCtrl = "selPurchase()";
		break;
}
?>
<script>
function selPurchase() {
	var rdo = document.getElementsByName('chk');

	for(i = 0; i < rdo.length; i++) {
		if(rdo[i].checked) {
			tmpValue = rdo[i].value;

			sel = opener.document.getElementById('pchsno');
			for(j = 1; j < sel.length; j++) {

				if(sel.options[j].value == tmpValue) {
					sel.options[j].selected = true;
					self.close();
					return true;
				}
			}
		}
	}

	alert("사입처를 선택해주세요.");
}

function urlPurchase() {
	var rdo = document.getElementsByName('chk');

	for(i = 0; i < rdo.length; i++) {
		if(rdo[i].checked) {
			opener.location.href = opener.location.pathname + "?pchsno=" + rdo[i].value;
			self.close();
			return true;
		}
	}

	alert("사입처를 선택해주세요.");
}

function goodsPurchase(targetNo) {
	var rdo = document.getElementsByName('chk');

	for(i = 0; i < rdo.length; i++) {
		if(rdo[i].checked) {
			opener.document.getElementById('pgno_' + targetNo).value = rdo[i].value;
			opener.document.getElementById('comnm_' + targetNo).innerHTML = rdo[i].title;
			self.close();
			return true;
		}
	}

	alert("사입처를 선택해주세요.");
}
</script>

<div class="title title_top">업체 선택</div>

<form style="margin:0px; padding:0px;">
<table cellpadding="4" cellspacing="0" border="0" width="100%">
<tr>
	<td>
		<select name="skey">
			<option value="all" <?=$selected['skey']['all']?>> 통합검색 </option>
			<option value="comnm" <?=$selected['skey']['comnm']?>> 사입처명 </option>
			<option value="ceonm" <?=$selected['skey']['ceonm']?>> 대표자명 </option>
			<option value="phone" <?=$selected['skey']['phone']?>> 연락처 </option>
		</select>
		<input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" />
		<input type="image" align="absmiddle" style="border:0px;" src="../img/btn_search2.gif" />
	</td>
</tr>
</table>
</form>

<form name="pList" method="post">
<input type="hidden" name="mode" />
<input type="hidden" name="qstr" value="<?=$qstr?>" />
<input type="hidden" name="query" value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>" />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="9"></td></tr>
<tr class="rndbg">
	<th><a href="javascript:chkBox(document.getElementsByName('chk'),'rev');" class="white">선택</a></th>
	<th>사입처</th>
	<th>대표자</th>
	<th>연락처</th>
	<th>최근 등록일</th>
	<th>등록 상품</th>
</tr>
<tr><td class="rnd" colspan="9"></td></tr>
<?
while($data = $db->fetch($res)) {
	if($data['comcd'] == "0000") list($data['count']) = $db->fetch("SELECT COUNT(G.goodsno) FROM gd_goods AS G LEFT JOIN ".GD_PURCHASE_GOODS." AS PG ON G.goodsno = PG.goodsno WHERE PG.pchsno IS NULL");
	else {
		$rs = $db->query("SELECT goodsno FROM ".GD_PURCHASE_GOODS." WHERE pchsno = '".$data['pchsno']."' GROUP BY goodsno");
		$data['count'] = $db->count_($rs);
	}
?>
<tr height=40 align="center">
	<td class="noline"><input type="radio" name="chk" value="<?=$data['pchsno']?>" title="<?=$data['comnm']?>" /></td>
	<td><font class="small" color="#616161"><?=$data['comnm']?></font></td>
	<td><font class="small" color="#616161"><?=$data['ceonm']?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['phone1']?></font></td>
	<td><font class="ver81" color="#616161"><?=substr($data['regdt'], 0, 10)?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['count']?></font></td>
</tr>
<tr><td colspan="9" class="rndline"></td></tr>
<? } ?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td height="35" align="center"><font class="ver8"><?=$pg->page['navi']?></font></td>
</tr>
<tr>
	<td height="35" align="center"><a href="javascript:;" onclick="<?=$funcCtrl?>"><img src="../img/btn_cancelconfirm.gif" /></a></td>
</tr>
</table>

</form>
<body>
</html>