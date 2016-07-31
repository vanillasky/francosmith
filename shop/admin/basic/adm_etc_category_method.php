<?
$location = "기타관리 > 상품분류 연결방식 전환 ";
include "../_header.php";

// 상품분류 연결방식 전환 대상 상품 총 수
list ($totalCount)		= $db->fetch("SELECT COUNT(0) FROM ".GD_GOODS);

// 임시 - 카테고리 접근/구매권한을 수정을 한경우 경고창을 띄우기
list ($cateLevelCheck)	= $db->fetch("SELECT COUNT(0) FROM ".GD_CATEGORY." WHERE level > 0");
?>
<script type="text/javascript" src="../godo.loading.indicator.js"></script>
<script type="text/javascript">
function changeCategoryMethod()
{
	var msg = "새로운 상품분류 연결방식으로 전환을 진행합니다.\n\n상품수가 많은 경우 전환시간이 다소 길어질 수 있으니\n\n가급적 주문량이 적은 시간을 이용하여 주시기 바랍니다.\n\n계속 진행 하시겠습니까?\n\n※ 전환 후에는 변경전 상태로 되돌릴 수 없습니다.";
	if(confirm(msg)){
		popupLayerNotice('상품분류 연결방식 전환','./adm_etc_category_method_popup.php?mode=categoryLink&totalCount=<?php echo $totalCount;?>',505,140);
		return true;
	}
	else {
		return false;
	}
}
</script>
<div class="title title_top">상품분류 연결방식 전환 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=45')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<div style="border:3px solid #000; padding:5px 5px 5px 15px; margin-bottom:10px;">
	<p style="color:#0099ff; font-weight:bold;">※ [상품분류(카테고리) 연결방식 변경 안내]</p>
	<p style="color:#000000; font-weight:bold;">■ 변경전 : 상위분류(카테고리) 분석 조회 방식</p>
	<p style="color:#999999;">
		예) 1차 분류 > 2차 분류 > 3차 분류<br>
	</p>
	<p style="color:#000000; font-weight:bold; padding-top:10px">■ 변경후 : 상위분류(카테고리) 개별 등록 조회 방식</p>
	<p style="color:#999999;">
		예)&nbsp;&nbsp;1차 분류<br>
		&nbsp; &nbsp; &nbsp; &nbsp;1차 분류 > 2차 분류<br>
		&nbsp; &nbsp; &nbsp; &nbsp;1차 분류 > 2차 분류 > 3차 분류<br>
	</p>
	<p style="color:#000000; font-weight:bold; padding-top:10px">★ 이런점이 좋아집니다!</p>
	<p style="color:#999999;">
		 - 조회 분류 편집 기능 : 상위분류 개별 편집(삭제)이 가능하므로 상품을 표시하고자 하는 분류에만 보여지도록 할 수 있습니다!<br>
		 - 상품 조회 속도 향상 : 상품분류 분석 단계가 없으므로 상품 조회 시 로딩 속도가 변경전보다 빨라집니다!<br>
		 - <span class="extext">Tip. <a href="./adm_etc_cache_db.php" class="extext_l">[웹사이트 속도 향상]</a> 기능과 함께 사용 시 대량으로 상품을 등록하여도 문제없이 빠르게 이용하실 수 있습니다.</span>
	</p>
</div>

