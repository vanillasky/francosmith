<?php /* Template_ 2.2.7 2016/04/21 13:56:49 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/service/faq.htm 000003796 */ 
if (is_array($GLOBALS["loop"])) $TPL__loop_1=count($GLOBALS["loop"]); else if (is_object($GLOBALS["loop"]) && in_array("Countable", class_implements($GLOBALS["loop"]))) $TPL__loop_1=$GLOBALS["loop"]->count();else $TPL__loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>

<!-- 상단이미지 || 현재위치 -->
<div class="page_title_div">
	<div class="page_title">자주하는질문</div>
	<div class="page_path"><a href="/shop/">HOME</a> &gt;  <a href="/shop/service/customer.php?&">고객센터</a> &gt; <span class='bold'>FAQ</span></div>
</div>
<div class="page_title_line"></div>

<div style="width:100%; padding-top:20px;"></div>

<div class="indiv"><!-- Start indiv -->

	<!-- 검색 : Start -->
	<form id="faq_form">

	<div class="cs-search-box-conatiner" >
		<div class="search-box-border">
			<div style="width:100%; text-align:center;">
				<input type="text" name="faq_sword" value="<?php echo $_GET['faq_sword']?>" size="32" class="mid-height">
				<button class="button-dark button-medium" onclick="faq_form.submit();">검색</button>
			</div>	
			<div class="search-box-categories">
<?php if((is_array($TPL_R1=codeitem('faq'))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {$TPL_S1=count($TPL_R1);$TPL_I1=-1;foreach($TPL_R1 as $TPL_K1=>$TPL_V1){$TPL_I1++;?>
				<a href="?sitemcd=<?php echo $TPL_K1?>"><font color=#757575><?php echo $TPL_V1?></font></a>
<?php if($TPL_I1!=$TPL_S1- 1){?> <font color=#cccccc> | </font> <?php }?>
<?php }}?>
			</div>
		</div>
	</div>

	</form>
	<!-- 검색 : End -->
	
	<div style="width:100%; padding-top:20px;"></div>
	
	<table class="mypage-board-table">
	<tr class="mypage-board-title">
		<th width=8%>번호</th>
		<th width=25%>질문유형</th>
		<th>제목</th>
	</tr>
	</table>
	
<?php if($TPL__loop_1){foreach($GLOBALS["loop"] as $TPL_V1){?>
	<div>
		<table class="cs-board-list" onclick="view_content(this)" id="faq_<?php echo $TPL_V1["sno"]?>">
		<tr class="cs-board-row">
			<td width="8%" align="center"><?php echo $TPL_V1["idx"]?></td>
			<td width="25%" align="center">[<?php echo $TPL_V1["itemcd"]?>]</td>
			<td align="left"><?php echo $TPL_V1["question"]?></td>
		</tr>
		</table>

		<div class="cs-board-answer-wrapper" style="display:none;">
			<table class="cs-board-answer">
			<tr valign="top">
				<td width="8%"></td>
				<td width="25%"></td>
				<th style="color:#0000bf;width:40; padding-top:1"><img src="/shop/data/skin/freemart/img/common/faq_a.gif"></th>
				<td class="faq_content"><?php echo $TPL_V1["answer"]?></td>
			</tr>
			</table>
		</div>
<?php }}else{?>

	<div class="cs-board-list no-result" >
		검색결과가 없습니다. 다시 검색하여 주세요.
	</div>
<?php }?>

	</div>
</div><!-- End indiv -->


<script type="text/javascript">
var preContent;

function view_content(obj)
{
	var div = obj.parentNode;

	for (var i=1, m=div.childNodes.length;i<m;i++) {
		if (div.childNodes[i].nodeType != 1) continue;	// text node.
		else if (obj == div.childNodes[ i ]) continue;

		obj = div.childNodes[ i ];
		break;
	}

	if (preContent && obj!=preContent){
		obj.style.display = "block";
		preContent.style.display = "none";
	}
	else if (preContent && obj==preContent) preContent.style.display = ( preContent.style.display == "none" ? "block" : "none" );
	else if (preContent == null ) obj.style.display = "block";

	preContent = obj;
}

{ // 초기출력
	var no = "faq_<?php echo $_GET['ssno']?>";
	if ( document.getElementById( no ) ) view_content( document.getElementById( no ) );
}
</script>

<?php $this->print_("footer",$TPL_SCP,1);?>