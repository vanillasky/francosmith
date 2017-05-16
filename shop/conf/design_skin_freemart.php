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
'_et'			=> 'codemirror',
);

$design_skin['outline/_footer.htm'] = array(
'linkurl'			=> 'main/index.php',
'text'			=> '하단레이아웃',
'_et'			=> 'codemirror',
);

$design_skin['outline/footer/standard.htm'] = array(
'linkurl'			=> 'main/index.php',
'text'			=> '하단기본타입',
'_et'			=> 'codemirror',
);

$design_skin['outline/side/standard.htm'] = array(
'linkurl'			=> 'main/index.php',
'text'			=> '측면기본타입',
'_et'			=> 'codemirror',
);

$design_skin['main/index.htm'] = array(
'linkurl'			=> 'index.php',
'outline_header'			=> 'outline/header/main.htm',
'outline_footer'			=> 'outline/footer/main_footer.htm',
'text'			=> '쇼핑몰 메인본문',
'_et'			=> 'codemirror',
);

$design_skin['goods/list/tpl_01.htm'] = array(
'linkurl'			=> 'goods/goods_list.php',
'text'			=> '갤러리형',
'_et'			=> 'codemirror',
);

$design_skin['goods/list/tpl_02.htm'] = array(
'linkurl'			=> 'goods/goods_list.php',
'text'			=> '리스트형',
'_et'			=> 'codemirror',
);

$design_skin['goods/list/tpl_03.htm'] = array(
'linkurl'			=> 'goods/goods_list.php',
'text'			=> '리스트 그룹형',
'_et'			=> 'codemirror',
);

$design_skin['goods/goods_list.htm'] = array(
'linkurl'			=> 'goods/goods_list.php',
'outline_side'			=> 'outline/side/category_side.htm',
'text'			=> '분류화면',
'_et'			=> 'codemirror',
);

$design_skin['goods/goods_view.htm'] = array(
'linkurl'			=> 'goods/goods_view.php',
'text'			=> '상품상세화면',
'_et'			=> 'codemirror',
);

$design_skin['goods/goods_search.htm'] = array(
'linkurl'			=> 'goods/goods_search.php',
'text'			=> '상세검색화면',
'_et'			=> 'codemirror',
);

