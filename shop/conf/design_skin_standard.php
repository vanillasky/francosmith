<?
$design_skin = array();

$design_skin['default'] = array(
'outline_header'			=> 'outline/header/standard.htm',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'outline/footer/standard.htm',
'outline_sidefloat'			=> 'left',
);

$design_skin['outline/_header.htm'] = array(
'linkurl'			=> 'main/index.php',
'text'			=> '상단레이아웃',
);

$design_skin['outline/_footer.htm'] = array(
'linkurl'			=> 'main/index.php',
'outline_header'			=> 'outline/header/main.htm',
'outline_side'			=> 'outline/side/cs.htm',
'outline_footer'			=> 'outline/footer/main_footer.htm',
'text'			=> '하단레이아웃',
'_et'			=> 'codemirror',
);

$design_skin['outline/footer/standard.htm'] = array(
'linkurl'			=> 'main/index.php',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> '하단기본타입',
'_et'			=> 'codemirror',
);

$design_skin['outline/side/standard.htm'] = array(
'linkurl'			=> 'main/index.php',
'text'			=> '측면기본타입',
);

$design_skin['main/index.htm'] = array(
'linkurl'			=> 'index.php',
'outline_header'			=> 'outline/header/main.htm',
'outline_footer'			=> 'outline/footer/main_footer.htm',
'text'			=> '쇼핑몰 메인본문',
'_et'			=> 'codemirror',
);

$design_skin['goods/list/tpl_01.htm'] = array(
'text'			=> '갤러리형',
'linkurl'			=> 'goods/goods_list.php',
);

$design_skin['goods/list/tpl_02.htm'] = array(
'text'			=> '리스트형',
'linkurl'			=> 'goods/goods_list.php',
);

$design_skin['goods/list/tpl_03.htm'] = array(
'text'			=> '리스트 그룹형',
'linkurl'			=> 'goods/goods_list.php',
);

$design_skin['goods/goods_list.htm'] = array(
'text'			=> '분류화면',
'linkurl'			=> 'goods/goods_list.php',
);

$design_skin['goods/goods_view.htm'] = array(
'text'			=> '상품상세화면',
'linkurl'			=> 'goods/goods_view.php',
);

$design_skin['goods/goods_search.htm'] = array(
'text'			=> '상세검색화면',
'linkurl'			=> 'goods/goods_search.php',
);

