<?php
// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
// HTTP/1.0
header("Pragma: no-cache");
$key = $_GET['key'];
$domain = str_replace("www.", "", $_SERVER['HTTP_HOST']);
$auth_domain = array($domain,'www.'.$domain);
$referer = parse_url($_SERVER['HTTP_REFERER']);

if($referer['scheme'] == 'http'){
	if( !in_array($referer['host'],$auth_domain) || $key != md5('kwons.co.kr') ) exit;
}else{
	if( !in_array($referer['host'].':'.$referer['port'],$auth_domain) || $key != md5('kwons.co.kr') ) exit;
}

@include dirname(__FILE__) . "/../conf/config.php";
?>
var nowPage = location.pathname; //해당페이지 경로!!
var Move_x = '';
var Move_y = '';
var target_Element = '';
var Org_target_Element = '';
var type =  false;
var returns = '';
var Evgoodcd = ''; //상품 번호!!
var Ev_wishlistNo = ''; //위쉬리스트 번호!!
var EvDellsno = ''; //장바구니 번호
var img_moveCnt = '13';
var Cart_divx = ''; //cart div좌표값
var Cart_divy = ''; //cart div좌표값
var scrollTop = '';
var appname = navigator.appName.charAt(0);
var Div_MovesYn = 'n';  //이미지이동클릭여부
var Cart_delYn = 'n'; //스크롤카드 삭제클릭여부
var Wish_ckYn = 'n'; //스크롤 위시리스트 클릭여부!!
var Move_GoodsType = ''; //드래그상품 타입!!
var Move_Imgsize_off = 60;  //스크롤 상품 기본 이미지사이즈
var Move_Imgsize_on = 50;  //스크롤 상품 클릭 및 이동시 이미지 사이즈
var Cart_position = 0; //장바구니 위치이동 기본값
var wish_position = 0; //상품보관함 위치이동 기본값
var action_Position_x = '';  //이벤트 첫 위치!!
var action_Position_y = '';  //이벤트 첫 위치!!

var cart_divCloseYn = 'n'; //장바구니 on,off 여부
var wish_divCloseYn = 'y'; //상품보관함 on,off 여부

/* 브라우저별 이벤트 처리*/
function addEvent(obj, evType, fn){
	if (obj.addEventListener) {
		obj.addEventListener(evType, fn, false);
		return true;
	} else if (obj.attachEvent) {
		var r = obj.attachEvent("on"+evType, fn);
		return r;
	} else {
		return false;
	}
}

function delEvent(obj, evType, fn){
	if (obj.removeEventListener) {
		obj.removeEventListener(evType, fn, false);
		return true;
	} else if (obj.detachEvent) {
		var r = obj.detachEvent("on"+evType, fn);
		return r;
	} else {
		return false;
	}
}

