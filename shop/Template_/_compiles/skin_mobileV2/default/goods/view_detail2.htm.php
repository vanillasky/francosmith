<?php /* Template_ 2.2.7 2013/05/28 17:53:01 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/goods/view_detail2.htm 000009473 */ 
if (is_array($TPL_VAR["extra_info"])) $TPL_extra_info_1=count($TPL_VAR["extra_info"]); else if (is_object($TPL_VAR["extra_info"]) && in_array("Countable", class_implements($TPL_VAR["extra_info"]))) $TPL_extra_info_1=$TPL_VAR["extra_info"]->count();else $TPL_extra_info_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<?php $this->print_("sub_header",$TPL_SCP,1);?>


<script type="text/javascript">
var strprice = "<?php echo $TPL_VAR["strprice"]?>";

$(document).ready(function(){
	$("[id=goodsorder-hide]").css("height", $("[id=goodsorder-hide]").height()+30);
	$("[id=goodscart-hide]").css("height", $("[id=goodscart-hide]").height()+30);
	$("[id=goodswish-hide]").css("height", $("[id=goodswish-hide]").height()+30);
	$("#goodsorder-hide").css("position", "absolute");
	$("#goodscart-hide").css("position", "absolute");
	$("#goodswish-hide").css("position", "absolute");

	$("meta[name=viewport]").attr("content", "user-scalable=yes, initial-scale=1.0, maximum-scale=10.0, minimum-scale=1.0, width=device-width, height=device-height");
});
function popOpt(btn_nm) {
	if (strprice.length > 0) {
		$("[id=goodsres-hide] .text_msg").text("가격대체문구 상품입니다");
		$("[id=goodsres-hide]").fadeIn(300);
		setTimeout( function() {
			$("[id=goodsres-hide]").fadeOut(300);
		}, 1000);
		return;
	}
	var opt_visiable = false;

	if($("[id=goods"+btn_nm+"-hide]").is(':hidden') == false) {
		opt_visiable = true;
	}

	$("[id$=hide]").fadeOut(300);

	if(!opt_visiable) $("[id=goods"+btn_nm+"-hide]").fadeIn(300).css("top", ($(window).scrollTop()+10)+"px");
}

function m2CheckForm(obj_id) {
	var $ea = $("form[name=frmView] [name=ea]");
	var $opt = $("form[name=frmView] [name='opt[]']");

	$opt.val($("#"+obj_id+" [name=goods_opt]").val());
	if($("#"+obj_id+" [name=goods_opt]>option").length > 1) {
		if($opt.val() == "" || $opt.val() == "undefined") {
			alert('선택사항을 선택해 주세요');
			$("#"+obj_id+" [name=goods_opt]").focus();
			return false;
		}
	}
	if(obj_id!="goodswish-hide") {
		$ea.val($("#"+obj_id+" [name=order_cnt]").val());
		if(isNaN($ea.val()) || $ea.val() < 1) {
			alert('수량은 숫자로 입력해 주세요');
			$("#"+obj_id+" [name=order_cnt]").focus();
			return false;
		}
	}

	// 추가옵션 체크  및 처리
	//------------------------------------------------------------------------------
	var $_add_opt_val = new Array();
	var check_add_opt = true;
	$("#"+obj_id+" [name=addopt[]]").each(function(index, Element) {
		if (Element.getAttribute("required")!= null ) {
			if (chkText(Element,Element.value,Element.getAttribute("msgR")) == false) {
				check_add_opt = false;
				return;
			}
		}
	});
	if (check_add_opt == false) return false;

	// Form 에 엘레먼트 추가하기
	if ($("#"+obj_id+" [name=addopt[]]").length > 0) {
		$("form [name=addopt[]]").remove();
		$addopt_new = $("#"+obj_id+" [name=addopt[]]").clone();
		$addopt_new.each( function(index, Element) {
			Element.value = $("#"+obj_id+" [name=addopt[]]").get(index).value;
		});
		$addopt_new.css("display", "none");
		$("form[name=frmView]").append($addopt_new);
	}
	//------------------------------------------------------------------------------

	return true;
}

function indbAction(obj_id) {
	if (strprice.length > 0) {
		$("[id=goodsres-hide] .text_msg").text("가격대체문구 상품입니다");
		$("[id=goodsres-hide]").fadeIn(300);
		setTimeout( function() {
			$("[id=goodsres-hide]").fadeOut(300);
		}, 1000);
		return;
	}
	switch(obj_id) {
		case 'goodsorder-hide' :
			var $frm = $("form[name=frmView]");
			var $mode =	$("form[name=frmView] [name=mode]");

			$mode.val('addItem');
			if(m2CheckForm(obj_id)===false) return;

			$frm.attr("action", "../ord/order.php");
			$frm.submit();
			break;

		case 'goodscart-hide' :
			var $frm = $("form[name=frmView]");
			var $mode =	$("form[name=frmView] [name=mode]");

			$mode.val('addCart');
			if(m2CheckForm(obj_id)===false) return;

			var serializedData = $("form[name=frmView]").serialize();

			$.ajax({
				type:"post",
				url:"./ajaxAction.php",
				dataType:"json",
				data: serializedData,
				success:function(result){
					$("form [name=addopt[]]").remove();
					popOpt('cart');
					showResMsg(result);
				},
				error:function(xhr, ajaxOptions, thrownError){
					n1 = xhr.responseText.indexOf("<script>");
					n2 = xhr.responseText.indexOf("<\/script>");
					if (n1>0 && n2 >n1) {
						errmsg = xhr.responseText.substring(n1+"<script>".length, n2);
						errmsg = errmsg.replace(/alert/gi, "");
						alert(errmsg);
					} else {
						alert('장바구니 추가실패!\n다시 시도하여주시기 바랍니다.');
					}
				}
			});
			$("form [name=addopt[]]").remove();
			break;

		case 'goodswish-hide' :

			var $frm = $("form[name=frmView]");
			var $mode =	$("form[name=frmView] [name=mode]");

			$mode.val('addWishlist');
			if(m2CheckForm(obj_id)===false) return;

			var serializedData = $("form[name=frmView]").serialize();
			$.ajax({
				type:"post",
				url:"./ajaxAction.php",
				dataType:"json",
				data: serializedData,
				success:function(result){

					popOpt('wish');
					showResMsg(result);

					//if(result.msg) alert(result.msg);
					//if(result.exeCode) eval(result.exeCode);
				},
				error:function(){
					alert('일시적인 에러가 발생하였습니다.\n다시 시도하여주시기 바랍니다.');
				}
			});

			break;
	}
}

