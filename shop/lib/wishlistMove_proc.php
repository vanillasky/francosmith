<?
header('Content-Type: text/html; charset=euc-kr');

include "../lib/library.php";
include "../lib/json.class.php";
include "../lib/cart.class.php";

//위시리스트 등록!!
if( $_GET['mode'] == "addItem" ){

		//옵션정보
		if( $_GET['Move_GoodsType'] == "list" ){
			$gQuery = "
				select
					a.img_s,
					a.opttype,
					b.opt1,
					b.opt2
				from
					".GD_GOODS." a , ".GD_GOODS_OPTION." b
				where
					a.goodsno = b.goodsno and go_is_deleted <> '1' and go_is_display = '1' and
					a.open = '1' and
					a.goodsno = '$_GET[goodsno]'
				order by b.sno asc
			";
			$row = $db->fetch($gQuery);
			$opt1 = $row['opt1'];
			$opt2 = $row['opt2'];
		}
		if( $_GET['Move_GoodsType'] == "cart" ){
			$orderitem_mode = "cart";
			$cart = new Cart;
			$cart_Cnt = count($cart->item);
			$opt1 = '';
			$opt2 = '';
			for( $c=0 ; $c < $cart_Cnt; $c++ ){
				if( $cart->item[$c]['goodsno'] == $_GET['goodsno'] ){
					$opt1 = $cart->item[$c]['opt']['0'];
					$opt2 = $cart->item[$c]['opt']['1'];
					break;
				}
			}
		}

	//위시리스트 중복확인!!
	$sessYn = 'n';
	$infoQuery = "
		select
			count(*) as in_cnt
		from
			".GD_MEMBER_WISHLIST."
		where
			m_no = '$sess[m_no]' and
			goodsno = '$_GET[goodsno]' and
			opt1 = '$opt1' and
			opt2 = '$opt2'
	";
	if( $sess['m_no'] ){
		$sessYn = 'y';
		$_inrow = $db->fetch($infoQuery);
	}else{
		$sessYn = 'n';
	}

	$_inYn = 'n';
	if( $_inrow['in_cnt'] > 0 ){
		$_inYn = 'n';
	}else{
		$_inYn = 'y';

		$Query = "
			insert into
				".GD_MEMBER_WISHLIST."
			set
				m_no = '$sess[m_no]',
				goodsno = '$_GET[goodsno]',
				opt1 = '$opt1',
				opt2 = '$opt2',
				regdt = now()
		";
		$db->query($Query);
	}
		$_inarray = array();
		$_inarray['_inYn'] = $_inYn;
		$_inarray['sessYn'] = $sessYn;
		$wishlist_func = get_wishlist($sess['m_no']);
		$_WLF['wishlist'] = $wishlist_func;
		$data = array_merge( array( 'Remode'=>$_GET['mode'] ), $_WLF );
		$_info = array_merge( array( '_inarray'=>$_inarray ), $data );
		$json = new Services_JSON();
		$output = $json->encode($_info);

		echo $output;
}

//위시리스트 삭제시
	if( $_GET['mode'] == "delItem" ){
		$_inarray = array();
		if( $sess['m_no'] ){
			$_inarray['sessYn'] = 'y';
			$Query = "delete from ".GD_MEMBER_WISHLIST." where sno = '$_GET[Ev_wishlistNo]' and m_no = '$sess[m_no]'";
			$db->query($Query);

			$wishlist_func = get_wishlist($sess['m_no']);
			$_WLF['wishlist'] = $wishlist_func;
			$data = array_merge( array( '_inarray'=>$_inarray ), $_WLF );
			$_info = array_merge( array( 'Remode'=>$_GET['mode'] ), $data );
		}else{
			$_inarray['sessYn'] = 'n';
			$_info = array_merge( array( 'Remode'=>$_GET['mode'] ), $_inarray );
		}
		$json = new Services_JSON();
		$output = $json->encode($_info);
		echo $output;
	}

//위시리스트 보기!!
if( $_GET['mode'] == "wishlist_view" ){
	$_inarray = array();

	if( $sess['m_no'] ){
		$_inarray['sessYn'] = 'y';
		$wishlist_func = get_wishlist($sess['m_no']);
		$_WLF['wishlist'] = $wishlist_func;
		$data = array_merge( array( '_inarray'=>$_inarray ), $_WLF );
		$_info = array_merge( array( 'Remode'=>$_GET['mode'] ), $data );
	}else{
		$_inarray['sessYn'] = 'n';
		$_info = array_merge( array( 'Remode'=>$_GET['mode'] ), $_inarray );
	}

	$json = new Services_JSON();
	$output = $json->encode($_info);
	echo $output;

}
?>