function getTargetElement(evt)
{
	if ( evt.srcElement ) return target_Element = evt.srcElement; // 익스
	else return target_Element = evt.target; // 익스외
}



	//MOVE
	function Ex_move(event){
		if( appname == "M" ){ //익스
			target_Element = event.srcElement;
		}else{ //익스외
			if (event.which !=1){
				return false;
			}
			else{
				type = true;
				target_Element = event.target;
			}
		}
		type = true;
        Move_x = event.clientX;
        Move_y = event.clientY;
		scrollTop = get_objectTop(target_Element.parentNode);

		if( appname == "M" ) target_Element.onmousemove = move_start;
		else{
			//window.captureEvents(Event.MOUSEMOVE);
			document.onmousemove = Momove_start;
		}
	}

	function move_stop(event){

		delEvent(document, "mousemove", Momove_start);
		type =  false;
		loopStop(); //초기화

		if( appname == "M" ){ //익스
			returns = window.setInterval("loop()",1);
			CartZone_check();
		}else{ //익스외
			var div_wsize = eval(Cart_divx) + eval(document.getElementById('cartID').clientWidth);
			var div_hsize = eval(Cart_divy) + eval(document.getElementById('cartID').clientHeight);
			returns = window.setInterval(loop,1);

			var newDivY = eval(get_objectTop(target_Element)) - eval(10);
			if( (Move_x > Cart_divx && Move_x < div_wsize) && (newDivY > Cart_divy && newDivY < div_hsize) && Cart_delYn == 'n' ){
				Cart_Request(Evgoodcd,'addItem');
			}
		}
	}


	function loop(){
			Nowx = int_n(target_Element.style.left);
			Nowy = int_n(target_Element.style.top);

				if( 0 < Nowx ) target_Element.style.left = Nowx - eval(img_moveCnt);
				if( 0 < Nowy ) target_Element.style.top  = Nowy - eval(img_moveCnt);

				if( 0 > Nowx ) target_Element.style.left = Nowx + eval(img_moveCnt);
				if( 0 > Nowy ) target_Element.style.top  = Nowy + eval(img_moveCnt);

			//초기화
			if( ( Nowx > -img_moveCnt && Nowx  <= 0 ) || ( Nowy > -img_moveCnt && Nowy <= 0 ) ){
				target_Element.style.left = 0;
				target_Element.style.top = 0;
				//클론 이미지 히든처리!!
				if( Cart_delYn == 'n' && Wish_ckYn == 'n'){
					Div_MovesYn = 'n';
					document.getElementById("Clone_OrgimgID").style.display = 'none';
					if( appname == "M" ) Org_target_Element.style.filter = "";
					else Org_target_Element.style.opacity = "";
				}else{ //삭제시 보드값을 초기로 돌림!!
					target_Element.width = Move_Imgsize_off;
					target_Element.height = Move_Imgsize_off;
					target_Element.className = 'Cartz_imgline';
					if( Cart_delYn == 'y' ) Cart_delYn = 'n';
					if( Wish_ckYn == 'y' ) Wish_ckYn = "n";
					document.getElementById('mCart_DelzoneID').className='Delz_move_Off';
				}
				loopStop();
			}
			Move_GoodsType = ''; //드레그 상품 타입 초기화!!
	}

	function loopStop(){
		window.clearInterval(returns);
	}

	//익스 moveing!!
	function move_start(){
		if(type == true){
			var Nowx = event.clientX - Move_x;
			var Nowy = event.clientY - Move_y;
			target_Element.style.left = int_n(target_Element.style.left) + Nowx;
			target_Element.style.top = int_n(target_Element.style.top) + Nowy;
			Move_x = event.clientX;
			Move_y = event.clientY;
			CartZone_check();
			return false;
		}
	}
	//익스외의 moveing!!
	function Momove_start(event){
		if(type == true){
			var Nowx = event.clientX - Move_x;
			var Nowy = event.clientY - Move_y;
			target_Element.style.left = int_n(target_Element.style.left) + Nowx;
			target_Element.style.top = int_n(target_Element.style.top) + Nowy;
			Move_x = event.clientX;
			Move_y = event.clientY;
			CartZone_check();
			return false;
		}
	}

	//레이어 카트,위시리스트,삭제 영역함수!!
	function Move_zone(thisID){

			if( thisID == "cartTableID" ) var add_h = 30;
			else var add_h = 0;

			var this_imgStyle = document.getElementById(thisID);
			var newthisX = eval(get_objectLeft(this_imgStyle));
			var newthisY = eval(get_objectTop(this_imgStyle));
			var this_w = this_imgStyle.clientWidth;
			var this_h = eval(this_imgStyle.clientHeight) + eval(add_h);
			var this_wsize = eval(newthisX) + eval(this_w);
			var this_hsize = eval(newthisY) + eval(this_h)
			var nowthisY = eval(get_objectTop(target_Element)) + eval(40);

			var Return_value = new Array();
			Return_value['newthisX'] = newthisX;
			Return_value['newthisY'] = newthisY;
			Return_value['this_wsize'] = this_wsize;
			Return_value['this_hsize'] = this_hsize;
			Return_value['nowthisY'] = nowthisY;
			return Return_value;
	}

	//이미지 드래그시 카드 div 영역검사!!
	function CartZone_check(){

		//위시리스트 영역 start -----
		var wis_imgStyle = document.getElementById('wishlist_ID');
				var W_mTZone = Move_zone('wishlist_ID'); //삭제영역불러오기!!

				if(type == true){
					if( Move_x > W_mTZone['newthisX'] && Move_x < W_mTZone['this_wsize'] && W_mTZone['nowthisY'] > W_mTZone['newthisY'] && W_mTZone['nowthisY'] < W_mTZone['this_hsize'] && Wish_ckYn == 'n' ) wis_imgStyle.className='Wishz_move_On';
					else wis_imgStyle.className='Wishz_move_Off';
				}else{
					if( Move_x > W_mTZone['newthisX'] && Move_x < W_mTZone['this_wsize'] && W_mTZone['nowthisY'] > W_mTZone['newthisY'] && W_mTZone['nowthisY'] < W_mTZone['this_hsize'] && Wish_ckYn == 'n' ){
					if( wish_divCloseYn == 'n' ) Wishlist_Request(Evgoodcd,'addItem');
					else{ alert('상품보관함영역이 Close 상태입니다.'); }
					wis_imgStyle.className='Wishz_move_Off';
					}else wis_imgStyle.className='Wishz_move_Off';
				}
		//위시리스트 영역 end  -----

		var CartOrgTableID = document.getElementById('cartTableID');
		var Cart_imgStyle = document.getElementById('moveCartID');
		var C_mTZone = Move_zone('moveCartID'); //카트영역불러오기!!

				if(type == true){
					if( Move_x > C_mTZone['newthisX'] && Move_x < C_mTZone['this_wsize'] && C_mTZone['nowthisY'] > C_mTZone['newthisY'] && C_mTZone['nowthisY'] < C_mTZone['this_hsize'] && Cart_delYn == 'n' ){
						Cart_imgStyle.className='Cartz_move_On';
						}
					else {
					Cart_imgStyle.className='Cartz_move_Off';
					}
				}else{
					if( Move_x > C_mTZone['newthisX'] && Move_x < C_mTZone['this_wsize'] && C_mTZone['nowthisY'] > C_mTZone['newthisY'] && C_mTZone['nowthisY'] < C_mTZone['this_hsize'] && Cart_delYn == 'n' ){
						if( cart_divCloseYn == 'n' ) Cart_Request(Evgoodcd,'addItem');
						Cart_imgStyle.className='Cartz_move_Off';
					}
					else Cart_imgStyle.className='Cartz_move_Off';
				}
		//제품 카트에 넣을경우 end -----

		//카트의 제품 삭제시 start -----
				var Del_imgStyle = document.getElementById('mCart_DelzoneID');
				var D_mTZone = Move_zone('mCart_DelzoneID'); //삭제영역불러오기!!

				if(type == true){
					if( Move_x > D_mTZone['newthisX'] && Move_x < D_mTZone['this_wsize'] && D_mTZone['nowthisY'] > D_mTZone['newthisY'] && D_mTZone['nowthisY'] < D_mTZone['this_hsize'] ) Del_imgStyle.className='Delz_move_On';
					else Del_imgStyle.className='Delz_move_Off';
				}else{
					if( Move_x > D_mTZone['newthisX'] && Move_x < D_mTZone['this_wsize'] && D_mTZone['nowthisY'] > D_mTZone['newthisY'] && D_mTZone['nowthisY'] < D_mTZone['this_hsize'] ){
						if( Move_GoodsType == "cart" ) Cart_Request(Evgoodcd,'delItem');
						if( Move_GoodsType == "wishlist" ) Wishlist_Request(Evgoodcd,'delItem');
						//if( Move_GoodsType == "list" ){ alert('리스트의 상품은 삭제할 수 없습니다.');Del_imgStyle.className='Delz_move_Off'; }
					}else Del_imgStyle.className='Delz_move_Off';
				}
		//카트의 제품 삭제시 end  -----
	}



	function int_n(cnt){
		if( isNaN(parseInt(cnt)) == true ) var re_cnt = 0;
		else var re_cnt = parseInt(cnt);
		return re_cnt;
	}

	//카트 div 위치값!!
	function Cart_divxy(thisdiv_ID){
		function get_objectTop(obj){
		if (obj.offsetParent == document.body) return obj.offsetTop;
		else return obj.offsetTop + get_objectTop(obj.offsetParent);
		}

		function get_objectLeft(obj){
			if (obj.offsetParent == document.body) return obj.offsetLeft;
			else return obj.offsetLeft + get_objectLeft(obj.offsetParent);
		}

		var obj = document.getElementById(thisdiv_ID);
		var x = get_objectLeft(obj);
		var y = get_objectTop(obj);
		return x+'/'+y
	}


	//카트 등록하기
	function Cart_Request(goodsno,type){

		//카트에서 카트등록못하게...
		if( Move_GoodsType == "" && type == "addItem" ){
			alert('이미등록된 상품입니다.');return;
		}

		if( type == "delItem" ){
			if( Move_GoodsType == 'list' && Cart_delYn == 'n' && Wish_ckYn == 'n' ){
				alert('리스트상품은 삭제하실 수 없습니다.');
				document.getElementById('mCart_DelzoneID').style.backgroundColor = '#FFFFFF';
				return;
			}
			var idx = EvDellsno;
		}

		//테이블 삭제하기!!
		if( Move_GoodsType == "wishlist" && type == "delItem" ){
			Table_close('wishlist_ID');
		}else{
			Table_close('moveCartID');
		}

		//로딩이미지 출력!!
		document.getElementById('cart_LoadingID').style.display = 'block';

		var ajax = new Ajax.Request(
			"<?=$cfg['rootDir']?>/lib/cartMove_proc.php?goodsno="+goodsno+"&mode="+type+"&idx="+idx+"&Move_GoodsType="+Move_GoodsType+"&dummy="+new Date().getTime(),
			{
			method : 'get',
			onComplete : Cart_setResponse
			}
		);
	}

	//위시리스트 등록하기
	function Wishlist_Request(goodsno,type){

		Table_close('wishlist_ID');

		//로딩이미지 출력!!
		if( wish_divCloseYn == 'n' ){
			var wish_loadingImg = document.getElementById('wish_LoadingID');
			wish_loadingImg.style.display = 'block';
		}

		var ajax = new Ajax.Request(
			"<?=$cfg['rootDir']?>/lib/wishlistMove_proc.php?goodsno="+goodsno+"&Ev_wishlistNo="+Ev_wishlistNo+"&mode="+type+"&Move_GoodsType="+Move_GoodsType+"&dummy="+new Date().getTime(),
			{
			method : 'get',
			onComplete : Wishlist_setResponse
			}
		);
	}

	//위시리스트 리턴값!!
	function Wishlist_setResponse(req){
		//alert(req.responseText);
		var re_ajax = eval( '(' + req.responseText + ')' );

		//로딩이미지 삭제!!
		document.getElementById('wish_LoadingID').style.display = 'none';

		left_scroll_Text(); //상품보관함 이미지설명 설정부르기

		//상품보관함 페이지일경우! 리로드!!
		if( re_ajax.Remode == "delItem" || re_ajax.Remode == "addItem" ){
			if( nowPage == '<?=$cfg['rootDir']?>/mypage/mypage_wishlist.php' ) document.location.href = nowPage;
		}

		if( re_ajax._inarray['sessYn'] == "n" ){
			alert('로그인을 하셔야 위시리스트에 등록됩니다.');
		}

		if( re_ajax.Remode == "addItem" && re_ajax._inarray['_inYn'] == "n" ){
			alert('상품보관함에 등록되어진 상품입니다.');
		}

		if( re_ajax.Remode == "delItem" ) Wishlist_Request('','wishlist_view');
		else{
			if( re_ajax._inarray['sessYn'] == "y" ){
				wishlist_listing(re_ajax.wishlist);
			}
		}


	}

	//카트 리턴값
	function Cart_setResponse(req){
		//alert(req.responseText);
		var re_ajax = eval( '(' + req.responseText + ')' );

		//로딩이미지 삭제!!
		document.getElementById('cart_LoadingID').style.display = 'none';

		if( re_ajax.Remode == "Not_ea1" ){
			alert('상품의 잔여 재고가 존재하지 않습니다.');
			Cart_Request('','Cart_view');
			return;
		}
		if( re_ajax.Remode == "Not_ea2" ){
			alert('품절된 상품입니다.');
			Cart_Request('','Cart_view');
			return;
		}
		if( re_ajax.Remode == "Not_ea3" ){
			alert('판매되지 않는 상품입니다.');
			Cart_Request('','Cart_view');
			return;
		}

		left_scroll_Text(); //상품보관함 이미지설명 설정부르기

		//장바구니 페이지일경우!! 리로드!!
		if( re_ajax.Remode == "delItem" || re_ajax.Remode == "addItem" || re_ajax.Remode == "modItem" ){
			if( nowPage == '<?=$cfg['rootDir']?>/goods/goods_cart.php' ) document.location.href = nowPage;
		}

		//스크롤카트 삭제일경우!!
		if( re_ajax.Remode == "delItem" ) Cart_Request('','Cart_view');
		else{
			Cart_listing(re_ajax.item,re_ajax.goodsprice,re_ajax.Remode); //삭제가 아닐경우!!
		}
	}

	//카트 table 생성 ---!!
	var Cart_lenTo = 0; //장바구니 총 제품 수
	var now_Request = '';
	var now_totalprice = '';
	var now_mode = '';

	var goodsimg;
	var regexp_goodsimg_url = /^http(s)?:\/\/.+$/;

	function Cart_listing(Request,totalprice,mode){

		if( !Request && cart_divCloseYn == "n" ){
			document.getElementById("cart_scroll_txtbgID").style.display = "block";
			Cart_lenTo = 0;
		}

		//정보유지!!
		now_Request = Request;
		now_totalprice = totalprice;
		now_mode =  mode;

		Totalprice_view(totalprice); //총금액 호출!!
		var data_len = Request.length;
		Cart_lenTo = data_len;
		var Cart_view = document.getElementById('moveCartID');
		var la_Cnt = 0;

		Lm_move_basic(); //장바구니 상하이동버튼 활성화!!
		left_scroll_Text(); //상품보관함 이미지설명 설정부르기

		if( data_len > 0 ){
			Cart_view.style.height = '';
			for ( n = Cart_position; n < data_len; n++ ){
				if( la_Cnt < 3 ){

					// 이미지
					goodsimg  = (regexp_goodsimg_url.test(Request[n]['img'])) ? Request[n]['img'] : "<?=$cfg['rootDir']?>/data/goods/" + Request[n]['img'];

					// 제품명!!
					var cart_title_str = Request[n]['goodsnm'].substr(0,6) ;
					var view_data = Cart_view.innerHTML+"<div><div align='center' style='padding-top:5px;' style='width:"+ Move_Imgsize_off +";height:"+ Move_Imgsize_off +";'><img name='"+n+'/'+Request[n]['goodsno']+"' src='"+goodsimg+"' width='"+Move_Imgsize_off+"' height='"+Move_Imgsize_off+"' class='Cartz_imgline' onmousedown='Div_type(\"cart\");Cart_delclick(event);Ex_move(event);' onmouseup='move_stop(event);' style='position: relative; cursor:move' onmouseover='startMove(\"cart_"+n+"\",\"L\");'  onmouseout='startMove(\"cart_"+n+"\",\"R\");' id='cart_"+n+"'></div></div><div style='padding-top:5px;padding-left:5px;'>"+ cart_title_str +"<br>"+formatNumber(Request[n]['price'].toString())+"</div>";
					Cart_view.innerHTML = view_data;
					la_Cnt++;
				}
			}
		} else {
			Cart_view.style.height = '92px';
		} //if end
	}
	//카트 table end --- !!

	//스크롤 위시리스트 생성!! start ---
	var wish_lenTo = 0; //장바구니 총 제품 수
	var wish_new_Data = '';
	function wishlist_listing(data){

		if( !data && wish_divCloseYn == "n" ){
			document.getElementById("wish_scroll_txtbgID").style.display = "block";
			wish_lenTo = 0;
		}

		//정보유지!!
		wish_new_Data = data;

		var data_len = data.length;

		wish_lenTo = data_len;
		var data_view = document.getElementById('wishlist_ID');
		var la_Cnt = 0;

		Lm_move_basic(); //상품보관함 상하이동버튼 활성화!!
		left_scroll_Text(); //상품보관함 이미지설명 설정부르기

		if( data_len > 0 ){
			data_view.style.height = '';
			for ( n = wish_position; n < data_len; n++ ){
				if( la_Cnt < 3 ){
					// 제품명!!
					var wish_title_str = data[n]['goodsnm'].substr(0,6) ;
					var view_data = data_view.innerHTML+"<div><div align='center' style='padding-top:5px;' style='width:"+ Move_Imgsize_off +";height:"+ Move_Imgsize_off +";'><img name='"+data[n]['sno']+'/'+data[n]['goodsno']+"' src='<?=$cfg['rootDir']?>/data/goods/"+data[n]['img_s']+"' width='"+Move_Imgsize_off+"' height='"+Move_Imgsize_off+"' class='Cartz_imgline' onmousedown='Wish_click(event);Ex_move(event);' onmouseup='move_stop(event);' style='position: relative; cursor:move' onmouseover='startMove(\"wish_"+n+"\",\"L\");'  onmouseout='startMove(\"wish_"+n+"\",\"R\");' id='wish_"+n+"'></div></div><div style='padding-top:5px;padding-left:5px;'>"+wish_title_str+"<br>"+formatNumber(data[n]['price'].toString())+"</div>";
					data_view.innerHTML = view_data;
					la_Cnt++;
				}
			}
		} else {
			data_view.style.height = '94px';
		}
	}
	//스크롤 위시리스트 생성!! end  ---

