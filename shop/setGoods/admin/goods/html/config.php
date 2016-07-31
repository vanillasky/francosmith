<?
//초기값 세팅
if(!$setGoodsConfig[state]) $setGoodsConfig[state] = 'N';
if(!$setGoodsConfig[setGoodsBanner]) $setGoodsConfig[setGoodsBanner] = "sky_ban_codi.gif";
?>
<html>
<head>
	<title>'Godo Shoppingmall e나무 Season4 관리자모드'</title>
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
	<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="../../js/default.js"></script>	
	<link rel="styleSheet" href="../../../admin/style.css">
	<link rel="styleSheet" href="../../../admin/_contextmenu/contextmenu.css?<?=time()?>">
	<script src="../../../admin/common.js"></script>
	<style>
		/*** 어드민 레이아웃 설정 ***/
		body {margin:0 0 0 0px}
	</style>
	<script>
		function chkForm2(fm)
		{
			/*
			if (fm.sessTime.value && fm.sessTime.value<20){
				alert("회원인증 유지시간 제한시 20분 이상만 가능합니다");
				fm.sessTime.value = 20;
				fm.sessTime.focus();
				return false;
			}
			*/
		}
		function copy_txt(val){
			window.clipboardData.setData('Text', val);
			alert("클립보드에 복사되었습니다.");
		}

		function cl_use() {
			jQuery('#tb0').attr('disabled',false);
			jQuery('#tb1').attr('disabled',false);
			jQuery('#tb2').attr('disabled',false);
			jQuery('#tb3').attr('disabled',false);
		}

		function cl_none() {
			jQuery('#tb0').attr('disabled',true);
			jQuery('#tb1').attr('disabled',true);
			jQuery('#tb2').attr('disabled',true);
			jQuery('#tb3').attr('disabled',true);			
		}

	</script>
</head>
<body>

