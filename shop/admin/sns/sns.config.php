<?
$location = "SNS 서비스 > SNS 공유하기 설정관리";
include "../_header.php";
$cfgfile = "../../conf/sns.cfg.php";
if(file_exists($cfgfile)) require $cfgfile;

if (!$snsCfg['msg_kakao_shopnm']) $snsCfg['msg_kakao_shopnm'] = '{shopnm}';
if (!$snsCfg['msg_kakao_goodsnm']) $snsCfg['msg_kakao_goodsnm'] = '{goodsnm}';
if (!$snsCfg['msg_kakao_goodsurl']) $snsCfg['msg_kakao_goodsurl'] = '{goodsurl}';
if (!$snsCfg['msg_kakaoStory_shopnm']) $snsCfg['msg_kakaoStory_shopnm'] = '{shopnm}';
if (!$snsCfg['msg_kakaoStory_goodsnm']) $snsCfg['msg_kakaoStory_goodsnm'] = '{goodsnm}';
if (!$snsCfg['msg_kakaoStory_goodsurl']) $snsCfg['msg_kakaoStory_goodsurl'] = '{goodsurl}';
if (!$snsCfg['msg_twitter']) $snsCfg['msg_twitter'] = '[{shopnm}] {goodsnm}'.chr(10).'{goodsurl}';
if (!$snsCfg['msg_facebook']) $snsCfg['msg_facebook'] = '[{shopnm}] {goodsnm}';
if (!$snsCfg['msg_pinterest']) $snsCfg['msg_pinterest'] = '[{shopnm}] {goodsnm}';
if (!$snsCfg['useBtn']) $snsCfg['useBtn'] = 'n';
$checked['useBtn'][$snsCfg['useBtn']] = 'checked';

if(!$snsCfg['use_kakao'] || $snsCfg['use_kakao'] == 'y') $checked['use_kakao']['y'] = 'checked';
if(!$snsCfg['use_kakaoStory'] || $snsCfg['use_kakaoStory'] == 'y') $checked['use_kakaoStory']['y'] = 'checked';
if(!$snsCfg['use_twitter'] || $snsCfg['use_twitter'] == 'y') $checked['use_twitter']['y'] = 'checked';
if(!$snsCfg['use_facebook'] || $snsCfg['use_facebook'] == 'y') $checked['use_facebook']['y'] = 'checked';
if($snsCfg['use_pinterest'] == 'y') $checked['use_pinterest']['y'] = 'checked';
if(!$snsCfg['use_urlcopy'] || $snsCfg['use_urlcopy'] == 'y') $checked['use_urlcopy']['y'] = 'checked';

?>
<script type="text/javascript">

function copy_txt(val){
	window.clipboardData.setData('Text', val);
}

function enableForm(val) {
	document.getElementById("msg_twitter").disabled = !val;
	document.getElementById("msg_facebook").disabled = !val;
	document.getElementById("msg_pinterest").disabled = !val;
}