function Totalprice_view(price){
	if( price > 0 ) var totalprice = formatNumber(price.toString());
	else totalprice = 0;
	document.getElementById('mCart_totalpriceID').innerHTML = "<div style='height:25;font-size:11px;font-family:돋움, 굴림;padding-top:5px;' align='center'>총<b>"+ totalprice +"</b></div>";
}


//테이블의 tr 삭제하기
function Table_close(tableID){
	document.getElementById(tableID).style.height = '94px';
	document.getElementById(tableID).innerHTML = "<div></div>";
}

//스크롤카트 삭제클릭
//클릭색상 수정시 반드시 if문의 색상도 수정해주셔야 합니다..
var Del_srcElement = '';
function Cart_delclick(event){
	if( appname == "M" ) var click_en = event.srcElement;
	else var click_en = event.target;
	Del_srcElement = click_en;

	if( Cart_delYn == 'n' ){
		Cart_delYn = 'y';
		Div_type('cart'); //드래그시 드래그 상품정의 호출!!
		Del_goodcd = Del_srcElement.name.split('/');
		EvDellsno = Del_goodcd[0]; //삭제시 장바구니순서값
		Evgoodcd = Del_goodcd[1]; //삭제시 장바구니순서값
		Del_srcElement.width = Move_Imgsize_on;
		Del_srcElement.height = Move_Imgsize_on;
		Del_srcElement.className = 'Cartz_click';
		document.getElementById('S_infoID').style.display = "none"; //스크롤 장바구니 정보 삭제
	}
}

