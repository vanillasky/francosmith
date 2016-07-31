<?
$location = "상품관리 > 블로그샵 상품정보 일괄이전";
include "../_header.php";
$blogshop = new blogshop();
$ar_result=array();
if($blogshop->linked && $_POST['chk']) {
	$ar_blog_goodsno = $_POST['chk'];
	$cate_no = implode("",$_POST['cate']);
	
	
	foreach($ar_blog_goodsno as $blog_goodsno) {
		
		$img_l_name="";
		$img_m_name="";
		$img_i_name="";
		$img_s_name="";

		$result = $blogshop->get_goods_image($blog_goodsno);
		if(is_file($result) && filesize($result)) {
			include "../../conf/config.php";

			$_dir	= "../../data/goods/";
			$name_key = time();
			$img_l_name = $name_key."l0.jpg";
			$img_m_name = $name_key."_m_0.jpg";
			$img_i_name = $name_key."_i_0.jpg";
			$img_s_name = $name_key."_s_0.jpg";

			@rename($result,$_dir.$img_l_name);
			@chmod($_dir.$img_l_name,0707);

			thumbnail($_dir.$img_l_name,$_dir.$img_m_name ,$cfg['img_m']);
			@chmod($_dir.$img_m_name,0707);

			thumbnail($_dir.$img_l_name,$_dir.$img_i_name ,$cfg['img_m']);
			@chmod($_dir.$img_i_name,0707);

			thumbnail($_dir.$img_l_name,$_dir.$img_s_name ,$cfg['img_m']);
			@chmod($_dir.$img_s_name,0707);
		}




		$result = $blogshop->get_goods_from_blog_goodsno($blog_goodsno);
		foreach($result as $k=>$v) {
			$result[$k]=addslashes($v);
		}

		$insert_query = "
		insert into gd_goods set
			goodsnm = '{$result['goodsnm']}',
			origin = '{$result['origin']}',
			maker = '{$result['maker']}',
			longdesc = '{$result['longdesc']}',
			open = '1',
			runout = '0',
			opttype = 'single',
			launchdt = '{$result['launchdt']}',
			meta_title='',
			useblog='y',
			img_l='{$img_l_name}',
			img_i='{$img_i_name}',
			img_m='{$img_m_name}',
			img_s='{$img_s_name}',
			regdt=now()
		";
		$db->query($insert_query);

		$goodsno= $db->lastID();

		$insert_query = "
		insert into gd_goods_option set
			goodsno='{$goodsno}',
			opt1='',
			opt2='',
			price='{$result['price']}',
			consumer='0',
			supply='0',
			reserve='0',
			stock='0',
			link='1'
		";
		$db->query($insert_query);

		$opt_sno= $db->lastID();
		$update_query = "
		update gd_goods_option set
			optno='{$opt_sno}'
		where
			sno='$opt_sno'
		";
		$db->query($update_query);

		$insert_query = "
		insert into gd_goods_link set
			goodsno='{$goodsno}',
			category='{$cate_no}',
			sort=-unix_timestamp(),
			hidden='0'
		";
		$db->query($insert_query);
		

		$blogshop->link_goods($blog_goodsno,$goodsno);
		$ar_result[]=$goodsno;


	}
		



}




?>

<div class="title title_top">블로그샵 상품정보 일괄이전<span>이니P2P와 연동된 블로그샵 상품을 쇼핑몰 상품으로 전환시킵니다</span> </div>
<br>
<span>2010년 3월 09일 00:00에 00개의 블로그샵 상품정보가 쇼핑몰 상품으로 이동하였습니다</span>

<? include "../_footer.php"; ?>
