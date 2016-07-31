<?
$location = "투데이샵 > 상품토크관리";
include "../_header.php";
include "../../lib/page.class.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}

### 공백 제거
$_GET['sword'] = trim($_GET['sword']);
if (!$_GET['status']) $_GET['status'] = 'ing';

list ($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_TODAYSHOP_TALK." WHERE notice=0");

if (!$_GET['page_num']) $_GET['page_num'] = 10;
$selected['page_num'][$_GET['page_num']] = "selected";
$selected['skey'][$_GET['skey']] = "selected";
$checked['status'][$_GET['status']] = "checked";
$checked['notice'][$_GET['notice']] = "checked";

if ($_GET['sword']) $where[] = $_GET['skey']." LIKE '%".$_GET['sword']."%'";
if ($_GET['regdt'][0] && $_GET['regdt'][1]) $where[] = "tt.regdt BETWEEN DATE_FORMAT(".$_GET['regdt'][0].",'%Y-%m-%d 00:00:00') AND DATE_FORMAT(".$_GET['regdt'][1].",'%Y-%m-%d 23:59:59')";
switch($_GET['status']) {
	case 'ing' : {
		$where[] = "(now() BETWEEN tg.startdt AND tg.enddt)";
		break;
	}
	case 'schedule' : {
		$where[] = "(now() < tg.startdt)";
		break;
	}
	case 'close' : {
		$where[] = "(now() > tg.enddt)";
		break;
	}
}

$db_table = GD_TODAYSHOP_TALK." AS tt LEFT JOIN ".GD_TODAYSHOP_GOODS." AS tg ON tt.tgsno=tg.tgsno LEFT JOIN ".GD_GOODS." AS g ON tg.goodsno=g.goodsno";
$orderby = ($_GET['sort']) ? $_GET['sort'] : "tt.gid DESC, HEX(thread)";

// 전체, 공지사항만
if ($_GET['notice'] != 'n') {
	if (is_array($where) && empty($where) === false) $whereSql = implode(' AND ', $where);
	$noticeSql = "SELECT tt.ttsno, tt.tgsno, tt.gid, tt.thread, tt.comment, tt.writer, tt.notice, tg.goodsno, tg.startdt, tg.enddt, tt.regdt, g.goodsnm, g.img_s, g.icon, g.runout FROM ".$db_table." WHERE tt.notice>0 ".((empty($whereSql)===false)? 'AND (tt.tgsno=0 OR ('.$whereSql.'))' : '')." ORDER BY tt.notice, tt.gid DESC";
	$noticeRes = $db->query($noticeSql);
	unset($whereSql);
}

$where[] = "tt.notice=0"; // 일반글
$pg = new Page($_GET['page'],$_GET['page_num']);
if ($_GET['notice'] != 'y' ) {
	$pg->field = " tt.ttsno, tt.tgsno, tt.gid, tt.thread, tt.comment, tt.writer, tt.notice, tg.goodsno, tg.startdt, tg.enddt, tt.regdt, g.goodsnm, g.img_s, g.icon, g.runout ";
	$pg->setQuery($db_table,$where,$orderby);
	$pg->exec();
	$res = $db->query($pg->query);
}
?>

<script type="text/javascript">
<!--
function eSort(obj,fld)
{
	var form = document.frmList;
	if (obj.innerText.charAt(1)=="▲") fld += " desc";
	form.sort.value = fld;
	form.submit();
}

function sort(sort)
{
	var fm = document.frmList;
	fm.sort.value = sort;
	fm.submit();
}
function sort_chk(sort)
{
	if (!sort) return;
	sort = sort.replace(" ","_");
	var obj = document.getElementsByName('sort_'+sort);
	if (obj.length){
		div = obj[0].src.split('list_');
		for (i=0;i<obj.length;i++){
			chg = (div[1]=="up_off.gif") ? "up_on.gif" : "down_on.gif";
			obj[i].src = div[0] + "list_" + chg;
		}
	}
}

