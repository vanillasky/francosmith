<?php /* Template_ 2.2.7 2013/08/07 14:16:20 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/goods/cart.htm 000017880 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>

<?php  $TPL_VAR["page_title"] = "장바구니";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>


<script>
function act(mode)
{
	var fm = document.frmCart;
	if (isChked('idxs[]')){
		fm.mode.value = mode;
		fm.submit();
	}
}
</script>
<style type="text/css">
#option-modify-layer {
	position : absolute;
	left : 10%;
	width : 80%;
	background : #ffffff;
	display : block;
	border-radius:1em;
	box-shadow:2px 2px 4px #7f7f7f;
	z-index: 1000;
}

.option_modify_title {
	background:#313030;
	width:100%;
	border-top-left-radius:1em;
	border-top-right-radius:1em;
	height:45px;
	border-bottom:solid 1px #b2b2b2;
	margin-bottom:6px;
}

.option_modify_title .title{
	padding-left:14px;
	line-height:45px;
	font-size:16px;
	font-weight:bold;
	color:#FFFFFF;
	font-family:dotum;
}

.option_modify_btn {
	margin-top:16px;
	margin-bottom:25px;
	text-align:center;
}

#option-modify-btn {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_red01_off.png") top left no-repeat; height:34px; width:94px; border:none; text-align:center; font-size:14px; font-weight:bold; color:#ffffff;}
#option-cancel-btn {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_black01_off.png") top left no-repeat; height:34px; width:94px; border:none; text-align:center; font-size:14px; font-weight:bold; color:#ffffff;}
#option-modify-btn:active {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_red01_on.png") top left no-repeat; height:34px; width:94px; border:none; text-align:center; font-size:14px; font-weight:bold; color:#ffffff;}
#option-cancel-btn:active {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_black01_on.png") top left no-repeat; height:34px; width:94px; border:none; text-align:center; font-size:14px; font-weight:bold; color:#ffffff;}


.cnt_minus_btn {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_minus_off.png") top left no-repeat; height:26px; width:26px; border:none; text-align:center; color:#ffffff; margin-left:3px; float:right;}
.cnt_plus_btn {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_plus_off.png") top left no-repeat; height:26px; width:26px; border:none; text-align:center; color:#ffffff; margin-left:3px;  float:right;}
.cnt_minus_btn:active {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_minus_on.png") top left no-repeat; height:26px; width:26px; border:none; text-align:center; color:#ffffff; margin-left:3px;}
.cnt_plus_btn:active {background:url("/shop/data/skin_mobileV2/default/common/img/layer/btn_plus_on.png") top left no-repeat; height:26px; width:26px; border:none; text-align:center; color:#ffffff; margin-left:3px;}

.origin-option-modify-layer-item { padding-left:14px; padding-right:18px; height:26px; padding-top:4px;}
.origin-option-modify-layer-item .title{ font-size:12px; line-height:26px; height:26px; width:38%; font-family:dotum; display:block; float:left; text-align:left;}
.origin-option-modify-layer-item .content{ font-size:12px; line-height:26px; height:26px; width:62%; font-family:dotum; display:block; float:right; text-align:right;}
.origin-option-modify-layer-item .content select{ height:26px; width:100%;}

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

section#cart { background:#FFFFFF;}
section#checkout_area { background:#FFFFFF;  padding-bottom:20px; }

</style>
<script type="text/javascript">
$(document).ready(function(){
	var
	$optionModifyLayer = $("#option-modify-layer").css({
		"top" : ($(window).scrollTop()+70)+"px"
	}).bind('delete', function(){
		$(this).fadeOut("fast");
		$("#background").fadeOut("fast");
	}).bind('create', function(){
		$(this).fadeIn("fast");
		$("#background").fadeIn("fast");
	}),
	$form = $optionModifyLayer.children("form").submit(function(){

		var form = this;

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
	}),
	$background = $("#background").click(function(){
		$optionModifyLayer.trigger('delete');
	});
	$(".order_item_list button.option-modify-btn").click(function(){
		var
		ID = $(this).attr("data-id"),
		GOODSNO = $(this).attr("data-goodsno"),
		OPTION1 = $(this).attr("data-option1"),
		OPTION2 = $(this).attr("data-option2"),
		ADD_OPTION_SNO = $(this).attr("data-addsno").split("|"),
		EA = $(this).attr("data-ea"),
		ITEM_HTML = '<div class="origin-option-modify-layer-item"><span class="title"></span><span class="content"></span></div>';
		ADD_OPTION_SNO.pop();
		$.ajax({
			"url" : "../proc/mAjaxAction.php",
			"type" : "post",
			"data" : {
				"mode" : "get_option",
				"goodsno" : GOODSNO,
				"id" : ID
			},
			"dataType" : "json",
			"success" : function(option) {
				$optionModifyLayer.css({
					"top" : ($(window).scrollTop()+70)+"px"
				});
				$form[0].idx.value = ID;
				$background.css({
					"height" : ($("#wrap").height()+"px"),
					"opacity" : "0.2",
					"display" : "block",
				});
				var $optionModifyItemList = $form.children(".option-modify-item-list").html("");

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
							$optionModifyItemList.append($optionRow1);

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
								$optionModifyItemList.append($optionRow2);

								$selectBox.children("[value="+OPTION1+"]").attr("selected", "selected").change();
								$selectBox2.children("[value="+OPTION2+"]").attr("selected", "selected");
							}
							else {
								$selectBox.children("[value="+OPTION1+"]").attr("selected", "selected");
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
							if (OPTION1 == combination.opt1 && OPTION2 == combination.opt2) $_option.attr("selected", "seleted");
							$selectBox.append($_option);
						}
						$optionContent.append($selectBox);
						$optionRow.append($optionName).append($optionContent);
						$optionModifyItemList.append($optionRow);
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
						if ($.inArray(addOption.sno, ADD_OPTION_SNO) > -1) $_option.attr("selected", "selected");
						$selectBox.append($_option);
					}
					$addContent.append($selectBox);

					$addRow.append($addName, $addContent);

					$optionModifyItemList.append($addRow);
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
						maxlength : v.opt,
						value: option.addopt_inputable[step].value
					}).css({'width':'100%'}),
					$_addInput = $(document.createElement("input")).attr({
						name: '_addopt_inputable[]',
						type: 'hidden'
					});

					if (option.addopt_inputable_req[_idx++] == true) $addInput.attr({"required" : "required", "fld_esssential" : "fld_esssential"});

					$addName.text(step);

					$addContent.append($addInput).append($_addInput);
					$addRow.append($addName, $addContent);
					$optionModifyItemList.append($addRow);
				}

				// 수량입력란 구성
				var
				$eaRow = $(ITEM_HTML),
				$eaTitle = $eaRow.find(".title"),
				$eaContent = $eaRow.find(".content"),
				$eaInput = $(document.createElement("input")).attr("type", "text").attr("size", "4").attr("name", "ea").val(EA).css({
					"text-align" : "right",
					"height" : "22px",
					"width" : "50px",
					"float" : "right"
				}).change(function(){
					orderCntCalc($(this), $(this).val(), true);
				});
				$eaTitle.text("수량");
				$eaContent.append($(document.createElement("button")).attr("type", "button").addClass("cnt_plus_btn").text(" ").click(function(){
					orderCntCalc($eaInput, 1);
					return false;
				})).append($(document.createElement("button")).attr("type", "button").addClass("cnt_minus_btn").text(" ").click(function(){
					orderCntCalc($eaInput, -1);
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

				$eaContent.append($eaInput);
				$eaRow.append($eaTitle).append($eaContent);
				$optionModifyItemList.append($eaRow);

				$('#option-modify-layer .cancel-option-modify').click(function(){
					$optionModifyLayer.trigger('delete');
					return false;
				});
				$optionModifyLayer.trigger('create');
			}
		});
	});
});

var nsGodo_CartAction = function() {

	function popup(url,w_name, w_width,w_height) {
		var x = (screen.availWidth - w_width) / 2;
		var y = (screen.availHeight - w_height) / 2;
		return window.open(url,w_name,"width="+w_width+",height="+w_height+",top="+y+",left="+x+",scrollbars=no");
	}

	return {
		cart_type : 'regular',
		data : [],
		pushdata: function(obj) {
			this.data.push(obj);
		},
		editOption : function(idx) {
			popup('../goods/popup_goods_cart_edit.php?idx='+idx+'&cart_type='+ this.cart_type,'WIN_CARTOPTION',350,500);
		},
		wishList : function() {
			if (!this.check()) {
				alert('보관할 상품을 선택해 주세요.');
				return false;
			}

			var org_action = f.action;
			var org_mode = f.mode.value;
			var f = document.frmCart;
			f.action = '../mypage/mypage_wishlist.php';
			f.mode.value = 'addItemFromCart';
			f.submit();
			// 원복
			f.action = org_action;
			f.mode.value = org_mode;
		},
		order : function() {
			var f = document.frmCart;

			var org_action = f.action;
			var org_mode = f.mode.value;
			f.action = '../ord/order.php' ;
			f.mode.value = 'setOrder';
			f.submit();
			// 원복
			f.action = org_action;
			f.mode.value = org_mode;
		},
		del : function() {

			if (!this.check()) {
				alert('삭제할 상품을 선택해 주세요.');
				return false;
			}

			var org_action = f.action;
			var org_mode = f.mode.value;
			var f = document.frmCart;
			f.action = '../goods/cart.php' ;
			f.mode.value = 'delItems';
			f.submit();
			// 원복
			f.action = org_action;
			f.mode.value = org_mode;
		},
		check : function() {

			var chks = document.getElementsByName('idxs[]');
			var cnt = 0;

			for (var i=0,m=chks.length;i<m ;i++) {
				if (chks[i].checked == true) cnt++
			}

			return cnt > 0 ? true : false;

		},
		recalc : function() {

			var chks = document.getElementsByName('idxs[]');

			var total_reserve = 0;
			var total_price = 0;

			for (var i=0,m=chks.length;i<m ;i++) {
				if (chks[i].checked == true) {
					total_price += parseInt(this.data[i].price * this.data[i].ea );
					total_reserve += parseInt(this.data[i].reserve);
				}
			}
			if (total_price ==0) {
				for(var i=0, m=this.data.length;i<m;i++) {
					total_price += parseInt(this.data[i].price * this.data[i].ea );
					total_reserve += parseInt(this.data[i].reserve);
				}
			}
			document.getElementById('el-orderitem-total-reserve').innerText = comma(total_reserve);
			document.getElementById('el-orderitem-total-price').innerText = comma(total_price);
		}
	}
}();
</script>

<section id="cart" class="content">
	<form name="frmCart" method="post">
	<input type="hidden" name="mode" value="modItem" />
	<?php echo $this->define('tpl_include_file_1',"proc/orderitem.htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>


<?php if(count($TPL_VAR["cart"]->item)){?>
	<div class="ord_area">
		<div id="all-ord-btn"><div id="all-ord-btn-effect" onclick="nsGodo_CartAction.order();return false;" >주문하기</div></div>
	</div>
<?php }?>

	</form>

</section>

<section id="option-modify-layer" style="display: none;">
	<div class="option_modify_title"><div class="title">옵션수정</div></div>
	<form method="post" action="./cart.php?mode=editOption">
		<input type="hidden" name="mode" value="editOption"/>
		<input type="hidden" name="idx" value=""/>
		<article class="option-modify-item-list"></article>
		<div class="option_modify_btn">
			<input id="option-modify-btn" class="submit-option-modify" type="submit" value="수 정"/>
			&nbsp;&nbsp;&nbsp;<input id="option-cancel-btn" class="cancel-option-modify" type="button" value="취 소"/>
		</div>
	</form>
</section>

<div id="background"></div>

<section class="content">
<?php echo $TPL_VAR["naverCheckout"]?>

</section>

<?php $this->print_("footer",$TPL_SCP,1);?>