$design_skin['goods/goods_today.htm'] = array(
'text'			=> '최근본상품',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['goods/goods_cart.htm'] = array(
'linkurl'			=> 'goods/goods_cart.php',
'text'			=> '장바구니',
'_et'			=> 'codemirror',
);

$design_skin['goods/goods_review.htm'] = array(
'linkurl'			=> 'goods/goods_review.php',
'text'			=> '이용후기 메인',
'_et'			=> 'codemirror',
);

$design_skin['goods/goods_review_list.htm'] = array(
'linkurl'			=> 'goods/goods_view.php',
'text'			=> '이용후기 목록',
'_et'			=> 'codemirror',
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
'linkurl'			=> 'goods/goods_view.php',
'text'			=> '상품문의 목록',
'_et'			=> 'codemirror',
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
'_et'			=> 'codemirror',
);

$design_skin['service/private.htm'] = array(
'text'			=> '개인정보취급방침',
'linkurl'			=> 'service/private.php',
);

$design_skin['service/sitemap.htm'] = array(
'linkurl'			=> 'service/sitemap.php',
'text'			=> '사이트맵',
'_et'			=> 'codemirror',
);

$design_skin['service/cooperation.htm'] = array(
'text'			=> '광고제휴문의',
);

$design_skin['service/customer.htm'] = array(
'linkurl'			=> 'service/customer.php',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> '고객센터',
'_et'			=> 'codemirror',
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
'linkurl'			=> 'main/index.php',
'text'			=> '고객센터_left',
'_et'			=> 'codemirror',
);

$design_skin['outline/side/mypage.htm'] = array(
'linkurl'			=> 'member/myinfo.php',
'text'			=> '마이페이지_left',
'_et'			=> 'codemirror',
);

$design_skin['service/faq.htm'] = array(
'linkurl'			=> 'service/faq.php',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> 'FAQ',
'_et'			=> 'codemirror',
);

$design_skin['member/myinfo.htm'] = array(
'linkurl'			=> 'member/myinfo.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '회원정보수정',
'_et'			=> 'codemirror',
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
'text'			=> '이용약관 내용',
'linkurl'			=> 'service/agreement.php',
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
'_et'			=> 'codemirror',
);

$design_skin['mypage/mypage_emoney.htm'] = array(
'linkurl'			=> 'mypage/mypage_emoney.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '적립금내역',
'_et'			=> 'codemirror',
);

$design_skin['mypage/mypage_orderlist.htm'] = array(
'linkurl'			=> 'mypage/mypage_orderlist.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '주문내역/배송조회',
'_et'			=> 'codemirror',
);

$design_skin['mypage/mypage_qna.htm'] = array(
'linkurl'			=> 'mypage/mypage_qna.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '1:1문의',
'_et'			=> 'codemirror',
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
'_et'			=> 'codemirror',
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
'_et'			=> 'codemirror',
);

$design_skin['mypage/mypage_wishlist.htm'] = array(
'linkurl'			=> 'mypage/mypage_wishlist.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '상품보관함',
'_et'			=> 'codemirror',
);

$design_skin['mypage/mypage_today.htm'] = array(
'linkurl'			=> 'mypage/mypage_today.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '최근본상품목록',
'_et'			=> 'codemirror',
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
'linkurl'			=> 'proc/scroll.js',
'text'			=> '스크롤배너 스크립트',
'_et'			=> 'codemirror',
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
'linkurl'			=> 'board/list.php',
'text'			=> '목록',
'_et'			=> 'codemirror',
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
'_et'			=> 'codemirror',
);

$design_skin['proc/ccsms.htm'] = array(
'linkurl'			=> 'proc/ccsms.php',
'text'			=> '관리자에게 SMS상담문의하기',
'_et'			=> 'codemirror',
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
'text'			=> '개인정보취급방침 내용',
'linkurl'			=> 'service/private.php',
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
'popup_use'			=> 'N',
'popup_spotw'			=> '130',
'popup_spoth'			=> '130',
'popup_sizew'			=> '325',
'popup_sizeh'			=> '445',
'popup_type'			=> 'layerMove',
'_et'			=> 'codemirror',
'linkurl'			=> 'main/html.php?htmid=popup/move.htm',
);

$design_skin['mypage/settle.htm'] = array(
'linkurl'			=> 'order/settle.php',
'text'			=> '재결제하기(카드/무통장)',
);

$design_skin['outline/footer/main_footer.htm'] = array(
'linkurl'			=> 'main/index.php',
'text'			=> '메인용 하단파일',
'_et'			=> 'codemirror',
);

$design_skin['outline/header/standard.htm'] = array(
'linkurl'			=> 'main/index.php',
'text'			=> '상단기본파일',
'inbg_img'			=> 'outline.header.standard_inbg.gif',
'_et'			=> 'codemirror',
);

$design_skin['outline/header/main.htm'] = array(
'linkurl'			=> 'main/index.php',
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

$design_skin['goods/facepage.htm'] = array(
'linkurl'			=> 'goods/facepage.htm',
'text'			=> '페이스북',
'inbg_color'			=> 'ffffff',
);

$design_skin['mypage/mypage.htm'] = array(
'linkurl'			=> 'mypage/mypage.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '마이페이지 대시보드',
'_et'			=> 'codemirror',
);

$design_skin['popup/thanks-giving.htm'] = array(
'text'			=> '추석연휴 배송안내',
'popup_use'			=> 'Y',
'popup_spotw'			=> '150',
'popup_spoth'			=> '250',
'popup_sizew'			=> '350',
'popup_sizeh'			=> '430',
'popup_dt2tm'			=> 'Y',
'popup_sdt'			=> '20140901',
'popup_edt'			=> '20140910',
'popup_stime'			=> '10',
'popup_etime'			=> '2359',
'popup_type'			=> 'layerMove',
'_et'			=> 'codemirror',
'linkurl'			=> 'main/html.php?htmid=popup/thanks-giving.htm',
);

$design_skin['mypage/_myBoxLayer.htm'] = array(
'linkurl'			=> 'main/html.php?htmid=mypage/_myBoxLayer.htm',
'text'			=> '마이페이지 레이어',
'_et'			=> 'codemirror',
);

$design_skin['mypage/_myBox.htm'] = array(
'linkurl'			=> 'mypage/_myBox.php',
'_et'			=> 'codemirror',
);

$design_skin['goods/goods_grp_01.htm'] = array(
'linkurl'			=> 'goods/goods_grp_01.php',
'_et'			=> 'codemirror',
);

$design_skin['mypage/paper_coupon.htm'] = array(
'linkurl'			=> 'mypage/paper_coupon.php',
'_et'			=> 'codemirror',
);

$design_skin['goods/list/brands.htm'] = array(
'linkurl'			=> 'main/html.php?htmid=goods/list/brands.htm',
'text'			=> 'Shop By Brand',
'_et'			=> 'codemirror',
);

$design_skin['goods/goods_brand.htm'] = array(
'linkurl'			=> 'goods/goods_brand.php',
'text'			=> '브랜드화면',
'_et'			=> 'codemirror',
);

$design_skin['proc/shopping_info.htm'] = array(
'linkurl'			=> 'proc/shopping_info.php',
'text'			=> '배송-교환안내',
'_et'			=> 'codemirror',
);

$design_skin['proc/main/brand_list.htm'] = array(
'linkurl'			=> 'main/html.php?htmid=proc/main/brand_list.htm',
'text'			=> '전체브랜드',
'_et'			=> 'codemirror',
);

$design_skin['proc/main/category_all.htm'] = array(
'linkurl'			=> 'main/html.php?htmid=proc/main/category_all.htm',
'text'			=> '전체카테고리',
'_et'			=> 'codemirror',
);

$design_skin['popup/delivery_notice.htm'] = array(
'text'			=> '2015년 업무종료',
'popup_use'			=> 'Y',
'popup_spotw'			=> '150',
'popup_spoth'			=> '250',
'popup_sizew'			=> '470',
'popup_sizeh'			=> '680',
'popup_dt2tm'			=> 'Y',
'popup_sdt'			=> '20151229',
'popup_edt'			=> '20151231',
'popup_etime'			=> '2400',
'popup_type'			=> 'layerMove',
'_et'			=> 'codemirror',
'linkurl'			=> 'main/html.php?htmid=popup/delivery_notice.htm',
);

$design_skin['goods/list/tpl_06.htm'] = array(
'linkurl'			=> 'goods/goods_list.php',
'text'			=> '가로스크롤바형',
'_et'			=> 'codemirror',
);

$design_skin['goods/list/tpl_07.htm'] = array(
'linkurl'			=> 'goods/goods_list.php',
'text'			=> '탭형',
'_et'			=> 'codemirror',
);

$design_skin['goods/list/tpl_08.htm'] = array(
'linkurl'			=> 'goods/goods_list.php',
'text'			=> '선택강조형',
'_et'			=> 'codemirror',
);

$design_skin['goods/popup_cart_add.htm'] = array(
'linkurl'			=> 'goods/popup_cart_add.php',
'text'			=> '장바구니담기 팝업',
'_et'			=> 'codemirror',
);

$design_skin['popup/new_year.htm'] = array(
'text'			=> '설연휴 배송안내',
'popup_use'			=> 'Y',
'popup_spotw'			=> '200',
'popup_spoth'			=> '500',
'popup_sizew'			=> '383',
'popup_sizeh'			=> '640',
'popup_dt2tm'			=> 'Y',
'popup_sdt'			=> '20160202',
'popup_edt'			=> '20160210',
'popup_stime'			=> '1800',
'popup_etime'			=> '2400',
'popup_type'			=> 'layerMove',
'_et'			=> 'codemirror',
'linkurl'			=> 'main/html.php?htmid=popup/new_year.htm',
);

$design_skin['goods/goods_preview.htm'] = array(
'linkurl'			=> 'goods/goods_view.php',
'text'			=> '상품상세화면(새창)',
'_et'			=> 'codemirror',
);

$design_skin['service/return_policy.htm'] = array(
'linkurl'			=> 'main/html.php?htmid=service/return_policy.htm',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> '배송/교환안내',
'_et'			=> 'codemirror',
);

$design_skin['service/contactus.htm'] = array(
'linkurl'			=> 'main/html.php?htmid=service/contactus.htm',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> 'Contact Us',
'_et'			=> 'codemirror',
);

$design_skin['proc/main/menu_list.htm'] = array(
'linkurl'			=> 'main/html.php?htmid=proc/main/menu_list.htm',
'text'			=> '메뉴리스트',
'_et'			=> 'codemirror',
);

$design_skin['outline/side/category_side.htm'] = array(
'text'			=> '상품분류-좌측',
'linkurl'			=> 'main/html.php?htmid=outline/side/category_side.htm',
);

$design_skin['popup/festool_201607_01.htm'] = array(
'text'			=> '페스툴 이벤트1',
'popup_use'			=> 'Y',
'popup_spotw'			=> '100',
'popup_spoth'			=> '10',
'popup_sizew'			=> '470',
'popup_sizeh'			=> '660',
'popup_type'			=> 'layerMove',
'_et'			=> 'textarea',
'linkurl'			=> 'main/html.php?htmid=popup/festool_201607_01.htm',
);

$design_skin['main/index_internal.htm'] = array(
	'linkurl'			=> 'index.php',
	'outline_header'	=> 'outline/header/main.htm',
	'outline_footer'	=> 'outline/footer/main_footer.htm',
	'text'			=> '쇼핑몰 메인본문',
	'_et'			=> 'codemirror',
);

?>