window.onload = function(){ sort_chk("<?=$_GET['sort']?>"); }
//-->
</script>
<script type="text/javascript">
Talk = {
	initList: function() {
		var divObj = document.frmTalk.getElementsByTagName("DIV");
		for(var i = 0; i < divObj.length; i++) {
			if (divObj[i].id.match(/talkFrm[0-9]*/g)) {
				while(divObj[i].childNodes.length > 0) {
					divObj[i].children(0).removeNode(true);
				}
				divObj[i].style.display = "none";
			}
		}
	},
	getForm: function(ttsno) {
		var obj = document.getElementById("talk"+ttsno);
		var rtn = "<span style=\"display:block\"><textarea name=\"comment\" style=\"width:99%; height:90px;\">"+((obj)?obj.innerHTML.replace(/<BR>/gi, "\r\n"):"")+"</textarea></span>";
		rtn += "<span style=\"display:block\">작성자 : <input type=\"text\" name=\"writer\" value=\"<?=$_SESSION['member']['name']?>\" /> <span class=\"noline\"><input type=image src=\"../img/i_regist.gif\" /></span></span>";
		return rtn;
	},
	reply: function(ttsno) {
		var obj = document.getElementById("talkFrm"+ttsno);
		if (obj.style.display != "none" && document.frmTalk.mode.value == "reply") return;
		Talk.initList();
		var frm = Talk.getForm();
		obj.innerHTML = frm;
		obj.style.display = "block";
		document.frmTalk.mode.value = "reply";
		document.frmTalk.ttsno.value = ttsno;
	},
	edit: function(ttsno) {
		var obj = document.getElementById("talkFrm"+ttsno);
		if (obj.style.display != "none" && document.frmTalk.mode.value == "edit") return;
		Talk.initList();
		var frm = Talk.getForm(ttsno);
		obj.innerHTML = frm;
		obj.style.display = "block";
		document.frmTalk.mode.value = "edit";
		document.frmTalk.ttsno.value = ttsno;
	},
	remove: function(ttsno) {
		if (confirm("삭제하시겠습니까?")) {
			document.frmTalk.mode.value = "remove";
			document.frmTalk.ttsno.value = ttsno;
			document.frmTalk.submit();
		}
	},
	chkForm: function(fobj) {
		if (!fobj.comment.value.replace(/\s/g,"")) {
			fobj.comment.focus();
			alert("내용을 입력하세요.");
			return false;
		}
		if (!fobj.writer.value.replace(/\s/g,"")) {
			fobj.writer.focus();
			alert("작성자를 입력하세요.");
			return false;
		}
		return true;
	},
	setNotice: function(obj) {
		switch(obj.name) {
			case 'notice': {
				document.frmWrite.allgoods.disabled = !obj.checked;
				Talk.setNotice(document.frmWrite.allgoods);
				break;
			}
			case 'allgoods': {
				if (obj.disabled) obj.checked = "";
				document.frmWrite.tgsno.disabled = obj.checked;
				break;
			}
		}
	}
}
</script>

<form method="post" action="indb.config.php" name="fmSet" target="ifrmHidden">
	<div class="title title_top">상품토크 설정 <span>리스트 갯수 제한 및 글쓰기에 대한 권한을 설정하실 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>리스트 갯수</td>
		<td><input type="text" name="talkCnt" value="<?=$todayShop->cfg['talkCnt']?>" size="6" class="rline" onkeydown="onlynumber();" /> 개</td>
	</tr>
	</table>
	<div class=button_top><input type=image src="../img/btn_save3.gif"></div>
</form>
<p />

<form name=frmList>
<input type=hidden name="sort" value="<?=$_GET['sort']?>">
	<div class="title title_top">토크리스트<span>투데이샵에 등록된 토크리스트를 검색합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
	<table class=tb>
	<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
	<tr>
		<td>검색어</td>
		<td colspan="3">
			<select name="skey">
				<option value="goodsnm" <?=$selected['skey']['goodsnm']?>>상품명
				<option value="tg.goodsno" <?=$selected['skey']['tg.goodssno']?>>상품고유번호
				<option value="writer" <?=$selected['skey']['writer']?>>작성자
			</select>
			<input type=text name="sword" value="<?=$_GET['sword']?>" class="line" style="height:22px">
		</td>
	</tr>
	<tr>
		<td>작성기간</td>
		<td colspan="3">
			<input type=text name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
			<input type=text name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
			<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
		</td>
	</tr>
	<tr>
		<td>판매여부</td>
		<td class="noline">
			<label><input type="radio" name="status" value="all" <?=$checked['status']['all']?> /> 전체</label>
			<label><input type="radio" name="status" value="ing" <?=$checked['status']['ing']?> /> 판매중</label>
			<label><input type="radio" name="status" value="schedule" <?=$checked['status']['schedule']?> /> 판매예정</label>
			<label><input type="radio" name="status" value="close" <?=$checked['status']['close']?> /> 판매종료</label>
		</td>
		<td>공지사항</td>
		<td class="noline">
			<label><input type="radio" name="notice" value="" <?=$checked['notice']['']?> /> 전체</label>
			<label><input type="radio" name="notice" value="y" <?=$checked['notice']['y']?> /> 공지사항만</label>
			<label><input type="radio" name="notice" value="n" <?=$checked['notice']['n']?> /> 공지사항제외</label>
		</td>
	</tr>
	</table>
	<div class=button_top><input type=image src="../img/btn_search2.gif"></div>
	<div style="padding-top:15px"></div>
	<table width=100% cellpadding=0 cellspacing=0>
	<tr>
		<? if ($_GET['notice'] != 'y') { ?>
		<td class=pageInfo>
			<font class=ver8>총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode[total]?></b>개, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</font>
		</td>
		<? } ?>
		<td align=right>
			<table cellpadding=0 cellspacing=0 border=0>
			<tr>
				<td valign=bottom>
					<img src="../img/sname_date.gif"><a href="javascript:sort('regdt desc')"><img name=sort_regdt_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('regdt')"><img name=sort_regdt src="../img/list_down_off.gif"></a></td>
					<td style="padding-left:20px">
					<img src="../img/sname_output.gif" align=absmiddle>
					<select name=page_num onchange="this.form.submit()">
					<?
					$r_pagenum = array(10,20,40,60,100);
					foreach ($r_pagenum as $v){
					?>
					<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>개 출력
					<? } ?>
					</select>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
