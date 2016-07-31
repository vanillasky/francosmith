<?php /* Template_ 2.2.7 2013/09/23 14:33:06 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/goods/view2.htm 000063617 */  $this->include_("commoninfo");
if (is_array($GLOBALS["opt"])) $TPL__opt_1=count($GLOBALS["opt"]); else if (is_object($GLOBALS["opt"]) && in_array("Countable", class_implements($GLOBALS["opt"]))) $TPL__opt_1=$GLOBALS["opt"]->count();else $TPL__opt_1=0;
if (is_array($GLOBALS["addopt"])) $TPL__addopt_1=count($GLOBALS["addopt"]); else if (is_object($GLOBALS["addopt"]) && in_array("Countable", class_implements($GLOBALS["addopt"]))) $TPL__addopt_1=$GLOBALS["addopt"]->count();else $TPL__addopt_1=0;
if (is_array($GLOBALS["addopt_inputable"])) $TPL__addopt_inputable_1=count($GLOBALS["addopt_inputable"]); else if (is_object($GLOBALS["addopt_inputable"]) && in_array("Countable", class_implements($GLOBALS["addopt_inputable"]))) $TPL__addopt_inputable_1=$GLOBALS["addopt_inputable"]->count();else $TPL__addopt_inputable_1=0;
if (is_array($TPL_VAR["ex"])) $TPL_ex_1=count($TPL_VAR["ex"]); else if (is_object($TPL_VAR["ex"]) && in_array("Countable", class_implements($TPL_VAR["ex"]))) $TPL_ex_1=$TPL_VAR["ex"]->count();else $TPL_ex_1=0;
if (is_array($TPL_VAR["review_loop"])) $TPL_review_loop_1=count($TPL_VAR["review_loop"]); else if (is_object($TPL_VAR["review_loop"]) && in_array("Countable", class_implements($TPL_VAR["review_loop"]))) $TPL_review_loop_1=$TPL_VAR["review_loop"]->count();else $TPL_review_loop_1=0;
if (is_array($TPL_VAR["qna_loop"])) $TPL_qna_loop_1=count($TPL_VAR["qna_loop"]); else if (is_object($TPL_VAR["qna_loop"]) && in_array("Countable", class_implements($TPL_VAR["qna_loop"]))) $TPL_qna_loop_1=$TPL_VAR["qna_loop"]->count();else $TPL_qna_loop_1=0;
if (is_array($TPL_VAR["a_coupon"])) $TPL_a_coupon_1=count($TPL_VAR["a_coupon"]); else if (is_object($TPL_VAR["a_coupon"]) && in_array("Countable", class_implements($TPL_VAR["a_coupon"]))) $TPL_a_coupon_1=$TPL_VAR["a_coupon"]->count();else $TPL_a_coupon_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<?php $this->print_("sub_header",$TPL_SCP,1);?>

<script src="/shop/lib/js/countdown.js"></script>
<script type="text/javascript">
var strprice = '<?php echo $TPL_VAR["strprice"]?>';
var coponlist_scroll;

