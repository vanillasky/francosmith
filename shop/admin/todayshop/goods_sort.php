<?

//$hiddenLeft = 1;
$location = "투데이샵 > 상품노출관리";
include "../_header.php";
include "../../lib/page.class.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}
$tsCfg = $todayShop->cfg;

// 노출방식 설정
$checked['sortOrder'][$tsCfg['sortOrder']] = "checked";

// 현재 판매중인 상품 리스트를 가져옴
$query = "
	SELECT
		tg.tgsno, tg.startdt, tg.enddt, go.price, go.consumer, g.goodsnm, g.img_i, g.img_s, tg.sms, tg.limit_ea, tg.showtimer, tg.visible
	FROM ".GD_TODAYSHOP_GOODS." AS tg
	INNER JOIN ".GD_GOODS." AS g ON tg.goodsno = g.goodsno

	LEFT JOIN ".GD_GOODS_OPTION." AS go ON tg.goodsno=go.goodsno AND go.link=1 and go_is_deleted <> '1'
	WHERE
			 /*tg.visible=1 AND*/ g.runout = 0
		AND (tg.startdt IS NULL OR now() >= tg.startdt)
		AND (tg.enddt IS NULL OR now() <= tg.enddt)

	".($todayShop->getGoodsSortSql($todayShop->cfg['sortOrder']))."
