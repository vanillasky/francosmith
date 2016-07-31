<?php /* Template_ 2.2.7 2016/04/05 10:50:31 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/goods_review_list.htm 000006564 */ 
if (is_array($GLOBALS["loop"])) $TPL__loop_1=count($GLOBALS["loop"]); else if (is_object($GLOBALS["loop"]) && in_array("Countable", class_implements($GLOBALS["loop"]))) $TPL__loop_1=$GLOBALS["loop"]->count();else $TPL__loop_1=0;?>
<html>
<head>
<script src="/shop/data/skin/freemart/common.js"></script>
<link rel="styleSheet" href="/shop/data/skin/freemart/style.css">
<link rel="styleSheet" href="/shop/data/skin/freemart/prod.css">
<link rel="styleSheet" href="/shop/data/skin/freemart/button.css">
<link rel="styleSheet" href="/shop/data/skin/freemart/font.css" />	

<script type="text/javascript">

function popup_register( mode, goodsno, sno )
{
<?php if(empty($GLOBALS["cfg"]['reviewWriteAuth'])&&!$GLOBALS["sess"]){?>
	alert( "회원전용입니다." );
<?php }else{?>
	if ( mode == 'del_review' ) var win = window.open("goods_review_del.php?mode=" + mode + "&sno=" + sno,"qna_register","width=400,height=200");
	else var win = window.open("goods_review_register.php?mode=" + mode + "&goodsno=" + goodsno + "&sno=" + sno,"qna_register","width=600,height=550");
	win.focus();
<?php }?>
}

var preContent;
var IntervarId;

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
	IntervarId = setInterval( 'resizeFrame()', 100 );
}


function resizeFrame()
{
    
	var oBody = document.body;
    var tb_contents = document.getElementById("contents-wrapper");
    var i_height = tb_contents.offsetHeight;
    if(i_height==0){
    	i_height  = 100;
    }

    parent.resizeFrameHeight('inreview',i_height);
    if ( IntervarId ) clearInterval( IntervarId );

	var i_width = tb_contents.offsetWidth;
	
    if(i_width==0){
    	i_width  = 100;
    }
	
    // 강제세팅
	//i_width = 995;
    //parent.resizeFrameWidth('inreview',i_width);
    //if ( IntervarId ) clearInterval( IntervarId );
}
</script>
</head>


<body onload="resizeFrame();" >

<div id="contents-wrapper" >
	<div id="review-title">
		<h2>REVIEWS&nbsp;(<?php echo $TPL_VAR["review_count"]?>)</h2>
<?php if($TPL_VAR["review_count"]== 0){?>
		<p>첫 번째로 후기를 작성하신분께 적립금(1,000)을 드립니다.</p>
<?php }?>
		
		<div>
			<button class="button-big-wide button-red" onclick="popup_register( 'add_review', '<?php echo $GLOBALS["goodsno"]?>' )">상품평 쓰기</button>
		</div>
		
		<div style="width:100%;margin-bottom:10px;padding-bottom:10px;">
			<div class="dot-line"></div>
		</div>
	</div>
	

<?php if($TPL__loop_1){foreach($GLOBALS["loop"] as $TPL_V1){?>
	<div>
		<table class="review-list" onclick="view_content(this,event)">
		<tr height="30" >
			<!-- <td width=50 align="center"><?php echo $TPL_V1["idx"]?></td> -->
			<td width="80" class="star-point">
<?php if($TPL_V1["point"]> 0){?>
<?php if((is_array($TPL_R2=array_fill( 0,$TPL_V1["point"],''))&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>★<?php }}?>
<?php }?>
			</td>
			
<?php if($TPL_V1["type"]=='Q'){?>
			<td><div style="background-image: url(/shop/data/skin/freemart/img/common/icon_review.gif); background-repeat:no-repeat;background-position:left 6px;padding:3px 0px 0px 12px;"><?php echo $TPL_V1["subject"]?><?php if($TPL_V1["attach"]){?>&nbsp;&nbsp;<img src="/shop/data/skin/freemart/img/disk_icon.gif" align="absmiddle"><?php }?></div></td>
<?php }else{?>
			<td style="padding-left:5px"><div style="background-image: url(/shop/data/skin/freemart/img/common/ico_a.gif); background-repeat:no-repeat;background-position:left 3px;padding:3px 0px 0px 27px;"> <?php echo $TPL_V1["subject"]?><?php if($TPL_V1["attach"]){?>&nbsp;&nbsp;<img src="/shop/data/skin/freemart/img/disk_icon.gif" align="absmiddle"><?php }?></div></td>
<?php }?>
			<td width=150><?php if($TPL_V1["name"]){?><?php echo $TPL_V1["name"]?><?php }elseif($TPL_V1["m_name"]){?><?php echo $TPL_V1["m_name"]?><?php }else{?><?php echo $TPL_V1["m_id"]?><?php }?></td>
			<td width=80><?php echo substr($TPL_V1["regdt"], 0, 10)?></td>
			
		</tr>
		</table>

		<div style="display:none;padding:10;border-bottom-style:solid;border-bottom-color:#E6E6E6;border-bottom-width:1;">
			<div style="padding-left:90px">
<?php if($TPL_V1["image"]!=''){?>
				<?php echo $TPL_V1["image"]?> <br><br>
<?php }?>
				<?php echo $TPL_V1["contents"]?>

			</div>
			
			<div style="text-align:right;">
<?php if($TPL_V1["authreply"]=='Y'){?>
			<button onclick="popup_register( 'reply_review', '<?php echo $TPL_V1["goodsno"]?>', '<?php echo $TPL_V1["sno"]?>' );" class="button-small button-dark">답변</button>
<?php }?>
<?php if($TPL_V1["authmodify"]=='Y'){?>
			<button onclick="popup_register( 'mod_review', '<?php echo $TPL_V1["goodsno"]?>', '<?php echo $TPL_V1["sno"]?>' );" class="button-small button-dark">수정</button>
<?php }?>
<?php if($TPL_V1["authdelete"]=='Y'){?>
			<button onclick="popup_register( 'del_review', '<?php echo $TPL_V1["goodsno"]?>', '<?php echo $TPL_V1["sno"]?>' );" class="button-small button-dark">삭제</button>
<?php }?>
			</div>
		</div>
<?php }}?> <!-- loop end -->
	</div>
	
	<!-- 
	<div style="float:right;padding:10px 5px">
		<a href="<?php echo url("goods/goods_review.php")?>&" target="_parent"><img src="/shop/data/skin/freemart/img/common/info_btn_totalview.gif"></a>
		<a href="javascript:;" onclick="popup_register( 'add_review', '<?php echo $GLOBALS["goodsno"]?>' )"><img src="/shop/data/skin/freemart/img/common/btn_review.gif"></a>
	</div>
     -->
     
	<div style="clear:both;text-align:center; padding-top:10px; padding-bottom:15px;"><?php echo $TPL_VAR["pg"]->page['navi']?></div>
	
</div>

</body>
</html>