<?php /* Template_ 2.2.7 2014/07/30 21:42:58 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/goods_event.htm 000012322 */ 
if (is_array($GLOBALS["r_cate"])) $TPL__r_cate_1=count($GLOBALS["r_cate"]); else if (is_object($GLOBALS["r_cate"]) && in_array("Countable", class_implements($GLOBALS["r_cate"]))) $TPL__r_cate_1=$GLOBALS["r_cate"]->count();else $TPL__r_cate_1=0;
if (is_array($GLOBALS["r_brand"])) $TPL__r_brand_1=count($GLOBALS["r_brand"]); else if (is_object($GLOBALS["r_brand"]) && in_array("Countable", class_implements($GLOBALS["r_brand"]))) $TPL__r_brand_1=$GLOBALS["r_brand"]->count();else $TPL__r_brand_1=0;
if (is_array($TPL_VAR["page_num"])) $TPL_page_num_1=count($TPL_VAR["page_num"]); else if (is_object($TPL_VAR["page_num"]) && in_array("Countable", class_implements($TPL_VAR["page_num"]))) $TPL_page_num_1=$TPL_VAR["page_num"]->count();else $TPL_page_num_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 현재위치 -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<TR>
	<TD class="b_cate"><img src="/shop/data/skin/freemart/img/common/icon_goodalign.gif" border=0 align=absmiddle><a href="<?php echo url("goods/goods_event.php?")?>&sno=<?php echo $_GET["sno"]?>"><?php echo $TPL_VAR["subject"]?></a></TD>
	<td class="path">HOME > <B>이벤트</B></td>
</TR>
<tr>
	<td height=1 bgcolor="#E6E6E6" colspan=2></td>
</tr>
<tr>
	<td height=10 colspan=2></td>
</tr>
</TABLE>


<?php echo $TPL_VAR["body"]?>

<div align="center"><?php echo $TPL_VAR["qrcode"]?></div>

<div class="indiv"><!-- Start indiv -->

