<?php /* Template_ 2.2.7 2016/04/07 17:37:21 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/mypage/mypage.htm 000014154 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;
if (is_array($TPL_VAR["orderInfo"])) $TPL_orderInfo_1=count($TPL_VAR["orderInfo"]); else if (is_object($TPL_VAR["orderInfo"]) && in_array("Countable", class_implements($TPL_VAR["orderInfo"]))) $TPL_orderInfo_1=$TPL_VAR["orderInfo"]->count();else $TPL_orderInfo_1=0;
if (is_array($TPL_VAR["qna"])) $TPL_qna_1=count($TPL_VAR["qna"]); else if (is_object($TPL_VAR["qna"]) && in_array("Countable", class_implements($TPL_VAR["qna"]))) $TPL_qna_1=$TPL_VAR["qna"]->count();else $TPL_qna_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>



<script type='text/javascript'>
	function order_confirm(ordno)
	{
		var fm = document.frmOrderList;
		fm.mode.value = 'confirm';
		fm.ordno.value = ordno;
		fm.action = 'indb.php';
		if (confirm('주문하신 상품을 수령하셨습니까?')) fm.submit();
	}

	function popup_register( mode, sno )
	{
		if ( mode == 'del_qna' ) var win = window.open('../mypage/mypage_qna_del.php?mode=' + mode + '&sno=' + sno,'qna_register','width=400,height=200');
		else var win = window.open('../mypage/mypage_qna_register.php?mode=' + mode + '&sno=' + sno,'qna_register','width=600,height=500');
		win.focus();
	}

	var oldIdx;
	function viewContent(idx) {
		var nowIdx = idx.split('_')[1];
		var cntqna = document.getElementById('cntqna').value;
		if(oldIdx == nowIdx) {
			document.getElementById(idx).style.display='none';
			oldIdx = '';
		} else {
			for(var i=1 ; i<=cntqna ; i++) {
				if(i == nowIdx) document.getElementById(idx).style.display='table-row';
				else document.getElementById('idx_'+i).style.display='none';
			}
			oldIdx = nowIdx;
		}
	}

	/* 최근 본 상품 스크립트 */
	var latelySlide = new eSlide;
	latelySlide.id = 'scrolling_latelySlide';
	latelySlide.mode = 'left';
	latelySlide.line = 5;
	latelySlide.width = 115;
	latelySlide.height = 220;
	latelySlide.align = 'center';
	latelySlide.valign = 'top';
	latelySlide.fps = 40;
	latelySlide.repeat = true;
	latelySlide.paddingLeft = 0;
	latelySlide.direction = ('<?php echo $TPL_VAR["dpCfg"]["dOpt4"]?>' === '1') ? 1 : -1;
<?php if($TPL_loop_1){foreach($TPL_VAR["loop"] as $TPL_V1){?>
		 var tmp = "<div class='slidediv'><a href='<?php echo $TPL_V1["goods_view_url"]?>'><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["size"],addslashes('class='.$TPL_VAR["css_selector"].' rsize'))?></a></div><div class='stitle'><?php if($TPL_V1["coupon"]){?><div><span class='pink bold'><?php echo $TPL_V1["coupon"]?></span> <img src='/shop/data/skin/freemart/img/icon/good_icon_coupon.gif' align='absmiddle'></div> <?php }?><?php if($TPL_V1["brandnm"]){?><div class='stxt'><span class='bold'><?php echo $TPL_V1["brandnm"]?></span></div> <?php }?><?php if($TPL_V1["goodsnm"]){?><div class='gsnm'><a href='<?php echo $TPL_V1["goods_view_url"]?>' title='<?php echo strip_tags($TPL_V1["goodsnm"])?>'><?php echo strip_tags($TPL_V1["goodsnm"])?></a> </div><?php }?><?php if($TPL_V1["soldout_icon"]){?><div><?php if($TPL_V1["soldout_icon"]=='custom'){?><img src='../data/goods/icon/custom/soldout_icon'><?php }else{?><img src='/shop/data/skin/freemart/img/icon/good_icon_soldout.gif'><?php }?></div><?php }?><?php if($TPL_V1["icon"]){?><?php echo $TPL_V1["icon"]?> <?php }?></div>\
			<div class='sprice'><?php if(!$TPL_V1["strprice"]){?><?php if($TPL_V1["goodsDiscountPrice"]){?><?php if($TPL_V1["oriPrice"]){?><div class='gray'><strike><?php echo number_format($TPL_V1["oriPrice"])?></strike>↓</div><?php }?><span><?php echo number_format($TPL_V1["goodsDiscountPrice"])?>원</span><?php }elseif($TPL_V1["price"]){?><?php if($TPL_V1["consumer"]){?><div class='gray'><strike><?php echo number_format($TPL_V1["consumer"])?></strike>↓</div><?php }?><span><?php echo number_format($TPL_V1["price"])?>원</span> <?php if($TPL_V1["special_discount_amount"]){?><img src='/shop/data/skin/freemart/img/icon/goods_special_discount.gif'><?php }?><?php }?><?php if($TPL_V1["soldout_price_string"]){?><?php echo $TPL_V1["soldout_price_string"]?><?php }?><?php if($TPL_V1["soldout_price_image"]){?><?php echo $TPL_V1["soldout_price_image"]?><?php }?><?php }else{?><?php echo $TPL_V1["strprice"]?><?php }?></div>\
			<div class='sdefault'>적립금:<?php echo number_format($TPL_V1["reserve"])?>원</div>";
		latelySlide.add(tmp);

<?php }}?>
	/* 최근 본 상품 스크립트 */