<form name=form method=post action="./indb.php" enctype="multipart/form-data" onsubmit="return chkForm2(this)">
	<input type="hidden" name="fn" value="F">
	<input type="hidden" name="setGoodsBanner_old" value="<?=$setGoodsConfig[setGoodsBanner]?>">
	<div class="title title_top">사용설정<span><a href="javascript:manual('http://guide.godo.co.kr/season4/board/view.php?id=product&no=41')"><img src="../../../admin/img/btn_q.gif" border=0 align=absmiddle></a></span></div>
	<table class=tb>
		<col class=cellC>
		<col class=cellL>
		<tr>
			<td>사용여부 설정</td>
			<td>
				 <span style="width:80px;" class="noline"><input type="radio" name="state" value="Y" <?if($setGoodsConfig[state]=='Y'){?>checked<?}?> onclick="cl_use()"/>&nbsp;사용</span> 
				 <span  class="noline"><input type="radio" name="state" value="N" <?if($setGoodsConfig[state]=='N'){?>checked<?}?>  onclick="cl_none()"/>&nbsp;사용안함</span>
				 <div class="extext" style="margin:3px 0 0 3px;line-height:150%">
					<div style="padding:5px,0;"><b>* [사용] 으로 설정 시</b><br>
					- 코디 진열페이지와 코디 상세페이지가 활성화 되며, 시즌4 메인 스킨에 코디 진열페이지가 링크된 배너가 출력됩니다.
					</div>

					<div style="padding:5px,0;">
					<b>* [사용안함] 으로 설정 시</b><br>
					- 코디 진열페이지와 코디 상세페이지가 비활성화 되며, 페이지 접근 시 연결 되지 않습니다. <br>
					- 시즌4 메인스킨에 코디 진열페이지가 링크된 배너가 출력되지 않습니다.<br>
					</div>
				</div>
			
			</td>		
		</tr>
	</table>

	<div>
		<div class="title title_top">배너등록 설정<span><a href="javascript:manual('http://guide.godo.co.kr/season4/board/view.php?id=product&no=41')"><img src="../../../admin/img/btn_q.gif" border=0 align=absmiddle></a></span></div>
		<table class=tb id="tb0">
			<col class=cellC>
			<col class=cellL>
			<tr>
				<td>코디 페이지 연결<br>배너 설정</td>
				<td>
					<div style="margin:0 0 20px; 0">
						<span style="color:green;padding:0,5px,0,0;">[코디 진열페이지 URL]</span> http://<?=$_SERVER['HTTP_HOST']?>/shop/setGoods/
						<span><a href="http://<?=$_SERVER['HTTP_HOST']?>/shop/setGoods/" target="_blank"><img src="../../../admin/img/btn_go.gif" alt="바로가기" /></a></span>
					</div>
					
					<div>
						배너 치환코드 :  {Banner} <span style="padding:0,5px;vertical-align:bottom;cursor:pointer;"><img src="../../../admin/img/btn_cate_copy.gif" onclick="copy_txt('{Banner}')" /></span>
					</div>

					<div class="extext" style="margin:5px 0 0 3px;line-height:150%">
						1. 치환코드를 복사하여 ‘디자인관리>페이지별 디자인’ 원하는 위치에 '붙여넣기(Ctrl+V)'하여 삽입해 주세요. <br>
						&nbsp;&nbsp;&nbsp;※ 시즌4 기본스킨(apple_tree)은 배너 치완코드가 기본으로 삽입/적용되어 있습니다.<br>
						2. 아래에 적용된 배너 이미지가 해당 위치에 출력되며, 배너 클릭시 코디 진열페이지URL로 연결됩니다.		
					</div>

					<div style="margin:10px 0 10px; 0 text-align:top;">
						배너 이미지 : <img src="../../data/banner/<?=$setGoodsConfig[setGoodsBanner]?>" align="middle"><br>
					</div>

					<div style="margin:10px 0 10px; 0">
						이미지 Upload : <input type="file" id="setGoodsBanner" name="setGoodsBanner" style="width:300px;">

					</div>

				</td>		
			</tr>
		</table>
	</div>
	<div>
		<div class="title title_top">진열페이지 설정<span><a href="javascript:manual('http://guide.godo.co.kr/season4/board/view.php?id=product&no=41')"><img src="../../../admin/img/btn_q.gif" border=0 align=absmiddle></a></span></div>
		<table class=tb id="tb1">
			<col class=cellC>
			<col class=cellL>
			<tr>
				<td>코디 진열 타입</td>
				<td>				
					<div class="display-type-wrap">
					<img src="../../images/img_codytype_tile.gif" />
					<div class="noline" style="padding:0,35px;">
					<input type="radio" name="display_type" value="" checked>
					</div>
					<!-- 추후에 갤러리형 추가할 예정-->
				</td>		
			</tr>
			<tr>
				<td>진열순서</td>
				<td>
					<?	
						if($setGoodsConfig[listing] == 'D') $listingD = "checked";
						else if($setGoodsConfig[listing] == 'L') $listingL = "checked";
						else $listingR = "checked";
					?>
					<span style="width:200px;float:left;" class="noline"><input type="radio" name="listing" value="R" <?=$listingR?>>랜덤 (새로 접근시 재 배치)</span>
					<span style="width:80px;float:left;" class="noline"><input type="radio" name="listing" value="D" <?=$listingD?>>등록 순 </span>
					<span style="width:150px;" class="noline"><input type="radio" name="listing" value="L" <?=$listingL?>>인기순 (좋아요 개수 순)</span>
				</td>		
			</tr>
			<tr>
				<td>코디상품 진열 설정</td>
				<td>
					<?	
						if($setGoodsConfig[goods_display] == 'N') $goods_displayN = "checked";
						else $goods_displayY = "checked";
					?>
					<span style="width:230px;" class="noline"><input type="radio" name="goods_display" value="Y" <?=$goods_displayY?>>품절상품이 포함된 코디상품 진열함 </span> 
					<span style="width:230px;" class="noline"><input type="radio" name="goods_display" value="N" <?=$goods_displayN?>>품절상품이 포함된 코디상품 진열안함</span>
					<div class="extext" style="margin:10px 0 0 3px;line-height:150%">
						코디내에 품절상품이 포함되어 있을 경우의 진열여부를 설정합니다.<br>
						고객이 품절상품이 포함된 코디상품을 주문시 품절상품은 제외되어 주문이 진행됩니다.		
					</div>
				</td>		
			</tr>
		</table>
	</div>
	
	<div>
		<div class="title title_top">상세페이지 설정<span><a href="javascript:manual('http://guide.godo.co.kr/season4/board/view.php?id=product&no=41')"><img src="../../../admin/img/btn_q.gif" border=0 align=absmiddle></a></span></div>
		<table class='tb' id="tb2">
			<col class=cellC>
			<col class=cellL>
			<tr>
				<td>다른 코디 보기 출력</td>
				<td>
					<?	
						if($setGoodsConfig[means] == '2') $means2 = "checked";
						else if($setGoodsConfig[means] == '3') $means3 = "checked";
						else if($setGoodsConfig[means] == '4') $means4 = "checked";
						else $means1 = "checked";
					?>
					<span style="width:150px;float:left;" class="noline"><input type="radio" name="means" value="1" <?=$means1?>>모든 진열코디</span>
					<span style="width:200px;float:left;" class="noline"><input type="radio" name="means" value="2" <?=$means2?>>등록순(최근 코디) 10개중</span>
					<span style="width:200px;float:left;" class="noline"><input type="radio" name="means" value="3" <?=$means3?>>인기순(좋아요 개수 순) 10개중</span>
					<span style="width:200px;clear:both;" class="noline"><input type="radio" name="means" value="4" <?=$means4?>>출력안함</span>
			
						<div class="extext" style="margin:10px 0 0 3px;line-height:150%">
						 코디 상세페이지의 [다른코디보기] 영역에 선택 조건에 따라 6개 코디상품이 랜덤으로 표시됩니다.
					</div>
				</td>		
			</tr>		
			<tr>
				<td>댓글 사용</td>
				<td>
					<?	
						if($setGoodsConfig[memo] == 'N') $memoN = "checked";
						else $memoY = "checked";

						if($setGoodsConfig[memo_permission] == 'all') $all = "selected";
						else $user = "selected";				
					?>
					<span style="width:150px;float:left;" class="noline"><input type="radio" name="memo" value="Y" <?=$memoY?>>사용: 댓글쓰기 권한 </span>
					<span style="width:150px;float:left;" class="noline">	<select name="memo_permission">
							<option value="all" <?=$all?>>기본:전체허용
							<option value="user" <?=$user?>>회원전용
						</select>

					</span>
					<span style="width:150px;clear:both;" class="noline"><input type="radio" name="memo" value="N" <?=$memoN?>>사용안함</span>
					<div class="extext" style="margin:10px 0 0 3px;line-height:150%">
						상세페이지 하단영역에 댓글(후기)달기 기능이 활성화 되어 출력됩니다.<br>
						등록된 댓글은 진열페이지에도 최근 등록순으로 7개까지 출력됩니다.
					</div>
				</td>		
			</tr>
			<!--tr>
				<td>SNS 공유하기</td>
				<td>
					<?	
						if($setGoodsConfig[SNS] == 'N') $SNSN = "checked";
						else $SNSY = "checked";
					?>
					<input type="radio" name="SNS" value="Y" <?=$SNSY?>>사용					
					<input type="radio" name="SNS" value="N" <?=$SNSN?>>사용안함  [전송내용 설정하기]
					<div class="extext" style="margin:3px 0 0 3px;line-height:150%">
						코디상세 페이지에 SNS아이콘이 노출됩니다.
					</div>
				</td>		
			</tr-->		
		</table>
	</div>

	<div>
		<div class="title title_top">부가기능 설정<span><a href="javascript:manual('http://guide.godo.co.kr/season4/board/view.php?id=product&no=41')"><img src="../../../admin/img/btn_q.gif" border=0 align=absmiddle></a></span></div>
		<table class=tb id="tb3">
			<col class=cellC>
			<col class=cellL>
			<tr>
				<td>상품별 관련코디 연결</td>
				<td>
					<?	
						if($setGoodsConfig[setconnection] == 'Y') $setconnectionY = "checked";
						else $setconnectionN = "checked";
					?>
					<span style="width:80px;float:left;" class="noline"><input type="radio" name="setconnection" value="Y" <?=$setconnectionY?>>사용 </span>
					<span style="width:80px;float:left;" class="noline"><input type="radio" name="setconnection" value="N" <?=$setconnectionN?>>사용안함</span>

					<div class="extext" style="clear:both;margin:3px 0 0 3px;line-height:150%">
						개별상품 상세페이지에 해당 상품이 포함된 코디가 진열된 페이지를 연결해 주는 기능입니다.<br>
						사용으로 설정시, 개별상품 상세페이지에 [해당상품 관련코디 보기] 버튼이 생성됩니다. <br>
						버튼 클릭시 해당 상품이 포함된 코디상품 리스트 정보가 제공됩니다.<br>
					 </div>

				</td>		
			</tr>
		</table>
	</div>
	<div class="button">
		<input type=image src="../../../admin/img/btn_register.gif">		
	</div>
