<?
$location = "가격비교사이트 입점 > 가격비교설정";
@include "../_header.php";
@include "../../conf/engine.php";

$omi[engine_url] = "/shop/engine/engine.php?mode=omi&modeView=y";
if(!$omi[gubun])	$omi[gubun] = "omi";

$enuri[engine_url] = "/shop/engine/enuri.php?type=category";
if(!$enuri[gubun])	$enuri[gubun] = "enuri";

$bb[engine_url] = "/shop/engine/bb.php";
$bb[gubun] = "bb";

$mm[engine_url] = "/shop/engine/engine.php?mode=mm&modeView=y";
$mm[gubun] = "mm";

$danawa[engine_url] = "/shop/engine/engine.php?mode=danawa&modeView=y";
$danawa[engine_url2] = "/shop/engine/engine.php?mode=danawa_new&modeView=y";
$danawa[gubun] = "danawa";

$naver_elec[engine_url] = "/shop/engine/engine.php?mode=naver_elec&modeView=y";
$naver_bea[engine_url] = "/shop/engine/engine.php?mode=naver_bea&modeView=y";
$naver_milk[engine_url] = "/shop/engine/engine.php?mode=naver_milk&modeView=y";

$yahoo[engine_url] = "/shop/engine/engine.php?mode=yahoo&modeView=y";
$yahoo[gubun] = "yahoo";
?>
<div class="title title_top">가격비교사이트 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=9')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div style="padding-top:5"><img src="../img/icon_list.gif" align=absmiddle><b><font color=ff6600>상품정보를 변경하였거나 상품 DB URL이 빈공간으로 되어있을 경우에는 해당 가격비교사이트를 반드시 업데이트해주세요.</font></B></div>
<br>
<form name=form method=post action="indb.php">
<input type=hidden name=mode value="engine">
<div style="height:30px;padding: 5 0 10 10">
무이자할부정보&nbsp;:&nbsp;</b><input type=text name='card[cardfree]' size=50 value='<?=$card[cardfree]?>'>&nbsp;<font class=small color=565656>ex) 국민12/삼성3	</font></div>