//스크롤 위시리스트클릭시!!
var Ck_wishlist_Elem = '';
function Wish_click(event){

	if( appname == "M" ) var click_en = event.srcElement;
	else var click_en = event.target;

	Ck_wishlist_Elem = click_en;
	if( Wish_ckYn == 'n' ){
		Wish_ckYn = 'y';
		Div_type('wishlist'); //드래그시 드래그 상품정의 호출!!
		wish_sp = Ck_wishlist_Elem.name.split('/'); //위시리스트 클릭시 위시리스트 sno값!
		Ev_wishlistNo = wish_sp['0']; //위시리스트 번호
		Evgoodcd = wish_sp['1']; //상품번호
		Ck_wishlist_Elem.width = Move_Imgsize_on;
		Ck_wishlist_Elem.height = Move_Imgsize_on;
		Ck_wishlist_Elem.className = 'Wishz_click';
		document.getElementById('S_infoID').style.display = "none"; //스크롤 위시리스트 정보 삭제
	}
}


/*
//스크롤카트 삭제
function Cart_del(){
	var idx = Del_srcElement.name;
	if( Cart_delYn == 'y' && Del_srcElement.style.border == "#fe6b49 1px solid" && event.keyCode == '46'){
		if(confirm('삭제하시면 장바구니에서 완전히 삭제됩니다.\n\n삭제하시겠습니까?')){
			Cart_Request(idx,'delItem');
			Cart_delYn = 'n';
		}else return;
	}
}
//document.onkeydown = Cart_del;
*/

