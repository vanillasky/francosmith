<?php /* Template_ 2.2.7 2014/12/23 16:42:11 /www/francotr3287_godo_co_kr/shop/data/skin/standard/goods/goods_review_list.htm 000006558 */ 
if (is_array($GLOBALS["loop"])) $TPL__loop_1=count($GLOBALS["loop"]); else if (is_object($GLOBALS["loop"]) && in_array("Countable", class_implements($GLOBALS["loop"]))) $TPL__loop_1=$GLOBALS["loop"]->count();else $TPL__loop_1=0;?>
<html>
<head>
<script src="/shop/data/skin/standard/common.js"></script>
<link rel="styleSheet" href="/shop/data/skin/standard/style.css">
<script language="javascript">

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
	if ( document.getElementById && ( e.srcElement.tagName == 'A' || e.srcElement.tagName == 'IMG' ) ) return;
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
    parent.resizeFrameWidth('inreview',i_width);
    if ( IntervarId ) clearInterval( IntervarId );
}
</script>
</head>
<body style="margin-top:10px" onLoad="resizeFrame()">

<div id="contents-wrapper">
<table border=0 cellpadding=0 cellspacing=0>
	<tr>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_03.gif) no-repeat;" nowrap width="100" height="24"></TD>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_03_bg.gif) repeat-x;" width='90%'><b>(<?php echo $TPL_VAR["review_count"]?>)</b></TD>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_03_right.gif) no-repeat;" nowrap width="10" height="24"></TD>
	</tr>
</table>
<?php if($TPL__loop_1){foreach($GLOBALS["loop"] as $TPL_V1){?>
<div>
<table width=100% cellpadding=0 cellspacing=0 style="border-bottom-style:solid;border-bottom-color:#E6E6E6;border-bottom-width:1;cursor:pointer;" onclick="view_content(this,event)">
<tr height=25 onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="">
	<td width=50 align="center"><?php echo $TPL_V1["idx"]?></td>
<?php if($TPL_V1["type"]=='Q'){?>
	<td><div style="background-image: url(/shop/data/skin/standard/img/common/icon_review.gif); background-repeat:no-repeat;background-position:left 6px;padding:3px 0px 0px 12px;"><?php echo $TPL_V1["subject"]?><?php if($TPL_V1["attach"]){?>&nbsp;&nbsp;<img src="/shop/data/skin/standard/img/disk_icon.gif" align="absmiddle"><?php }?></div></td>
<?php }else{?>
	<td style="padding-left:5px"><div style="background-image: url(/shop/data/skin/standard/img/common/ico_a.gif); background-repeat:no-repeat;background-position:left 3px;padding:3px 0px 0px 27px;"> <?php echo $TPL_V1["subject"]?><?php if($TPL_V1["attach"]){?>&nbsp;&nbsp;<img src="/shop/data/skin/standard/img/disk_icon.gif" align="absmiddle"><?php }?></div></td>
<?php }?>
	<td width=80><?php if($TPL_V1["name"]){?><?php echo $TPL_V1["name"]?><?php }elseif($TPL_V1["m_name"]){?><?php echo $TPL_V1["m_name"]?><?php }else{?><?php echo $TPL_V1["m_id"]?><?php }?></td>
	<td width=80><?php echo substr($TPL_V1["regdt"], 0, 10)?></td>
	<td width=80>
<?php if($TPL_V1["point"]> 0){?>
<?php if((is_array($TPL_R2=array_fill( 0,$TPL_V1["point"],''))&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>★<?php }}?>
<?php }?>
	</td>
</tr>
</table>
<div style="display:none;padding:10;border-bottom-style:solid;border-bottom-color:#E6E6E6;border-bottom-width:1;">
	<div width="100%" style="padding-left:17">
<?php if($TPL_V1["image"]!=''){?>
	<?php echo $TPL_V1["image"]?> <br><br>
<?php }?>
	<?php echo $TPL_V1["contents"]?>

	</div>
	<div style="text-align:right;">
<?php if($TPL_V1["authreply"]=='Y'){?>
	<a href="javascript:;" onclick="popup_register( 'reply_review', '<?php echo $TPL_V1["goodsno"]?>', '<?php echo $TPL_V1["sno"]?>' );"><img src="/shop/data/skin/standard/img/common/btn_reply.gif" border="0" align="absmiddle"></a>
<?php }?>
<?php if($TPL_V1["authmodify"]=='Y'){?>
	<a href="javascript:;" onclick="popup_register( 'mod_review', '<?php echo $TPL_V1["goodsno"]?>', '<?php echo $TPL_V1["sno"]?>' );"><img src="/shop/data/skin/standard/img/common/btn_modify2.gif" border="0" align="absmiddle"></a>
<?php }?>
<?php if($TPL_V1["authdelete"]=='Y'){?>
	<a href="javascript:;" onclick="popup_register( 'del_review', '<?php echo $TPL_V1["goodsno"]?>', '<?php echo $TPL_V1["sno"]?>' );"><img src="/shop/data/skin/standard/img/common/btn_delete.gif" border="0" align="absmiddle"></a>
<?php }?>
</div>
</div>
<?php }}?>

<div style="float:right;padding:10px 5px">
<a href="<?php echo url("goods/goods_review.php")?>&" target="_parent"><img src="/shop/data/skin/standard/img/common/info_btn_totalview.gif"></a>
<a href="javascript:;" onclick="popup_register( 'add_review', '<?php echo $GLOBALS["goodsno"]?>' )"><img src="/shop/data/skin/standard/img/common/btn_review.gif"></a>
</div>

<div style="clear:both;text-align:center;padding-bottom:15px;"><?php echo $TPL_VAR["pg"]->page['navi']?></div>
</div>
</body>
</html>