function showResMsg(obj) {

	var sec = 0;

	if(obj.sec == null || obj.sec == "undefined") {
		sec = 1000;
	}
	else {
		sec = obj.sec;
	}

	$("[id=goodsres-hide] .text_msg").text(obj.msg);
	$("[id=goodsres-hide]").fadeIn(300);

	setTimeout( function() {
		$("[id=goodsres-hide]").fadeOut(300);

		if(obj.url && obj.url != "undefined") {
			document.location.href = obj.url;
		}

	}, sec);
}

$(function() {
	var msg = "<?php echo $TPL_VAR["msg_kakao2"]?>";
	var url = "<?php echo $TPL_VAR["msg_kakao3"]?>";
	var appname = "<?php echo $TPL_VAR["msg_kakao1"]?>";
	var link = new com.kakao.talk.KakaoLink("<?php echo $GLOBALS["_SERVER"]['HTTP_HOST']?>", "1.0", url, msg, appname);

	$("#kakao").click(function() {// button click event
		link.execute();
	});
});
/*
$(document).ready(function(){
	var msg_obj = {};
	msg_obj.msg = '스크롤을 내려주세요';
	msg_obj.sec = 2000;
	showResMsg(msg_obj);
});
*/
</script>
<style type="text/css">
.goods_price2 {height:20px;line-height:20px;text-align:right;}
.goods_dc {height:20px;line-height:20px;text-align:right;color:#88eeff;}

section#goodsdetail2 {background:#FFFFFF;}
section#goodsdetail2 .top_title{clear:both; height:40px;background:url('/shop/data/skin_mobileV2/default/common/img/myp/name_bg.png') repeat-x; line-height:40px; padding-left:10px; color:#FFFFFF; font-size:16px; font-weight:bold; text-align:center;}
section#goodsdetail2 .top_title .back_btn{float:left; background:url('/shop/data/skin_mobileV2/default/common/img/detailp/btn_back_off.png') no-repeat; width:45px; height:27px; margin-top:7px; position:absolute;}
section#goodsdetail2 .top_title .back_btn:active{background:url('/shop/data/skin_mobileV2/default/common/img/detailp/btn_back_on.png') no-repeat;}
section#goodsdetail2 .desc-area .desc-area-info{height:40px; text-align:center; font-size:12px; color:#353535; line-height:40px;}

</style>
<form name="frmView" method="post" onsubmit="return false;">
	<input type="hidden" name="mode" value="" />
	<input type="hidden" name="goodsno" value="<?php echo $TPL_VAR["goodsno"]?>" />
	<input type="hidden" name="goodsCoupon" value="<?php echo $TPL_VAR["coupon"]?>" />
	<input type="hidden" name="ea" value="" />
	<input type="hidden" name="opt[]" value="" />
	<input type="hidden" name="addopt[]" value="" />
</form>


<section id="goodsdetail2" class="content">
	<div class="top_title">
		<div class="back_btn" onClick="javascript:history.go(-1);"></div>
		<div class="goods_nm">상품상세설명</div>
	</div>
	<div class="desc-area">
		<div class="desc-area-info">
			상품 상세설명을 확대하실 수 있습니다.
		</div>
		<?php echo $TPL_VAR["NaverMileageAccum"]?>

<?php if($TPL_VAR["mlongdesc"]){?><?php echo $TPL_VAR["mlongdesc"]?><?php }else{?><?php echo $TPL_VAR["longdesc"]?><?php }?>
<?php if($TPL_extra_info_1){?>
			<style>
			table.extra-information {background:#e0e0e0;margin:30px 0 60px 0;}
			table.extra-information th,
			table.extra-information td {font-weight:normal;text-align:left;padding-left:15px;background:#ffffff;font-family:Dotum;font-size:11px;height:28px;}

			table.extra-information th {width:15%;background:#f5f5f5;color:#515151;}
			table.extra-information td {width:35%;color:#666666;}

			</style>
			<table width=100% border=0 cellpadding=0 cellspacing=1 class="extra-information">
			<tr>
<?php if($TPL_extra_info_1){foreach($TPL_VAR["extra_info"] as $TPL_K1=>$TPL_V1){?>
				<th><?php echo $TPL_V1["title"]?></th>
				<td <?php if($TPL_V1["colspan"]> 1){?>colspan="<?php echo $TPL_V1["colspan"]?>"<?php }?>><?php echo $TPL_V1["desc"]?></td>
<?php if($TPL_V1["nkey"]&&(!$GLOBALS["extra_info"][$TPL_V1["nkey"]]||$TPL_K1% 2== 0)){?>
				</tr><tr>
<?php }?>
<?php }}?>
			</tr>
			</table>
<?php }?>
	</div>

</section>



<?php $this->print_("footer",$TPL_SCP,1);?>