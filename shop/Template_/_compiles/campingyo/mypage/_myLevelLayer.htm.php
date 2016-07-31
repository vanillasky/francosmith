<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/mypage/_myLevelLayer.htm 000002279 */ ?>
<div id="MyLevelLayerBox" style="z-index:10000;position:absolute;width:187px;height:220px;display:block;text-align:center;">

<div><img src="/shop/data/skin/campingyo/img/common/h_member_level.gif"></div>
<div style="padding:0 25px 0 25px;width:296px;background:url(/shop/data/skin/campingyo/img/common/bg_member_level.gif) repeat-y top left;text-align:left;line-height:140%;font-size:11px;font-family:돋움; color:#464646; letter-spacing:-1">
	<strong><?php echo $TPL_VAR["mygroupinfo"]["name"]?></strong>회원님<br>
	<strong><?php echo $GLOBALS["cfg"]["shopName"]?></strong>을 사랑해 주셔서 감사합니다.<br>
	회원등급이 <strong><?php echo $TPL_VAR["mygroupinfo"]["previous_grpnm"]?></strong>에서 <strong><?php echo $TPL_VAR["mygroupinfo"]["current_grpnm"]?></strong>로 변경되었습니다.
</div>
<div><img src="/shop/data/skin/campingyo/img/common/foot_member_level.gif"></div>
<div style="width:296px;border-left:3px solid #DDDDDD;border-right:3px solid #DDDDDD;background:#ffffff;">
<a href="javascript:void(0);" onClick="document.getElementById('MyLevelLayerBox').style.display='none'"><img src="/shop/data/skin/campingyo/img/common/btn_member_level.gif"></a>
</div>
<div><img src="/shop/data/skin/campingyo/img/common/foot01_member_level.gif"></div>
</div>

<script>


function fnMyLevelLayerBoxPosition(w,h) {	// 가로, 세로

	var _doc_size = {
		width : document.body.scrollWidth || document.documentElement.scrollWidth,
		height: document.body.scrollHeight || document.documentElement.scrollHeight
	}


	var _win_size = {
		width : window.innerWidth	|| (window.document.documentElement.clientWidth	|| window.document.body.clientWidth),
		height: window.innerHeight	|| (window.document.documentElement.clientHeight|| window.document.body.clientHeight)
	}

	with (document.getElementById('MyLevelLayerBox').style) {
		position = "absolute";
		width = w + 'px';
		height = h + 'px';
		zIndex = 10000;
		left = (_win_size.width + w) / 2 - w + 'px';
		top = ((_win_size.height + h) / 2 - h) + document.body.scrollTop + 'px';
		display = "block";
	};
}

fnMyLevelLayerBoxPosition(296, 200);
</script>