$(document).ready(function(){
	$("[id=goodsorder-hide]").css("height", $("[id=goodsorder-hide]").height()+30);
	$("[id=goodscart-hide]").css("height", $("[id=goodscart-hide]").height()+30);
	$("[id=goodswish-hide]").css("height", $("[id=goodswish-hide]").height()+30);
	$("#goodsorder-hide").css("position", "absolute");
	$("#goodscart-hide").css("position", "absolute");
	$("#goodswish-hide").css("position", "absolute");


	//다른상품 더보기 data 가져오기
	getGoodsListDataOther();

	$(".goods-other-wrap").hide();

<?php if($TPL_VAR["coupon"]||$TPL_VAR["coupon_emoney"]){?>
	couponlist_scroll = new iScroll('scroll-area');
<?php }?>

	var view_area = $("[name=view_area]").val();

	if(view_area == 'review') {
		changeTab('review');
		var area_pos = $(".goods-info-area").offset();
		$('html, body').animate( { scrollTop:area_pos.top }, 300);
	}
	else if(view_area == 'qna') {
		changeTab('qna');
		var area_pos = $(".goods-info-area").offset();
		$('html, body').animate( { scrollTop:area_pos.top }, 300);

	}


	$(".goods-qna-certification").click(function(){
		var $this = $(this), sno = $this.attr("data-sno"), password = $("#goods-qna-password-"+sno).val();
		if (!password) {
			alert("비밀번호를 입력해주세요.");
			return false;
		}
		$.ajax({
			"url" : "ajaxAction.php",
			"type" : "post",
			"data" : "sno="+sno+"&password="+$("#goods-qna-password-"+sno).val()+"&mode=getGoodsQna",
			"dataType" : "json",
			"success" : function(responseData)
			{
				if (!responseData || !responseData.contents) alert("비밀번호가 일치하지 않습니다.");
				else {
					var add_html = '';
					add_html +='<div class="qna-item-content-question">';
					add_html +='<div class="question-icon"></div>'+responseData.contents+'</div>';

					for(var i=0; i<responseData.reply.length; i++) {
						add_html +='<div class="qna-item-content-answer">';
						add_html +='<div class="answer-icon"></div>'+responseData.reply[i].contents+'</div>';
					}

					$this.parent().parent().html(add_html);
				}
			}
		});
		return false;
	});

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

$.fn.scrollView = function () {
    return this.each(function () {
        $('html, body').animate({
            scrollTop: $(this).offset().top
        }, 1000);
    });
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


function changeTab(tab_type) {

	$("[class=^tab-]").removeClass('active-tab');
	$(".tab-basic").removeClass('active-tab');
	$(".tab-review").removeClass('active-tab');
	$(".tab-qna").removeClass('active-tab');

	$(".tab-basic .bar-area").removeClass('active-bar').removeClass('active-bar2');
	$(".tab-review .bar-area").removeClass('active-bar').removeClass('active-bar2');

	$(".content-basic").hide();
	$(".content-review").hide();
	$(".content-qna").hide();

	if(tab_type == 'basic') {
		$(".tab-basic").addClass('active-tab');
		$(".tab-basic .bar-area").addClass('active-bar');

		$(".content-basic").show();
	}
	else if(tab_type == 'review') {
		$(".tab-review").addClass('active-tab');
		$(".tab-basic .bar-area").addClass('active-bar').addClass('active-bar2');
		$(".tab-review .bar-area").addClass('active-bar');

		$(".content-review").show();
	}
	else if(tab_type == 'qna') {
		$(".tab-qna").addClass('active-tab');
		$(".tab-review .bar-area").addClass('active-bar').addClass('active-bar2');

		$(".content-qna").show();
	}
}

function showReviewContent(review_sno) {
	if($("#review-item-content-" + review_sno).css("display") == "none") {
		$("#review-item-content-" + review_sno).slideDown(100);
	}
	else {
		$("#review-item-content-" + review_sno).slideUp(100);
	}

}

function showQnaContent(qna_sno) {
	if($("#qna-item-content-" + qna_sno).css("display") == "none") {
		$("#qna-item-content-" + qna_sno).slideDown(100);
	}
	else {
		$("#qna-item-content-" + qna_sno).slideUp(100);
	}
}

function showOtherGodds() {

	if($(".goods-other-wrap").css("display") == "none"){
		$(".goods-other-wrap").slideDown(100);
		$(".right_other_btn").addClass("right_other_btn2");
		$(".right_other_btn2").removeClass("right_other_btn");


	} else {
		$(".goods-other-wrap").slideUp(100);
		$(".right_other_btn2").addClass("right_other_btn");
		$(".right_other_btn").removeClass("right_other_btn2");
	}
}

function showCommonInfo(commoninfo_idx) {

	if($("#commoninfo-content-" + commoninfo_idx).css("display") == "none") {

		$("#commoninfo-content-" + commoninfo_idx).slideDown(100);
		$("#commoninfo-title-" + commoninfo_idx).addClass("active_title");
		$("#commoninfo-title-" + commoninfo_idx + " .down_arrow").addClass("up_arrow");
	}
	else {

		$("#commoninfo-content-" + commoninfo_idx).slideUp(100);
		$("#commoninfo-title-" + commoninfo_idx).removeClass("active_title");
		$("#commoninfo-title-" + commoninfo_idx + " .down_arrow").removeClass("up_arrow");

	}
}

function showCouponList() {
	$("#background").show();

	$(".couponlist-area").css("bottom", "-"+$(".couponlist-area").height()+"px");
	$(".couponlist-area").show();

	$(".couponlist-area").animate({bottom:0}, 300, function(){
		couponlist_scroll.refresh();
	});
}

function closeCouponList() {

	$(".couponlist-area").animate({bottom:$(".couponlist-area").height()-($(".couponlist-area").height()*2)}, 300, function(){
		$(".couponlist-area").hide();
		$("#background").hide();
	});

}

function indbAction2(obj_id) {
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
			if(m2CheckForm2(obj_id)===false) return;

			$frm.attr("action", "../ord/order.php");
			$frm.submit();
			break;

		case 'goodscart-hide' :
			var $frm = $("form[name=frmView]");
			var $mode =	$("form[name=frmView] [name=mode]");

			$mode.val('addCart');
			if(m2CheckForm2(obj_id)===false) return;

			var serializedData = $("form[name=frmView]").serialize();

			$.ajax({
				type:"post",
				url:"./ajaxAction.php",
				dataType:"json",
				data: serializedData,
				success:function(result){
					popOpt('cart');
					showResMsg2(result);
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

			break;

		case 'goodswish-hide' :
			var $frm = $("form[name=frmView]");
			var $mode =	$("form[name=frmView] [name=mode]");

			$mode.val('addWishlist');
			if(m2CheckForm2(obj_id)===false) return;

			var serializedData = $("form[name=frmView]").serialize();
			$.ajax({
				type:"post",
				url:"./ajaxAction.php",
				dataType:"json",
				data: serializedData,
				success:function(result){
					popOpt('wish');
					showResMsg2(result);
				},
				error:function(){
					alert('일시적인 에러가 발생하였습니다.\n다시 시도하여주시기 바랍니다.');
				}
			});

			break;
	}
}

function m2CheckForm2(obj_id) {
	var $ea = $("form[name=frmView] [name=ea]");
	var $opt = $("form[name=frmView] [name='opt[]']");

	$opt.val($("[name=goods_opt]").val());
	if($("[name=goods_opt]>option").length > 1) {
		if($opt.val() == "" || $opt.val() == "undefined") {
			alert('선택사항을 선택해 주세요');
			$("[name=goods_opt]").focus();
			return false;
		}
	}
	if(obj_id!="goodswish-hide") {
		if ($("[name=order_cnt]").attr("disabled") == true) {
			alert("품절된 상품입니다");
			return false;
		}
		$ea.val($("[name=order_cnt]").val());
		if(isNaN($ea.val()) || $ea.val() < 1) {
			alert('수량은 숫자로 입력해 주세요');
			$("[name=order_cnt]").focus();
			return false;
		}
	}

	// 추가옵션 체크  및 처리
	//------------------------------------------------------------------------------
	var $_add_opt_val = new Array();
	var check_add_opt = true;
	$("[name=addopt[]]").each(function(index, Element) {
		if (Element.getAttribute("required")!= null ) {
			if (chkText(Element,Element.value,Element.getAttribute("msgR")) == false) {
				check_add_opt = false;
				return;
			}
		}
	});
	if (check_add_opt == false) return false;
	//------------------------------------------------------------------------------

	// 입력옵션 체크 및 처리
	// 기본 form validator 인 chkForm 함수를 이용
	var form = $('form[name=frmView]').get(0)
	var ret = chkForm(form);

	if (ret) {

		var v, tmp;

		$(form).find('input[name="addopt_inputable[]"]').each(function(idx, el) {

			el = $(el);
			v = '';

			if (el.val()) {
				tmp = el.attr('option-value').split('^');
				tmp[2] = el.val();
				v = tmp.join('^');
			}

			$(form).find('input[name="_addopt_inputable[]"]').eq(idx).val(v);
		});
	}

	return ret;

}

function showResMsg2(obj) {
	var sec = 0;

	if(obj.sec == null || obj.sec == "undefined") {
		sec = 1000;
	}
	else {
		sec = obj.sec;
	}

	$("[id=goodsres-hide2] .text_msg").text(obj.msg);
	$("[id=goodsres-hide2]").fadeIn(300);

	setTimeout( function() {
		$("[id=goodsres-hide2]").fadeOut(300);

		if(obj.url && obj.url != "undefined") {
			document.location.href = obj.url;
		}

	}, sec);
}


</script>
<style type="text/css">

.swipe {
  overflow: hidden;
  visibility: hidden;
  position: relative;
}
.swipe-wrap {
  overflow: hidden;
  position: relative;
}
.swipe-wrap > div {
  float:left;
  width:100%;
  position: relative;
}

.goods_price2 {height:20px;line-height:20px;text-align:right;}
.goods_dc {height:20px;line-height:20px;text-align:right;color:#88eeff;}
section#goodsview2 {background:#FFFFFF;}
section#goodsview2 .top_title{clear:both; height:40px;background:url('/shop/data/skin_mobileV2/default/common/img/myp/name_bg.png') repeat-x; line-height:40px; padding-left:10px; color:#FFFFFF; font-size:16px; font-weight:bold;}
section#goodsview2 .top_btn{clear:both; height:40px;background:url('/shop/data/skin_mobileV2/default/common/img/detailp/listbtn_bg.png') repeat-x; line-height:40px;}
section#goodsview2 .top_btn .left_list_btn{ float:left; width:70px; height:27px; background:url('/shop/data/skin_mobileV2/default/common/img/detailp/btn_list_off.png') no-repeat; line-height:27px; color:#FFFFFF; font-size:12px; text-align:center; margin-left:7px; margin-top:7px;}
section#goodsview2 .top_btn .right_other_btn{float:right; width:119px; height:27px; background:url('/shop/data/skin_mobileV2/default/common/img/detailp/btn_more_off.png') no-repeat; line-height:27px; color:#FFFFFF; font-size:12px; text-align:center; margin-right:7px; margin-top:7px;}
section#goodsview2 .top_btn .right_other_btn2{float:right; width:119px; height:27px; background:url('/shop/data/skin_mobileV2/default/common/img/detailp/btn_more02_off.png') no-repeat; line-height:27px; color:#FFFFFF; font-size:12px; text-align:center; margin-right:7px; margin-top:7px;}
section#goodsview2 .top_btn .left_list_btn:active{background:url('/shop/data/skin_mobileV2/default/common/img/detailp/btn_list_on.png') no-repeat;}
section#goodsview2 .top_btn .right_other_btn:active{background:url('/shop/data/skin_mobileV2/default/common/img/detailp/btn_more_on.png') no-repeat;}
section#goodsview2 .top_btn .right_other_btn2:active{background:url('/shop/data/skin_mobileV2/default/common/img/detailp/btn_more02_on.png') no-repeat;}

section#goodsview2 .goods-other-wrap { height:76px; background:url('/shop/data/skin_mobileV2/default/common/img/detailp/more_bg.png') repeat-x; background-size:1px 76px;}
section#goodsview2 .goods-other-area { width:320px; margin:auto;}
section#goodsview2 .goods-other-area .goods-other-content { width:320px; margin:auto; }
section#goodsview2 .goods-other-area .goods-other-content .goods-other-item{ width:50px; height:50px; float:left; margin-top:13px;}
section#goodsview2 .goods-other-area .goods-other-content .left-margin{ margin-left:11px;}
section#goodsview2 .goods-other-area .goods-other-content .right-margin{ margin-right:12px;}
section#goodsview2 .goods-other-area .goods-other-content .goods-other-item img{ width:100%; height:100%;}

section#goodsview2 .goods-other-wrap { height:76px; background:url('/shop/data/skin_mobileV2/default/common/img/detailp/more_bg.png') repeat-x; background-size:1px 76px;}
section#goodsview2 .goods-other-wrap .goods-other-arrow { position:absolute; width:100%;}
section#goodsview2 .goods-other-wrap .goods-other-arrow-left {position:absolute; width:27px; z-index:99; float:left;}
section#goodsview2 .goods-other-wrap .goods-other-arrow .left-arrow{ width:27px; height:37px; margin-top:20px; float:left; background:url('/shop/data/skin_mobileV2/default/common/img/detailp/btn_arrow_pre.png') no-repeat; z-index:99;}
section#goodsview2 .goods-other-wrap .goods-other-arrow-right {position:absolute; width:27px; z-index:99; float:right; right:0px;}
section#goodsview2 .goods-other-wrap .goods-other-arrow .right-arrow{  width:27px; height:37px; margin-top:20px; float:right; background:url('/shop/data/skin_mobileV2/default/common/img/detailp/btn_arrow_next.png') no-repeat; z-index:99;}

section#goodsview2 .goods-contents-area {padding-bottom:26px;}
section#goodsview2 .goods-contents-area .goods-contents-area-top{padding:12px;}
section#goodsview2 .goods-contents-area .thumbnail-area{border:solid 1px #d9d9d9;}
section#goodsview2 .goods-contents-area .thumbnail-area .thumbnail-img{padding:none;margin:none;}
section#goodsview2 .goods-contents-area .thumbnail-area .thumbnail-img img{width:100%; margin:none; margin-bottom:-3px;}
section#goodsview2 .goods-contents-area .thumbnail-area .zoom-area{z-index:99; position:relative; width:37px; height:37px; background:url('/shop/data/skin_mobileV2/default/common/img/detailp/btn_zoomin_off.png') no-repeat; float:right; margin-top:-37px;}
section#goodsview2 .goods-contents-area .thumbnail-area .zoom-area:active{background:url('/shop/data/skin_mobileV2/default/common/img/detailp/btn_zoomin_on.png') no-repeat;}
section#goodsview2 .goods-contents-area .price-area {border:solid 1px #d9d9d9; border-top:none; height:46px;}
section#goodsview2 .goods-contents-area .price-area .price-text{float:left; }
section#goodsview2 .goods-contents-area .price-area .price-text .goods_price{ color:#f03c3c; font-size:16px; font-weight:bold; font-family:dotum; margin-left:15px; line-height:30px;}
section#goodsview2 .goods-contents-area .price-area .price-text .goods_dc{ color:#56758F; font-size:12px; font-weight:bold; font-family:dotum; margin-left:15px; line-height:16px; }
section#goodsview2 .goods-contents-area .price-area .goods_coupon{ background:url('/shop/data/skin_mobileV2/default/common/img/detailp/btn_coupon_off.png') no-repeat;width:80px; height:27px; line-height:27px; color:#FFFFFF; font-size:12px; float:right; text-align:center; margin-top:10px; margin-right:15px;}
section#goodsview2 .goods-contents-area .goods_sales_status{border:solid 1px #d9d9d9; height:43px; margin-top:8px; clear:both;line-height:43px;color:#f03c3c; font-size:16px; font-weight:bold; font-family:dotum;padding-left:15px;}

section#goodsview2 .goods-contents-area .share-area {border:solid 1px #d9d9d9; height:43px; margin-top:8px; clear:both;}
section#goodsview2 .goods-contents-area .share-area .share-title{height:43px; font-size:12px; color:#353535; margin-left:15px; line-height:43px; margin-right:18px; float:left;}
section#goodsview2 .goods-contents-area .share-area .share-btn {float:left;}
section#goodsview2 .goods-contents-area .share-area .share-btn .sns01{background:transparent url("/shop/data/skin_mobileV2/default/common/img/detailp/icon_twitter_off.png") no-repeat;  width:29px; height:29px; float:left; margin-right:12px; margin-top:7px;}
section#goodsview2 .goods-contents-area .share-area .share-btn .sns01:active{background:transparent url("/shop/data/skin_mobileV2/default/common/img/detailp/icon_twitter_on.png") no-repeat;}
section#goodsview2 .goods-contents-area .share-area .share-btn .sns02{background:transparent url("/shop/data/skin_mobileV2/default/common/img/detailp/icon_facebook_off.png") no-repeat;  width:29px; height:29px; float:left; margin-right:12px; margin-top:7px;}
section#goodsview2 .goods-contents-area .share-area .share-btn .sns02:active{background:transparent url("/shop/data/skin_mobileV2/default/common/img/detailp/icon_facebook_on.png") no-repeat;}
section#goodsview2 .goods-contents-area .share-area .share-btn .sns03{background:transparent url("/shop/data/skin_mobileV2/default/common/img/detailp/icon_kakao_off.png") no-repeat;  width:29px; height:29px; float:left; margin-right:12px; margin-top:7px;}
section#goodsview2 .goods-contents-area .share-area .share-btn .sns03:active{background:transparent url("/shop/data/skin_mobileV2/default/common/img/detailp/icon_kakao_on.png") no-repeat;}
section#goodsview2 .goods-contents-area .share-area .share-btn .sns04{background:transparent url("/shop/data/skin_mobileV2/default/common/img/detailp/icon_me2day_off.png") no-repeat;  width:29px; height:29px; float:left; margin-right:12px; margin-top:7px;}
section#goodsview2 .goods-contents-area .share-area .share-btn .sns04:active{background:transparent url("/shop/data/skin_mobileV2/default/common/img/detailp/icon_me2day_on.png") no-repeat;}
section#goodsview2 .goods-contents-area .share-area .share-btn .sns05{background:transparent url("/shop/data/skin_mobileV2/default/common/img/detailp/icon_cyworld_off.png") no-repeat;  width:29px; height:29px; float:left; margin-top:7px;}
section#goodsview2 .goods-contents-area .share-area .share-btn .sns05:active{background:transparent url("/shop/data/skin_mobileV2/default/common/img/detailp/icon_cyworld_on.png") no-repeat;}

section#goodsview2 .goods-contents-area .buy-info-area {margin-top:13px; margin-bottom:18px;}
section#goodsview2 .goods-contents-area .buy-info-item {height:26px; margin-bottom:4px; line-height:26px;}
section#goodsview2 .goods-contents-area .buy-info-item .buy-info-title {float:left; max-width:40%; color:#353535;}
section#goodsview2 .goods-contents-area .buy-info-item .buy-info-contents {float:right; max-width:60%;}
section#goodsview2 .goods-contents-area .buy-info-item .buy-info-contents select{height:26px; width:174px; text-align:right;}
section#goodsview2 .goods-contents-area .buy-info-item .buy-info-contents input{height:20px; text-align:right; float:right; width:50px;}
section#goodsview2 .goods-contents-area .buy-info-item .buy-info-contents input.inputable-addoption{width:165px;}	/* select element's width -9px (padding + border) */
section#goodsview2 .goods-contents-area .buy-info-item .buy-info-contents .cnt_plus{width:26px; height:26px; background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_plus_off.png") no-repeat; float:right; margin-left:5px; }
section#goodsview2 .goods-contents-area .buy-info-item .buy-info-contents .cnt_plus:active{background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_plus_on.png") no-repeat;}
section#goodsview2 .goods-contents-area .buy-info-item .buy-info-contents .cnt_minus{width:26px; height:26px; background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_minus_off.png") no-repeat; float:right; margin-left:5px;}
section#goodsview2 .goods-contents-area .buy-info-item .buy-info-contents .cnt_minus:active{background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_minus_on.png") no-repeat;}
section#goodsview2 .goods-contents-area .buy-info-item p {height:16px;line-height:16px; margin-bottom:8px;}

section#goodsview2 .goods-contents-area .btn-area {width:296px; height:38px; margin:auto;  text-align:center; color:#ffffff; font-size:14px; line-height:38px; margin:0 auto;}
section#goodsview2 .goods-contents-area .btn-area .btn-buy {width:94px; height:38px; background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_buy_off.png") no-repeat; float:left; margin-right:6px;}
section#goodsview2 .goods-contents-area .btn-area .btn-buy:active {background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_buy_on.png") no-repeat;}
section#goodsview2 .goods-contents-area .btn-area .btn-cart {width:94px; height:38px; background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_cart_off.png") no-repeat; float:left; margin-right:6px;}
section#goodsview2 .goods-contents-area .btn-area .btn-cart:active {background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_cart_on.png") no-repeat;}
section#goodsview2 .goods-contents-area .btn-area .btn-wish {width:94px; height:38px; background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_wishlist_off.png") no-repeat; float:left;}
section#goodsview2 .goods-contents-area .btn-area .btn-wish:active {background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_wishlist_on.png") no-repeat;}

section#goodsview2 .goods-contents-area .other-settle-area {margin:18px 0px;}

section#goodsview2 .goods-contents-area .detail-view-area {width:296px;margin:auto; margin-bottom:18px;}
section#goodsview2 .goods-contents-area .detail-view-area .btn-detail {width:296px; height:38px; background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_detailview_off.png") no-repeat; line-height:38px; text-align:center; font-size:14px; color:#FFFFFF;}
section#goodsview2 .goods-contents-area .detail-view-area .btn-detail:active {background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_detailview_on.png") no-repeat;}

section#goodsview2 .goods-contents-area .goods-info-area {}
section#goodsview2 .goods-contents-area .goods-info-area .tab-area{ height:33px;}
section#goodsview2 .goods-contents-area .goods-info-area .tab-area .tab-basic{ float:left; background:url("/shop/data/skin_mobileV2/default/common/img/detailp/infotab_bg.png") repeat-x; width:34%; font-size:12px; color:#353535; line-height:33px;text-align:center; font-weight:bold;}
section#goodsview2 .goods-contents-area .goods-info-area .tab-area .tab-review{ float:left; background:url("/shop/data/skin_mobileV2/default/common/img/detailp/infotab_bg.png") repeat-x; width:32%; font-size:12px; color:#353535; line-height:33px;text-align:center; font-weight:bold;}
section#goodsview2 .goods-contents-area .goods-info-area .tab-area .tab-qna{ float:left; background:url("/shop/data/skin_mobileV2/default/common/img/detailp/infotab_bg.png") repeat-x; width:34%; font-size:12px; color:#353535; line-height:33px;text-align:center; font-weight:bold;}
section#goodsview2 .goods-contents-area .goods-info-area .tab-area .bar-area{float:right; width:2px;height:33px; background:url("/shop/data/skin_mobileV2/default/common/img/detailp/bar_infotab.png") no-repeat;}
section#goodsview2 .goods-contents-area .goods-info-area .tab-area .active-bar{background:url("/shop/data/skin_mobileV2/default/common/img/detailp/bar_infotab.png") no-repeat; background-size:2px 32px;}
section#goodsview2 .goods-contents-area .goods-info-area .tab-area .active-bar2{width:1px;background:url("/shop/data/skin_mobileV2/default/common/img/detailp/bar_infotab.png") no-repeat left top; background-size:2px 33px;}
section#goodsview2 .goods-contents-area .goods-info-area .tab-area .active-tab {background:#FFFFFF; line-height:32px; border-top:solid 1px #dadada;color:#353535;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area {padding:12px 12px 0px 15px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-basic .content-item {clear:both; height:24px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-basic .content-item .content-title{font-size:12px; color:#353535; float:left; width:102px; line-height:24px; height:24px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-basic .content-item .content-content{font-size:12px; color:#353535; float:left;line-height:24px; height:24px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-basic .content-item .red{color:#f03c3c;font-weight:bold;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-basic .content-item .blue{color:#56758F;font-weight:bold;}

section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-title {font-size:14px; font-weight:bold; font-family:dotum; color:#353535; height:27px; line-height:27px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-title .title{float:left;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-title .title .title_cnt{color:#466996}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-title .write-btn{float:right;width:80px; height:27px; line-height:27px; font-size:12px; color:#FFFFFF; font-weight:normal;text-align:center; background:url("/shop/data/skin_mobileV2/default/common/img/info/btn_blue01_off.png") no-repeat;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-title .write-btn:active{background:url("/shop/data/skin_mobileV2/default/common/img/info/btn_blue01_on.png") no-repeat;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-item { border:solid 1px #d9d9d9; border-bottom:none; margin-top:8px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-item .review-item-title { border-bottom:solid 1px #d9d9d9; padding:8px 14px 8px 14px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-item .review-item-title .review-item-subject {font-weight:bold; color:#353535; line-height:19px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-item .review-item-title .review-item-id {color:#353535; line-height:19px; }
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-item .review-item-title .review-item-id .review-item-star {float:right; color:#d4d4d4; font-size:12px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-item .review-item-title .review-item-id .review-item-star .active{color:#FECE00;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-item .review-item-content { background:#F5F5F5;display:none;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-item .review-item-content .review-item-content-review{ border-bottom:solid 1px #d9d9d9; padding:8px 14px 8px 14px; }
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-item .review-item-content .review-item-content-reply{ border-bottom:solid 1px #d9d9d9; padding:8px 14px 8px 14px; }
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-item .review-item-content .review-item-content-reply .reply-icon {float:left; background:url("/shop/data/skin_mobileV2/default/common/img/info/icon_reply.png") no-repeat; width:29px; height:14px; margin-right:5px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-more-btn { background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_more_off.png") no-repeat; width:296px; height:38px; margin:auto; line-height:38px; text-align:center; color:#FFFFFF; font-size:12px; margin-top:12px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-review .review-more-btn:active { background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_more_on.png") no-repeat;}

section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-title {font-size:14px; font-weight:bold; font-family:dotum; color:#353535;  height:27px;  line-height:27px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-title .title{float:left;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-title .title .title_cnt{color:#466996}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-title .write-btn{float:right;width:80px; height:27px; line-height:27px; font-size:12px; color:#FFFFFF; font-weight:normal;text-align:center; background:url("/shop/data/skin_mobileV2/default/common/img/info/btn_blue01_off.png") no-repeat;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-title .write-btn:active{background:url("/shop/data/skin_mobileV2/default/common/img/info/btn_blue01_on.png") no-repeat;}

section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-item { border:solid 1px #d9d9d9; border-bottom:none; margin-top:8px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-item .qna-item-title { border-bottom:solid 1px #d9d9d9; padding:8px 14px 8px 14px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-item .qna-item-title .qna-item-subject {font-weight:bold; color:#353535; line-height:19px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-item .qna-item-title .qna-item-id {color:#353535; line-height:19px; }
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-item .qna-item-title .qna-item-id .answer-n {float:right; width:53px; height:17px; background:url("/shop/data/skin_mobileV2/default/common/img/info/icon_ing.png") no-repeat;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-item .qna-item-title .qna-item-id .answer-y {float:right;  width:43px; height:17px; background:url("/shop/data/skin_mobileV2/default/common/img/info/icon_finish.png") no-repeat;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-item .qna-item-content { background:#F5F5F5; display:none;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-item .qna-item-content .qna-item-content-question{ border-bottom:solid 1px #d9d9d9; padding:8px 14px 8px 14px; }
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-item .qna-item-content .qna-item-content-answer{ border-bottom:solid 1px #d9d9d9; padding:8px 14px 8px 14px; }
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-item .qna-item-content .qna-item-content-answer .answer-icon {float:left; background:url("/shop/data/skin_mobileV2/default/common/img/info/icon_answer.png") no-repeat; width:16px; height:14px; margin-right:5px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-item .qna-item-content .qna-item-content-question .question-icon {float:left; background:url("/shop/data/skin_mobileV2/default/common/img/nmyp/icon_question.png") no-repeat; width:16px; height:14px; margin-right:5px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-more-btn { background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_more_off.png") no-repeat; width:296px; height:38px; margin:auto; line-height:38px; text-align:center; color:#FFFFFF; font-size:12px; margin-top:12px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-qna .qna-more-btn:active { background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_more_on.png") no-repeat;}


section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-basic .commoninfo-area { margin-top:16px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-basic .commoninfo-area .commoninfo-wrap {border:solid 1px #d9d9d9; border-bottom:none; }
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-basic .commoninfo-area .commoninfo-wrap .commoninfo-title{border-bottom:solid 1px #d9d9d9; padding:0px 10px 0px 10px; height:33px; line-height:33px;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-basic .commoninfo-area .commoninfo-wrap .commoninfo-title .down_arrow{background:url("/shop/data/skin_mobileV2/default/common/img/info/icon_arrow_down.png") no-repeat; width:15px; height:15px; margin-top:9px;float:right;}
section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-basic .commoninfo-area .commoninfo-wrap .commoninfo-title .up_arrow{background:url("/shop/data/skin_mobileV2/default/common/img/info/icon_arrow_up.png") no-repeat; width:15px; height:15px; margin-top:9px;float:right;}

section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-basic .commoninfo-area .commoninfo-wrap .active_title{color:#436593; font-weight:bold;}

section#goodsview2 .goods-contents-area .goods-info-area .content-area .content-basic .commoninfo-area .commoninfo-wrap .commoninfo-content{border-bottom:solid 1px #d9d9d9; padding:12px 12px 12px 12px; background:#f5f5f5; display:none;}

section#goodsview2 .goods-contents-area .couponlist-area {bottom:0px; position:fixed; width:100%; background:#FFFFFF; z-index:99; display:none;}
section#goodsview2 .goods-contents-area .couponlist-title {background:url("/shop/data/skin_mobileV2/default/common/img/detailp/bg_coupon_tit.png") repeat-x; height:48px;}
section#goodsview2 .goods-contents-area .couponlist-title .title{height:48px; line-height:48px; margin-left:15px; font-size:16px; color:#FFFFFF; font-family:dotum;font-weight:bold;float:left;}
section#goodsview2 .goods-contents-area .couponlist-title .close-btn{background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_close01_off.png") no-repeat; width:31px; height:32px; margin-top:8px;float:right;margin-right:10px;}
section#goodsview2 .goods-contents-area .couponlist-title .close-btn:active{background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_close01_on.png") no-repeat; }
section#goodsview2 .goods-contents-area .couponlist-item{height:43px; border-bottom:solid 1px #dbdbdb;}
section#goodsview2 .goods-contents-area .couponlist-item .couponlist-item-name{height:43px; line-height:43px; font-size:12px; color:#353535; margin-left:15px;  float:left;}
section#goodsview2 .goods-contents-area .couponlist-item .couponlist-item-name .mobile_coupon{color:#f03c3c;}
section#goodsview2 .goods-contents-area .couponlist-item .download-btn{background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_coupon_off.png") no-repeat; width:80px; height:27px; margin-top:8px; line-height:27px; font-size:12px; color:#FFFFFF; float:right; margin-right:12px;text-align:center;}
section#goodsview2 .goods-contents-area .couponlist-item .download-btn:active{background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_coupon_on.png") no-repeat;}
section#goodsview2 .goods-contents-area .couponlist-title .close-btn{background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_close01_off.png") no-repeat; width:31px; height:32px; margin-top:8px;float:right;margin-right:10px;}

section#goodsview2 .goods-contents-area .couponlist-area .couponlist-item-area {position:relative; max-height:220px; width:100%; overflow:hidden;}

.goods-qna-certification {background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_delete02_off.png") no-repeat; background-size:40px 21px; width:40px; height:21px; border:none; font-size:10px; padding:none; text-align:center;}
.goods-qna-certification:active {background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_delete02_on.png") no-repeat;}

#background {
	position : fixed;
	left : 0;
	top : 0;
	bottom:0px;
	width : 100%;
	height : 100%;
	background : rgba(0, 0, 0, 0.2);
	display:none;
	z-index:98;
}

</style>


<input type="hidden" name="list_category" value="<?php echo $TPL_VAR["category"]?>" />
<input type="hidden" name="list_kw" value="<?php echo $TPL_VAR["kw"]?>" />
<input type="hidden" name="view_area" value="<?php echo $TPL_VAR["view_area"]?>" />

<section id="goodsview2" class="content">
	<div class="top_title">
		<div class="goods_nm">
<?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]){?>
		<?php echo $TPL_VAR["goodsnm"]?>

<?php }elseif($TPL_VAR["slevel"]<$TPL_VAR["clevel"]&&$TPL_VAR["auth_step"][ 1]=='Y'){?>
		<?php echo $TPL_VAR["goodsnm"]?>

<?php }?>
		</div>
	</div>
	<div class="top_btn">
<?php if($TPL_VAR["kw"]){?>
		<div class="left_list_btn" onClick="javascript:document.location.href='/'+mobile_root+'/goods/list.php?kw=<?php echo $TPL_VAR["kw"]?>';">
				&nbsp;&nbsp;&nbsp;목록보기
		</div>
<?php }elseif($TPL_VAR["category"]){?>
		<div class="left_list_btn" onClick="javascript:document.location.href='/'+mobile_root+'/goods/list.php?category=<?php echo $TPL_VAR["category"]?>';">
				&nbsp;&nbsp;&nbsp;목록보기
		</div>
<?php }elseif($TPL_VAR["referer"]){?>
		<div class="left_list_btn" onClick="javascript:document.location.href='<?php echo $TPL_VAR["referer"]?>';">
				&nbsp;&nbsp;&nbsp;목록보기
		</div>
<?php }?>
<?php if($TPL_VAR["category"]||$TPL_VAR["kw"]){?>
		<div class="right_other_btn" onClick="javascript:showOtherGodds();">
			다른상품 더보기&nbsp;&nbsp;&nbsp;
		</div>
<?php }?>
	</div>
	<div class="goods-other-wrap">
		<div class="goods-other-arrow">
			<div class="goods-other-arrow-left"><div class="left-arrow" onClick="javascript:objSwipe.prev();"></div></div>
			<div class="goods-other-arrow-right"><div class="right-arrow" onClick="javascript:objSwipe.next();"></div></div>
		</div>
		<div  id="swipe-other-goods" class="goods-other-area" >
			<div>

			</div>
		</div>
	</div>
	<div class="goods-contents-area">
		<div class="goods-contents-area-top">
		<div class="thumbnail-area">
			<div class="thumbnail-img"><?php echo goodsimgMobile($TPL_VAR["l_img"][ 0], 500)?></div>
			<div class="zoom-area"  onClick="javascript:document.location.href='/'+mobile_root+'/goods/view_bigimg.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>';">
				<div class="zoom-icon"></div>
			</div>
		</div>
		<div class="price-area">
			<div class="price-text">
<?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]){?>
<?php if($TPL_VAR["runout"]&&$GLOBALS["cfg_soldout"]["price"]=='image'){?>
					<div class="goods_price"><img src="../data/goods/icon/custom/soldout_price"></div>
<?php }elseif($TPL_VAR["runout"]&&$GLOBALS["cfg_soldout"]["price"]=='string'){?>
					<div class="goods_price"><?php echo $GLOBALS["cfg_soldout"]["price_string"]?></div>
<?php }else{?>
					<div class="goods_price"><?php if(!$TPL_VAR["strprice"]){?> <?php echo number_format($TPL_VAR["price"])?>원 <?php }else{?> <?php echo $TPL_VAR["strprice"]?> <?php }?></div>
<?php }?>
<?php if($TPL_VAR["discount_mobile"]){?>
					<div class="goods_dc"><?php echo $TPL_VAR["discount_mobile"]?></div>
<?php }?>
<?php }elseif($TPL_VAR["slevel"]<$TPL_VAR["clevel"]&&$TPL_VAR["auth_step"][ 2]=='Y'){?>
<?php if($TPL_VAR["runout"]&&$GLOBALS["cfg_soldout"]["price"]=='image'){?>
					<div class="goods_price"><img src="../data/goods/icon/custom/soldout_price"></div>
<?php }elseif($TPL_VAR["runout"]&&$GLOBALS["cfg_soldout"]["price"]=='string'){?>
					<div class="goods_price"><?php echo $GLOBALS["cfg_soldout"]["price_string"]?></div>
<?php }else{?>
					<div class="goods_price"><?php if(!$TPL_VAR["strprice"]){?> <?php echo number_format($TPL_VAR["price"])?>원 <?php }else{?> <?php echo $TPL_VAR["strprice"]?> <?php }?></div>
<?php }?>
<?php if($TPL_VAR["discount_mobile"]){?>
					<div class="goods_dc"><?php echo $TPL_VAR["discount_mobile"]?></div>
<?php }?>
<?php }?>
			</div>
			<!-- 할인쿠폰 다운받기 -->
<?php if($TPL_VAR["coupon"]||$TPL_VAR["coupon_emoney"]){?>
			<div class="goods_coupon" onClick="javascript:showCouponList();">
				쿠폰받기
			</div>
<?php }?>
		</div>

<?php if($TPL_VAR["sales_status"]=='range'){?>
		<div class="goods_sales_status">
		남은시간 : <span id="el-countdown-1"></span>
		</div>
		<script type="text/javascript">
		Countdown.init('<?php echo date('Y-m-d H:i:s',$TPL_VAR["sales_range_end"])?>', 'el-countdown-1');
		</script>
<?php }elseif($TPL_VAR["sales_status"]=='before'){?>
		<div class="goods_sales_status">
		<?php echo date('Y-m-d H:i:s',$TPL_VAR["sales_range_start"])?> 판매시작합니다.
		</div>
<?php }elseif($TPL_VAR["sales_status"]=='end'){?>
		<div class="goods_sales_status">
		판매가 종료되었습니다.
		</div>
<?php }?>

<?php if($TPL_VAR["snsBtn"]){?>
		<div class="share-area">
			<div class="share-title">
				공유하기
			</div>
			<div class="share-btn">
				<?php echo $TPL_VAR["snsBtn"]?>

			</div>
		</div>
<?php }?>
		<form name="frmView" method="post" onsubmit="return false;">
		<input type="hidden" name="mode" value="" />
		<input type="hidden" name="goodsno" value="<?php echo $TPL_VAR["goodsno"]?>" />
		<input type="hidden" name="goodsCoupon" value="<?php echo $TPL_VAR["coupon"]?>" />
		<input type="hidden" name="ea" value="" />
		<input type="hidden" name="opt[]" value="" />
		<div class="buy-info-area">
<?php if($GLOBALS["opt"]){?>
			<div class="buy-info-item">
				<div class="buy-info-title">선택옵션</div>
				<div class="buy-info-contents">
					<select name="goods_opt">
						<option value="">선택사항</option>
<?php if($TPL__opt_1){foreach($GLOBALS["opt"] as $TPL_V1){?><?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
						<option value="<?php echo $TPL_V2["opt1"]?><?php if($TPL_V2["opt2"]){?>|<?php echo $TPL_V2["opt2"]?><?php }?>" <?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> disabled class=disabled<?php }?>> <?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?>[품절]<?php }?> <?php echo $TPL_V2["opt1"]?><?php if($TPL_V2["opt2"]){?>/<?php echo $TPL_V2["opt2"]?><?php }?> <?php if($TPL_V2["price"]!=$TPL_VAR["price"]){?>(<?php echo number_format($TPL_V2["price"])?>원)<?php }?></option>
<?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> [품절]<?php }?>
<?php }}?><?php }}?>
					</select>
				</div>
			</div>
<?php }?>
			<div class="buy-info-item">
				<div class="buy-info-title">수량</div>
				<div class="buy-info-contents">
					<div class="cnt_plus" onClick="javascript:orderCntCalc(1);"></div>
					<div class="cnt_minus" onClick="javascript:orderCntCalc(-1);"></div>
					<input type="number" name="order_cnt" size="5" value="<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?>" <?php if($TPL_VAR["min_ea"]){?>min="<?php echo $TPL_VAR["min_ea"]?>"<?php }?> <?php if($TPL_VAR["max_ea"]){?>max="<?php echo $TPL_VAR["max_ea"]?>"<?php }?> <?php if($TPL_VAR["sales_unit"]){?>step="<?php echo $TPL_VAR["sales_unit"]?>"<?php }?> onchange="orderCntCalc(this, this.value, true);"/>
				</div>
			</div>
<?php if($TPL_VAR["min_ea"]||$TPL_VAR["max_ea"]||$TPL_VAR["sales_unit"]){?>
			<div class="buy-info-item" style="height:auto; !important;">
				<div style="clear:both;text-align:right;">
<?php if($TPL_VAR["min_ea"]> 1){?><p>최소구매수량 : <?php echo $TPL_VAR["min_ea"]?>개</p><?php }?>
<?php if($TPL_VAR["max_ea"]){?><p>최대구매수량 : <?php echo $TPL_VAR["max_ea"]?>개</p><?php }?>
<?php if($TPL_VAR["sales_unit"]> 1){?><p>묶음주문단위 : <?php echo $TPL_VAR["sales_unit"]?>개</p><?php }?>
				</div>
			</div>
<?php }?>

			<!-- 추가 옵션 -->
<?php if($GLOBALS["addopt"]){?>
<?php if($TPL__addopt_1){$TPL_I1=-1;foreach($GLOBALS["addopt"] as $TPL_K1=>$TPL_V1){$TPL_I1++;?>
			<div class="buy-info-item">
				<div class="buy-info-title"><?php echo $TPL_K1?><?php if($GLOBALS["addoptreq"][$TPL_I1]=='o'){?>(필수)<?php }?></div>
				<div class="buy-info-contents">
					<select name="addopt[]" <?php if($GLOBALS["addoptreq"][$TPL_I1]=='o'){?> required="required" label="<?php echo $TPL_K1?>"<?php }?>> msgR="<?php echo $TPL_K1?>"
					<option value="">==<?php echo $TPL_K1?> 선택==
<?php if(!$GLOBALS["addoptreq"][$TPL_I1]){?><option value="-1">선택안함<?php }?>
<?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
					<option value="<?php echo $TPL_V2["sno"]?>^<?php echo $TPL_K1?>^<?php echo $TPL_V2["opt"]?>^<?php echo $TPL_V2["addprice"]?>"><?php echo $TPL_V2["opt"]?>

<?php if($TPL_V2["addprice"]){?>(<?php echo number_format($TPL_V2["addprice"])?>원)<?php }?>
<?php }}?>
					</select>
				</div>
			</div>
<?php }}?>
<?php }?>

			<!-- 입력 옵션 -->
<?php if($GLOBALS["addopt_inputable"]){?><?php if($TPL__addopt_inputable_1){$TPL_I1=-1;foreach($GLOBALS["addopt_inputable"] as $TPL_K1=>$TPL_V1){$TPL_I1++;?>
			<div class="buy-info-item">
				<div class="buy-info-title"><?php echo $TPL_K1?><?php if($GLOBALS["addopt_inputable_req"][$TPL_I1]=='o'){?>(필수)<?php }?></div>
				<div class="buy-info-contents">
					<input type="hidden" name="_addopt_inputable[]" value="">
					<input type="text" name="addopt_inputable[]" label="<?php echo $TPL_K1?>" option-value="<?php echo $TPL_V1["sno"]?>^<?php echo $TPL_K1?>^<?php echo $TPL_V1["opt"]?>^<?php echo $TPL_V1["addprice"]?>" value="" <?php if($GLOBALS["addopt_inputable_req"][$TPL_I1]){?>required fld_esssential<?php }?> maxlength="<?php echo $TPL_V1["opt"]?>" class="inputable-addoption">
				</div>
			</div>
<?php }}?><?php }?>
		</div>
<?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]){?>
		<div class="btn-area">
			<div class="btn-buy" onClick="javascript:indbAction2('goodsorder-hide');">바로구매</div>
			<div class="btn-cart" onClick="javascript:indbAction2('goodscart-hide');">장바구니</div>
			<div class="btn-wish" onClick="javascript:indbAction2('goodswish-hide');">찜하기</div>
		</div>
<?php }?>
		</form>
		<!-- 네이버 체크아웃, 옥션 아이페이 등 -->
		<div class="other-settle-area">
			<?php echo $TPL_VAR["naverCheckout"]?>

		</div>

		<div class="detail-view-area">
			<div class="btn-detail" onClick="javascript:location.href='/'+mobile_root+'/goods/view_detail.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>';">상세정보 보기</div>
		</div>
		</div>
		<div class="goods-info-area">
			<div class="tab-area">
				<div class="tab-basic active-tab" onclick="javascript:changeTab('basic');">기본정보<div class="bar-area active-bar"></div></div>
				<div class="tab-review" onclick="javascript:changeTab('review');">상품평 <?php if($TPL_VAR["review_cnt"]> 0){?>(<?php echo $TPL_VAR["review_cnt"]?>)<?php }?><div class="bar-area"></div></div>
				<div class="tab-qna" onclick="javascript:changeTab('qna');">상품문의 <?php if($TPL_VAR["qna_cnt"]> 0){?>(<?php echo $TPL_VAR["qna_cnt"]?>)<?php }?></div>
			</div>
			<div class="content-area">
				<!-- 상품 기본정보 시작 -->
				<div class="content-basic">
<?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]||($TPL_VAR["slevel"]<$TPL_VAR["clevel"]&&$TPL_VAR["auth_step"][ 2]=='Y')){?>
<?php if($TPL_VAR["strprice"]){?>
						<div class="content-item">
							<div class="content-title">판매가격</div>
							<div class="content-content red"><?php echo $TPL_VAR["strprice"]?></div>
						</div>
<?php }else{?>
							<div class="content-item">
								<div class="content-title">판매가격</div>
								<div class="content-content red"><?php echo number_format($TPL_VAR["price"])?>원</div>
							</div>
<?php if($TPL_VAR["consumer"]){?>
							<div class="content-item">
								<div class="content-title">소비자가격</div>
								<div class="content-content red"><?php echo number_format($TPL_VAR["consumer"])?>원</div>
							</div>
<?php }?>
<?php if($TPL_VAR["discount_mobile"]){?>
							<div class="content-item">
								<div class="content-title">모바일할인</div>
								<div class="content-content blue"><?php echo $TPL_VAR["discount_mobile"]?></div>
							</div>
<?php }?>
<?php if($TPL_VAR["special_discount_amount"]){?>
							<div class="content-item">
								<div class="content-title">상품할인금액</div>
								<div class="content-content blue">- <?php echo number_format($TPL_VAR["special_discount_amount"])?>원</div>
							</div>
<?php }?>

<?php if($TPL_VAR["realprice"]){?>
							<div class="content-item">
								<div class="content-title">회원할인가격</div>
								<div class="content-content blue"><?php echo number_format($TPL_VAR["realprice"])?>원</div>
							</div>
<?php }?>
<?php if($TPL_VAR["reserve"]){?>
							<div class="content-item">
								<div class="content-title">적립금</div>
								<div class="content-content blue"><?php echo number_format($TPL_VAR["reserve"])?>원</div>
							</div>
<?php }?>
<?php if($TPL_VAR["naverNcash"]=='Y'){?>
							<div class="content-item">
								<div class="content-title">네이버마일리지</div>
								<div class="content-content blue"><?php if($TPL_VAR["exception"]){?><?php echo $TPL_VAR["exception"]?><?php }else{?><?php if($TPL_VAR["N_ba"]){?><span id="naver-mileage-base-accum-rate" style="font-weight:bold;color:#1ec228;"><?php echo $TPL_VAR["N_ba"]?>%</span><?php }?><span id="naver-mileage-add-accum-rate" style="font-weight:bold;color:#1ec228;"></span> 적립<?php }?>&nbsp;<img src="/shop/data/skin_mobileV2/default/img/nmileage/n_mileage_info2.gif" onclick="javascript:mileage_info();" style="cursor: pointer; vertical-align: middle;"></div>
							</div>
<?php }?>
<?php if($TPL_VAR["coupon"]){?>
							<div class="content-item">
								<div class="content-title">쿠폰적용가격</div>
								<div class="content-content red"><?php echo number_format($TPL_VAR["couponprice"])?>원</div>
							</div>
<?php }?>
<?php if($TPL_VAR["coupon_emoney"]){?>
							<div class="content-item">
								<div class="content-title">쿠폰적립금</div>
								<div class="content-content blue"><?php echo number_format($TPL_VAR["coupon_emoney"])?>원</div>
							</div>
<?php }?>

<?php if($TPL_VAR["delivery_type"]== 1){?>
							<div class="content-item">
								<div class="content-title">배송비</div>
								<div class="content-content">무료배송</div>
							</div>
<?php }elseif($TPL_VAR["delivery_type"]== 2){?>
							<div class="content-item">
								<div class="content-title">개별배송비</div>
								<div class="content-content"><?php echo number_format($TPL_VAR["goods_delivery"])?>원</div>
							</div>
<?php }elseif($TPL_VAR["delivery_type"]== 3){?>
							<div class="content-item">
								<div class="content-title">착불배송비</div>
								<div class="content-content"><?php echo number_format($TPL_VAR["goods_delivery"])?>원</div>
							</div>
<?php }elseif($TPL_VAR["delivery_type"]== 4){?>
							<div class="content-item">
								<div class="content-title">고정배송비</div>
								<div class="content-content"><?php echo number_format($TPL_VAR["goods_delivery"])?>원</div>
							</div>
<?php }elseif($TPL_VAR["delivery_type"]== 5){?>
							<div class="content-item">
								<div class="content-title">수량별배송비</div>
								<div class="content-content"><?php echo number_format($TPL_VAR["goods_delivery"])?>원 (수량에따라 배송비 추가)</div>
							</div>
<?php }?>
<?php }?>
<?php }?>
<?php if($TPL_VAR["goodscd"]){?>
					<div class="content-item">
						<div class="content-title">제품코드</div>
						<div class="content-content"><?php echo $TPL_VAR["goodscd"]?></div>
					</div>
<?php }?>
<?php if($TPL_VAR["origin"]){?>
					<div class="content-item">
						<div class="content-title">원산지</div>
						<div class="content-content"><?php echo $TPL_VAR["origin"]?></div>
					</div>
<?php }?>
<?php if($TPL_VAR["maker"]){?>
					<div class="content-item">
						<div class="content-title">제조사</div>
						<div class="content-content"><?php echo $TPL_VAR["maker"]?></div>
					</div>
<?php }?>
<?php if($TPL_VAR["brand"]){?>
					<div class="content-item">
						<div class="content-title">브랜드</div>
						<div class="content-content"><?php echo $TPL_VAR["brand"]?></div>
					</div>
<?php }?>
<?php if($TPL_VAR["launchdt"]){?>
					<div class="content-item">
						<div class="content-title">출시일</div>
						<div class="content-content"><?php echo $TPL_VAR["launchdt"]?></div>
					</div>
<?php }?>
<?php if($TPL_ex_1){foreach($TPL_VAR["ex"] as $TPL_K1=>$TPL_V1){?>
					<div class="content-item">
						<div class="content-title"><?php echo $TPL_K1?></div>
						<div class="content-content"><?php echo $TPL_V1?></div>
					</div>
<?php }}?>
<?php if(commoninfo()){?>
					<div class="commoninfo-area">
						<div class="commoninfo-wrap">
<?php if((is_array($TPL_R1=commoninfo())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
							<div class="commoninfo-title" id="commoninfo-title-<?php echo $TPL_V1["idx"]?>" onClick="javascript:showCommonInfo('<?php echo $TPL_V1["idx"]?>');">
								<?php echo $TPL_V1["title"]?><div class="down_arrow"></div>
							</div>
							<div class="commoninfo-content" id="commoninfo-content-<?php echo $TPL_V1["idx"]?>">
								<?php echo $TPL_V1["info"]?>

							</div>
<?php }}?>
						</div>
					</div>
<?php }?>

				</div>
				<!-- 상품 기본정보 끝 -->
				<!-- 상품 후기 시작 -->
				<div class="content-review" style="display:none;">
<?php if($TPL_VAR["review_cnt"]){?>
					<div class="review-title"><div class="title"><span class="title_cnt">총 <?php echo $TPL_VAR["review_cnt"]?>개</span>의 상품평</div><a href="../myp/review_register.php?mode=add_review&goodsno=<?php echo $TPL_VAR["goodsno"]?>"><div class="write-btn">상품평쓰기</div></a></div>
					<div class="review-list">
						<div class="review-item">
<?php if($TPL_review_loop_1){foreach($TPL_VAR["review_loop"] as $TPL_V1){
if (is_array($TPL_V1["reply"])) $TPL_reply_2=count($TPL_V1["reply"]); else if (is_object($TPL_V1["reply"]) && in_array("Countable", class_implements($TPL_V1["reply"]))) $TPL_reply_2=$TPL_V1["reply"]->count();else $TPL_reply_2=0;?>
							<div class="review-item-title" onClick="javascript:showReviewContent('<?php echo $TPL_V1["sno"]?>');">
								<div class="review-item-subject"><?php echo $TPL_V1["subject"]?></div>
								<div class="review-item-id"><?php echo $TPL_V1["review_name"]?> | <?php echo $TPL_V1["regdt"]?><div class="review-item-star"><?php echo $TPL_V1["point_star"]?></div></div>
							</div>
							<div class="review-item-content" id="review-item-content-<?php echo $TPL_V1["sno"]?>">
								<div class="review-item-content-review">
									<?php echo $TPL_V1["contents"]?>

<?php if($TPL_V1["image"]){?>
									<div>
									<?php echo $TPL_V1["image"]?>

									</div>
<?php }?>
								</div>
<?php if($TPL_reply_2){foreach($TPL_V1["reply"] as $TPL_V2){?>
								<div class="review-item-content-reply">
									<div class="reply-icon"></div><?php echo $TPL_V2["contents"]?>

								</div>
<?php }}?>
							</div>
<?php }}?>
						</div>
<?php if($TPL_VAR["review_cnt"]> 10){?>
						<div class="review-more-btn" onClick="document.location.href='../myp/review.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>';">더보기</div>
<?php }?>

<?php }else{?>
					<div class="review-title"><div class="title">상품평이 없습니다</div><a href="../myp/review_register.php?mode=add_review&goodsno=<?php echo $TPL_VAR["goodsno"]?>"><div class="write-btn">상품평쓰기</div></a></div>
					<div class="review-list">
<?php }?>
					</div>
				</div>
				<!-- 상품 후기 끝 -->
				<!-- 상품 문의 시작 -->
				<div class="content-qna" style="display:none;">
<?php if($TPL_VAR["qna_cnt"]){?>
					<div class="qna-title"><div class="title"><span class="title_cnt">총 <?php echo $TPL_VAR["qna_cnt"]?>개</span>의 상품문의</div><a href="../goods/goods_qna_register.php?mode=add_qna&goodsno=<?php echo $TPL_VAR["goodsno"]?>"><div class="write-btn">문의하기</div></a></div>
					<div class="qna-list">
						<div class="qna-item">
<?php if($TPL_qna_loop_1){foreach($TPL_VAR["qna_loop"] as $TPL_V1){
if (is_array($TPL_V1["reply"])) $TPL_reply_2=count($TPL_V1["reply"]); else if (is_object($TPL_V1["reply"]) && in_array("Countable", class_implements($TPL_V1["reply"]))) $TPL_reply_2=$TPL_V1["reply"]->count();else $TPL_reply_2=0;?>
							<div class="qna-item-title" onClick="javascript:showQnaContent('<?php echo $TPL_V1["sno"]?>');">
								<div class="qna-item-subject"><?php echo $TPL_V1["subject"]?></div>
								<div class="qna-item-id"><?php echo $TPL_V1["qna_name"]?> | <?php echo $TPL_V1["regdt"]?>

<?php if($TPL_V1["reply_cnt"]> 0){?>
								<div class="answer-y"></div>
<?php }else{?>
								<div class="answer-n"></div>
<?php }?>
								</div>

							</div>
							<div class="qna-item-content"  id="qna-item-content-<?php echo $TPL_V1["sno"]?>">

<?php if($TPL_V1["accessable"]){?>
								<div class="qna-item-content-question">
									<div class="question-icon"></div><?php echo $TPL_V1["contents"]?>

								</div>
<?php if($TPL_reply_2){foreach($TPL_V1["reply"] as $TPL_V2){?>
								<div class="qna-item-content-answer">
									<div class="answer-icon"></div><?php echo $TPL_V2["contents"]?>

								</div>
<?php }}?>
<?php }else{?>
								<div class="qna-item-content-question">
<?php if($TPL_V1["m_no"]> 0){?>
									비밀글 입니다.
<?php }else{?>
									비밀번호 :
									<input type="password" id="goods-qna-password-<?php echo $TPL_V1["sno"]?>" name="password" required="required"/>
									<button type="button" data-sno="<?php echo $TPL_V1["sno"]?>"  class="goods-qna-certification">확인</button>
<?php }?>
								</div>
<?php }?>


							</div>
<?php }}?>
						</div>
<?php if($TPL_VAR["qna_cnt"]> 10){?>
						<div class="qna-more-btn" onClick="document.location.href='../goods/goods_qna_list.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>';">더보기</div>
<?php }?>
					</div>
<?php }else{?>
					<div class="qna-title"><div class="title">상품문의가 없습니다</div><a href="../goods/goods_qna_register.php?mode=add_qna&goodsno=<?php echo $TPL_VAR["goodsno"]?>"><div class="write-btn">문의하기</div></a></div>
					<div class="qna-list">
<?php }?>
				</div>
				<!-- 상품 문의 끝 -->
			</div>
		</div>

<?php if($TPL_VAR["coupon"]||$TPL_VAR["coupon_emoney"]){?>
		<div class="couponlist-area">
			<div class="couponlist-title">
				<div class="title">다운로드 쿠폰 List <?php if($TPL_VAR["coupon_cnt"]> 0){?>(<?php echo $TPL_VAR["coupon_cnt"]?>)<?php }?> </div>
				<div class="close-btn" onClick="javascript:closeCouponList();"></div>
			</div>
			<div class="couponlist-item-area">
				<div id="scroll-area">
				<ul>
<?php if($TPL_a_coupon_1){foreach($TPL_VAR["a_coupon"] as $TPL_V1){?>
				<li>
				<div class="couponlist-item">
					<div class="couponlist-item-name"><?php echo $TPL_V1["coupon"]?><?php if($TPL_V1["c_screen"]=='m'){?><span class="mobile_coupon"> (모바일전용)</span><?php }?></div>
					<a href="<?php echo $GLOBALS["cfg"]["rootDir"]?>/proc/dn_coupon_goods.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>&couponcd=<?php echo $TPL_V1["couponcd"]?>'" target="ifrmHidden"><div class="download-btn">쿠폰받기</div></a>
				</div>
				</li>
<?php }}?>
				</ul>
				</div>
			</div>
		</div>
<?php }?>
		<div id="background"></div>
	</div>
</section>


<?php $this->print_("footer",$TPL_SCP,1);?>