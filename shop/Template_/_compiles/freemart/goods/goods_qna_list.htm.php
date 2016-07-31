<?php /* Template_ 2.2.7 2016/04/05 05:27:37 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/goods_qna_list.htm 000005327 */ 
if (is_array($GLOBALS["loop"])) $TPL__loop_1=count($GLOBALS["loop"]); else if (is_object($GLOBALS["loop"]) && in_array("Countable", class_implements($GLOBALS["loop"]))) $TPL__loop_1=$GLOBALS["loop"]->count();else $TPL__loop_1=0;?>
<html>
<head>
<script src="/shop/data/skin/freemart/common.js"></script>
<link rel="styleSheet" href="/shop/data/skin/freemart/style.css">
<link rel="styleSheet" href="/shop/data/skin/freemart/prod.css">
<link rel="styleSheet" href="/shop/data/skin/freemart/button.css">
<link rel="styleSheet" href="/shop/data/skin/freemart/font.css" />	


<script id="qna_chk"></script>
<script type="text/javascript">
function dynamicScript(url) {
	var script = document.createElement("script");
	script.type = "text/javascript";

	script.onload = script.onreadystatechange = function() {
		if(!script.readyState || script.readyState == "loaded" || script.readyState == "complete"){
			script.onload = script.onreadystatechange = null;
		}
	}

	script.src = url;
	document.getElementsByTagName("head")[0].appendChild(script);
}

function popup_register( mode, goodsno, sno )
{
<?php if(empty($GLOBALS["cfg"]['qnaWriteAuth'])&&!$GLOBALS["sess"]){?>
	alert( "회원전용입니다." );
<?php }else{?>
	if ( mode == 'del_qna' ) var win = window.open("goods_qna_del.php?mode=" + mode + "&sno=" + sno,"qna_register","width=400,height=200");
	else var win = window.open("goods_qna_register.php?mode=" + mode + "&goodsno=" + goodsno + "&sno=" + sno,"qna_register","width=650,height=752,scrollbars=yes");
	win.focus();
<?php }?>
}

var preContent;
var IntervarId;

function view_content(sno)
{
	var obj = document.getElementById('content_id_'+sno);
	if(obj.style.display == "none"){
		dynamicScript("./goods_qna_chk.php?mode=view&sno="+sno);
	}else{
		obj.style.display = "none";
	}
	preContent = obj;
	IntervarId = setInterval( 'resizeFrame();', 100 );
}

function popup_pass(sno){
	var win = window.open("goods_qna_pass.php?sno=" + sno,"qna_register","width=400,height=200");
}

function resizeFrame()
{
    var oBody = document.body;
    var tb_contents = document.getElementById("contents-wrapper");
    var i_height = tb_contents.offsetHeight;
    if(i_height==0){
    	i_height  = 100;
    }   
    parent.resizeFrameHeight('inqna',i_height);
    if ( IntervarId ) clearInterval( IntervarId );

	var i_width = tb_contents.offsetWidth;
    if(i_width==0){
    	i_width  = 100;
    }   
    parent.resizeFrameWidth('inqna',i_width);
    if ( IntervarId ) clearInterval( IntervarId );
}

</script>
</head>
<body style="margin-top:10px" onload="setTimeout('resizeFrame()',1)">

<div id="contents-wrapper">

	<div id="review-title">
		<h2>상품문의&nbsp;(<?php echo $TPL_VAR["qna_count"]?>)</h2>
		<div>
			<button class="button-big-wide button-red" onclick="popup_register( 'add_review', '<?php echo $GLOBALS["goodsno"]?>' )">상품문의 작성</button>
		</div>
		<div class="dot-line"></div>
		
	</div>
	
<?php if($TPL__loop_1){foreach($GLOBALS["loop"] as $TPL_V1){?>
	<div>
		<table width="100%" cellpadding="0" cellspacing="0" style="border-bottom-style:solid;border-bottom-color:#E6E6E6;border-bottom-width:1;cursor:pointer;">
		<tr height="25" onmouseover="this.style.background='#F7F7F7'" onmouseout="this.style.background=''" onclick="view_content('<?php echo $TPL_V1["sno"]?>');">
			<!-- <td width="50" align="center"><?php echo $TPL_V1["idx"]?></td> -->
<?php if($TPL_V1["type"]=='Q'){?>
			<td style="padding-left:0px"><div style="background-image: url(/shop/data/skin/freemart/img/common/ico_q.gif); background-repeat:no-repeat;background-position:left 3px;padding:3px 0px 0px 20px;"><?php echo $TPL_V1["subject"]?><?php if($TPL_V1["secretIcon"]){?>&nbsp;<img src="/shop/data/skin/freemart/img/common/icn_secret.gif" align=absmiddle><?php }?></div></td>
<?php }else{?>
			<td style="padding-left:5px"><div style="background-image: url(/shop/data/skin/freemart/img/common/ico_a.gif); background-repeat:no-repeat;background-position:left 3px;padding:3px 0px 0px 27px;"> <?php echo $TPL_V1["subject"]?><?php if($TPL_V1["secretIcon"]){?>&nbsp;<img src="/shop/data/skin/freemart/img/common/icn_secret.gif" align=absmiddle><?php }?></div></td>
<?php }?>
			<td width="150"><?php if($TPL_V1["name"]){?><?php echo $TPL_V1["name"]?><?php }elseif($TPL_V1["m_name"]){?><?php echo $TPL_V1["m_name"]?><?php }else{?><?php echo $TPL_V1["m_id"]?><?php }?></td>
			<td width="80"><?php echo substr($TPL_V1["regdt"], 0, 10)?></td>
		</tr>
		</table>
	</div>
	<div id="content_id_<?php echo $TPL_V1["sno"]?>" style="display:none"></div>
<?php }}?>
    
    <!-- 
	<div style="float:right;padding:10px 5px">
		<a href="<?php echo url("goods/goods_qna.php")?>&" target="_parent"><img src="/shop/data/skin/freemart/img/common/info_btn_totalview.gif"></a>
		<a href="javascript:;" onclick="popup_register( 'add_qna', '<?php echo $GLOBALS["goodsno"]?>' )"><img src="/shop/data/skin/freemart/img/common/btn_qna.gif"></a>
	</div>
 -->
	<div style="clear:both;text-align:center;padding-top:10px; padding-bottom:15px;"><?php echo $TPL_VAR["pg"]->page['navi']?></div>
</div>
</body>
</html>