</form>

<form name="frmTalk" action="indb.goods_talk.php" method="post" onsubmit="return Talk.chkForm(this)" target="ifrmHidden">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="ttsno" value="" />
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th width=60>번호</th>
	<th></th>
	<th width=10></th>
	<th>상품명 / 내용</th>
	<th>작성자</th>
	<th>등록일</th>
</tr>
<tr><td class=rnd colspan=6></td></tr>
<col width=40 span=2 align=center>
<?
// 공지사항
while ($data=$db->fetch($noticeRes)){
	$icon = setIcon($data['icon'],$data['regdt'],"../");
?>
<tr><td height=4 colspan=12></td></tr>
<tr height=20>
	<td rowspan="2">공지</td>
	<td rowspan="2">
		<? if ($data['tgsno']) { ?>
		<a href="../../todayshop/today_goods.php?tgsno=<?=$data['tgsno']?>" target=_blank><?=goodsimg($data['img_s'],40,'',1)?></a>
		<? } ?>
	</td>
	<td rowspan="2"></td>
	<td colspan="3">
		<span style="display:block; margin-left:<?=(strlen($data['thread'])/2)*10?>px;">
			<? if ($data['tgsno']) { ?>
			<a href="./goods_reg.php?mode=modify&tgsno=<?=$data['tgsno']?>">
				<font color=303030>[<?=$data['goodsnm']?>]</font> <font class=ver81 color=444444><?=$data['startdt']?> - <?=$data['enddt']?></font>
			</a>
			<? if ($icon){ ?><span style="display:block; padding-top:3px"><?=$icon?></span><? } ?>
			<? if ($data['runout']){ ?><span style="display:block; padding-top:3px"><img src="../../data/skin/<?=$cfg['tplSkin']?>/img/icon/good_icon_soldout.gif"></span><? } ?>
			<? } else { ?>
			[전체상품]
			<? } ?>
		</span>
	</td>
</tr>
<tr height=20>
	<td>
		<ul style="margin-bottom:0px; margin-left:<?=(strlen($data['thread'])/2)*10?>px;">
			<li id="talk<?=$data['ttsno']?>"><?=nl2br($data['comment'])?></li>
		</ul>
		<a onclick="Talk.edit(<?=$data['ttsno']?>)" style="cursor:pointer"><img src="../img/i_edit.gif"></a>
		<a onclick="Talk.remove(<?=$data['ttsno']?>)" style="cursor:pointer"><img src="../img/i_del.gif"></a>
		<div id="talkFrm<?=$data['ttsno']?>" style="display:none; width:100%;"></div>
	</td>
	<td align=center><?=$data['writer']?></td>
	<td align=center><font class=ver81 color=444444><?=substr($data['regdt'],0,10)?></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=6 class=rndline></td></tr>
<?
}
unset($data, $noticeRes);

