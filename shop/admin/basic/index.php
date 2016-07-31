<?
header("location:./adm_basic_index.php");
exit;
$mainpage = 1;

# div 위치이동 2007-02-21 kwons
$scriptLoad='<script language="javascript" src="./divmove_table.js"></script>';
# 다이어리 호출
$scriptLoad.='<script language="javascript" src="./malldiary.js"></script>';
# 현재 위치 표시
$location = "관리자메인";

include "../_header.php";
@include "../goods/stockalarm.php";

# 계정용량체크
if (function_exists('disk')){
	list( $disk_errno, $disk_msg ) = disk();
	if ( !empty( $disk_errno ) ){
		echo "
		<script language='javascript'>
		if ( !getCookie( 'blnCookie_disk' ) ) {
			var win=popup_return( '../proc/warning_disk_pop.php', 'disk_err', 320, 260, 100, 100, 'no' );
			win.focus();
		}
		</script>
		";
	}
}

### 회원 생일자 SMS
include "./birth_sms.php";

### 회원 등급 조정
$member_grp = Core::loader('member_grp');
$member_grp->execUpdate();
?>
<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td valign="top" style="background:url('../img/left_bg.gif') repeat-y top left;">

	<!------------  측면메뉴 시작 -------------------------------------->

	<? include "./main.left_info.php";	# 메인 좌측 정보 ?>

	<? include "./main.left_service.php";	# 메인 좌측 서비스 사용현황 ?>

	<? include "./main.left_marketing.php";	# 메인 좌측 마케팅 사용현황 ?>

	<div id="maxleft"><script>panel('maxleft', 'basic');</script></div>

	<!------------  측면메뉴 끝 -------------------------------------->

	</td>

	<td width="620" height="100%" valign="top" style="background:url('../img/cover_index.gif') repeat-x #ffffff;padding:0 10px;">

	<!------------  메인본문 시작 -------------------------------------->
	<div style="width:600px;">

	<!--------------  Location 시작 ------------------->
	<div style="padding:16px 0 3px 0;">
		<img src="../img/b_home.gif"/><span id="location" style="font-family:Dotum; font-size:11px; color:#444444;"><span style="color:#888888">HOME</span> > <?=$location?></span>
	</div>
	<!--------------  Location 끝 ------------------->

	<!--------------  배너 시작 ------------------->
	<div id="maxtop"><script>panel('maxtop', 'basic');</script></div>
	<!--------------  배너 끝 ------------------->


	<?if($sms_auto['send_c'] == "on" && $birth['total']['cnt'] > 0){?>
	<!--------------  오늘 생일자 시작 ------------------->
	<table cellpadding="0" cellspacing="0" border="0" width="600">
	<tr>
	<td style="padding-bottom:10px; padding-top:8px" align="center">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
		<td width="10px"></td>
		<td><img src="../img/icon_birthday.gif" align="absmiddle" /></td>
		<td style="padding-left:5px;padding-top:2px" class="end1">오늘 생일자 : <font class="ta8"><b><?=$birth['total']['cnt']?></b></font> 명</td>
		<td width="20px"></td>
		<td><img src="../img/icon_smsok.gif" align="absmiddle" /></td>
		<td style="padding-left:5px;padding-top:2px" class="end1"><font class="ta8">SMS</font> 전송가능 회원 : <font class="ta8"><b><?=($birth['total']['cnt']-$birth['sendN']['cnt'])?></b></font> 명</td>
		<td width="20px"></td>
		<td><img src="../img/icon_smsno.gif" align="absmiddle" /></td>
		<td style="padding-left:5px;padding-top:2px" class="end1"><font class="ta8">SMS</font> 전송불가능 회원 : <font class="ta8"><b><?=$birth['sendN']['cnt']?></b></font> 명</td>
		<td width="20px"></td>
		<td><a href="../member/list.php?smsyn=y&birthtype=s&birthdate[0]=<?=date("md")?>&mobileYN=y"><img src="../img/btn_smsmember.gif" align="absmiddle" /></a></td>
		</tr>
		</table>
	</td>
	</tr>
	</table>
	<!--------------  오늘 생일자 끝 ------------------->
	<?}?>
	<!--------------  메인현황 시작 ------------------->
	<script language="javascript" src="./main.state.js"></script>
	<div id="Main_State_DisplayID" style="width:600px;"><img src="../img/loading.gif" /></div>
	<script>
	NowMainDisplay.inData();
	</script>
	<!--------------  메인현황 끝 ------------------->

	<!-- 메인중간패널 -->
	<div id="maxmiddle"><script>panel('maxmiddle', 'basic');</script></div>

	<div style="height:15px"></div>

	<!---------------------------- 패치,신규 기능/서비스 시작 ------------------------>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td width="50%"><a href="http://www.godo.co.kr/customer_center/patch.php" target='_blank'><img src="../img/t_patch_bar.gif" /></a></td>
		<td width="50%"><a href="http://www.godo.co.kr/shopFunction/index.php" target='_blank'><img src="../img/t_newservice.gif" /></a></td>
	</tr>
	<tr>
		<td valign="top" class="mainBoardLbox"><iframe src="http://gongji.godo.co.kr/userinterface/season4/ifrm_patch_s4max.php?no=<?=$godo[sno]?>" frameborder="0" scrolling="no" style="width:100%;height:104px"></iframe></td>
		<td valign="top" class="mainBoardRbox"><iframe src="http://gongji.godo.co.kr/userinterface/season4/ifrm_newservice.php" frameborder="0" scrolling="no" style="width:100%;height:104px"></iframe></td>
	</tr>
	</table>
	<!---------------------------- 패치,신규 기능/서비스 끝 ------------------------>

	<!--------------  부가서비스 현황 시작 ------------------->
	<div style="height:20px"></div>
	<script>
	function SubMenu(mode,type) {
		var dnm = 'hint_' + mode;
		var div = document.getElementById( dnm );
		if(type=='open'){
			div.style.display = "block";
		}else if (type == 'over') {
			div.style.display = "block";
		}else if (type == 'out') {
			div.style.display = "none";
		}
	}
	</script>

	<div id="maxservice"><script>panel('maxservice', 'basic');</script></div>
	<div style="height:20px"></div>
	<!--------------  부가서비스 현황 끝 ------------------->

	<!-----------------  도메인 검색 시작 ---------------------->
	<? include "./main.domain.php"; ?>
	<!-----------------  도메인 검색 끝 ---------------------->

	<!----------------------- 1:1게시판, 문의게시판, 후기게시판 시작 ------------------------>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:15px;">
	<tr>
		<td width=33%><a href="/shop/admin/board/member_qna.php"><img src="../img/t_oneone.gif" /></a></td>
		<td width=33%><a href="/shop/admin/board/goods_qna.php"><img src="../img/t_qna.gif" /></a></td>
		<td width=33%><a href="/shop/admin/board/goods_review.php"><img src="../img/t_review_bbs.gif" /></a></td>
	</tr>
	<tr>
		<td class="mainBoardLbox">

				<table border="0" cellpadding="0" cellspacing="0">
				<?
				$query = "select * from ".GD_MEMBER_QNA." where sno=parent order by sno desc limit 5";
				$res = $db->query($query);
				while ($data=$db->fetch($res)){
				$data['subject'] = strcut($data['subject'],'37');
				?>
				<tr><td height="5" colspan="2"></td></tr>
				<tr><td valign="top"><img src="../img/icon_list1.gif" align="absmiddle" /></td>
				<td><a href="../board/member_qna.php"><font class="small1" color="#676767"><?=$data['subject']?></a></td></tr>
				<? } ?>
				</table>

		</td>
		<td class="mainBoardRbox">

				<table border="0" cellpadding="0" cellspacing="0">
				<?
				$query = "select * from ".GD_GOODS_QNA." where sno=parent order by sno desc limit 5";
				$res = $db->query($query);
				while ($data=$db->fetch($res)){
				$data['subject'] = strcut($data['subject'],'37');
				?>
				<tr><td height="5" colspan="2"></td></tr>
				<tr><td valign="top"><img src="../img/icon_list1.gif" align="absmiddle" /></td>
				<td><a href="../board/goods_qna.php"><font class="small1" color="#676767"><?=$data['subject']?></a></td></tr>
				<? } ?>
				</table>

		</td>
		<td class="mainBoardRbox">

				<table border="0" cellpadding="0" cellspacing="0">
				<?
				$query = "select * from ".GD_GOODS_REVIEW." order by sno desc limit 5";
				$res = $db->query($query);
				while ($data=$db->fetch($res)){
				$data['subject'] = strcut($data['subject'],'38');
				?>
				<tr><td height="5" colspan="2"></td></tr>
				<tr><td valign="top"><img src="../img/icon_list1.gif" align="absmiddle" /></td>
				<td><a href="../board/goods_review.php"><font class="small1" color="#676767"><?=$data['subject']?></a></td></tr>
				<? } ?>
				</table>

		</td>
	</tr>
	</table>
	<!----------------------- 1:1게시판, 문의게시판, 후기게시판 끝 ------------------------>

	<!-------- 최근 등록한 상품, 진행중인 이벤트 시작 ------------------------>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:15px;">
	<tr>
		<td width=50%><a href="/shop/admin/goods/list.php"><img src="../img/t_recent_goods.gif" /></a></td>
		<td width=50%><a href="/shop/admin/event/list.php"><img src="../img/t_ing_event.gif" /></a></td>
	</tr>
	<tr>
		<td valign="top" class="mainBoardLbox">

				<table border="0" cellpadding="0" cellspacing="0">
				<?
				$query = "select * from ".GD_GOODS." order by regdt desc limit 5";
				$res = $db->query($query);
				while ($data=$db->fetch($res)){
				$data['goodsnm'] = htmlspecialchars(strcut(strip_tags($data['goodsnm']), 57), ENT_QUOTES);
				?>
				<tr><td height="5" colspan="2"></td></tr>
				<tr><td valign="top"><img src="../img/icon_list1.gif" align="absmiddle" /></td>
				<td><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><font class="small1" color="#676767"><?=$data['goodsnm']?></a></td></tr>
				<? } ?>
				</table>

		</td>
		<td valign="top" class="mainBoardRbox">

				<table border="0" cellpadding="0" cellspacing="0">
				<?
				$query = "select * from ".GD_EVENT." order by sno desc limit 5";
				$res = $db->query($query);
				while ($data=$db->fetch($res)){
				$data['subject'] = strcut($data['subject'],'57');
				?>
				<tr><td height="5" colspan="2"></td></tr>
				<tr><td valign="top"><img src="../img/icon_list1.gif" align="absmiddle" /></td>
				<td><a href="../event/register.php?mode=modEvent&sno=<?=$data[sno]?>"><font class="small1" color="#676767"><?=$data['subject']?></a></td></tr>
				<? } ?>
				</table>

		</td>
	</tr>
	</table>
	<!-------- 최근 등록한 상품, 진행중인 이벤트 끝 ------------------------>

	<!-------- 한주간 많이 팔린 상품, 단골고객 시작 ------------------------>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:15px;">
	<tr>
		<td width="50%"><a href="/shop/admin/log/popu.goods.php"><img src="../img/t_best_goods.gif" /></a></td>
		<td width="50%"><a href="/shop/admin/member/list.php?skey=all&sword=&sstatus=&slevel=&ssum_sale%5B%5D=&ssum_sale%5B%5D=&semoney%5B%5D=&semoney%5B%5D=&sregdt%5B%5D=&sregdt%5B%5D=&slastdt%5B%5D=&slastdt%5B%5D=&sex=&sage=&scnt_login%5B%5D=&scnt_login%5B%5D=&dormancy=&mailing=&smsyn=&birthtype=&birthdate%5B%5D=&birthdate%5B%5D=&marriyn=&marridate%5B%5D=&marridate%5B%5D=&sort=sum_sale+desc&page_num=10"><img src="../img/t_best_members.gif" /></a></td>
	</tr>
	<tr>
		<td valign="top" class="mainBoardLbox">

				<table width="96%" border="0" cellpadding="0" cellspacing="0">
				<tr>
				<?
				$query = "select goodsno,count(goodsno) as cnt from ".GD_ORDER_ITEM." where istep in (1,2,3,4) and ordno > '".strtotime("-7 day")."000' group by goodsno order by cnt desc limit 3";
				$res = $db->query($query);
				while ($data=$db->fetch($res)){
					$goodsData['goodsno'][]	= $data['goodsno'];
				}
				if(is_array($goodsData)){
					$query = "select * from ".GD_GOODS." where goodsno in (".implode(",",$goodsData['goodsno']).")";
					$res = $db->query($query);
					while ($data=$db->fetch($res)){
						$goodsData['goodsnm'][$data['goodsno']]	= htmlspecialchars(strcut(strip_tags($data['goodsnm']), 24), ENT_QUOTES);
						$goodsData['img_s'][$data['goodsno']]	= $data['img_s'];
					}
					$i = 0;
					$noString	= array("1st.","2nd.","3rd.");
					foreach($goodsData['goodsno'] AS $gKey => $gVal){
				?>
					<td valign="top" align="center" width=304>
					<table cellpadding=0 cellpadding=0 border=0>
					<tr>
						<td align="center" width="33%" style="font:13px tahoma;letter-spacing:0px"><?=$noString[$i]?></td>
					</tr>
					<tr>
						<td align="center" width="33%"><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$gVal?>',825,600)"><?=goodsimg($goodsData['img_s'][$gVal],"50,50",'',1)?></a></td>
					</tr>
					<tr>
						<td align="center" style="padding-top:7px"><font class="small1" color="#676767"><?=$goodsData['goodsnm'][$gVal]?></font></td>
					</tr>
					</table>
					</td>
				<?
						$i++;
					}
				}
				?>
				</tr>
				</table>

		</td>
		<td valign="top" class="mainBoardRbox">

				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<?
				$query = "select m_no,count(m_no) as cnt,sum(prn_settleprice) as price from ".GD_ORDER." where step in (1,2,3,4) and ordno > '".strtotime("-90 day")."000' and m_no > 0 group by m_no order by cnt desc limit 5";
				$res = $db->query($query);
				while ($data=$db->fetch($res)){
					$memData['m_no'][]					= $data['m_no'];
					$memData['cnt'][$data['m_no']]		= $data['cnt'];
					$memData['price'][$data['m_no']]	= $data['price'];
				}
				if(is_array($memData)){
					$query = "select m_no,name,m_id from ".GD_MEMBER." where m_no in (".implode(",",$memData['m_no']).") limit 5";
					$res = $db->query($query);
					while ($data=$db->fetch($res)){
						$memData['name'][$data['m_no']]	= $data['name'];
						$memData['m_id'][$data['m_no']]	= $data['m_id'];
					}
					foreach($memData['cnt'] AS $mKey => $mVal){
				?>
				<tr><td height="5" colspan="2"></td></tr>
				<tr>
					<td>
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="50%"><img src="../img/icon_list1.gif" align="absmiddle" /><a href="javascript:popupLayer('../member/Crm_view.php?m_id=<?=$memData['m_id'][$mKey]?>',780,600);"><font class="small1" color="#676767"><?=$memData['name'][$mKey]?></font></a> <font class="ver71" color="#676767">(<?=$memData['m_id'][$mKey]?>)</font></td>
						<td width="30%" align="right"><font class="ver71" color="#676767"><?=number_format($memData['price'][$mKey])?><font class=small1> 원</font></font></td>
						<td width="20%" align="right"><font class="ver71" color="#676767"><?=number_format($memData['cnt'][$mKey])?><font class=small1> 회주문&nbsp;</font></font></td>
					</tr>
					</table>
					</td>
				</tr>
				<?
					}
				}
				?>
				</table>

		</td>
	</tr>
	</table>
	<!-------- 한주간 많이 팔린 상품, 단골고객 끝 ------------------------>

	<!-------- 캘린더 시작 ------------------------>
	<table width="600" border="0" cellpadding="0" cellspacing="0" bgcolor="white">
	<tr>
		<td><?include "./malldiary_form.php";?></td>
	</tr>
	<tr>
		<td valign="top" id="s_area"></td>
	</tr>
	</table>

	<script>
	//일정관리 호출!!
	calender('not','f.now');
	</script>
	<!-------- 캘린더 끝 ------------------------>

	<!---------------------------- 고도 아카데미,활용팁 시작 ------------------------>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:5px;">
	<tr>
		<td width="50%"><a href="http://edu.godo.co.kr/general/list.php" target='_blank'><img src="../img/t_academy.gif" /></a></td>
		<td width="50%"><a href="http://edu.godo.co.kr/community/bbs.php?id=enamoo_admin" target='_blank'><img src="../img/t_tip_bar.gif" /></a></td>
	</tr>
	<tr>
		<td valign="top" class="mainBoardLbox"><iframe src="http://gongji.godo.co.kr/userinterface/season4/ifrm_edu.php?no=<?=$godo[sno]?>" frameborder="0" style="width:100%;height:104px"></iframe></td>
		<td valign="top" class="mainBoardRbox"><iframe src="http://gongji.godo.co.kr/userinterface/season4/ifrm_edu_admin_max.php" frameborder="0" style="width:100%;height:104px"></iframe></td>
	</tr>
	</table>
	<!---------------------------- 고도 아카데미,활용팁 끝 ------------------------>

	<!---------------------------- 공지사항, 쇼핑몰뉴스 시작 ------------------------>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:15px;">
	<tr>
		<td width=50%><a href="http://www.godo.co.kr/news/notice_list.php" target='_blank'><img src="../img/t_gongji_bar.gif" /></a></td>
		<td width=50%><a href="javascript:void(0);" onClick="window.open('http://www.godo.co.kr/main/newservice_list.php','popupNewService','toolbar=no,menubar=no,state=no,scrollbars=yes,resizable=no,width=663,height=550');"><img src="../img/t_news_bar.gif" /></a></td>
	</tr>
	<tr>
		<td valign="top" class="mainBoardLbox"><iframe src="http://gongji.godo.co.kr/userinterface/season4/ifrm_notice_max.php" frameborder="0" style="width:100%;height:104px"></iframe></td>
		<td valign="top" class="mainBoardRbox"><iframe src="http://gongji.godo.co.kr/userinterface/season4/ifrm_news_max.php" frameborder="0" style="width:100%;height:104px"></iframe></td>
	</tr>
	</table>
	<!---------------------------- 공지사항, 쇼핑몰뉴스 끝 ------------------------>

	<div id="maxbottom"><script>panel('maxbottom', 'basic');</script></div>

	</div>
	</td>
	<!-------------------------------- 본문 끝 ------------------------------->

	<!------------------------------ 오른쪽 시작 ------------------------------->
	<td valign="top">
	<div id="maxright"><script>panel('maxright', 'basic');</script></div>
	</td>

	<td valign="top" width="100%" height="100%">

	<!------------------------------ 메모 시작 ------------------------------->
	<form name="fm_memo" method="post" action="indb.php" target="ifrmHidden">
	<input type="hidden" name="mode" value="memo" />
	<table width="245" height="100%" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td valign="top" colspan="3" height="100%" style="background:url(../img/note_bg.gif) repeat-y top left">

		<div style="background:url('../img/note_tip.gif') no-repeat top left;padding:0px;height:174px;width:245px;">
		<div id="maxoperate"><script>panel('maxoperate', 'basic');</script></div>
		</div>

		<div style="background:url('../img/note_memo.gif') no-repeat top left;padding:0px;width:245px;height:66px;">
		</div>

		<div style="background-color:#ffffff; margin-left:27px; padding:8px 0 0 0; width:200px; font:11px dotum;color:#000000;font-weight:bold;letter-spacing:0px">
		<?
		if ( @filectime("../../conf/mini_memo.php") ) {
			$memo_date = date("Y-m-d",@filectime("../../conf/mini_memo.php"));
			$memo_time = date("H:i:s",@filectime("../../conf/mini_memo.php"));
		}
		?>
		<img src="../img/b_txt.gif"><span style="font-size:12px;font-weight:normal">최종저장</span> <span style="color:#34a2ef;"><?=$memo_date?></span> <span style="color:#666666;"><?=$memo_time?></span>
		</div>

		<table cellpadding="0" cellspacing="0" border="0" height="100%">
		<tr>
			<td valign="top" height="100%">
				<textarea name="miniMemo" style="margin-left:30px;background-color:transparent;overflow-y:hidden;border:0px;color:#333333;line-height:22px;width:200px;height:100%;"><? @include "../../conf/mini_memo.php";?></textarea>
			</td>
		</tr>
		</table>

		</td>
	</tr>
	<tr>
		<td>
		<div style="background:url('../img/note_bg2.gif') no-repeat top left;width:245px;height:278px;padding:10px 0 0 55px;">
		<input type="image" src="../img/note_save.gif" class="null" />
		<img src="../img/note_del.gif" onclick="document.getElementsByName('miniMemo')[0].value='';" style="cursor:pointer;" />
		</div>
		</td>
	</tr>
	</table>
	</form>
	<!------------------------------ 메모 시작 ------------------------------->

	</td>
</tr>
</table>
<?
if($cfg[autoCancel]){
	list($cnt) = $db->fetch("SELECT COUNT(*) FROM (SELECT a.ordno, b.memo FROM ".GD_ORDER." a LEFT JOIN ".GD_ORDER_CANCEL." b ON a.ordno = b.ordno AND b.memo = '자동주문취소' WHERE a.orddt <= DATE_SUB(NOW(), INTERVAL ".$cfg['autoCancel']." ".($cfg['autoCancelUnit'] == 'h' ? 'HOUR' : 'DAY').") AND a.step='0' AND a.step2='0' AND a.settlekind='a')c WHERE c.memo IS NULL");
	if($cnt) echo "<script>window.onload = function (){ popupLayer('../proc/popup.autoCancel.php',500,300); };</script>";
}
include "../_footer.php"; ?>
<?if(isset($_COOKIE['maxpopup']) === false){?>
<div id="maxpopup"><script>panel('maxpopup', 'basic');</script><div>
<?}?>