</script>


<div class='mypage-wrapper'>
	<!-- 상단이미지 || 현재위치 -->
	<div class="page_title_div">
		<div class="page_title">MY DASHBOARD</div>
		<div class="page_path"><a href="/shop/">HOME</a> &gt; <span class='bold'>마이페이지</span></div>
	</div>
	<div class="page_title_line"></div>
	<!-- 상단이미지 || 현재위치 -->

	<!-- 진행 중인 주문 -->
	<div class='mplist'>
		<div class='ordtitle'>
			<span class='ordment'><span class='b_cate'>진행 중인 주문</span><span class='mpsubtit'> | 최근 <span class='pink'>30</span>일 내 주문 내역</span></span>
			<span class='ordlink'><button type='button' onclick='location.href="<?php echo url("mypage/mypage_orderlist.php")?>&"' class='w93' style="cursor:pointer;">전체 주문 보기</button></span>
		</div>
		<div class='ordlistdiv'>
			<table class='ordlisttbl' cellpadding='0' cellspacing='0' summary='최근 30일 내 주문 내역'>
				<caption>최근 30일 내 주문 내역</caption>
				<colgroup>
					<col width='16%'>
					<col width='16%'>
					<col width='16%'>
					<col width='16%'>
					<col width='16%'>
					<col width='*'>
				</colgroup>
				<tr>
					<th scope='col'>입금대기중</th>
					<th scope='col'>결제완료</th>
					<th scope='col'>배송준비중</th>
					<th scope='col'>배송중</th>
					<th scope='col'>배송완료</th>
					<th scope='col'>취소/교환/반품</th>
				</tr>
				<tr>
					<td scope='row'><span class='pink'><?php echo $TPL_VAR["ordering"]["pendingPayment"]?></span> 건</td>
					<td><span class='pink'><?php echo $TPL_VAR["ordering"]["confirmPayment"]?></span> 건</td>
					<td><span class='pink'><?php echo $TPL_VAR["ordering"]["deliveryPrepare"]?></span> 건</td>
					<td><span class='pink'><?php echo $TPL_VAR["ordering"]["delivering"]?></span> 건</td>
					<td><span class='pink'><?php echo $TPL_VAR["ordering"]["deliveryComplete"]?></span> 건</td>
					<td><span class='pink'><?php echo $TPL_VAR["ordering"]["cancel"]?></span> 건</td>
				</tr>
			</table>
		</div>
	</div>
	<!-- 진행 중인 주문 -->

	<!-- 최근 주문 정보 -->
	<div class='mplist'>
		<div class='ordtitle'>
			<span class='ordment'><span class='b_cate'>최근 주문 정보</span><span class='mpsubtit'> | 최근 <span class='pink'>3</span>건의 주문 정보</span></span>
			<span class='ordlink'><button type='button' onclick='location.href="<?php echo url("mypage/mypage_orderlist.php")?>&"' class='w93' style="cursor:pointer;">전체 주문 보기</button></span>
		</div>
		<div class='ordlistdiv'>
			<form name='frmOrderList' method='post'>
				<input type='hidden' name='mode'>
				<input type='hidden' name='ordno'>
				<table class='ordlatelytbl' cellpadding='0' cellspacing='0'  summary='최근 3건의 주문 정보'>
					<caption>최근 3건의 주문 정보</caption>
					<colgroup>
						<col width='18%'>
						<col width='18%'>
						<col width='10%'>
						<col width='10%'>
						<col width='10%'>
						<col width='10%'>
						<col width='10%'>
						<col width='*'>
					</colgroup>
					<tr>
						<th scope='col'>주문일시</th>
						<th scope='col'>주문번호</th>
						<th scope='col'>결제방법</th>
						<th scope='col'>주문금액</th>
						<th scope='col'>취소금액</th>
						<th scope='col'>주문상태</th>
						<th scope='col'>수령확인</th>
						<th scope='col'>상세보기</th>
					</tr>