// 일반글
while ($data=$db->fetch($res)) {
	$icon = setIcon($data['icon'],$data['regdt'],"../");
	if (strlen($data['thread']) == 0) $pastGoodsno = '';
?>
<tr><td height=4 colspan=12></td></tr>
<? if ($pastGoodsno != $data['goodsno']) { ?>
<tr height=20>
	<td rowspan="2"><font class=ver8 color=616161><?=$pg->idx--?></font></td>
	<td rowspan="2"><a href="../../todayshop/today_goods.php?tgsno=<?=$data['tgsno']?>" target=_blank><?=goodsimg($data['img_s'],40,'',1)?></a></td>
	<td rowspan="2"></td>
	<td colspan="3">
		<span style="display:block; margin-bottom:0px; margin-left:<?=(strlen($data['thread'])/2)*10?>px;">
			<a href="./goods_reg.php?mode=modify&tgsno=<?=$data['tgsno']?>">
				<font color=303030>[<?=$data['goodsnm']?>]</font> <font class=ver81 color=444444><?=$data['startdt']?> - <?=$data['enddt']?></font>
			</a>
			<? if ($icon){ ?><span style="display:block; padding-top:3px"><?=$icon?></span><? } ?>
			<? if ($data['runout']){ ?><span style="display:block; padding-top:3px"><img src="../../data/skin/<?=$cfg['tplSkin']?>/img/icon/good_icon_soldout.gif"></span><? } ?>
		</span>
	</td>
</tr>
<? } ?>
<tr height=20>
<? if ($pastGoodsno == $data['goodsno']) { ?>
	<td><font class=ver8 color=616161><?=$pg->idx--?></font></td>
	<td></td>
	<td></td>
<? } ?>
	<td>
		<ul style="display:inline-block; margin-bottom:0px; margin-left:<?=(strlen($data['thread'])/2)*10?>px;">
			<li style="float:left; margin-right:5px;"><?=(strlen($data['thread'])>0)?'└':''?></li>
			<li style="float:left">
				<span id="talk<?=$data['ttsno']?>"><?=nl2br($data['comment'])?></span><br />
				<a onclick="Talk.reply(<?=$data['ttsno']?>)" style="cursor:pointer"><img src="../img/i_reply.gif"></a>
				<a onclick="Talk.edit(<?=$data['ttsno']?>)" style="cursor:pointer"><img src="../img/i_edit.gif"></a>
				<a onclick="Talk.remove(<?=$data['ttsno']?>)" style="cursor:pointer"><img src="../img/i_del.gif"></a>
			</li>
		</ul>
		<div id="talkFrm<?=$data['ttsno']?>" style="display:none; width:100%;"></div>
	</td>
	<td align=center><?=$data['writer']?></td>
	<td align=center><font class=ver81 color=444444><?=substr($data['regdt'],0,10)?></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=6 class=rndline></td></tr>
<?
	$pastGoodsno = $data['goodsno'];
}
unset($data, $res);
?>
</table>
</form>

<div align=center class=pageNavi><font class=ver8><?=$pg->page['navi']?></font></div>
<p />

<form name="frmWrite" action="indb.goods_talk.php" method="post" onsubmit="return Talk.chkForm(this)" target="ifrmHidden">
<input type="hidden" name="mode" value="regist" />
<table class=tb>
<col class=cellC><col class=cellL><col class=cellL style="width:90px;">
<tr>
	<td>글쓰기</td>
	<td>
		<?
			$tgSql = "SELECT tg.tgsno, g.goodsnm, tg.startdt, tg.enddt FROM ".GD_TODAYSHOP_GOODS." AS tg JOIN ".GD_GOODS." AS g ON tg.goodsno=g.goodsno WHERE tg.enddt>=now() ORDER BY tg.tgsno DESC";
			$tgRes = $db->query($tgSql);
		?>
		<table style="table-layout:fixed">
		<tr>
			<td style="width:50px; height:28px;">상품</td>
			<td colspan="2"><select name="tgsno">
		<?
			while($data = $db->fetch($tgRes)) {
		?>
			<option value="<?=$data['tgsno']?>">[<?=$data['goodsnm']?>] <?=$data['startdt'].' - '.$data['enddt']?></option>
		<?
			}
		?>
			</select></td>
		</tr>
		<tr>
			<td style="width:50px; height:28px;">작성자</td>
			<td><input type="text" name="writer" value="<?=$_SESSION['member']['name']?>" /></td>
			<td>
				<label class="noline"><input type="checkbox" name="notice" value="1" onclick="Talk.setNotice(this)" /> 공지사항</label>
				(<label class="noline"><input type="checkbox" name="allgoods" value="y" onclick="Talk.setNotice(this)" disabled="disabled" /> 전체상품적용</label>)
			</td>
		</tr>
		</table>
		<div><textarea name="comment" style="width:99%; height:90px;"></textarea></div>
	</td>
	<td class="noline";>
		<input type=image src="../img/btn_save3.gif">
	</td>
</tr>
</table>
</form>
<p />

<? include "../_footer.php"; ?>