<?php if($GLOBALS["r_cate"]){?>
<p />
<!-- 이벤트 카테고리 시작 -->
<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr>
	<td background="/shop/data/skin/freemart/img/common/ed_top_back.gif">
	<table width=100% border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td width=50%><img src="/shop/data/skin/freemart/img/common/ed_left_top.gif" border=0></td>
		<td width=50% align=right><img src="/shop/data/skin/freemart/img/common/ed_right_top.gif" border=0></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td bgcolor="#F5F5F5" style="padding:5px 10px; border-left:1px solid #E2E1E1; border-right:1px solid #E2E1E1"><img src="/shop/data/skin/freemart/img/common/icon_submenu.gif" border=0 align=absmiddle><b>상품분류</b></td>
</tr>
<tr>
	<td bgcolor="#F5F5F5" style="padding:5px 10px; border-left:1px solid #E2E1E1; border-right:1px solid #E2E1E1"></td>
</tr>
<tr>
	<td style="padding:5 10 5 10; line-height:20px; border-left:1px solid #E2E1E1; border-right:1px solid #E2E1E1">
<?php if($TPL__r_cate_1){$TPL_I1=-1;foreach($GLOBALS["r_cate"] as $TPL_V1){$TPL_I1++;?>
<?php if($GLOBALS["category"]==$TPL_V1["category"]){?><b><?php }?>
	<a href="<?php echo url("goods/goods_event.php?")?>&category=<?php echo $TPL_V1["category"]?>&sno=<?php echo $_GET["sno"]?>"><?php echo $TPL_V1["catnm"]?></a>
<?php if($GLOBALS["category"]==$TPL_V1["category"]){?></b><?php }?>
<?php if($TPL_I1!=$TPL__r_cate_1- 1){?> <font color=#cccccc>|</font> <?php }?>
<?php }}?>
	</td>
</tr>
<tr>
	<td background="/shop/data/skin/freemart/img/common/ed_bot_back.gif">
	<table width=100% border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td width=50%><img src="/shop/data/skin/freemart/img/common/ed_left_bot.gif" border=0></td>
		<td width=50% align=right><img src="/shop/data/skin/freemart/img/common/ed_right_bot.gif" border=0></td>
	</tr>
	</table>
	</td>
</tr>
</table>
<!-- 이벤트 카테고리 끝 -->
<?php }?>

<?php if($GLOBALS["r_brand"]){?>
<p />
<!-- 이벤트 브랜드 시작 -->
<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr>
	<td background="/shop/data/skin/freemart/img/common/ed_top_back.gif">
	<table width=100% border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td width=50%><img src="/shop/data/skin/freemart/img/common/ed_left_top.gif" border=0></td>
		<td width=50% align=right><img src="/shop/data/skin/freemart/img/common/ed_right_top.gif" border=0></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td bgcolor="#F5F5F5" style="padding:5px 10px; border-left:1px solid #E2E1E1; border-right:1px solid #E2E1E1"><img src="/shop/data/skin/freemart/img/common/icon_submenu.gif" border=0 align=absmiddle><b>브랜드</b></td>
</tr>
<tr>
	<td bgcolor="#F5F5F5" style="padding:5px 10px; border-left:1px solid #E2E1E1; border-right:1px solid #E2E1E1"></td>
</tr>
<tr>
	<td style="padding:5 10 5 10; line-height:20px; border-left:1px solid #E2E1E1; border-right:1px solid #E2E1E1">
<?php if($TPL__r_brand_1){$TPL_I1=-1;foreach($GLOBALS["r_brand"] as $TPL_V1){$TPL_I1++;?>
<?php if($GLOBALS["brandno"]==$TPL_V1["brandno"]){?><b><?php }?>
	<a href="<?php echo url("goods/goods_event.php?")?>&brandno=<?php echo $TPL_V1["brandno"]?>&sno=<?php echo $_GET["sno"]?>"><?php echo $TPL_V1["brandnm"]?></a>
<?php if($GLOBALS["brandno"]==$TPL_V1["brandno"]){?></b><?php }?>
<?php if($TPL_I1!=$TPL__r_brand_1- 1){?> <font color=#cccccc>|</font> <?php }?>
<?php }}?>
	</td>
</tr>
<tr>
	<td background="/shop/data/skin/freemart/img/common/ed_bot_back.gif">
	<table width=100% border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td width=50%><img src="/shop/data/skin/freemart/img/common/ed_left_bot.gif" border=0></td>
		<td width=50% align=right><img src="/shop/data/skin/freemart/img/common/ed_right_bot.gif" border=0></td>
	</tr>
	</table>
	</td>
</tr>
</table>
<!-- 이벤트 브랜드 끝 -->
<?php }?>

<form name=frmList>
<input type=hidden name=sno value="<?php echo $TPL_VAR["sno"]?>">
<input type=hidden name=sort value="<?php echo $_GET['sort']?>">
<input type=hidden name=page_num value="<?php echo $_GET['page_num']?>">

<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr><td height=20></td></tr>
<tr>
	<td bgcolor=9e9e9e class=small height=27 style="padding:0 0 0 5">
	<table width=100% border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td id="b_white"><img src="/shop/data/skin/freemart/img/common/icon_goodalign2.gif" align=absmiddle>
		<FONT COLOR="#FFFFFF">총 <b><?php echo $TPL_VAR["pg"]->recode['total']?></b>개의 상품이 있습니다.</FONT>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
<td>
<!-- capture_start("list_top") -->
<table width=100% cellpadding=0 cellspacing=0>
<tr>
	<td><img src="/shop/data/skin/freemart/img/common/goodlist_txt_01.gif"><a href="javascript:sort('maker desc')"><img name=sort_maker_desc src="/shop/data/skin/freemart/img/common/list_up_off.gif"></a><a href="javascript:sort('maker')"><img name=sort_maker src="/shop/data/skin/freemart/img/common/list_down_off.gif"></a><img src="/shop/data/skin/freemart/img/common/goodlist_txt_03.gif"><a href="javascript:sort('goodsnm desc')"><img name=sort_goodsnm_desc src="/shop/data/skin/freemart/img/common/list_up_off.gif"></a><a href="javascript:sort('goodsnm')"><img name=sort_goodsnm src="/shop/data/skin/freemart/img/common/list_down_off.gif"></a><img src="/shop/data/skin/freemart/img/common/goodlist_txt_04.gif"><a href="javascript:sort('price desc')"><img name=sort_price_desc src="/shop/data/skin/freemart/img/common/list_up_off.gif"></a><a href="javascript:sort('price')"><img name=sort_price src="/shop/data/skin/freemart/img/common/list_down_off.gif"></a><img src="/shop/data/skin/freemart/img/common/goodlist_txt_05.gif"><a href="javascript:sort('reserve desc')"><img name=sort_reserve_desc src="/shop/data/skin/freemart/img/common/list_up_off.gif"></a><a href="javascript:sort('reserve')"><img name=sort_reserve src="/shop/data/skin/freemart/img/common/list_down_off.gif"></a></td>
	<td align=right><img src="/shop/data/skin/freemart/img/common/goodlist_txt_06.gif" align=absmiddle><select onchange="this.form.page_num.value=this.value;this.form.submit()" style="font:8pt 돋움"><?php if($TPL_page_num_1){foreach($TPL_VAR["page_num"] as $TPL_V1){?><option value="<?php echo $TPL_V1?>" <?php echo $GLOBALS["selected"]["page_num"][$TPL_V1]?>><?php echo $TPL_V1?>개씩 정렬<?php }}?></select></td>
</tr>
</table>
<!-- capture_end ("list_top") -->
</td>
</tr>
<tr><td height=1 bgcolor=#DDDDDD></td></tr>
<tr>
	<td style="padding:15 0">
	<?php echo $this->assign('loop',$TPL_VAR["loop"])?>

	<?php echo $this->assign('cols',$TPL_VAR["cols"])?>

	<?php echo $this->assign('size',$TPL_VAR["size"])?>

	<?php echo $this->define('tpl_include_file_1',"goods/list/".$TPL_VAR["tpl"].".htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

	</td>
</tr>
<tr><td height=1 bgcolor=#DDDDDD></td></tr>
<tr>
	<td>
	<!-- capture_start("list_top") -->
<table width=100% cellpadding=0 cellspacing=0>
<tr>
	<td><img src="/shop/data/skin/freemart/img/common/goodlist_txt_01.gif"><a href="javascript:sort('maker desc')"><img name=sort_maker_desc src="/shop/data/skin/freemart/img/common/list_up_off.gif"></a><a href="javascript:sort('maker')"><img name=sort_maker src="/shop/data/skin/freemart/img/common/list_down_off.gif"></a><img src="/shop/data/skin/freemart/img/common/goodlist_txt_03.gif"><a href="javascript:sort('goodsnm desc')"><img name=sort_goodsnm_desc src="/shop/data/skin/freemart/img/common/list_up_off.gif"></a><a href="javascript:sort('goodsnm')"><img name=sort_goodsnm src="/shop/data/skin/freemart/img/common/list_down_off.gif"></a><img src="/shop/data/skin/freemart/img/common/goodlist_txt_04.gif"><a href="javascript:sort('price desc')"><img name=sort_price_desc src="/shop/data/skin/freemart/img/common/list_up_off.gif"></a><a href="javascript:sort('price')"><img name=sort_price src="/shop/data/skin/freemart/img/common/list_down_off.gif"></a><img src="/shop/data/skin/freemart/img/common/goodlist_txt_05.gif"><a href="javascript:sort('reserve desc')"><img name=sort_reserve_desc src="/shop/data/skin/freemart/img/common/list_up_off.gif"></a><a href="javascript:sort('reserve')"><img name=sort_reserve src="/shop/data/skin/freemart/img/common/list_down_off.gif"></a></td>
	<td align=right><img src="/shop/data/skin/freemart/img/common/goodlist_txt_06.gif" align=absmiddle><select onchange="this.form.page_num.value=this.value;this.form.submit()" style="font:8pt 돋움"><?php if($TPL_page_num_1){foreach($TPL_VAR["page_num"] as $TPL_V1){?><option value="<?php echo $TPL_V1?>" <?php echo $GLOBALS["selected"]["page_num"][$TPL_V1]?>><?php echo $TPL_V1?>개씩 정렬<?php }}?></select></td>
</tr>
</table>
<!-- capture_end ("list_top") -->
	</td>
</tr>
<tr><td height=2 bgcolor=#DDDDDD></td></tr>
<tr><td align=center height=50><?php echo $TPL_VAR["pg"]->page['navi']?></td></tr>
</table>

</form>
<form name=frmCharge method=post>
<input type=hidden name=mode value="">
<input type=hidden name=goodsno value="">
<input type=hidden name=ea value="1">
<input type=hidden name=opt[] id=opt value="">
</form>
</div><!-- End indiv -->

<div style="display:none;position:absolute;z-index:10;cursor:hand;" id="qrExplain" onclick="qrExplain_close();">
<table cellpadding="0" cellspacing="0" border="0">
  <tr>
	<td width="4" height="271" valign="top" background="/shop/data/skin/freemart/img/common/page02_detail_blt.gif" style="background-repeat:no-repeat"></td>
	<td  width="285" height="271" valign="top" background="/shop/data/skin/freemart/img/common/page02_detail.gif"></td>
  </tr>
  </table>
  <div style='width:289' onclick="qrExplain_close();" style="cursor:hand;text-align:center">[닫기]</div>
</div>

<script>

function qr_explain()
{
	var qrExplainObj = document.getElementById("qrExplain");

	qrExplainObj.style.top = event.clientY + document.body.scrollTop - 15;
	qrExplainObj.style.left = event.clientX + document.body.scrollLeft + 40;
	qrExplainObj.style.display = "block";
}

function qrExplain_close()
{
	var qrExplainObj = document.getElementById("qrExplain");
	qrExplainObj.style.display = "none";
}

function act(target,goodsno,opt1,opt2)
{
	var form = document.frmCharge;

	form.mode.value = "addItem";
	form.goodsno.value = goodsno;

	if(opt2) opt1 += opt2;
	document.getElementById("opt").value=opt1;

	form.action = target + ".php";
	form.submit();
}
function sort(sort)
{
	var fm = document.frmList;
	fm.sort.value = sort;
	fm.submit();
}
function sort_chk(sort)
{
	if (!sort) return;
	sort = sort.replace(" ","_");
	var obj = document.getElementsByName('sort_'+sort);
	if (obj.length){
		div = obj[0].src.split('list_');
		for (i=0;i<obj.length;i++){
			chg = (div[1]=="up_off.gif") ? "up_on.gif" : "down_on.gif";
			obj[i].src = div[0] + "list_" + chg;
		}
	}
}
</script>


<script>
<?php if($_GET['sort']){?>
sort_chk('<?php echo $_GET['sort']?>');
<?php }?>
</script>

<?php $this->print_("footer",$TPL_SCP,1);?>