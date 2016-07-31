<?
// 설정부분
$sapop['divID']		= ($sapop['divID'])		? $sapop['divID']	: "sapDivID";	// 팝업 div의 id 값
$sapop['left']		= ($sapop['left'])		? $sapop['left']	: 185;			// 팝업 위치 : x좌표
$sapop['top']		= ($sapop['top'])		? $sapop['top']		: 170;			// 팝업 위치 : y좌표
$sapop['width']		= ($sapop['width'])		? $sapop['width']	: 500;			// 팝업 크기 : 가로
$sapop['height']	= ($sapop['height'])	? $sapop['height']	: 250;			// 팝업 크기 : 세로



@include "../../conf/config.purchase.php";
if($purchaseSet['popYn'] == "1" && is_numeric($purchaseSet['popStock']) && !$_COOKIE[$sapop['divID']]) {
	$sql = "
		SELECT
			O.goodsno, O.stock, G.goodsnm, P.pchsno, P.comnm, P.phone1, P.phone2
		FROM gd_goods_option AS O
			LEFT JOIN gd_goods AS G ON O.goodsno = G.goodsno AND open = '1'
			LEFT JOIN ".GD_PURCHASE_GOODS." AS PG ON O.goodsno = PG.goodsno
			LEFT JOIN ".GD_PURCHASE." AS P ON PG.pchsno = P.pchsno
		WHERE O.stock <= '".$purchaseSet['popStock']."' and go_is_deleted <> '1' GROUP BY PG.goodsno ORDER BY O.stock ASC LIMIT 0, 10";
	$rs = $db->query($sql);
	$sapop['total'] =$db->count_($rs);

	if($sapop['total'] > 0) {
?>
<script language="JavaScript">
	// varibale, value, period day
	function sapSetCookie(cKey, cValue, cPeriod) {
		var date = new Date();

		date.setDate(date.getDate() + cPeriod);
		document.cookie = cKey + '=' + escape(cValue) + ';expires=' + date.toGMTString();
	}

	function sapOpen() {
		$('<?=$sapop['divID']?>').style.display = "block";
		$('<?=$sapop['divID']?>').style.visibility = "visible";
	}

	function sapClose() {
		$('<?=$sapop['divID']?>').style.display = "none";
		$('<?=$sapop['divID']?>').style.visibility = "hidden";
	}

	function sapToday() {
		sapClose();
		sapSetCookie("<?=$sapop['divID']?>", "1", "1");
	}
</script>
<style type="text/css">
.sapopBoard { top:<?=$sapop['top']?>px; left:<?=$sapop['left']?>px; width:<?=$sapop['width']?>px; position:absolute; border:1px #848484 solid; background:#FFFFFF; z-index:2;}
.sapopBoard .saPopTitle { padding:0px 10px; font-size:12px; font-weight:bold; color:#333333; }
.sapopBoard .saPopDate { padding:2px 10px 7px 10px; font-size:11px; color:#666666; }
.sapopBoard .saPopList { padding:0px 10px; }
.sapopBoard .saPopList .saPopListBoard { border:0px #CCCCCC solid; }
.sapopBoard .saPopList .saPopListBoard .saPopListItem { padding:5px; border-bottom:1px #CCCCCC solid; }
.sapopBoard .saPopList .saPopPage {  }
.sapopBoard .saPopBtnArea { height:40px; padding:0px 10px; }
</style>
<div id="<?=$sapop['divID']?>" class="sapopBoard">
<div class="popDescArea">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td class="saPopTitle"><div class="title title_top">사입처 상품 매진 알림 서비스</div></td>
</tr>
<tr>
	<td class="saPopDate">
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td align="left"><?=date("Y년 m월 d일 상품 매진 알림")." ( 총 <span style=\"color:#FF0000;\">".$sapop['total']."건</span> )";?></td>
			<td align="right"><a href="../goods/purchase_soldout.php" style="color:#FF0000;">더보기</a></td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td id="saListArea" class="saPopList"><table cellpadding="0" cellspacing="0" border="0" width="100%" class="saPopListBoard">
		<tr><td class="rnd" colspan="5"></td></tr>
		<tr align="center" valign="middle" class="rndbg">
			<td>번호</td>
			<td>상품명</td>
			<td>재고량</td>
			<td>사입처</td>
			<td>연락처</td>
		</tr>
		<tr><td class="rnd" colspan="5"></td></tr>
<?
		for($i = 0; $row = $db->fetch($rs); $i++) {
			if($row['phone1'] == "--") $row['phone1'] = "";
?>
		<tr align="center" valign="middle">
			<td class="saPopListItem"><?=$sapop['total'] - $i?></td>
			<td class="saPopListItem" align="left" title="<?=$row['goodsnm']?>"><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$row['goodsno']?>',850,600)"><?=strcut($row['goodsnm'], 30)?></a></td>
			<td class="saPopListItem"><?=number_format($row['stock'])?></td>
			<td class="saPopListItem"><a href="../goods/purchase_info.php?mode=pchs_mod&pchsno=<?=$row['pchsno']?>"><?=($row['comnm']) ? $row['comnm'] : "&nbsp;"?></td>
			<td class="saPopListItem"><?=($row['phone1']) ? $row['phone1']." <a href=\"javascript:popup('../member/popup.sms.php?mobile=".str_replace("-", "", $row['phone1'])."',780,600)\"><img src=\"../img/btn_smsmailsend.gif\" align=\"absmiddle\"></a>" : "&nbsp;"?></td>
		</tr>
<?
		}
?>
		<tr align="center" valign="middle">
			<td colspan="5" class="saPopPage"></td>
		</tr>
	</table></td>
</tr>
<tr>
	<td class="saPopBtnArea" align="right" valign="middle"><table cellpadding="3" cellspacing="0" border="0" class="saPopBtnBoard">
		<tr>
			<td>
				<input type="checkbox" name="saPopTodayBtn" id="saPopTodayBtn" onclick="sapToday();" />
				<label for="saPopTodayBtn">오늘 서비스 창 닫기</label> &nbsp;
				<a href="javascript:;" title="닫기" onclick="sapClose();"><img src="../img/btn_delinum_close.gif" align="absbottom" /></a>
			</td>
		</tr>
	</table></td>
</tr>
</table>
</div>
</div>
<?
	}
}
?>