<table class=tb>
	<tr class=rndbg>
		<td align=center><b>사용여부</b></td>
		<td align=center><b>가격비교사이트</b></td>
		<!--<td align=center><b>구분자</b></td>-->
		<td align=center><b>설정</b></td>
		<td align=center><b>최근업데이트일시</b></td>
		<td align=center><b>업데이트</b></td>
	</tr>
	<tr class=cellL height=30>
		<td align=center><input type=checkbox class=null name=omi[chk] value='1' <?=($omi[chk])?"checked":""?>></td>
		<td style="padding-left:10"><a href="http://omi.co.kr" target="_blank"><font color=444444>오미</a></td>
		<input type=hidden name="omi[gubun]" value="<?=$omi[gubun]?>">
		<!--<td align=center><?=$omi[gubun]?></td>-->
		<input type=hidden name="omi[engine_url]" value="<?=$omi[engine_url]?>">
		<td align=left style='padding-left:5px;'>
			<?if(file_exists('../../conf/engine/omi.php')){?>
			<font class=ver81 color=444444>DB_URL&nbsp;:&nbsp;<a href="<?=$omi[engine_url]?>" target=_blank><font color=000000>http://<?=$_SERVER['HTTP_HOST']?><?=$omi[engine_url]?> <img src="../img/btn_naver_view.gif" align=absmiddle></a>
			<?}?>
		</td>
		<td align=center><font class=ver71 color=565656>
			<?if(file_exists('../../conf/engine/omi.php')) echo date('Y-m-d h:i:s',filectime ( '../../conf/engine/omi.php'));?>
		</td>
		<td align=center><a href='/shop/engine/engine.php?mode=omi' target='ifrmHidden'><img src="../img/btn_price_update.gif"></a></td>
	</tr>

	<tr class=cellL>
		<td align=center><input type=checkbox class=null name=enuri[chk] value='1' <?=($enuri[chk])?"checked":""?>></td>
		<td style="padding-left:10"><a href="http://enuri.com" target="_blank"><font color=444444>에누리</a></td>
		<input type=hidden name="enuri[gubun]" value="<?=$enuri[gubun]?>">
		<!--<td align=center><?=$enuri[gubun]?></td>-->
		<input type=hidden name="enuri[engine_url]" value="<?=$enuri[engine_url]?>">
		<td align=left style='padding-left:5px;' colspan=2>

		<div style="height:30px;padding-top:5px;"><font class=ver81 color=444444>DB_URL&nbsp;:&nbsp;<?if($enuri[engine_url]){?><a href="<?=$enuri[engine_url]?>" target=_blank><font color=000000>http://<?=$_SERVER['HTTP_HOST']?><?=$enuri[engine_url]?> <img src="../img/btn_naver_view.gif" align=absmiddle></a><?}?></div></td>
		<td align=center> - </td>
	</tr>

	<tr class=cellL>
		<td align=center><input type=checkbox class=null name=bb[chk] value='1' <?=($bb[chk])?"checked":""?>></td>
		<td style="padding-left:10"><a href="http://bb.co.kr" target="_blank"><font color=444444>베스트바이어</a></td>
		<input type=hidden name="bb[gubun]" value="<?=$bb[gubun]?>">
		<!--<td align=center><?=$bb[gubun]?></td>-->
		<input type=hidden name="bb[engine_url]" value="<?=$bb[engine_url]?>">
		<td align=left colspan=2 style='padding-left:5px;'>

		<div style="height:30px;padding-top:5px;"><font class=ver81 color=444444>DB_URL&nbsp;:&nbsp;<?if($bb[engine_url]){?><a href="<?=$bb[engine_url]?>" target=_blank><font color=000000>http://<?=$_SERVER['HTTP_HOST']?><?=$bb[engine_url]?> <img src="../img/btn_naver_view.gif" align=absmiddle></a><?}?></div></td>
		<td align=center> - </td>
	</tr>

	<tr class=cellL>
		<td align=center><input type=checkbox class=null name=mm[chk] value='1' <?=($mm[chk])?"checked":""?>></td>
		<td style="padding-left:10"><a href="http://mm.co.kr" target="_blank"><font color=444444>마이마진</a></td>
		<input type=hidden name="mm[gubun]" value="<?=$mm[gubun]?>">
		<!--<td align=center><?=$mm[gubun]?></td>-->
		<input type=hidden name="mm[engine_url]" value="<?=$mm[engine_url]?>">
		<td align=left style='padding-left:5px'>
			<div style="height:30px;padding-top:5px;"><font color=444444>쇼핑몰ID&nbsp;:&nbsp;<input type=text name='mm[mm_id]' size=30 value='<?=$mm[mm_id]?>'> <font class=small1 color=6d6d6d>마이마진으로부터 부여받은 ID 입력</font></div>
			<div style="height:30px;padding-top:5px;">
			<?if(file_exists('../../conf/engine/mm.php') && $mm[engine_url]){?>
			<font class=ver81 color=444444>DB_URL&nbsp;:&nbsp;<a href="<?=$mm[engine_url]?>" target=_blank><font color=000000>http://<?=$_SERVER['HTTP_HOST']?><?=$mm[engine_url]?> <img src="../img/btn_naver_view.gif" align=absmiddle></a>
			<?}else{?>
			DB_URL&nbsp;:&nbsp;업데이트필요
			<?}?>
			</div>
		</td>
		<td align=center><font class=ver71 color=565656><?if(file_exists('../../conf/engine/mm.php')) echo date('Y-m-d h:i:s',filectime ( '../../conf/engine/mm.php'));?></td>
		<td align=center><a href='/shop/engine/engine.php?mode=mm' target='ifrmHidden'><img src="../img/btn_price_update.gif"></a></td>
	</tr>

	<tr class=cellL height=30>
		<input type=hidden name="danawa[gubun]" value="<?=$danawa[gubun]?>">

		<input type=hidden name="danawa[engine_url]" value="<?=$danawa[engine_url]?>">
		<input type=hidden name="danawa[engine_url2]" value="<?=$danawa[engine_url2]?>">
		<td align=center rowspan=2><input type=checkbox class=null name=danawa[chk] value='1' <?=($danawa[chk])?"checked":""?>></td>
		<td style="padding-left:10" rowspan=2><a href="http://danawa.co.kr" target="_blank"><font color=444444>다나와</a></td>
		<td style="padding-left:5">
			<?if(file_exists('../../conf/engine/danawa.php') && $danawa[engine_url]){?>
			<font class=ver81 color=444444>전체 DB_URL&nbsp;:&nbsp;<a href="<?=$danawa[engine_url]?>" target=_blank><font color=000000>http://<?=$_SERVER['HTTP_HOST']?><?=$danawa[engine_url]?> <img src="../img/btn_naver_view.gif" align=absmiddle></a>
			<?}else{?>
			<font class=ver81 color=444444>전체 DB_URL&nbsp;:&nbsp;업데이트필요
			<?}?>
		</td>
		<td align=center><font class=ver71 color=565656><?if(file_exists('../../conf/engine/danawa.php')) echo date('Y-m-d h:i:s',filectime ( '../../conf/engine/danawa.php'));?></td>
		<td align=center><a href='/shop/engine/engine.php?mode=danawa' target='ifrmHidden'><img src="../img/btn_price_update.gif"></a></td>
	</tr>
	<tr height=30>

		<td style="padding-left:5">
			<?if(file_exists('../../conf/engine/danawa_new.php') && $danawa[engine_url2]){?>
			<font class=ver81 color=444444>신규 DB_URL&nbsp;:&nbsp;<a href="<?=$danawa[engine_url2]?>" target=_blank><font color=000000>http://<?=$_SERVER['HTTP_HOST']?><?=$danawa[engine_url2]?> <img src="../img/btn_naver_view.gif" align=absmiddle></a>
			<?}else{?>
			<font class=ver81 color=444444>신규 DB_URL&nbsp;:&nbsp;업데이트필요
			<?}?>
		</td>
		<td align=center><font class=ver71 color=565656><?if(file_exists('../../conf/engine/danawa_new.php')) echo date('Y-m-d h:i:s',filectime ( '../../conf/engine/danawa_new.php'));?></td>
		<td align=center><a href='/shop/engine/engine.php?mode=danawa_new' target='ifrmHidden'><img src="../img/btn_price_update.gif"></a></td>
	</tr>

	<tr height=30>
		<input type=hidden name=naver_elec[gubun] value='naver_elec'>
		<input type=hidden name="naver_elec[engine_url]" value="<?=$naver_elec[engine_url]?>">
		<td align=center><input type=checkbox class=null name=naver_elec[chk] value='1' <?=($naver_elec[chk])?"checked":""?>></td>
		<td style="padding-left:10"><a href="http://shopping.naver.com" target="_blank"><font color=444444>네이버(가전/통신)</a></td>
		<td style="padding-left:5">
			<?if(file_exists('../../conf/engine/naver_elec.php') && $naver_elec[engine_url]){?>
			<font class=ver81 color=444444>가전/통신&nbsp;&nbsp;<a href="<?=$naver_elec[engine_url]?>" target=_blank><font color=000000>http://<?=$_SERVER['HTTP_HOST']?><?=$naver_elec[engine_url]?> <img src="../img/btn_naver_view.gif" align=absmiddle></a>
			<?}else{?>
			<font class=ver81 color=444444>&nbsp;&nbsp;업데이트 필요
			<?}?>
		</td>
		<td align=center><font class=ver71 color=565656><?if(file_exists('../../conf/engine/naver_elec.php')) echo date('Y-m-d h:i:s',filectime ( '../../conf/engine/naver_elec.php'));?></td>
		<td align=center><a href='/shop/engine/engine.php?mode=naver_elec' target='ifrmHidden'><img src="../img/btn_price_update.gif"></a></td>
	</tr>

	<tr height=30>
		<input type=hidden name=naver_bea[gubun] value='naver_bea'>
		<input type=hidden name="naver_bea[engine_url]" value="<?=$naver_bea[engine_url]?>">
		<td align=center><input type=checkbox class=null name=naver_bea[chk] value='1' <?=($naver_bea[chk])?"checked":""?>></td>
		<td style="padding-left:10"><a href="http://shopping.naver.com" target="_blank"><font color=444444>네이버(화장품)</a></td>
		<td style="padding-left:5">
			<?if(file_exists('../../conf/engine/naver_bea.php') && $naver_bea[engine_url]){?>
			<font class=ver81 color=444444>화장품&nbsp;&nbsp;<a href="<?=$naver_bea[engine_url]?>" target=_blank><font color=000000>http://<?=$_SERVER['HTTP_HOST']?><?=$naver_bea[engine_url]?> <img src="../img/btn_naver_view.gif" align=absmiddle></a>
			<?}else{?>
			<font class=ver81 color=444444>&nbsp;&nbsp;업데이트 필요
			<?}?>
		</td>
		<td align=center><font class=ver71 color=565656><?if(file_exists('../../conf/engine/naver_bea.php')) echo date('Y-m-d h:i:s',filectime ( '../../conf/engine/naver_bea.php'));?></td>
		<td align=center><a href='/shop/engine/engine.php?mode=naver_bea' target='ifrmHidden'><img src="../img/btn_price_update.gif"></a></td>
	</tr>

	<tr height=30>
		<input type=hidden name=naver_milk[gubun] value='naver_milk'>
		<input type=hidden name="naver_milk[engine_url]" value="<?=$naver_milk[engine_url]?>">
		<td align=center><input type=checkbox class=null name=naver_milk[chk] value='1' <?=($naver_milk[chk])?"checked":""?>></td>
		<td style="padding-left:10"><a href="http://shopping.naver.com" target="_blank"><font color=444444>네이버(기저귀/분유)</a></td>
		<td style="padding-left:5">
			<?if(file_exists('../../conf/engine/naver_milk.php') && $naver_milk[engine_url]){?>
			<font class=ver81 color=444444>기저귀/분유&nbsp;&nbsp;<a href="<?=$naver_milk[engine_url]?>" target=_blank><font color=000000>http://<?=$_SERVER['HTTP_HOST']?><?=$naver_milk[engine_url]?> <img src="../img/btn_naver_view.gif" align=absmiddle></a>
			<?}else{?>
			<font class=ver81 color=444444>&nbsp;&nbsp;업데이트 필요
			<?}?>
		</td>
		<td align=center><font class=ver71 color=565656><?if(file_exists('../../conf/engine/naver_milk.php')) echo date('Y-m-d h:i:s',filectime ( '../../conf/engine/naver_milk.php'));?></td>
		<td align=center><a href='/shop/engine/engine.php?mode=naver_milk' target='ifrmHidden'><img src="../img/btn_price_update.gif"></a></td>
	</tr>

	<tr class=cellL>
		<input type=hidden name="$yahoo[gubun]" value="<?=$yahoo[gubun]?>">
		<td align=center><input type=checkbox class=null name=yahoo[chk] value='1' <?=($yahoo[chk])?"checked":""?>></td>
		<td style="padding-left:10"><a href="http://yahoo.co.kr" target="_blank"><font color=444444>야후쇼핑</a></td>
		<input type=hidden name="yahoo[gubun]" value="<?=$yahoo[gubun]?>">
		<input type=hidden name="yahoo[engine_url]" value="<?=$yahoo[engine_url]?>">
		<td align=left style='padding-left:5px'>
			<div style="height:25px;padding-top:5px;">
			무이자&nbsp;:&nbsp;</b><input type=text name='yahoo[cardfree]' size=50 value='<?=$yahoo[cardfree]?>'>&nbsp;<font class=small color=565656>ex) 국민:3%삼성:3&nbsp;&nbsp;&nbsp;전카드일 경우-전카드:3</font></div>
			<div style="height:25px;padding-top:5px;">
			<?if(file_exists('../../conf/engine/yahoo.php') && $yahoo[engine_url]){?>
			<font class=ver81 color=444444>DB_URL&nbsp;:&nbsp;<a href="<?=$yahoo[engine_url]?>" target=_blank><font color=000000>http://<?=$_SERVER['HTTP_HOST']?><?=$yahoo[engine_url]?> <img src="../img/btn_naver_view.gif" align=absmiddle></a>
			<?}else{?>
			DB_URL&nbsp;:&nbsp;업데이트필요
			<?}?>
			</div>
		</td>
		<td align=center><font class=ver71 color=565656><?if(file_exists('../../conf/engine/yahoo.php')) echo date('Y-m-d h:i:s',filectime ( '../../conf/engine/yahoo.php'));?></td>
		<td align=center><a href='/shop/engine/engine.php?mode=yahoo' target='ifrmHidden'><img src="../img/btn_price_update.gif"></a></td>
	</tr>
</table>
<div style="height:30px;padding-top:5px;" align=right>
	<a href="/shop/engine/engine.php?allmode=1" target="ifrmHidden"><img src='../img/btn_price_all_update.gif'></a>
</div>
<table cellpadding=0 cellspacing=0 border=0 align=center>
<tr>
	<td height=20  style="padding-top:3px;" width=500>
	    <div align=center class=small1 style="padding-bottom:3"><font color=6d6d6d>업데이트가 진행되면 아래 바를 통해 진행율이 보이게 됩니다.</font></div>
		<div style="height:8px;font:0;background:#f7f7f7;border:2 solid #cccccc">
		<div id=progressBar style="height:8px;background:#FF4E00;width:0"></div>
		</div>
	</td>
</tr>
</table>
<div class="button">
<input type=image src="../img/btn_register.gif">
</div>
</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">에누리와 베스트바이어의 경우 따로 업데이트 버튼을 누르지 않아도 지정한 시간에 자동으로 업데이트됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">가격비교 사이트에 내 쇼핑몰 상품을 등록시, 필요한 상품DB 링크주소를 제공해 드립니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">[사용]에 체크된 가격비교 사이트만 상품데이타가 제공됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">해당 가격비교 서비스와 계약을 하신후에 사용가능합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">마이마진의 경우 계약 하실때 부여 받은 쇼핑몰ID를 등록하신 후 [지금업데이트]를 실행하여야 마이마진으로 전달될 상품데이터가 정확히 생성됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">마이마진,오미의 경우 상품정보 변경(수정,등록)시 반드시 [지금업데이트]를 실행하여야 합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">다나와의 경우 신규 DB_URL은 전체DB_URL에 업데이트되어 있지 않은 신규 상품들 추가시 사용합니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
<? include "../_footer.php"; ?>