";
$rs = $db->query($query);
?>
<script type="text/javascript">
<!--
	Timer = {
		timers : new Array(),
		itv : new Array(),
		setTimer : function(nm) {
			var rTm = --Timer.timers[nm];
			var rDay = Math.floor(rTm / (24 * 60 * 60));
			rTm -= rDay * (24 * 60 * 60);
			var rHour = Math.floor(rTm / (60 * 60));
			rTm -= rHour * (60 * 60);
			var rMin = Math.floor(rTm / (60));
			rTm -= rMin * (60);
			var rSec = rTm;

			if (parseInt(rDay) == 0 && parseInt(rHour) == 0 && parseInt(rMin) == 0 && parseInt(rSec) == 0) {
				clearInterval(Timer.itv[nm]);
				Timer.itv[nm] = null;
				$(nm).innerHTML = "종료";
				return;
			}

			rDay = (rDay > 0)? rDay+"일 " : "";
			rHour = (rHour < 10)? "0"+rHour : rHour;
			rMin = (rMin < 10)? "0"+rMin : rMin;
			rSec = (rSec < 10)? "0"+rSec : rSec;

			$(nm).innerHTML = rDay + rHour + ":" + rMin + ":" + rSec;
		},
		getTimer : function(objNm, startDt, startTm, closeDt, closeTm) {
			new Ajax.Request("./todayshop_timer.php", {
				method: "post",
				parameters: "startDt="+startDt+"&startTm="+startTm+"&closeDt="+closeDt+"&closeTm="+closeTm,
				onSuccess: function(req) {
					var res=req.responseJSON;

					if (res.status == 'ing') {
						Timer.timers[objNm] = ++res.remainTm;
						Timer.setTimer(objNm);
						if (Timer.itv[objNm]) {
							clearInterval(Timer.itv[objNm]);
							Timer.itv[objNm] = null;
						}
						Timer.itv[objNm] = setInterval(function() {Timer.setTimer(objNm); }, 1000);
						setTimeout(function() {Timer.getTimer(objNm, startDt, startTm, closeDt, closeTm); }, 1000 * 60 * 2);
					}
				},
				onFailure: function() { }
			});
		}
	}

	Item = {
		selectedItemNum : null,
		selectedItem : null,
		selectItem : function(obj) {
			if (!document.getElementsByName("sortOrder")[2].checked) return;
			for(var i = 0; i < obj.parentNode.childNodes.length; i++) {
				if (obj.parentNode.childNodes[i].nodeType != 1 || obj.parentNode.childNodes[i].tagName != "TR") continue;
				obj.parentNode.childNodes[i].style.backgroundColor = "";
				if (obj.parentNode.childNodes[i] == obj) {
					obj.style.backgroundColor = '#FFE1D6';
					Item.selectedItem = obj;
					Item.selectedItemNum = Item.selectedItem.getElementsByTagName("FONT")[0].innerHTML;
				}
			}
		},
		moveItem : function(e) {
			var evt = (window.event)? window.event : e;
			var keycode = (evt.keyCode)? evt.keyCode : evt.which;

			if ((keycode != 38 && keycode != 40) || Item.selectedItem == null || Item.selectedItemNum == null) return;
			evt.returnValue = false;

			switch(keycode) {
				case 38 : { // up
					Item.moveUp();
					break;
				}
				case 40 : { // down
					Item.moveDown();
					break;
				}
			}
		},
		moveUp : function() {
			if (Item.selectedItemNum == 1) return;

			var beforeItem = new Array();
			beforeItem[3] = Item.prevRow(Item.selectedItem, 2);
			beforeItem[2] = Item.prevRow(beforeItem[3]);
			beforeItem[1] = Item.prevRow(beforeItem[2]);
			beforeItem[0] = Item.prevRow(beforeItem[1]);

			var targetLoc = Item.nextRow(Item.selectedItem, 3);
			for(var i = 0; i < beforeItem.length; i++) {
				Item.selectedItem.parentNode.insertBefore(beforeItem[i], targetLoc);
			}
			Item.setItemNum(Item.selectedItem, Item.getItemNum(Item.selectedItem)-1);
			Item.setItemNum(beforeItem[1], Item.getItemNum(beforeItem[1])+1);
			Item.selectedItemNum--;
		},
		moveDown : function() {
			if(!Item.nextRow(Item.selectedItem, 3)) return;

			var nextItem = new Array();
			nextItem[0] = Item.nextRow(Item.selectedItem, 3);
			nextItem[1] = Item.nextRow(nextItem[0]);
			nextItem[2] = Item.nextRow(nextItem[1]);
			nextItem[3] = Item.nextRow(nextItem[2]);

			var targetLoc = Item.prevRow(Item.selectedItem);
			for(var i = 0; i < nextItem.length; i++) {
				Item.selectedItem.parentNode.insertBefore(nextItem[i], targetLoc);
			}
			Item.setItemNum(Item.selectedItem, Item.getItemNum(Item.selectedItem)+1);
			Item.setItemNum(nextItem[1], Item.getItemNum(nextItem[1])-1);
			Item.selectedItemNum++;
		},
		prevRow : function(obj, step) {
			if (!step) step = 1;
			var pStep = 0;
			var pRow = obj;
			while(pRow = pRow.previousSibling) {
				if (pRow.nodeType == 1 && pRow.tagName == "TR") {
					if (++pStep == step) return pRow;
				}
			}
			return null;
		},
		nextRow : function(obj, step) {
			if (!step) step = 1;
			var nStep = 0;
			var nRow = obj;
			while(nRow = nRow.nextSibling) {
				if (nRow.nodeType == 1 && nRow.tagName == "TR") {
					if (++nStep == step) return nRow;
				}
			}
			return null;
		},
		getItemNum : function(obj) {
			return parseInt(obj.getElementsByTagName("FONT")[0].innerHTML);
		},
		setItemNum : function(obj, value) {
			obj.getElementsByTagName("FONT")[0].innerHTML = value;
		},
		init : function(mode) {
			var cursor = "";
			if (mode == 'admin') {
				cursor = "pointer";
			}
			else {
				if(Item.selectedItem) Item.selectedItem.style.backgroundColor = '';
				Item.selectedItemNum = null;
				cursor = "";
			}

			var list = document.getElementById("tblList");
			var trs = list.getElementsByTagName("TR");
			for(var i = 0; i < trs.length; i++) {
				if (trs[i].onmousedown) {
					trs[i].style.cursor = cursor;
				}
			}
		}
	}

	document.onkeydown = Item.moveItem;
//-->
</script>