//금액 표시기 , 구분
function formatNumber(str){
	 strarr=str.split(".");
	 s=strarr[0];

	 s=s.replace(/\D/g,"");
	 l=s.length-3;
	 while(l>0) {
	  s=s.substr(0,l)+","+s.substr(l);
	  l-=3;
	 }

	 strarr[0]=s;
	 s=strarr.join(".");
	 return s;
}




function Div_clone(event){
	Div_MovesYn = 'y';
	if( appname == "M" ){ //익스
		if( Org_target_Element != "" && Org_target_Element != event.srcElement ) Div_cloneClose(event); //이벤트 호출 후 다른 이벤트 호출시 이전이벤트 Close!!

		Evgoodcd = event.srcElement.name; //클릭시 상품 goodcd
		Org_target_Element = event.srcElement; //이벤트값
		var Div_timg = event.srcElement.src;
		Org_target_Element.style.filter = "Alpha(opacity=20)"; //원본 이미지 필터처리
	}else{
		if( Org_target_Element != "" && Org_target_Element != event.target ) Div_cloneClose(event); //이벤트 호출 후 다른 이벤트 호출시 이전이벤트 Close!!
		Evgoodcd = event.target.name; //클릭시 상품 goodcd
		Org_target_Element = event.target; //이벤트값
		var Div_timg = event.target.src;
		Org_target_Element.style.opacity = "0.5"; //원본 이미지 필터처리
	}

	//cart div좌표값
	var Cart_divSize = Cart_divxy('cartID').split('/');
	Cart_divx = Cart_divSize['0'];
	Cart_divy = Cart_divSize['1'];

	//복사 이미지 위치!!
	var Clone_divx=0;
	var Clone_divy=0;
	Clone_divx = get_objectLeft(Org_target_Element);
	Clone_divy = get_objectTop(Org_target_Element);
	action_Position_x = Clone_divx;
	action_Position_y = Clone_divy;

	//복사 div 스타일!!
	document.getElementById("Clone_OrgimgID").style.display = 'block';
	document.getElementById("Clone_OrgimgID").style.left = Clone_divx;
	document.getElementById("Clone_OrgimgID").style.top = Clone_divy;
	document.getElementById("Clone_OrgimgID").innerHTML = "<img src='"+Div_timg+"' border='0' width='80;height='80' onmousedown='Ex_move(event);' onmouseup='move_stop(event);' style='position: relative;' class='Move_listClone'>";
}

