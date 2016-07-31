<?php /* Template_ 2.2.7 2016/04/10 14:51:41 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/mypage/mypage_review.htm 000004823 */ 
if (is_array($GLOBALS["loop"])) $TPL__loop_1=count($GLOBALS["loop"]); else if (is_object($GLOBALS["loop"]) && in_array("Countable", class_implements($GLOBALS["loop"]))) $TPL__loop_1=$GLOBALS["loop"]->count();else $TPL__loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 상단이미지 || 현재위치 -->
<div class="page_title_div">
	<div class="page_title">PRODUCT REVIEWS</div>
	<div class="page_path"><a href="/shop/">HOME</a> &gt; <a href="/shop/mypage/mypage.php">마이페이지</a> &gt; <span class='bold'>나의상품후기</span></div>
</div>
<div class="page_title_line"></div>

<div style="width:100%; padding-top:20px;"></div>

<div class="indiv"><!-- Start indiv -->

	<table class="mypage-board-table">
	<tr class="mypage-board-title">
		<th width=50>번호</th>
		<th width=60>이미지</th>
		<th>상품명/후기</th>
		<th width=80>작성일</th>
		<th width=80>평점</th>
	</tr>
	</table>

<?php if($TPL__loop_1){foreach($GLOBALS["loop"] as $TPL_V1){?>
	<div>
		<table class="mypage-board-content" onclick="view_content(this, event)">
		<tr height=25 onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="">
			<td width=40 align="center"><?php echo $TPL_V1["idx"]?></td>
			<td width=60 align="center"><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo goodsimg($TPL_V1["img_s"], 50)?></a></td>
			<td>
				<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td align="left"><span style="font-weight:bold;"><?php echo $TPL_V1["goodsnm"]?></span> <a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><img src="/shop/data/skin/freemart/img/common/btn_goodview2.gif" align=absmiddle></a></td>
				</tr>
				<tr>
					<td align="left"><?php echo $TPL_V1["subject"]?><?php if($TPL_V1["attach"]){?>&nbsp;&nbsp;<img src="/shop/data/skin/freemart/img/disk_icon.gif" align="absmiddle"><?php }?></td>
				</tr>
				</table>
			</td>
			<td width=80 align="center"><?php echo substr($TPL_V1["regdt"], 0, 10)?></td>
			<td width=80>
<?php if($TPL_V1["point"]> 0){?>
<?php if((is_array($TPL_R2=array_fill( 0,$TPL_V1["point"],''))&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>★<?php }}?>
<?php }?>
			</td>
		</tr>
		</table>
		
		<div class="mypage-txt-wrapper">
			<div class="mypage-txt-full">
				<div style="width:100%; border:1px solid #ededed; margin-bottom:1em;">
<?php if($TPL_V1["image"]!=''){?>
					<div class="mypage-prod-img"><?php echo $TPL_V1["image"]?></div>
<?php }?>
					<div class="mypage-prod-img"><span><?php echo $TPL_V1["contents"]?></span></div>
				</div>
				
				<div style="text-align:right; margin-bottom:10px;">
					<button class="button-dark button-small" onclick="popup_register( 'mod_review', '<?php echo $TPL_V1["goodsno"]?>', '<?php echo $TPL_V1["sno"]?>' );">수정</button>
	    			<button class="button-dark button-small" onclick="popup_register( 'del_review', '<?php echo $TPL_V1["goodsno"]?>', '<?php echo $TPL_V1["sno"]?>' );">삭제</button>
				</div>
			</div>
		</div>
	</div>	
<?php }}?>

	<div class="pagediv"><?php echo $TPL_VAR["pg"]->page['navi']?></div>


	<script type="text/javascript">

		function popup_register( mode, goodsno, sno )
		{
			if ( mode == 'del_review' ) var win = window.open("../goods/goods_review_del.php?mode=" + mode + "&sno=" + sno,"qna_register","width=400,height=200");
			else var win = window.open("../goods/goods_review_register.php?mode=" + mode + "&goodsno=" + goodsno + "&sno=" + sno,"qna_register","width=600,height=550");
			win.focus();
		}
		
		var preContent;
		
		function view_content(obj, e)
		{
			if ( document.getElementById && ( this.tagName == 'A' || this.tagName == 'IMG' ) ) return;
			else if ( !document.getElementById && ( e.target.tagName == 'A' || e.target.tagName == 'IMG' ) ) return;
		
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
	</script>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>