$design_skin['goods/goods_today.htm'] = array(
'text'			=> '최근본상품',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['goods/goods_cart.htm'] = array(
'text'			=> '장바구니',
'linkurl'			=> 'goods/goods_cart.php',
);

$design_skin['goods/goods_review.htm'] = array(
'text'			=> '이용후기 메인',
'linkurl'			=> 'goods/goods_review.php',
);

$design_skin['goods/goods_review_list.htm'] = array(
'text'			=> '이용후기 목록',
'linkurl'			=> 'goods/goods_view.php',
'outline_header'			=> 'noprint',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'noprint',
);

$design_skin['goods/goods_review_register.htm'] = array(
'text'			=> '이용후기 작성',
'linkurl'			=> 'goods/goods_review_register.php',
'outline_header'			=> 'noprint',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'noprint',
);

$design_skin['goods/goods_review_del.htm'] = array(
'text'			=> '이용후기 삭제',
'linkurl'			=> 'goods/goods_review_del.php',
'outline_header'			=> 'noprint',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'noprint',
);

$design_skin['goods/goods_qna.htm'] = array(
'text'			=> '상품문의 메인',
'linkurl'			=> 'goods/goods_qna.php',
);

$design_skin['goods/goods_qna_view.htm'] = array(
'text'			=> '상품문의 상세',
'linkurl'			=> 'goods/goods_qna_view.php',
);

$design_skin['goods/goods_qna_list.htm'] = array(
'text'			=> '상품문의 목록',
'linkurl'			=> 'goods/goods_view.php',
'outline_header'			=> 'noprint',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'noprint',
);

$design_skin['goods/goods_qna_register.htm'] = array(
'text'			=> '상품문의 작성',
'linkurl'			=> 'goods/goods_qna_register.php',
'outline_header'			=> 'noprint',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'noprint',
);

$design_skin['goods/goods_qna_del.htm'] = array(
'text'			=> '상품문의 삭제',
'linkurl'			=> 'goods/goods_qna_del.php',
'outline_header'			=> 'noprint',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'noprint',
);

$design_skin['service/company.htm'] = array(
'text'			=> '회사소개',
'linkurl'			=> 'service/company.php',
);

$design_skin['service/agreement.htm'] = array(
'text'			=> '이용약관',
);

$design_skin['service/guide.htm'] = array(
'linkurl'			=> 'service/guide.php',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> '이용안내',
);

$design_skin['service/private.htm'] = array(
'text'			=> '개인정보취급방침',
'linkurl'			=> 'service/private.php',
);

$design_skin['service/sitemap.htm'] = array(
'text'			=> '사이트맵',
'linkurl'			=> 'service/sitemap.php',
);

$design_skin['service/cooperation.htm'] = array(
'text'			=> '광고제휴문의',
);

$design_skin['service/customer.htm'] = array(
'linkurl'			=> 'service/customer.php',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> '고객센터',
);

$design_skin['html/standard.htm'] = array(
'linkurl'			=> 'html/standard.php',
'text'			=> '추가페이지샘플',
);

$design_skin['html/addtest.htm'] = array(
'text'			=> 'addtest',
);

$design_skin['html/hoho.htm'] = array(
'text'			=> '후후',
);

$design_skin['popup/standard.htm'] = array(
'text'			=> '기본팝업',
'popup_use'			=> 'N',
'popup_spotw'			=> '0',
'popup_spoth'			=> '0',
'popup_sizew'			=> '300',
'popup_sizeh'			=> '400',
'linkurl'			=> 'main/html.php?htmid=popup/standard.htm',
);

$design_skin['outline/side/cs.htm'] = array(
'text'			=> '고객센터_left',
'linkurl'			=> 'main/index.php',
);

$design_skin['outline/side/mypage.htm'] = array(
'linkurl'			=> 'member/myinfo.php',
'text'			=> '마이페이지_left',
);

$design_skin['service/faq.htm'] = array(
'linkurl'			=> 'service/faq.php',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> 'FAQ',
);

$design_skin['member/myinfo.htm'] = array(
'linkurl'			=> 'member/myinfo.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '회원정보수정',
);

$design_skin['member/login.htm'] = array(
'text'			=> '로그인',
'linkurl'			=> 'member/login.php',
'outline_side'			=> 'outline/side/cs.htm',
);

$design_skin['member/join.htm'] = array(
'text'			=> '회원가입',
'linkurl'			=> 'member/join.php',
'outline_side'			=> 'outline/side/cs.htm',
);

$design_skin['member/find_id.htm'] = array(
'text'			=> '아이디 찾기',
'linkurl'			=> 'member/find_id.php',
'outline_side'			=> 'outline/side/cs.htm',
);

$design_skin['member/find_pwd.htm'] = array(
'linkurl'			=> 'member/find_pwd.php',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> '비밀번호 찾기',
);

$design_skin['member/hack.htm'] = array(
'linkurl'			=> 'member/hack.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '회원탈퇴',
);

$design_skin['member/myemoney.htm'] = array(
'text'			=> '적립금내역',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['member/goods_review.htm'] = array(
'text'			=> '나의 상품후기',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['member/goods_qna.htm'] = array(
'text'			=> '나의 상품문의',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['member/member_qna.htm'] = array(
'text'			=> '1:1 문의게시판',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['mypage/orderlist.htm'] = array(
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['member/mywishlist.htm'] = array(
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['main/intro.htm'] = array(
'text'			=> '인트로',
'linkurl'			=> 'intro.php',
);

$design_skin['proc/_agreement.txt'] = array(
'linkurl'			=> 'service/agreement.php',
'text'			=> '이용약관 내용',
'_et'			=> 'textarea',
);

$design_skin['goods/goods_event.htm'] = array(
'text'			=> '이벤트',
'linkurl'			=> 'goods/goods_event.php',
'outline_side'			=> 'noprint',
);

$design_skin['mypage/mypage_coupon.htm'] = array(
'linkurl'			=> 'mypage/mypage_coupon.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '할인쿠폰내역',
);

$design_skin['mypage/mypage_emoney.htm'] = array(
'linkurl'			=> 'mypage/mypage_emoney.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '적립금내역',
);

$design_skin['mypage/mypage_orderlist.htm'] = array(
'linkurl'			=> 'mypage/mypage_orderlist.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '주문내역/배송조회',
);

$design_skin['mypage/mypage_qna.htm'] = array(
'linkurl'			=> 'mypage/mypage_qna.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '1:1문의',
);

$design_skin['mypage/mypage_qna_del.htm'] = array(
'text'			=> '1:1문의 삭제',
'linkurl'			=> 'mypage/mypage_qna_del.php',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['mypage/mypage_qna_goods.htm'] = array(
'linkurl'			=> 'mypage/mypage_qna_goods.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '나의 상품문의',
);

$design_skin['mypage/mypage_qna_order.htm'] = array(
'text'			=> '1:1문의 주문번호검색창',
'linkurl'			=> 'mypage/mypage_qna_order.php',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['mypage/mypage_qna_register.htm'] = array(
'text'			=> '1:1문의 등록',
'linkurl'			=> 'mypage/mypage_qna_register.php',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['mypage/mypage_review.htm'] = array(
'linkurl'			=> 'mypage/mypage_review.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '나의 상품후기',
);

$design_skin['mypage/mypage_wishlist.htm'] = array(
'text'			=> '상품보관함',
'linkurl'			=> 'mypage/mypage_wishlist.php',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['mypage/mypage_today.htm'] = array(
'linkurl'			=> 'mypage/mypage_today.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '최근본상품목록',
);

$design_skin['proc/popup_zipcode.htm'] = array(
'text'			=> '우편번호검색',
'linkurl'			=> 'proc/popup_zipcode.php',
);

$design_skin['proc/popup_coupon.htm'] = array(
'text'			=> '할인쿠폰적용하기',
'linkurl'			=> 'proc/popup_coupon.php',
);

$design_skin['proc/scroll.js'] = array(
'text'			=> '스크롤배너 스크립트',
'linkurl'			=> 'proc/scroll.js',
);

$design_skin['proc/orderitem.htm'] = array(
'text'			=> '장바구니 상품목록',
'linkurl'			=> 'goods/goods_cart.php',
);

$design_skin['member/agreement.htm'] = array(
'text'			=> '회원가입 이용약관',
'linkurl'			=> 'member/join.php',
);

$design_skin['member/join_ok.htm'] = array(
'text'			=> '회원가입 완료',
'linkurl'			=> 'member/join_ok.php',
);

$design_skin['member/_form.htm'] = array(
'text'			=> '회원가입/수정 폼',
'linkurl'			=> 'member/join.php',
);

$design_skin['goods/goods_popup_large.htm'] = array(
'text'			=> '상품확대보기',
'linkurl'			=> 'goods/goods_popup_large.php',
);

$design_skin['goods/list/tpl_04.htm'] = array(
'linkurl'			=> 'goods/goods_list.php',
'text'			=> '상품이동형',
);

$design_skin['order/order.htm'] = array(
'text'			=> '주문하기(주문서작성)',
'linkurl'			=> 'order/order.php',
);

$design_skin['order/settle.htm'] = array(
'text'			=> '결제하기(카드/무통장)',
'linkurl'			=> 'order/settle.php',
);

$design_skin['order/order_end.htm'] = array(
'text'			=> '주문완료',
'linkurl'			=> 'order/order_end.php',
);

$design_skin['order/card.allat.htm'] = array(
'text'			=> '결제하기(올앳 PG)',
'linkurl'			=> 'order/card.allat.php',
);

$design_skin['proc/popup_email.htm'] = array(
'text'			=> '관리자에게 메일보내기',
'linkurl'			=> 'proc/popup_email.php',
'outline_header'			=> 'noprint',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'noprint',
);

$design_skin['mypage/mypage_orderview.htm'] = array(
'linkurl'			=> 'mypage/mypage_orderview.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '주문내역상세페이지',
);

$design_skin['goods/goods_grp_05.htm'] = array(
'linkurl'			=> 'goods/goods_grp_05.php',
);

$design_skin['board/default/list.htm'] = array(
'text'			=> '목록',
'linkurl'			=> 'board/list.php',
);

$design_skin['board/test/list.htm'] = array(
'text'			=> '목록',
'linkurl'			=> 'board/test/list.php',
);

$design_skin['board/gallery/list.htm'] = array(
'text'			=> '목록',
'linkurl'			=> 'board/list.php',
);

$design_skin['proc/menuCategory.htm'] = array(
'linkurl'			=> 'proc/menuCategory.php',
'text'			=> '카테고리메뉴',
);

$design_skin['proc/ccsms.htm'] = array(
'linkurl'			=> 'proc/ccsms.php',
'text'			=> '관리자에게 SMS상담문의하기',
);

$design_skin['popup/layer.htm'] = array(
'text'			=> '레이어팝업',
'popup_use'			=> 'N',
'popup_spotw'			=> '100',
'popup_spoth'			=> '100',
'popup_sizew'			=> '325',
'popup_sizeh'			=> '445',
'popup_type'			=> 'layer',
'linkurl'			=> 'main/html.php?htmid=popup/layer.htm',
);

$design_skin['service/_private.txt'] = array(
'linkurl'			=> 'service/private.php',
'text'			=> '개인정보취급방침 내용',
'_et'			=> 'textarea',
);

$design_skin['proc/test_include.htm'] = array(
'text'			=> '테스트_인쿠르트',
'linkurl'			=> 'main/html.php?htmid=proc/test_include.htm',
);

$design_skin['service/_private.htm'] = array(
'text'			=> '개인정보취급방침 (회원가입내 프레임)',
'linkurl'			=> 'service/_private.php',
);

$design_skin['board/1.htm'] = array(
'text'			=> '1',
'linkurl'			=> 'board/1.php',
);

$design_skin['popup/move.htm'] = array(
'text'			=> '이동레이어',
'popup_use'			=> 'Y',
'popup_spotw'			=> '130',
'popup_spoth'			=> '130',
'popup_sizew'			=> '325',
'popup_sizeh'			=> '445',
'popup_type'			=> 'layerMove',
'linkurl'			=> 'main/html.php?htmid=popup/move.htm',
);

$design_skin['mypage/settle.htm'] = array(
'linkurl'			=> 'order/settle.php',
'text'			=> '재결제하기(카드/무통장)',
);

$design_skin['outline/footer/main_footer.htm'] = array(
'linkurl'			=> 'main/index.php',
'outline_side'			=> 'outline/side/cs.htm',
'outline_footer'			=> 'outline/footer/main_footer.htm',
'text'			=> '메인용 하단파일',
'_et'			=> 'codemirror',
);

$design_skin['outline/header/standard.htm'] = array(
'linkurl'			=> 'main/index.php',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> '상단기본파일',
'inbg_img'			=> 'outline.header.standard_inbg.gif',
'_et'			=> 'codemirror',
);

$design_skin['outline/header/main.htm'] = array(
'linkurl'			=> 'main/index.php',
'outline_header'			=> 'outline/header/main.htm',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> '메인용 상단파일',
'inbg_img'			=> 'outline.header.main_inbg.gif',
'_et'			=> 'codemirror',
);

$design_skin['outline/side/mainleft.htm'] = array(
'linkurl'			=> 'main/index.php',
'text'			=> '메인용 측면파일',
);

$design_skin['setGoods/index.htm'] = array(
'linkurl'			=> 'setGoods/index.php',
'outline_side'			=> 'noprint',
'text'			=> '코디진열페이지',
);

$design_skin['setGoods/content.htm'] = array(
'linkurl'			=> 'setGoods/content.php',
'outline_side'			=> 'noprint',
'text'			=> '코디상세페이지',
);
?>