</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">* 코디상품 진열 정책 "중요!" </td></tr>
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">- 코디상품 내에 일부 상품이 미진열 및 삭제 처리가 된 경우, 해당 코디상품의 진열상태가 <font style="color:#37a3ee;font-weight:bold;">YES</font>-><font style="color:#ef2869;font-weight:bold;">NO</font>로 변경되며, 진열페이지에서 보여지지 않으며 상세페이지 접근되지 않습니다.</td></tr>
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">&nbsp;&nbsp;&nbsp;(진열페이지에서 제외되는데 다소 시간이 걸릴 수 있으니 이점 유의해 주세요.)</td></tr>
</table>
<br/>
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">* 코디관련 페이지 디자인설정</td></tr>
<tr><td style="padding-left:10">- 코디 상품의 진열 및 상세, 관련코디 페이지는 측면 메뉴영역 이 없는(측면감춤) 형태의 스타일로 제공됩니다. </td></tr>
<tr><td style="padding-left:10">- 디자인관리 좌측 트리 항목의 코디(Set) 페이지 <a href="../../../admin/design/codi.php?design_file=setGoods/index.htm" target="_blank" 
style="color:#ffffff;font-weight:bold">[ 코디진열페이지 ]</a> <a href="../../../admin/design/codi.php?design_file=setGoods/content.htm" target="_blank" style="color:#ffffff;font-weight:bold">[ 코디상세페이지 ]</a> 에서 해당 페이지별 상단, 측면, 하단의 레이아웃 디자인 수정이 가능합니다. </td></tr>
</table>
<br/>
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">* SNS아이콘 출력</td></tr>
<tr><td style="padding-left:10">- <a href="/shop/admin/sns/sns.config.php" target="_blank" style="color:#ffffff;font-weight:bold">[ 프로모션 > SNS서비스 > SNS공유하기설정관리 ]</a>에서 사용설정을 사용함으로 설정시, 코디 상세페이지에도 SNS아이콘이 출력됩니다. </td></tr>
<tr><td style="padding-left:10">- 코디 상세페이지에 SNS아이콘 출력을 원하지 않으실 경우, 디자인관리 좌측 트리 항목에서 </td></tr>
<tr><td style="padding-left:10">  &nbsp;&nbsp;<a href="../../../admin/design/codi.php?design_file=setGoods/content.htm" target="_blank" style="color:#ffffff;font-weight:bold">[ 코디(Set) > 코디상세페이지 ]</a> 를 열어 치환코드 {snsBtn} 를 삭제해 주세요.</td></tr>
<tr><td style="padding-left:10">- 상세페이지 스킨 내용수정 및 새 스킨 추가시 SNS공유하기를 사용으로 설정하여도 SNS버튼이 출력되지 
   않을 수 있습니다. </td></tr>
<tr><td style="padding-left:10">- 출력복원 방법 : SNS공유하기 치환코드 <b>{snsBtn}</b> 를 디자인관리 <a href="../../../admin/design/codi.php?design_file=setGoods/content.htm" target="_blank" style="color:#ffffff;font-weight:bold">[ 코디(Set) > 코디상세페이지 ]</a> 를 열어 
   원하는 위치에 코드를 삽입해 주세요.
</td></tr>
</table>
</div>
<script>cssRound('MSG01',null,null,'../../../admin/')</script>
<script>
table_design_load();

<?if($setGoodsConfig[state] == 'N'){?>cl_none();<?}?>
<?if($setGoodsConfig[state] == 'Y'){?>cl_use();<?}?>
</script>


</body>
</html>
