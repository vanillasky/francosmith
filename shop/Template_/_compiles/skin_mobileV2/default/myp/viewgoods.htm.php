<?php /* Template_ 2.2.7 2014/02/03 20:16:22 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/myp/viewgoods.htm 000024122 */ 
if (is_array($TPL_VAR["goods_data"])) $TPL_goods_data_1=count($TPL_VAR["goods_data"]); else if (is_object($TPL_VAR["goods_data"]) && in_array("Countable", class_implements($TPL_VAR["goods_data"]))) $TPL_goods_data_1=$TPL_VAR["goods_data"]->count();else $TPL_goods_data_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<?php  $TPL_VAR["page_title"] = "최근본 상품";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>

<style type="text/css">
#goods-order-layer {
	position : absolute;
	left : 2%;
	width : 96%;
	background : #ffffff;
	display : block;
	border-radius:1em;
	box-shadow:2px 2px 4px #7f7f7f;
	z-index: 1000;
}

.goods_order_title {
	background:#313030;
	width:100%;
	border-top-left-radius:1em;
	border-top-right-radius:1em;
	height:45px;
	border-bottom:solid 1px #b2b2b2;
	margin-bottom:6px;
}

.goods_order_title .title{
	padding-left:14px;
	line-height:45px;
	font-size:16px;
	font-weight:bold;
	color:#FFFFFF;
	font-family:dotum;
	float:left;
}

.goods_order_title #cancel-goods-btn {
	float:right;
	background:url("/shop/data/skin_mobileV2/default/common/img/nmyp/btn_close_off.png") no-repeat;
	border:none;
	width:31px;
	height:32px;
	margin-top:7px;
	margin-right:7px;
}

.goods_order_title #cancel-goods-btn:active{
	background:url("/shop/data/skin_mobileV2/default/common/img/nmyp/btn_close_on.png") no-repeat;
}

.goods_order_btn {
	margin-top:16px;
	margin-bottom:25px;
	text-align:center;
}
#checkout-button-area{
	width: 100%;
	height:0px;
}

#background {
	position : absolute;
	left : 0;
	top : 0;
	width : 100%;
	height : 100%;
	background : #000000;
	display : none;
	z-index: 999;
}

#order-goods-btn {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_red02_off.png") top left no-repeat; height:34px; width:75px; border:none; text-align:center; font-size:14px; font-weight:bold; color:#ffffff;}
#cart-goods-btn {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_black02_off.png") top left no-repeat; height:34px; width:75px; border:none; text-align:center; font-size:14px; font-weight:bold; color:#ffffff;}
#wish-goods-btn {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_gray02_off.png") top left no-repeat; height:34px; width:75px; border:none; text-align:center; font-size:14px; font-weight:bold; color:#ffffff;}



#order-goods-btn:active {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_red02_on.png") top left no-repeat;}
#cart-goods-btn:active {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_black02_on.png") top left no-repeat;}
#wish-goods-btn:active {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_gray02_on.png") top left no-repeat;}

.origin-goods-order-layer-item { padding-left:14px; padding-right:18px; height:26px; padding-top:4px;}
.origin-goods-order-layer-item .title{ font-size:12px; line-height:26px; height:26px; width:38%; font-family:dotum; display:block; float:left; text-align:left;}
.origin-goods-order-layer-item .content{ font-size:12px; line-height:26px; height:26px; width:62%; font-family:dotum; display:block; float:right; text-align:right;}
.origin-goods-order-layer-item .content select{ height:26px; width:100%;}