<form name="frmSort" action="indb.goods_sort.php" method="post" target="ifrmHidden">
	<div class="title title_top">상품노출관리<span>상품의 노출순서를 설정할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=9')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>노출방식 설정</td>
		<td class="noline">
			<label><input type="radio" name="sortOrder" value="open" <?=$checked['sortOrder']['open']?> onclick="Item.init(this.value)" /> 시작일순</label>
			<label><input type="radio" name="sortOrder" value="close" <?=$checked['sortOrder']['close']?> onclick="Item.init(this.value)" /> 종료일순</label>
			<label><input type="radio" name="sortOrder" value="admin" <?=$checked['sortOrder']['admin']?> onclick="Item.init(this.value)" /> 관리자 지정</label>
			<!--label><input type="radio" name="sortOrder" value="random" <?=$checked['sortOrder']['random']?> onclick="Item.init(this.value)" /> 랜덤설정</label-->
		</td>
	</tr>
	</table>
	<p/>
	<table cellpadding=0 cellspacing=0 class=small_tip bgcolor=F7F7F7 width=100%>
	<tr><td height=10></td></tr>
	<tr><td style="padding-left:20px"><img src="../img/arrow_downorg.gif" align=absmiddle> 상품노출 순서변경 도움말 <font class=extext>상품의 노출 순서를 관리자가 지정하는 경우에 사용하는 기능입니다.</font></td></tr>
	<tr><td style="padding-left:20px"><img src="../img/sa_cate_change.gif" style="border:2px solid #D4D3D3;"></td></tr>
	<tr><td height=10></td></tr>
	</table><div style="padding-top:15px"></div>

	<table id="tblList" width=100% cellpadding=0 cellspacing=0 border=0>
	<tr><td class=rnd colspan=7></td></tr>
	<tr class=rndbg>
		<th width=60>번호</th>
		<th></th>
		<th width=10></th>
		<th>상품명</th>
		<th width=150>진행기간</th>
		<th width=100>남은시간</th>
		<th width=50>진열</th>
	</tr>
	<tr><td class=rnd colspan=7></td></tr>
	<col width=40 span=2 align=center>
	<?
	$idx = 0;
	while ($data = $db->fetch($rs,1)) {
		$arrStart = explode(' ', $data['startdt']);
		$arrEnd = explode(' ', $data['enddt']);
	?>
	<tr><td height=4 colspan=7></td></tr>
	<tr height=25 onmousedown="Item.selectItem(this)">
		<td><font class=ver8 color=616161><?=++$idx?></font><input type="hidden" name="tgsno[]" value="<?=$data['tgsno']?>" /></td>
		<td style="border:1px #e9e9e9 solid;"><a href="../../todayshop/today_goods.php?tgsno=<?=$data['tgsno']?>" target=_blank><?=goodsimg($data['img_s'],40,'',1)?></a></td>
		<td></td>
		<td>
			<a href="./goods_reg.php?mode=modify&tgsno=<?=$data['tgsno']?>"><font color=303030><?=$data['goodsnm']?></a>
			<? if ($icon){ ?><div style="padding-top:3px"><?=$icon?></div><? } ?>
			<? if ($data['runout']){ ?><div style="padding-top:3px"><img src="../../data/skin/<?=$cfg['tplSkin']?>/img/icon/good_icon_soldout.gif"></div><? } ?>
		</td>
		<td align=center><font class=ver81 color=444444><?=$data['startdt']?> - <br/><?=$data['enddt']?></td>
		<td align=center><div id="timer<?=$data['tgsno']?>"></div><script type="text/javascript">Timer.getTimer("timer<?=$data['tgsno']?>", "<?=$arrStart[0]?>", "<?=$arrStart[1]?>", "<?=$arrEnd[0]?>", "<?=$arrEnd[1]?>")</script></td>
		<td align=center><?=str_replace(array('0','1'), array('N','Y'), $data['visible'])?></td>
	</tr>
	<tr><td height=4></td></tr>
	<tr><td colspan=7 class=rndline></td></tr>
	<? } ?>
	</table>
	<div align=center class=pageNavi><font class=ver8><?=$pg->page['navi']?></font></div>
	<div class="button">
		<input type=image src="../img/btn_register.gif">
		<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
	</div>
</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품 판매기간이 완료되면 다음 상품이 판매됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">판매되고 있는 상품의 노출 순서를 설정 할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">- 시작일순 : 설정한 진행기간의 시작일이 빠른 순으로 노출됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">- 완료일순 : 설정한 진행기간이 완료일에 임박한 순으로 노출됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">- 관리자지정 : 관리자가 직접 지정한 상품순으로 노출됩니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>