function Div_cloneClose(event){
		if( appname == "M" ) Org_target_Element.style.filter = "";
		else Org_target_Element.style.opacity = "";
		Div_MovesYn = 'n';
		Org_target_Element = '';
		Div_timg = '';
		document.getElementById("Clone_OrgimgID").style.display = 'none';
}

//드레그 상품의 타입정의!!
function Div_type(type){
	if( !type ) type = 'list';
	Move_GoodsType = type;
}

//스크롤바 제품 위치변경
function Lm_move(opt,type){
	//장바구니
	if( type == "cart" ){
		if( opt == "T" && Cart_position > 0 ){
			Cart_position = eval(Cart_position) - eval(1);
			Table_close('moveCartID');
			Cart_listing(now_Request,now_totalprice,now_mode);
		}

		if( opt == "B" && Cart_lenTo > ( Cart_position + 3 )){
			Cart_position = eval(Cart_position) + eval(1);
			Table_close('moveCartID');
			Cart_listing(now_Request,now_totalprice,now_mode);
		}
		Lm_move_basic();
	}

	//상품보관함
	if( type == "wishlist" ){
		if( opt == "T" && wish_position > 0 ){
			wish_position = eval(wish_position) - eval(1);
			Table_close('wishlist_ID');
			wishlist_listing(wish_new_Data);
		}

		if( opt == "B" && wish_lenTo > ( wish_position + 3 )){
			wish_position = eval(wish_position) + eval(1);
			Table_close('wishlist_ID');
			wishlist_listing(wish_new_Data);
		}
		Lm_move_basic();
	}

}