.cnt_minus_btn {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_minus_off.png") top left no-repeat; height:26px; width:26px; border:none; text-align:center; color:#ffffff; margin-left:3px;}
.cnt_plus_btn {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_plus_off.png") top left no-repeat; height:26px; width:26px; border:none; text-align:center; color:#ffffff; margin-left:3px;}
.cnt_minus_btn:active {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_minus_on.png") top left no-repeat; height:26px; width:26px; border:none; text-align:center; color:#ffffff; margin-left:3px;}
.cnt_plus_btn:active {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_plus_on.png") top left no-repeat; height:26px; width:26px; border:none; text-align:center; color:#ffffff; margin-left:3px;}
</style>
<script type="text/javascript">
$(document).ready(function(){
	var
	$goodsOrderLayer = $("#goods-order-layer").bind('delete', function(){
		$(this).fadeOut("fast");
		$("#background").fadeOut("fast");
	}).bind('create', function(){
		$(this).fadeIn("fast");
		$("#background").fadeIn("fast");
	}),
	$form = $goodsOrderLayer.children("form").submit(function(){
		if (chkForm(this)) {
			if (this.ea.value < 1) {
				alert("수량을 1개이상 입력해주세요");
				this.ea.focus();
				return false;
			}
			return true;
		}
		else {
			return false;
		}
	}),
	$background = $("#background").click(function(){
		$goodsOrderLayer.trigger('delete');
	});
	$("#order-goods-btn").click(function(){
		$form.attr("action", "<?php echo $GLOBALS["mobileRootDir"]?>/ord/order.php");
		$form.find("[name=mode]").val("addItem");
		$form.submit();
		return false;
	});
	$("#checkout-button-area").bind("load", function(){
		var contentBody = this.contentWindow.document.body;
		$(contentBody).css({
			"margin" : "0",
			"padding" : "0"
		});
		if (contentBody.children.length) $(this).height($(contentBody).height()).css("margin-bottom", 25);
		else $(this).height(0).css("margin-bottom", 0);
	});
	$("#cart-goods-btn").click(function(){
		$form.find("[name=mode]").val("addCart");

		if (checkForm($form[0]) === false) return false;

		$.ajax({
			"type" : "post",
			"url" : "<?php echo $GLOBALS["mobileRootDir"]?>/goods/ajaxAction.php",
			"dataType" : "json",
			"data" : $form.serialize(),
			"success" : function(result)
			{
				$.ajax({
					"url" : "<?php echo $GLOBALS["mobileRootDir"]?>/proc/mAjaxAction.php",
					"type" : "post",
					"data" : {
						"mode" : "get_cart_item"
					},
					"cash" : false,
					"dataType" : "json",
					"success" : function(cartItem)
					{
						if (cartItem.quantity) {
							$("#cart-btn .cart-item-quantity").text(" ("+cartItem.quantity.toString()+")");
						}
					}
				});
				alert(result.msg);
				$goodsOrderLayer.trigger('delete');
			},
			"error" : function(xhr)
			{
				var n1 = xhr.responseText.indexOf("<script>"), n2 = xhr.responseText.indexOf("<\/script>");
				if (n1>0 && n2 >n1) {
					var errmsg = xhr.responseText.substring(n1, n2 + "<\/script>".length);
					ifrmHidden.document.write(errmsg);
				} else {
					alert('장바구니 추가실패!\n다시 시도하여주시기 바랍니다.');
				}
			}
		});
		return false;
	});
	$("#wish-goods-btn").click(function(){
		$form.find("[name=mode]").val('addWishlist');

		if (checkForm($form[0]) === false) return false;

		$.ajax({
			"type" : "post",
			"url" : "<?php echo $GLOBALS["mobileRootDir"]?>/goods/ajaxAction.php",
			"dataType" : "json",
			"data" : $form.serialize(),
			"success" : function(result)
			{
				alert(result.msg);
				$goodsOrderLayer.trigger('delete');
			},
			"error" : function(xhr)
			{
				var n1 = xhr.responseText.indexOf("<script>"), n2 = xhr.responseText.indexOf("<\/script>");
				if (n1>0 && n2 >n1) {
					var errmsg = xhr.responseText.substring(n1, n2 + "<\/script>".length);
					ifrmHidden.document.write(errmsg);
				} else {
					alert('일시적인 에러가 발생하였습니다.\n다시 시도하여주시기 바랍니다.');
				}
			}
		});
		return false;
	});
	$('#cancel-goods-btn').click(function(){
		$goodsOrderLayer.trigger('delete');
		return false;
	});
	$(".goods-row .goods-item .cart-order-btn").live("click", function(){

		var
		GOODSNO = $(this).attr("data-goodsno"),
		ITEM_HTML = '<div class="origin-goods-order-layer-item"><span class="title"></span><span class="content"></span></div>';
		$("#checkout-button-area").attr("src", "../../shop/proc/NaverCheckout_Button.php?goodsno="+GOODSNO+"&device=MOBILE");
		$.ajax({
			"url" : "<?php echo $GLOBALS["mobileRootDir"]?>/proc/mAjaxAction.php",
			"type" : "post",
			"data" : {
				"mode" : "get_option",
				"goodsno" : GOODSNO
			},
			"dataType" : "json",
			"success" : function(option) {
				var $optionSelectItemList = $form.children(".option-select-item-list").html("");

				$goodsOrderLayer.css({
					"top" : ($(window).scrollTop()+150)+"px"
				}),
				$background.css({
					"height" : ($("#wrap").height()+"px"),
					"opacity" : "0.2",
					"display" : "block",
				});

				$form.find("[name=goodsno]").val(GOODSNO);

				// 분리형 옵션의 선택박스 구성
				if (option.combination != null) {
					if (option.type == "double") {
						if (option.list[0]) {
							var
							$optionRow1 = $(ITEM_HTML),
							$optionName1 = $optionRow1.find(".title"),
							$content1 = $optionRow1.find(".content"),
							$selectBox = $(document.createElement("select")).attr("required", "required").attr("name", "opt[]"),
							$option = $(document.createElement("option"));

							$optionName1.text(option.name[0]);
							$content1.append($selectBox);

							$selectBox.append($option.clone().text("선택해주세요").val(""));
							for (var index in option.list[0]) {
								var
								optionValue = option.list[0][index],
								optionText = optionValue,
								combination = option.combination[optionValue+"/"],
								$_option = $option.clone();
								if (!option.list[1] && combination) {
									optionText += " ("+combination.price+"원)";
									if (option.stockable == true && combination.stock < 1) {
										optionText += " [품절]";
									}
								}
								$_option.text(optionText).val(optionValue)
								$selectBox.append($_option);
							}

							$optionRow1.append($optionName1).append($content1);
							$optionSelectItemList.append($optionRow1);

							if (option.list[1]) {
								var
								$optionRow2 = $(ITEM_HTML),
								$optionName2 = $optionRow2.find(".title"),
								$content2 = $optionRow2.find(".content"),
								$selectBox2 = $($selectBox.get(0).cloneNode());
								$selectBox.change(function(){
									$selectBox2.html("");
									if ($(this).val()) {
										$selectBox2.append($option.clone().text("선택해주세요").val(""));
									}
									else {
										$selectBox2.append($option.clone().text("1차옵션을 먼저 선택해주세요").val(""));
										return;
									}
									for (var index in option.list[1]) {
										var
										optionValue = option.list[1][index],
										optionText = optionValue,
										combination = option.combination[$selectBox.val()+"/"+optionValue],
										$_option = $option.clone();
										if (combination) {
											optionText += " ("+combination.price+"원)";
											if (option.stockable == true && combination.stock < 1) {
												optionText += " [품절]";
												$_option.attr("disabled", "disabled");
											}
										}
										$_option.text(optionText).val(optionValue)
										$selectBox2.append($_option);
									}
								});
								$optionName2.text(option.name[1]);
								$content2.append($selectBox2);
								$selectBox2.append($option.clone().text("1차옵션을 먼저 선택해주세요").val(""));
								$optionRow2.append($optionName2).append($content2);
								$optionSelectItemList.append($optionRow2);
							}
						}
					}
					// 일체형 옵션의 선택박스 구성
					else {
						var
						$optionRow = $(ITEM_HTML),
						$optionName = $optionRow.find(".title"),
						$optionContent = $optionRow.find(".content"),
						$selectBox = $(document.createElement("select")).attr("required", "required").attr("name", "opt[]"),
						$option = $(document.createElement("option"));
						$optionName.text(option.name.join("/"));
						$selectBox.append($option.clone().text("선택해주세요").val(""));
						for (var index in option.combination) {
							var
							combination = option.combination[index],
							optionValue = combination.opt1+"|"+combination.opt2,
							optionText = index,
							$_option = $option.clone();
							optionText += " ("+combination.price+"원)";
							if (option.stockable == true && combination.stock < 1) {
								optionText += " [품절]";
								$_option.attr("disabled", "disabled");
							}
							$_option.text(optionText).val(optionValue);
							$selectBox.append($_option);
						}
						$optionContent.append($selectBox);
						$optionRow.append($optionName).append($optionContent);
						$optionSelectItemList.append($optionRow);
					}
				}

				// 추가옵션 구성
				var _idx = 0;
				for (var step in option.addopt) {
					var
					$addRow = $(ITEM_HTML),
					$addName = $addRow.find(".title"),
					$addContent = $addRow.find(".content"),
					$selectBox = $(document.createElement("select")).attr("name", "addopt[]"),
					$option = $(document.createElement("option"));

					$addName.text(step);

					if (option.addoptreq[_idx++] == true) $selectBox.attr("required", "required");
					$selectBox.append($option.clone().text("선택해주세요").val(""));
					for (var index in option.addopt[step]) {
						var
						addOption = option.addopt[step][index],
						$_option = $option.clone(),
						addOptionText = addOption.opt;
						if (addOption.addprice > 0) addOptionText += " (+ "+addOption.addprice+"원)";
						$_option.text(addOptionText).val(addOption.sno+"^"+step+"^"+addOption.opt+"^"+addOption.addprice);
						$selectBox.append($_option);
					}
					$addContent.append($selectBox);

					$addRow.append($addName, $addContent);

					$optionSelectItemList.append($addRow);
				}

				// 입력옵션 구성
				var _idx = 0;
				for (var step in option.addopt_inputable) {

					var v = option.addopt_inputable[step];
					var
					$addRow = $(ITEM_HTML),
					$addName = $addRow.find(".title"),
					$addContent = $addRow.find(".content"),
					$addInput = $(document.createElement("input")).attr({
						name: 'addopt_inputable[]',
						type: 'text',
						label: step,
						'option-value': v.sno + '^' + step + '^' + v.opt + '^' + v.addprice,
						maxlength : v.opt
					}).css({'width':'100%'}),
					$_addInput = $(document.createElement("input")).attr({
						name: '_addopt_inputable[]',
						type: 'hidden'
					});

					if (option.addopt_inputable_req[_idx++] == true) $addInput.attr({"required" : "required", "fld_esssential" : "fld_esssential"});

					$addName.text(step);

					$addContent.append($addInput).append($_addInput);
					$addRow.append($addName, $addContent);
					$optionSelectItemList.append($addRow);
				}

				// 수량입력란 구성
				var
				$eaRow = $(ITEM_HTML),
				$eaTitle = $eaRow.find(".title"),
				$eaContent = $eaRow.find(".content"),
				$eaInput = $(document.createElement("input")).attr({
					"type" : "text",
					"size" : "4",
					"name" : "ea",
					"value": option.min_ea ? option.min_ea : 1
				}).css({
					"text-align" : "right",
					"height" : "22px"
				}).change(function(){
					orderCntCalc($(this), $(this).val(), 'set');
				});
				$eaTitle.text("수량");
				$eaContent.append($eaInput);
				$eaContent.append($(document.createElement("button")).attr("type", "button").addClass("cnt_minus_btn").text("-").click(function(){
					orderCntCalc($eaInput, -1);
					return false;
				})).append($(document.createElement("button")).attr("type", "button").addClass("cnt_plus_btn").text("+").click(function(){
					orderCntCalc($eaInput, 1);
					return false;
				}));

				if (option.min_ea) {
					$eaInput.attr('min', option.min_ea);
				}

				if (option.max_ea) {
					$eaInput.attr('max', option.max_ea);
				}

				if (option.sales_unit) {
					$eaInput.attr('step', option.sales_unit);
				}

				$eaRow.append($eaTitle).append($eaContent);
				$optionSelectItemList.append($eaRow);

				$goodsOrderLayer.trigger('create');
			}
		});
	});
});

function checkForm(form)
{
	var ret = chkForm(form);

	if (ret) {
		if (form.ea.value < 1) {
			alert("수량을 1개이상 입력해주세요");
			form.ea.focus();
			return false;
		}

		// 입력옵션 체크 및 처리
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

		return true;
	}
	else {
		return false;
	}
}
function  moreGoods() {
	var item_cnt = $(".goods-item").length;

	data_param = "mode=get_view_goods_data&item_cnt=" + item_cnt;

	try {
		$.ajax({
			type: "post",
			url: "/m2/proc/mAjaxAction.php",
			cache:false,
			async:false,
			data: data_param,
			success: function (res) {

				makeGoodsItem(res);
			},
			dataType:"json"
		});
	}
	catch(e) {
		alert(e);
	}
}

function makeGoodsItem(goods_data) {

	if(goods_data.length > 0) {

		var add_html = "";

		for(var i=0; i<goods_data.length; i++) {

			if((i+1) % 3 == 1) {
				add_html += '<div class="goods-row">';
			}

			add_html += '<div class="goods-item"';
			if((i+1) % 3 == 1 || (i+1) % 3 == 2) {
				add_html += ' style="margin-right:5%;" ';
			}
			add_html += '>';
			add_html += '<div class="goods-img"><a href="../goods/view.php?goodsno='+goods_data[i].goodsno+'">'+goods_data[i].img_html+'</div>';
			add_html += '<div class="goods-nm"><a href="../goods/view.php?goodsno='+goods_data[i].goodsno+'">'+goods_data[i].goodsnm+'</div>';
			add_html += '<div class="goods-price"><a href="../goods/view.php?goodsno='+goods_data[i].goodsno+'">'+goods_data[i].price+'</div>';
			add_html += '<div class="goods-dc"></div>';
			add_html += '<div class="goods-btn"><div class="del-btn" onClick="javascript:delGoods(\''+goods_data[i].goodsno+'\');"></div><div class="cart-order-btn" data-goodsno="'+goods_data[i].goodsno+'" ></div></div>';
			add_html += '</div>';
			if((i+1) % 3 ==0 || (i+1) == goods_data.length) {
				add_html += '<div style="width:100%; height:0px; clear:both;"></div>';
				add_html += '</div>';
			}
		}
	}

	if(goods_data.length < 9) {
		$(".more-btn").hide();
	}

	$(".goods-content").append(add_html);
}

function delGoods(goodsno) {

	var frm = $("[name=frm_goods_view]");
	$("[name=mode]").val('del_goodsview');
	$("[name=goodsno]").val(goodsno);

	frm.submit();

}
</script>

<style type="text/css">

section#viewgoods {background:#FFFFFF; }
section#viewgoods .goods-area {padding:12px; height:100%;}
section#viewgoods .goods-area .goods-row{clear:both; margin:auto; min-width:296px;}
section#viewgoods .goods-area .goods-item{width:30%; margin-bottom:18px; display:block; float:left; min-width:87px;}
section#viewgoods .goods-area .goods-item .goods-img{width:100%; text-align:center;}
section#viewgoods .goods-area .goods-item .goods-img img{width:100%; margin:auto; border:solid 1px #dbdbdb;}
section#viewgoods .goods-area .goods-item .goods-nm {width:100%; height:28px; font-size:12px; color:#353535; margin-top:5px;overflow:hidden;}
section#viewgoods .goods-area .goods-item .goods-nm a{font-size:12px; color:#353535;}
section#viewgoods .goods-area .goods-item .goods-price {width:100%; height:18px; font-weight:bold; font-size:14px; color:#f03c3c; line-height:18px;}
section#viewgoods .goods-area .goods-item .goods-price a{font-weight:bold; font-size:14px; color:#f03c3c;}
section#viewgoods .goods-area .goods-item .goods-dc {width:100%; height:18px; font-size:12px; font-weight:bold; color:#436693; line-height:15px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
section#viewgoods .goods-area .goods-item .goods-dc a{font-size:12px; font-weight:bold; color:#436693;}
section#viewgoods .goods-area .goods-item .goods-btn {width:100%; height:21px; }
section#viewgoods .goods-area .goods-item .goods-btn .del-btn{width:100%; height:21px; width:31px; background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_delete_off.png") no-repeat; float:left;}
section#viewgoods .goods-area .goods-item .goods-btn .del-btn:active{background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_delete_on.png") no-repeat;}
section#viewgoods .goods-area .goods-item .goods-btn .cart-order-btn{width:100%; height:21px; width:56px; background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_order_off.png") no-repeat; float:right;}
section#viewgoods .goods-area .goods-item .goods-btn .cart-order-btn:active{background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_order_on.png") no-repeat;}

section#viewgoods .right-margin {margin-right:5%;}
section#viewgoods .goods-area .more-btn {width:296px; margin:auto; background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_more_off.png") no-repeat; text-align:center; height:38px; color:#ffffff; line-height:38px; font-size:14px;}
section#viewgoods .goods-area .more-btn:active {background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_more_on.png") no-repeat; }

</style>

<section id="viewgoods" class="content">
<?php if($TPL_VAR["goods_data"]){?>
	<div class="goods-area">
	<div class="goods-content">
<?php if($TPL_goods_data_1){$TPL_I1=-1;foreach($TPL_VAR["goods_data"] as $TPL_V1){$TPL_I1++;?>
<?php if(($TPL_I1+ 1)% 3== 1){?>
	<div class="goods-row">
<?php }?>
	<div class="goods-item" <?php if(($TPL_I1+ 1)% 3== 1||($TPL_I1+ 1)% 3== 2){?> style="margin-right:5%;" <?php }?> >
		<div class="goods-img">
			<a href="../goods/view.php?goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo goodsimgMobile($TPL_V1["img"], 100)?></a>
		</div>
		<div class="goods-nm">
			<a href="../goods/view.php?goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo $TPL_V1["goodsnm"]?></a>
		</div>
<?php if($TPL_V1["strprice"]){?>
		<div class="goods-price">
			<a href="../goods/view.php?goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo $TPL_V1["strprice"]?></a>
		</div>
<?php }else{?>
		<div class="goods-price">
			<a href="../goods/view.php?goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo number_format($TPL_V1["price"])?>원</a>
		</div>
<?php }?>
		<div class="goods-dc">
			<a href="../goods/view.php?goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo $TPL_V1["dc"]?></a>
		</div>
		<div class="goods-btn">
			<div class="del-btn" onClick="javascript:delGoods('<?php echo $TPL_V1["goodsno"]?>');"></div>
<?php if(!$TPL_V1["strprice"]){?>
			<div class="cart-order-btn" data-goodsno="<?php echo $TPL_V1["goodsno"]?>"></div>
<?php }?>
		</div>
	</div>
<?php if(($TPL_I1+ 1)% 3== 0||($TPL_I1+ 1)==$TPL_goods_data_1){?>
	<div style="width:100%; height:0px; clear:both;"></div>
	</div>
<?php }?>
<?php }}?>
	</div>
<?php if(!($TPL_goods_data_1< 9)){?>
	<div class="more-btn" onclick="javascript:moreGoods();">더보기</div>
<?php }?>
	</div>
<?php }else{?>

<?php }?>
<form name="frm_goods_view" method="post" action="indb.php" target="ifrmHidden">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="goodsno" value="" />
</form>


</section>

<section id="goods-order-layer" style="display: none;">
	<div class="goods_order_title"><div class="title">옵션선택</div><input id="cancel-goods-btn" class="cancel-goods" type="button" value=""/></div>
	<form method="post" action="" name="frmView">
		<input type="hidden" name="mode" value=""/>
		<input type="hidden" name="goodsno" value=""/>
		<article class="option-select-item-list"></article>
		<div class="goods_order_btn">
			<input id="order-goods-btn" class="order-goods" type="button" value="바로구매"/>
			<input id="cart-goods-btn" class="cart-goods" type="button" value="장바구니"/>
			<input id="wish-goods-btn" class="wish-goods" type="button" value="찜하기"/>
		</div>
	</form>
	<iframe id="checkout-button-area"></iframe>
</section>

<div id="background"></div>

<?php $this->print_("footer",$TPL_SCP,1);?>