<div class="admin-form" style="height:300px; margin:0 auto;">

	<h2 class="title ">상품분류 연결방식 전환 설정<span>상품분류 연결방식이 변경이 됩니다.</span></h2>

	<table class="admin-form-table">
	<tr>
		<th>전환여부</th>
		<td>
			<?php if (_CATEGORY_NEW_METHOD_ === true) { ?>
			<span style="font-weight:bold;color:#0080FF">
				<?php if ($godo['version'][0] >= '2.00.10.1120') { ?>
				이미 새로운 상품분류 연결방식 전환이 완료된 상태입니다.
				<?php } else { ?>
				새로운 상품분류 연결방식으로 전환하여 사용 중 입니다.
				<?php } ?>
			</span>
			<?php } else { ?>
				<span style="font-weight:bold;color:#FF0000">상품분류 연결방식을 전환해 주세요!</span>
				<?php if ($cateLevelCheck > 0) { ?>
					<span style="font-weight:normal;letter-spacing:-1px;font-size:11px;"><br>
						<span style="color:#FF0000;">
						※ 상품이 등록된 분류(카테고리)의 상위/하위분류의 구매권한이 다르게 설정되어 있는 경우, 상품의 접근/구매권한이 기존과 달라질 수 있습니다.<br>
						</span>
						&nbsp; &nbsp; &nbsp;상품분류와 상품의 권한정책 설정은 차후 별도 배포할 예정이오니 이점 유의하여 주시기 바랍니다.<br>
						&nbsp; &nbsp; &nbsp;예) A상품의 분류권한이 1차분류(제한없음)와 2차분류(일반회원-상세페이지)로 다르게 설정되어 있는 경우 비회원이 1차분류에서 상품구매가 가능
					</span>
				<?php } ?>
			<?php } ?>
		</td>
	</tr>
	<?php if (_CATEGORY_NEW_METHOD_ === false) { ?>
	<tr>
		<th>대상상품수</th>
		<td>
			<b><?php echo number_format($totalCount);?></b> 개 상품
		</td>
	</tr>
	<?php } ?>
	</table>

	<?php if (_CATEGORY_NEW_METHOD_ === false || $_GET['mode']) { ?>
	<div id="processBtn" style="margin:10px auto 0px auto; text-align:center;">
		<img src="../img/btn_category_method_change.gif" onclick="changeCategoryMethod();" class="hand" />
	</div>
	<?php } ?>
</div>

<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
		<tr>
			<td>
				<div><img src="../img/icon_list.gif" align="absmiddle"> 변경된 상품분류 연결방식은 ‘상품등록(수정)’메뉴의 ‘상품분류정보’를 통해 확인하실 수 있습니다.<br><br></div>
				<div><img src="../img/icon_list.gif" align="absmiddle"> [주의사항]<br>
					&nbsp; &nbsp; &nbsp; &nbsp; - 전환 후에는 변경전 상태로 되돌릴 수 없습니다.<br>
					&nbsp; &nbsp; &nbsp; &nbsp; - 전환 대상 상품수가 많은 경우 전환시간이 오래 걸릴 수 있으니 가급적 주문량이 적은 시간에 진행하여 주시기 바랍니다<br>
					&nbsp; &nbsp; &nbsp; &nbsp; - 전환 진행 중 취소하거나 브라우저, 윈도우, 시스템 문제로 중단된 경우 다시 진행하여 주시기 바랍니다.<br>
					&nbsp; &nbsp; &nbsp; &nbsp; - <span class="small_ex_point">‘다중분류’ 상품의 경우 새로운 상품분류 연결방식으로 전환 후 네이버 지식쇼핑, 다음 쇼핑하우, 다나와, 에누리 등 상품정보를 외부 사이트에 자동 전송 시,</span><br>
					&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span class="small_ex_point">이전에 전송했던 분류와 다른 분류로 전송될 수 있습니다.</span><br>
					&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span class="small_ex_point">하나의 상품에 여러 개의 분류가  등록된 경우 전송해야 할 분류기준을 알 수 없기 때문에 발생할 수 있는 부분이며,</span><br>
					&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span class="small_ex_point">차후 이러한 부분을 개선하고자 분류기준을 지정할 수 있는 기능을 개발하여 적용할 예정이오니 참고해 주시기 바랍니다.</span><br>
				</div>
			</td>
		</tr>
	</table>
</div>
<script>cssRound('MSG01')</script>
<script language="JavaScript" type="text/JavaScript">window.onload = function(){ (typeof(UNM) != "undefined" ? UNM.inner() : ''); };</script>

<?php include "../_footer.php"; ?>