//스크롤바 제품 위치변경 기본바 이미지
function Lm_move_basic(){
	//장바구니 up이미지
	if( Cart_position <= 0 ){
		document.getElementById('cart_upbarimgID').src = "../data/skin/"+this_tplSkin+"/img/common/scroll_bu_up_back.gif";
		document.getElementById('cart_upbarimgID').style.cursor = 'default';
	}else{
		document.getElementById('cart_upbarimgID').src = "../data/skin/"+this_tplSkin+"/img/common/scroll_bu_up.gif";
		document.getElementById('cart_upbarimgID').style.cursor = 'pointer';
	}
	//장바구니 down이미지
	if( Cart_lenTo <= ( Cart_position + 3 ) ){
		document.getElementById('cart_downbarimgID').src = "../data/skin/"+this_tplSkin+"/img/common/scroll_bu_down_back.gif";
		document.getElementById('cart_downbarimgID').style.cursor = 'default';
	}else{
		document.getElementById('cart_downbarimgID').src = "../data/skin/"+this_tplSkin+"/img/common/scroll_bu_down.gif";
		document.getElementById('cart_downbarimgID').style.cursor = 'pointer';
	}

	//위시리스트 up이미지
	if( wish_position <= 0 ){
		document.getElementById('wish_upbarimgID').src = "../data/skin/"+this_tplSkin+"/img/common/scroll_bu_up_back.gif";
		document.getElementById('wish_upbarimgID').style.cursor = 'default';
	}else{
		document.getElementById('wish_upbarimgID').src = "../data/skin/"+this_tplSkin+"/img/common/scroll_bu_up.gif";
		document.getElementById('wish_upbarimgID').style.cursor = 'pointer';
	}
	//위시리스트 down이미지
	if( wish_lenTo <= ( wish_position + 3 ) ){
		document.getElementById('wish_downbarimgID').src = "../data/skin/"+this_tplSkin+"/img/common/scroll_bu_down_back.gif";
		document.getElementById('wish_downbarimgID').style.cursor = 'default';
	}else{
		document.getElementById('wish_downbarimgID').src = "../data/skin/"+this_tplSkin+"/img/common/scroll_bu_down.gif";
		document.getElementById('wish_downbarimgID').style.cursor = 'pointer';
	}
}