<?php if($TPL_VAR["orderInfo"]){?>
<?php if($TPL_orderInfo_1){foreach($TPL_VAR["orderInfo"] as $TPL_V1){?>
							<tr onmouseover='this.style.background="#F7F7F7"' onmouseout='this.style.background="#fff"'>
								<td scope='row'><?php echo $TPL_V1["orddt"]?></td>
								<td><a href='<?php echo url("mypage/mypage_orderview.php?")?>&ordno=<?php echo $TPL_V1["ordno"]?>'><?php echo $TPL_V1["ordno"]?></a></td>
								<td><?php echo $TPL_V1["str_settlekind"]?></td>
								<td class='right'><?php echo number_format($TPL_V1["settleprice"])?></td>
								<td class='right'><?php echo number_format($TPL_V1["canceled_price"])?></td>
								<td class='stxt'><span class='blue'><?php echo $TPL_V1["str_step"]?></span></td>
								<td>&nbsp;
<?php if($TPL_V1["step"]== 3&&!$TPL_V1["step2"]){?>
										<a href='javascript:order_confirm(<?php echo $TPL_V1["ordno"]?>)'><img src='/shop/data/skin/freemart/img/common/btn_receive.gif'></a>
<?php }elseif($TPL_V1["escrowconfirm"]== 2){?>
										수령
<?php }?>
								</td>
								<td><a href='<?php echo url("mypage/mypage_orderview.php?")?>&ordno=<?php echo $TPL_V1["ordno"]?>'><img src='/shop/data/skin/freemart/img/common/btn_detailview.gif'></a></td>
							</tr>
<?php }}?>
<?php }else{?>
						<tr>
							<td colspan='8' class='nodata'>최근 주문 정보가 없습니다.</td>
						</tr>
<?php }?>
				</table>
			</form>
		</div>
	</div>
	<!-- 최근 주문 정보 -->

	<!-- 1:1 문의내역 -->
	<div class='mplist'>
		<div class='ordtitle'>
			<span class='ordment'><span class='b_cate'>1:1 문의내역</span><span class='mpsubtit'> | 최근 <span class='pink'>3</span>건의 1:1문의</span></span>
			<span class='ordlink'><button type='button' onclick='location.href="<?php echo url("mypage/mypage_qna.php")?>&"' class='w93' style="cursor:pointer;">1:1 문의게시판</button></span>
		</div>
		<div class='ordlistdiv'>
			<input type='hidden' name='cntqna' id='cntqna' value='<?php echo count($TPL_VAR["qna"])?>' />
			<table class='ordlatelytbl' cellpadding='0' cellspacing='0' summary='최근 3건의 1:1문의'>
				<caption>최근 3건의 1:1문의</caption>
				<colgroup>
					<col width='10%'>
					<col width='15%'>
					<col width='*'>
					<col width='12%'>
					<col width='12%'>
				</colgroup>
				<tr>
					<th>번호</th>
					<th>질문유형</th>
					<th>제목</th>
					<th>작성자</th>
					<th>작성일</th>
				</tr>