function display_msg(mode) {
	var shopnm = "쇼핑몰명";
	var goodsnm = "상품이름";
	var goodsurl = "http://test.com";

	if(mode == "kakao") {
		var res = "[" + document.getElementsByName("msg_kakao_shopnm")[0].value + "]<br />";
		res += document.getElementsByName("msg_kakao_goodsnm")[0].value + "<br />";
		res += document.getElementsByName("msg_kakao_goodsurl")[0].value;
		res = res.replace(/{shopnm}/g, shopnm);
		res = res.replace(/{goodsnm}/g, goodsnm);
		res = res.replace(/{goodsurl}/g, goodsurl);
	} else if (mode == "kakaoStory"){
		var res = document.getElementsByName("msg_kakaoStory_goodsurl")[0].value + " <br />";
		res += document.getElementsByName("msg_kakaoStory_goodsnm")[0].value + " <br />";
		res += document.getElementsByName("msg_kakaoStory_shopnm")[0].value;
		
		res = res.replace(/{shopnm}/g, shopnm);
		res = res.replace(/{goodsnm}/g, goodsnm);
		res = res.replace(/{goodsurl}/g, goodsurl);
	} else {
		var res = document.getElementsByName("msg_"+mode)[0].value;
		res = res.replace(/\n/g, "<br />");
		res = res.replace(/{shopnm}/g, shopnm);
		res = res.replace(/{goodsnm}/g, goodsnm);
		res = res.replace(/{goodsurl}/g, goodsurl);
	}

	switch(mode) {
		case 'kakao' : {
			res = res.replace(/(http:\/\/[^\s]*)/gi, "<a href='$1' target='_blank'>$1</a>");
			document.getElementById("dp_msg_kakao").innerHTML = res;
			break;
		}
		case 'kakaoStory' : {
			res = res.replace(/(http:\/\/[^\s]*)/gi, "<a href='$1' target='_blank'>$1</a>");
			document.getElementById("dp_msg_kakaoStory").innerHTML = res;
			break;
		}
		case 'twitter' : {
			res = res.replace(/(http:\/\/[^\s]*)/gi, "<a href='$1' target='_blank'>$1</a>");
			document.getElementById("dp_msg_twitter").innerHTML = res;
			break;
		}
		case 'facebook' : {
			res = res.replace(/\"([^\"]*)\":(http:\/\/[^\s]*)/gi, "<a href='$2' target='_blank'>$1</a>");
			document.getElementById("dp_msg_facebook").innerHTML = res;
			break;
		}
		case 'pinterest' : {
			res = res.replace(/\"([^\"]*)\":(http:\/\/[^\s]*)/gi, "<a href='$2' target='_blank'>$1</a>");
			document.getElementById("dp_msg_pinterest").innerHTML = res;
			break;
		}
	}
}

</script>
<div style="width:800">
<form name="frm" method="post" action="sns.indb.php" target="ifrmHidden">
<div class="title title_top">SNS 공유하기 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=14')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="30">사용설정</td>
	<td class="noline">
		<label><input type="radio" name="useBtn" value="y" <?=$checked['useBtn']['y']?> onclick="enableForm(true)" />사용함</label>
		<label><input type="radio" name="useBtn" value="n" <?=$checked['useBtn']['n']?> onclick="enableForm(false)" />사용안함</label>
	</td>
</tr>
<tr>
	<td height="30">치환코드</td>
	<td class="noline">
		<div style="padding:10px 0px;">
			<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="95%">
			<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
			<tr>
				<td>쇼핑몰명</td><td>{shopnm}</td>
				<td>상품명</td><td>{goodsnm}</td>
				<td>상품url</td><td>{goodsurl}</td>
			</tr>
			</table>
			<div style="padding-top:5px;" class="small1 extext">
				<div>기본문구 설정 치환코드는 SNS사이트로 전송되는 상품정보 문구입니다.</div>
				<div>기본으로 쇼핑몰명, 상품명, 상품URL정보가 포스팅 되도록 설정되어 있습니다.</div>
				<div>변경 시에는 미리보기를 통해 먼저 문구를 확인해 주세요.</div>
			</div>
		</div>
	</td>
</tr>
<tr>
	<td height="30">기본문구 설정</td>
	<td class="noline">
		<div>
			<div style="margin:10px 5px 2px 5px;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr valign="bottom">
					<td align="left"><img src="../img/icon_arrow.gif" /> <b>카카오스토리</b></td>
					<td align="right" style="padding-right:35px;"><input type="checkbox" name="use_kakaoStory" id="use_kakaoStory" value="y" <?=$checked['use_kakaoStory']['y']?> /> <label for="use_kakaoStory">출력여부</label></td>
				</tr>
				</table>
			</div>
			<div style="vertical-align:middle">
				<table width="95%" border="1" bordercolor="#e1e1e1" style="border-collapse:collapse">
				<col class="cellC"><col class="cellL">
				<tr>
					<td>상품URL</td>
					<td><input type="text" name="msg_kakaoStory_goodsurl" id="msg_kakaoStory_goodsurl" value="<?=$snsCfg['msg_kakaoStory_goodsurl']?>" style="border:1px solid #CCCCCC; width:95%;" class="line" /></td>
				</tr>
				<tr>
					<td>상품이름</td>
					<td><input type="text" name="msg_kakaoStory_goodsnm" id="msg_kakaoStory_goodsnm" value="<?=$snsCfg['msg_kakaoStory_goodsnm']?>" style="border:1px solid #CCCCCC; width:95%;" class="line" /></td>
				</tr>
				<tr>
					<td>쇼핑몰명</td>
					<td><input type="text" name="msg_kakaoStory_shopnm" id="msg_kakaoStory_shopnm" value="<?=$snsCfg['msg_kakaoStory_shopnm']?>" style="border:1px solid #CCCCCC; width:95%;" class="line" /></td>
				</tr>			
				</table>
			</div>
			<div style="width:95%; height:25px; padding:5px; text-align:right;"><a onclick="display_msg('kakaoStory')" style="cursor:pointer"><img src="../img/btn_freeview.gif" /></a></div>
			<div id="dp_msg_kakaoStory" style="width:95%; height:50px; border:dashed 1px #72CDFF; padding:5px;"></div>
			<div style="padding-top:5px;" class="small1 extext">
				<div>
				카카오스토리 기능은 <font color="red">모바일샵V2</font> 에서만 지원 합니다. <a href="../mobileShop/mobile_set.php" style="font-weight:bold;" class="extext">[모바일샵 설정 바로가기]</a><br>
				카카오스토리의 타이틀을 설정합니다. 상품URL은 자동으로 포함됩니다.
				</div>
			</div>
		</div>
		<div>
			<div style="margin:10px 5px 2px 5px;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr valign="bottom">
					<td align="left"><img src="../img/icon_arrow.gif" /> <b>카카오톡</b></td>
					<td align="right" style="padding-right:35px;"><input type="checkbox" name="use_kakao" id="use_kakao" value="y" <?=$checked['use_kakao']['y']?> /> <label for="use_kakao">출력여부</label></td>
				</tr>
				</table>
			</div>
			<div style="vertical-align:middle">
				<table width="95%" border="1" bordercolor="#e1e1e1" style="border-collapse:collapse">
				<col class="cellC"><col class="cellL">
				<tr>
					<td>쇼핑몰이름</td>
					<td><input type="text" name="msg_kakao_shopnm" id="msg_kakao_shopnm" value="<?=$snsCfg['msg_kakao_shopnm']?>" style="border:1px solid #CCCCCC; width:95%;" class="line" /></td>
				</tr>
				<tr>
					<td>전송 내용</td>
					<td><input type="text" name="msg_kakao_goodsnm" id="msg_kakao_goodsnm" value="<?=$snsCfg['msg_kakao_goodsnm']?>" style="border:1px solid #CCCCCC; width:95%;" class="line" /></td>
				</tr>
				<tr>
					<td>전송 URL</td>
					<td><input type="text" name="msg_kakao_goodsurl" id="msg_kakao_goodsurl" value="<?=$snsCfg['msg_kakao_goodsurl']?>" style="border:1px solid #CCCCCC; width:95%;" class="line" /></td>
				</tr>
				</table>
			</div>
			<div style="width:95%; height:25px; padding:5px; text-align:right;"><a onclick="display_msg('kakao')" style="cursor:pointer"><img src="../img/btn_freeview.gif" /></a></div>
			<div id="dp_msg_kakao" style="width:95%; height:50px; border:dashed 1px #72CDFF; padding:5px;"></div>
			<div style="padding-top:5px;" class="small1 extext">
				<div>카카오톡 기능은 모바일샵에서만 지원 합니다. <a href="../mobileShop/mobile_set.php" style="font-weight:bold;" class="extext">[모바일샵 설정 바로가기]</a></div>
			</div>
		</div>
		<div>
			<div style="margin:20px 5px 2px 5px;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr valign="bottom">
					<td align="left"><img src="../img/icon_arrow.gif" /> <b>트위터</b></td>
					<td align="right" style="padding-right:35px;"><input type="checkbox" name="use_twitter" id="use_twitter" value="y" <?=$checked['use_twitter']['y']?> /> <label for="use_twitter">출력여부</label></td>
				</tr>
				</table>
			</div>
			<div style="vertical-align:middle">
				<textarea name="msg_twitter" id="msg_twitter" style="width:95%; height=60px;" ><?=html_entity_decode($snsCfg['msg_twitter'])?></textarea>
			</div>
			<div style="width:95%; height:25px; padding:5px; text-align:right;"><a onclick="display_msg('twitter')" style="cursor:pointer"><img src="../img/btn_freeview.gif" /></a></div>
			<div id="dp_msg_twitter" style="width:95%; height:50px; border:dashed 1px #72CDFF; padding:5px;"></div>
			<div style="padding-top:5px;" class="small1 extext">
				<div>트위터는 140자까지만 입력이 가능합니다.</div>
				<div>상품명을 포함하여 140자 이상일 경우 상품명을 줄여 글자수 맞추게 됩니다. 문장이 너무 긴 경우에는 오류가 발생할 수 있습니다.</div>
			</div>
		</div>
		<div>
			<div style="margin:20px 5px 2px 5px;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr valign="bottom">
					<td align="left"><img src="../img/icon_arrow.gif" /> <b>페이스북</b></td>
					<td align="right" style="padding-right:35px;"><input type="checkbox" name="use_facebook" id="use_facebook" value="y" <?=$checked['use_facebook']['y']?> /> <label for="use_facebook">출력여부</label></td>
				</tr>
				</table>
			</div>
			<div style="display:inline-block; width:100%;">
				<textarea name="msg_facebook" id="msg_facebook" style="width:95%; height=60px;" ><?=html_entity_decode($snsCfg['msg_facebook'])?></textarea>
			</div>
			<div style="width:95%; height:25px; padding:5px; text-align:right;"><a onclick="display_msg('facebook')" style="cursor:pointer"><img src="../img/btn_freeview.gif" /></a></div>
			<div id="dp_msg_facebook" style="width:95%; height:50px; border:dashed 1px #72CDFF; padding:5px;"></div>
			<div style="padding-top:5px;padding-bottom:5px;" class="small1 extext">
				<div>페이스북의 타이틀을 설정합니다. 상품URL은 자동으로 포함됩니다. </div>
			</div>
		</div>
		<div>
			<div style="margin:20px 5px 2px 5px;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr valign="bottom">
					<td align="left"><img src="../img/icon_arrow.gif" /> <b>핀터레스트</b></td>
					<td align="right" style="padding-right:35px;"><input type="checkbox" name="use_pinterest" id="use_pinterest" value="y" <?=$checked['use_pinterest']['y']?> /> <label for="use_pinterest">출력여부</label></td>
				</tr>
				</table>
			</div>
			<div style="display:inline-block; width:100%;">
				<textarea name="msg_pinterest" id="msg_pinterest" style="width:95%; height=60px;" ><?=html_entity_decode($snsCfg['msg_pinterest'])?></textarea>
			</div>
			<div style="width:95%; height:25px; padding:5px; text-align:right;"><a onclick="display_msg('pinterest')" style="cursor:pointer"><img src="../img/btn_freeview.gif" /></a></div>
			<div id="dp_msg_pinterest" style="width:95%; height:50px; border:dashed 1px #72CDFF; padding:5px;"></div>
			<div style="padding-top:5px;padding-bottom:5px;" class="small1 extext">
				<div>핀터레스트의 타이틀을 설정합니다. 상품URL은 자동으로 포함됩니다. </div>
			</div>
		</div>
		<div>
			<div style="margin:20px 5px 2px 5px;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr valign="bottom">
					<td align="left"><img src="../img/icon_arrow.gif" /> <b>URL복사</b></td>
					<td align="right" style="padding-right:35px;"><input type="checkbox" name="use_urlcopy" id="use_urlcopy" value="y" <?=$checked['use_urlcopy']['y']?> /> <label for="use_urlcopy">출력여부</label></td>
				</tr>
				</table>
			</div>
			<div style="padding-top:5px;padding-bottom:5px;" class="small1 extext">
				<div>2015년 08월 27일 이전 제작 무료 스킨을 사용하시는 경우 반드시 스킨패치를 적용해야 기능 사용이 가능합니다. <a href="http://www.godo.co.kr/customer_center/patch.php?sno=2225" target="_blank">[패치 바로가기]</a></div>
			</div>
		</div>
	</td>
</tr>
<tr>
	<td height="50">공유하기 치환코드</td>
	<td>
		<div style="padding-top:5;">{snsBtn} <img class="hand" src="../img/i_copy.gif" onclick="copy_txt('{snsBtn}')" alt="복사하기" align="absmiddle"/></div>
		<div style="padding-top:10;" class="small1 extext">
			<div>공유하기 치환코드는 SNS 공유하기를 사용하였으나 상품페이지에 노출이 되지 않는 경우에 사용하시면 됩니다.</div>
		</div>
	</td>
</tr>
<tr>
	<td><div>공유하기 치환코드</div><div style="padding:5 0 5 0">삽입 방법</div></td>
	<td>
		<div style="padding-top:5">삽입되는 소스 페이지 : <a href="../../admin/design/codi.php" target="_blank">"쇼핑몰 관리자 > 디자인관리"</a> 좌측 트리 메뉴에서 "상품 > 상품상세화면" 메뉴 클릭</div>
	</td>
</tr>
</table>

<div class=button>
<input type="image" src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
</form>
<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>SNS공유하기 서비스를 '사용함'으로 설정하시면 상품상세 페이지에 트위터로 상품URL정보를 보낼 수 있는 아이콘 이미지가 자동으로 노출됩니다.</td></tr>
</table>
</div>
<script type="text/javascript">enableForm(<?=($snsCfg['useBtn']=='y')?'true':'false'?>)</script>
<script>cssRound('MSG02')</script>

</div>
<? include "../_footer.php"; ?>