//스크롤바 상품 정보 ------------------ start
var xx;
var moveType = 'L';
var target_x = 0;
var target_y = 0;
var thisplay = 'n';
var div_speed = 5; //스크롤 속도!!
function info_move()
{
	thisplay = 'y';
	var obj = document.getElementById('S_info_viewID');
	var objLeftSize = obj.clientWidth;

	if( div_speed != '5' ) div_speed = '5';

	if( moveType == "ing" ) obj.style.left = 0;

	if( moveType == 'L' ){
		if( int_n(obj.style.left) <= 0 ) stopMove();
		else obj.style.left = int_n(obj.style.left) - div_speed;
	}
	if( moveType == 'R' ){
		if( int_n(obj.style.left) > objLeftSize ){
			stopMove();
			document.getElementById('S_infoID').style.display = "none";
		}else obj.style.left = int_n(obj.style.left) + div_speed;
	}
}

var now_thisID = '';
var rmove_goodsno = 0;
function startMove(thisID,opt){

	if( Div_MovesYn == 'y' || Cart_delYn == 'y' || Wish_ckYn == 'y' ) return;

	if( !thisID ) thisID = now_thisID;
	else{ now_thisID = thisID; }

	moveType = opt;
	var thisdiv_ID = document.getElementById(thisID);
	target_x = eval( get_objectLeft(thisdiv_ID) ) - eval(int_n(document.getElementById('S_infoID').style.width));
	target_y = get_objectTop(thisdiv_ID);

	document.getElementById('S_infoID').style.display = "block";
	document.getElementById('S_infoID').style.left = target_x;
	document.getElementById('S_infoID').style.top = target_y;

	//제품 정보 등록!! - start
	var g_No = thisID.split('_');
	if( g_No[0] == 'cart' ){
		if( rmove_goodsno != 0 && rmove_goodsno != now_Request[g_No['1']]['goodsno'] ) document.getElementById('S_info_viewID').style.left = '150';
		rmove_goodsno = now_Request[g_No['1']]['goodsno'];
		var title_str = now_Request[g_No['1']]['goodsnm'].substr(0,10) ;
		document.getElementById('SgoodnmID').innerHTML =  '<b>' + title_str + '</b>';
		document.getElementById('SpriceID').innerHTML =  formatNumber(now_Request[g_No['1']]['price'].toString()) + '원';
		document.getElementById('SeaID').innerHTML =  formatNumber(now_Request[g_No['1']]['ea'].toString()) + '개';
	}
	if( g_No[0] == 'wish' ){
		if( rmove_goodsno != 0 && rmove_goodsno != wish_new_Data[g_No['1']]['goodsno'] ) document.getElementById('S_info_viewID').style.left = '150';
		rmove_goodsno = wish_new_Data[g_No['1']]['goodsno'];
		var title_str = wish_new_Data[g_No['1']]['goodsnm'].substr(0,10) ;
		document.getElementById('SgoodnmID').innerHTML =  '<b>' + title_str + '</b>';
		document.getElementById('SpriceID').innerHTML =  formatNumber(wish_new_Data[g_No['1']]['price'].toString()) + '원';
		document.getElementById('SeaID').innerHTML =  '1개';
	}
	//제품 정보 등록!! - end



	if( thisplay == 'n' && opt != 'ing' ) xx = window.setInterval("info_move()",10);
}

function stopMove(){
	thisplay = 'n';
	window.clearInterval(xx);
}
//스크롤바 상품 정보 ------------------ end


//왼쪽 스크롤 상품설명이미지 설졍
function left_scroll_Text(){

	if( cart_divCloseYn == "n" ){

		if( Cart_lenTo > 0 ) document.getElementById("cart_scroll_txtbgID").style.display = "none";
		else{
			document.getElementById("cart_scroll_txtbgID").style.display = "block";
		}
	}
	else{
		document.getElementById("cart_scroll_txtbgID").style.display = "none";
	}

	if( wish_divCloseYn == "n" ){
		if( wish_lenTo > 0 ) document.getElementById("wish_scroll_txtbgID").style.display = "none";
		else{
			document.getElementById("wish_scroll_txtbgID").style.display = "block";
		}

	}else{
		document.getElementById("wish_scroll_txtbgID").style.display = "none";
	}
}