<?php if($TPL_VAR["qna"]){?>
<?php if($TPL_qna_1){foreach($TPL_VAR["qna"] as $TPL_V1){?>
						<tr class='ordhover' onmouseover='this.style.background="#F7F7F7"' onmouseout='this.style.background="#fff"'>
<?php if($TPL_V1["sno"]==$TPL_V1["parent"]){?>
							<td><?php echo $TPL_V1["idx"]?></td>
							<td class='stxt' style='text-align:left'>[<?php echo $TPL_V1["itemcd"]?>]</td>
							<td style='text-align:left'><a href='javascript:viewContent("idx_<?php echo $TPL_V1["idx"]?>")'><?php echo $TPL_V1["subject"]?></a> <span class='stxt blue'>[<?php echo $TPL_V1["repleCnt"]?>]</span></td>
							<td><?php echo $TPL_V1["m_id"]?></td>
							<td><?php echo substr($TPL_V1["regdt"], 0, 10)?></td>
<?php }elseif($TPL_V1["sno"]!=$TPL_V1["parent"]){?>
							<td><?php echo $TPL_V1["idx"]?></td>
							<td class='stxt' style='text-align:left'><span class='blue'>ㄴ답변 : </span></td>
							<td style='text-align:left'><a href='javascript:viewContent("idx_<?php echo $TPL_V1["idx"]?>")'><?php echo $TPL_V1["subject"]?></a></td>
							<td><?php echo $TPL_V1["m_id"]?></td>
							<td><?php echo substr($TPL_V1["regdt"], 0, 10)?></td>
<?php }?>
						</tr>
						<tr style='display:none;' id='idx_<?php echo $TPL_V1["idx"]?>'>
							<td colspan='5' style='text-align:left;padding:10px;'>
<?php if($TPL_V1["ordno"]!='0'){?>
								<div style='padding-left:55px;'>[ 주문번호 <?php echo $TPL_V1["ordno"]?> 문의 ]</div>
<?php }?>
								<div style='padding-left:55px;'><?php echo $TPL_V1["contents"]?></div>
								<div style='text-align:right;'>
<?php if($TPL_V1["m_no"]==$GLOBALS["sess"]["m_no"]){?>
								<a href='javascript:popup_register( "reply_qna", "<?php echo $TPL_V1["sno"]?>" );'><img src='/shop/data/skin/freemart/img/common/btn_reply.gif' border='0' align='absmiddle'></a>
								<a href='javascript:popup_register( "mod_qna", "<?php echo $TPL_V1["sno"]?>" );'><img src='/shop/data/skin/freemart/img/common/btn_modify2.gif' border='0' align='absmiddle'></a>
								<a href='javascript:popup_register( "del_qna", "<?php echo $TPL_V1["sno"]?>" );'><img src='/shop/data/skin/freemart/img/common/btn_delete.gif' border='0' align='absmiddle'></a>
<?php }?>
							</td>
						</tr>
<?php }}?>
<?php }else{?>
					<tr>
						<td colspan='5' class='nodata'>1:1 문의 내역이 없습니다.</td>
					</tr>
<?php }?>
			</table>
		</div>
	</div>
	<!-- 1:1 문의내역 -->

	<!-- 최근 본 상품 목록 -->
	<div class='mplist'>
		<div class='ordtitle'>
			<span class='ordment'><span class='b_cate ordsubtitle'>최근 본 상품</span></span>
			<span class='ordlink'><button type='button' onclick='location.href="<?php echo url("mypage/mypage_today.php")?>&"' class='w108' style="cursor:pointer;">최근 본 상품 목록</button></span>
		</div>
		<div id='latelyList' class='ordlistdiv'>
			<table cellpadding='0' cellspacing='0' border='0' class='slidetbl'>
				<tr align='center'>
					<td class='prev'>
						<div class='slidebtn'><a href='javascript:latelySlide.go()'><img src='/shop/data/skin/freemart/img/common/btn_common_prev.gif' onmouseover='latelySlide.direct(-1)'></a></div>
					</td>
					<td valign='top'>
						<script>latelySlide.exec();</script>
					</td>
					<td class='next'>
						<div class='slidebtn'><a href='javascript:latelySlide.go()'><img src='/shop/data/skin/freemart/img/common/btn_common_next.gif' onmouseover='latelySlide.direct(1)'></a></div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<!-- 최근 본 상품 목록 -->
</div>
<p></p>
<script type='text/javascript'>
<?php if(!count($TPL_VAR["loop"])){?>
	document.getElementById('latelyList').innerHTML='최근 본 상품이 없습니다.';
	document.getElementById('latelyList').setAttribute("class", 'nodata_div');
<?php }?>
</script>
<?php $this->print_("footer",$TPL_SCP,1);?>