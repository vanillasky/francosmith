<?
$crm_view = true;
list($m_no,$m_id,$name) = $db->fetch("SELECT m_no, m_id, name FROM ".GD_MEMBER." WHERE m_no='".$_GET['m_no']."' OR m_id='".$_GET['m_id']."'");

$menu_array = array(
	'/shop/admin/member/Crm_view.php'=>'1'
	,'/shop/admin/member/Crm_info.php'=>'2'
	,'/shop/admin/member/orderlist.php'=>'3'
	,'/shop/admin/member/popup.emoney.php'=>'4'
	,'/shop/admin/member/popup.coupon.php'=>'5'
	,'/shop/admin/member/Crm_counsel.php'=>'6'
	,'/shop/admin/member/Crm_member_qna.php'=>'7'
);
?>
<link rel="styleSheet" href="../_contextmenu/contextmenu.css?1447375095">
<script type="text/javascript" src="../_contextmenu/contextmenu.js"></script>
<script type="text/javascript">
function searchMember(mode,e){
	if (mode == 1){
		if (e.value == "이름 or 아이디") e.value = "";
	} else if (mode == 2){
		if (!e.value) e.value = "이름 or 아이디";
	}
}
</script>
<style>
body {margin:0; padding:0;}
input[type='image'] {border:0;}
span.ver811 {width:auto !important;}

#crm_area {position:relative; width:1200px; height:100%;}
#crm_area #crm_info {float:left; width:960px; height:100%;}
#crm_area #crm_info #crm_info_header {position:fixed; width:100%; height:50px; background:#2fade7;}
#crm_area #crm_info #crm_info_header #crm_info_title {position:absolute; top:17px; left:25px;}
#crm_area #crm_info #crm_info_header #crm_info_search {position:absolute; top:15px; left:720px;}
#crm_area #crm_info #crm_info_header #crm_info_search #search_title {font-family:Dotum; font-size:12px; color:#fff;}
#crm_area #crm_info #crm_info_header #crm_info_search input {vertical-align:middle;}
#crm_area #crm_info #crm_info_header #crm_info_search input[name='sword'] {color:#858585;}

#crm_menu_area {_float:left; position:fixed; width:154px; height:100%; vertical-align:top; background:#f7f7f7; border-right:1px solid #dcdbe0; margin-top:50px; _margin-top:0; padding:30px 0 0 25px;}
#crm_menu {margin:0; padding:0; list-style:none; font-family:dotum; font-size:12px;}
#crm_menu li {padding-bottom:20px;}
#crm_menu li a {padding-left:5px;}
#crm_menu li.crm_menu_li a {background:url('../img/CRM_menu_dot.jpg') 0 50% no-repeat; color:#636363;}
#crm_menu li.crm_menu_li a:hover, .crm_menu_li_o a {background:url('../img/CRM_menu_dot_o.jpg') 0 50% no-repeat; font-weight:bold; color:#2fade7;}

#crm_content {_float:left; width:720px; padding:30px;}
#crm_content_area {width:720px; margin:50px 0 0 180px; _margin:0;}

#crm_counsel_area {_float:left; position:fixed; top:0; right:0; width:239px; height:100%;}
#crm_counsel_area #crm_counsel_title {width:100%; height:50px; background:#acacac; text-align:center;}
#crm_counsel_area #crm_counsel_title img {margin-top:17px;}
#crm_counsel_area #crm_counsel_info {height:100%; padding:30px 25px 0 25px; background:#fff; border-left:1px solid #dcdbe0;}
#crm_counsel_area #crm_counsel_info table {font-family:Dotum; font-size:12px; color:#000;}
#crm_counsel_area #crm_counsel_info table td {padding-bottom:20px;}

#counsel_tbl td {padding:5px; border-bottom:1px solid #e7e7e7; border-right:1px dotted #acacac;}

textarea[name='contents']:focus {background:#f7f7f7;}
</style>

<div id="crm_area">
	<div id="crm_info">
		<div id="crm_info_header">
			<div id="crm_info_title"><img src="../img/CRM_member_info_title.jpg" /></div>
			<div id="crm_info_search">
				<form name="frm" method="get" action="./popup.list.php">
				<input type="hidden" name="m_no" value="<?=$_GET['m_no']?>">
				<input type="hidden" name="m_id" value="<?=$_GET['m_id']?>">
				<input type="hidden" name="skey" value="all">
				<span id="search_title">회원검색</span>
				<input type="text" class="text" name="sword" value="<?=$_GET['sword'] ? $_GET['sword'] : "이름 or 아이디"?>" title="검색어 입력" onfocus="searchMember('1',this)" onblur="searchMember('2',this)" />
				<input type="image" src="../img/CRM_member_search.jpg" alt="검색" />
				</form>
			</div>
		</div>
		<div id="crm_menu_area">
		<?if ($m_id){?>
		<ul id="crm_menu">
			<li class="crm_menu_li<?echo $menu_array[$_SERVER['PHP_SELF']] == 1 ? "_o" : ""?>"><a href="Crm_view.php?m_id=<?=$m_id?>">CRM 홈</a></li>
			<li class="crm_menu_li<?echo $menu_array[$_SERVER['PHP_SELF']] == 2 ? "_o" : ""?>"><a href="Crm_info.php?m_id=<?=$m_id?>">회원정보수정</a></li>
			<li class="crm_menu_li<?echo $menu_array[$_SERVER['PHP_SELF']] == 3 ? "_o" : ""?>"><a href="orderlist.php?m_no=<?=$m_no?>">구매내역</a></li>
			<li class="crm_menu_li<?echo $menu_array[$_SERVER['PHP_SELF']] == 4 ? "_o" : ""?>"><a href="popup.emoney.php?m_no=<?=$m_no?>">적립금내역</a></li>
			<li class="crm_menu_li<?echo $menu_array[$_SERVER['PHP_SELF']] == 5 ? "_o" : ""?>"><a href="popup.coupon.php?m_no=<?=$m_no?>">쿠폰내역</a></li>
			<li class="crm_menu_li<?echo $menu_array[$_SERVER['PHP_SELF']] == 6 ? "_o" : ""?>"><a href="Crm_counsel.php?m_no=<?=$m_no?>">상담내역</a></li>
			<li class="crm_menu_li<?echo $menu_array[$_SERVER['PHP_SELF']] == 7 ? "_o" : ""?>"><a href="Crm_member_qna.php?skey=m_id&sword=<?=$m_id?>&sitemcd=all&m_no=<?=$m_no?>">1:1문의내역</a></li>
		</ul>
		<?} else {?>
		회원을 선택해주세요
		<?}?>
		</div>
		<div id="crm_content">
			<div id="crm_content_area">