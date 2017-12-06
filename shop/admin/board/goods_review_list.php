<?

include "../../lib/page.class.php";
@include "../../conf/phone.php";

list ($total) = $db->fetch("select count(*) from ".GD_GOODS_REVIEW); # �� ���ڵ��

### �����Ҵ�
if (!$_GET['page_num']) $_GET['page_num'] = 20; # ������ ���ڵ��
$selected['page_num'][$_GET['page_num']] = "selected";
$cfg['reviewFavoriteReplyUse'] = (!$cfg['reviewFavoriteReplyUse']) ? "n" : $cfg['reviewFavoriteReplyUse'];
### �ı� ���ε� �̹��� ���� ����
if($cfg['reviewFileNum']){
	$reviewFileNum = $cfg['reviewFileNum'];
} else {
	$reviewFileNum = 1;
}
$orderby = ($_GET['sort']) ? $_GET['sort'] : "parent desc, sno asc"; # ���� ����
$selected['sort'][$orderby]			= "selected";
$selected['skey'][$_GET['skey']]	= "selected";
$selected['point'][$_GET['point']]	= "selected";
$selected["reviewFavoriteReplyNo"][$cfg['reviewFavoriteReplyNo']]	= "selected";
$checked["reviewFavoriteReplyUse"][$cfg['reviewFavoriteReplyUse']]	= "checked";

### �˻�����
if ($_GET['cate']){
	$category	= array_notnull($_GET['cate']);
	$category	= $category[count($category)-1];

	if ($category){
		$subtable = " left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";

		// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
		$subwhere[]	= getCategoryLinkQuery('c.category', $category, 'where');
	}
}

if($_GET['point']){
	switch($_GET['point']){
		case "all": break;
		default: $t_where[] = "a.point = ".$_GET['point']; break;
	}
}

if ($_GET['skey'] && $_GET['sword']){
	if ($_GET['skey']== 'goodnm' ||  $_GET['skey']== 'all'){
		$tmp = array();
		$res = $db->query("select goodsno from ".GD_GOODS." where goodsnm like '%".$_GET['sword']."%'");
		while ($data=$db->fetch($res))$tmp[] = $data['goodsno'];
		if ( is_array( $tmp ) && count($tmp) ) $goodnm_where = "a.goodsno in(" . implode( ",", $tmp ) . ")";
		else $goodnm_where = "0";
	}

	if ($_GET['skey']== 'all') $subwhere[] = "( concat( subject, contents, ifnull(m_id, ''), ifnull(a.name, '') ) like '%".$_GET['sword']."%' or ".$goodnm_where." )";
	else if ($_GET['skey']== 'goodnm') $subwhere[] = $goodnm_where;
	else if ($_GET['skey']== 'm_id') $subwhere[] = "concat( ifnull(m_id, ''), ifnull(a.name, '') ) like '%".$_GET['sword']."%'";
	else $subwhere[] = "".$_GET['skey']." like '%".$_GET['sword']."%'";
}

if ($_GET['sregdt'][0] && $_GET['sregdt'][1]) $subwhere[] = "a.regdt between date_format(".$_GET['sregdt'][0].",'%Y-%m-%d 00:00:00') and date_format(".$_GET['sregdt'][1].",'%Y-%m-%d 23:59:59')";

if (count($subwhere))
{
	$parent = array();
	$res = $db->query( "select parent from ".GD_GOODS_REVIEW." a left join ".GD_MEMBER." b on a.m_no=b.m_no ".$subtable." where " . implode(" and ", $subwhere) );
	while ( $row = $db->fetch( $res ) ) $parent[] = $row['parent'];
	$parent = array_unique ($parent);
	if ( count( $parent ) ) $where[] = "a.parent in ('" . implode( "','", $parent ) . "')";
	else $where[] = "0";
}

### ���
$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->field = "distinct a.sno, a.parent, a.goodsno, a.subject, a.contents, a.point, a.regdt, a.name, a.m_no, a.emoney, a.attach, a.notice ";
$db_table = GD_GOODS_REVIEW." AS a LEFT JOIN gd_member AS m ON (a.m_no = m.m_no) ";
if($where) $t_where[] = implode(" AND ", $where);
$pg->setQuery($db_table, $t_where,"notice desc, ".$orderby);
$pg->exec();

$res = $db->query($pg->query);

$replyQuery = "SELECT sno, subject FROM ".GD_GOODS_FAVORITE_REPLY." WHERE customerType = 'review' ORDER BY regdt DESC";
$replyRes = $db->query($replyQuery);
$replyTotal = $db->count_($replyRes);
?>
<?getjskPc080();?>
<form name="frmList">
<input type="hidden" name="sort" value="<?=$_GET['sort']?>" />
<input type="hidden" name="page_num" value="<?=$_GET['page_num']?>" />
<div class="title title_top">��ǰ�ı����<span>������ ���� ��ǰ�ı⸦ ���캸�� �� �ֽ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=5')"><img src="../img/btn_q.gif" align="absmiddle" hspace="2" /></a></div>
<table class="tb" />
<col class="cellC" /><col class="cellL" />
<tr>
	<td>�з�����</td>
	<td colspan="3"><script>new categoryBox('cate[]',4,'<?=$category?>','','frmList');</script></td>
</tr>
<tr>
	<td>Ű����˻�����</td>
	<td colspan="3">
	<select name="skey">
	<option value="all" <?=$selected['skey']['all']?>> ���հ˻� </option>
	<option value="subject" <?=$selected['skey']['subject']?>> ���� </option>
	<option value="contents" <?=$selected['skey']['contents']?>> �ı⳻�� </option>
	<option value="m_id" <?=$selected['skey']['m_id']?>> �ۼ��� </option>
	<option value="goodnm" <?=$selected['skey']['goodnm']?>> ��ǰ�� </option>
	</select>
	<input type="text" class="line" name="sword" value="<?=$_GET['sword']?>" />
	</td>
</tr>
<tr>
	<td>�����</td>
	<td colspan="3">
	<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][0]?>" onclick="calendar(event);" onkeydown="onlynumber();" class="line" /> -
	<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][1]?>" onclick="calendar(event);" onkeydown="onlynumber();" class="line" />
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td>����</td>
	<td>
		<select name="point">
		<option value="all" <?= $selected["point"]["all"]?>>��ü</option>
		<option value="1" <?= $selected["point"]["1"]?>>��</option>
		<option value="2" <?= $selected["point"]["2"]?>>�ڡ�</option>
		<option value="3" <?= $selected["point"]["3"]?>>�ڡڡ�</option>
		<option value="4" <?= $selected["point"]["4"]?>>�ڡڡڡ�</option>
		<option value="5" <?= $selected["point"]["5"]?>>�ڡڡڡڡ�</option>
		</select>
	</td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>
</form>

<div style="padding-top:5px"></div>

<form name="fmSet" method="post" action="../board/customer_indb.php?mode=replySet">
<input type="hidden" name="sort" value="<?=$_GET['sort']?>">
<input type="hidden" name="page_num" value="<?=$_GET['page_num']?>">
<div class="title title_top">���־��� �亯 <span>�亯���� �ۼ��ÿ� �̸� �Է��س��� ���־��� �亯�� �ڵ�/�������� �ҷ��� �� �ֽ��ϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=5')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>
<table class="tb" style="margin-bottom:10px;">
<col class="cellC" width="15%"><col class="cellL">
<tr>
	<td>���־��� �亯 ����</td>
	<td class="noline">
		<div>
			<input type="radio" name="reviewFavoriteReplyUse" id="reviewFavoriteReplyUse_n" value="n" <?=$checked["reviewFavoriteReplyUse"]['n']?> onclick="checkReplyUse('n')" /> ����
			<input type="radio" name="reviewFavoriteReplyUse" id="reviewFavoriteReplyUse_y" value="y" <?=$checked["reviewFavoriteReplyUse"]['y']?> onclick="checkReplyUse('y')" /> �ڵ�
			<select name="reviewFavoriteReplyNo" id="reviewFavoriteReplyNo" style="margin:0px 10px;">
<? if(!$replyTotal) { ?>
				<option value="">���־��� �亯�� �Է��� �ּ���.</option>
<? } ?>
<? while($replyData=$db->fetch($replyRes)) { ?>
				<option value="<?=$replyData['sno']?>" <?= $selected["reviewFavoriteReplyNo"][$replyData['sno']]?>><?=strcut($replyData['subject'], 40)?></option>
<? } ?>
			</select>
			<a href="javascript:popup2('../board/customer_reply.php?type=review',800,800,1)"><img src="../img/icon_repeatqna.gif" align="absmiddle" /></a><input type="image" src="../img/btn_save2.gif" border="0" align="absmiddle" style="margin-left:20px;" /><br />
		</div>
		<div class="extext" style="margin:5px 0px;">* <b>�ڵ�</b>�� �����Ͻø� ������ �亯�� �亯���� �˾� â�� �ڵ����� �ԷµǾ� �����ϴ�.</div>
	</td>
</tr>
</table>
</form>

<div style="padding-top:5px"></div>

<table width="100%">
<tr>
	<td class="pageInfo"><font class="ver8">
	�� <b><?=number_format($total)?></b>��, �˻� <b><?=number_format($pg->recode['total'])?></b>��, <b><?=number_format($pg->page[now])?></b> of <?=number_format($pg->page['total'])?> Pages
	</td>
	<td align=right>
	<select onchange="frmList.sort.value=this.value; frmList.submit();">
	<option value="parent desc, sno asc" <?=$selected[sort]['parent desc, sno asc']?>>- �⺻ ����</option>
	<optgroup label="----------------"></optgroup>
	<option value="regdt desc" <?=$selected[sort]['regdt desc']?>>- ����� ���ġ�</option>
	<option value="regdt asc" <?=$selected[sort]['regdt asc']?>>- ����� ���ġ�</option>
	<option value="point desc" <?=$selected[sort]['point desc']?>>- ���� ���ġ�</option>
	<option value="point asc" <?=$selected[sort]['point asc']?>>- ���� ���ġ�</option>
	<optgroup label="----------------"></optgroup>
	<option value="subject desc" <?=$selected[sort]['subject desc']?>>- ���� ���ġ�</option>
	<option value="subject asc" <?=$selected[sort]['subject asc']?>>- ���� ���ġ�</option>
	</select>&nbsp;
	<select onchange="frmList.page_num.value=this.value; frmList.submit();">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>�� ���
	<? } ?>
	</select>
	</td>
</tr>
</table>
<!-- ��¥ bar - SMS ���۽� ��ũ��Ʈ ������ ���� ��¥ bar �� ǥ�� -->
<div id="sms_bar" style="width:0;height:10px;display:none"></div>
<form method="post" action="" name="fmList">
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td class="rnd" colspan="11"></td></tr>
<tr class="rndbg">
	<th width="60" onclick="javascript:PubAllSordes( 'reflect', fmList['confirmyn'] );" style="cursor:pointer;">����</th>
	<th width="60">��ȣ</th>
	<th width="70">�̹���</th>
	<th>��ǰ��/����</th>
	<th width="80">�ۼ���</th>
	<th width="80">�ۼ���</th>
	<th width="80">����</th>
	<th width="80">������</th>
	<th width="50">�亯</th>
	<th width="50">����</th>
	<th width="50">����</th>
</tr>
<tr><td class="rnd" colspan="11"></td></tr>
</table>

<?
$i = 0;
while ($data=$db->fetch($res)){
	// �����±� ����
	if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
		$data = validation::xssCleanArray($data, array(
				validation::DEFAULT_KEY => 'html',
				'subject' => array('html', 'ent_quotes'),
				'contents' => array('html', 'ent_quotes'),
		));
	}
	
	if ( empty($data['m_no']) ) $data['m_id'] = $data['name']; // ��ȸ����
	else {
		list( $data[m_id],$data[phone],$data[mobile],$data[dormant_regDate] ) = $db->fetch("select m_id,phone,mobile, dormant_regDate from ".GD_MEMBER." where m_no='$data[m_no]'" );
	}

	if ( $data['parent']==$data['sno'] ){ // ����
		$query = "select b.goodsnm,b.img_s,c.price
		from
			".GD_GOODS." b
			left join ".GD_GOODS_OPTION." c on b.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
		where
			b.goodsno = '" . $data['goodsno'] . "'";
		list( $data['goodsnm'], $data['img_s'], $data['price'] ) = $db->fetch($query);

		list( $data['replecnt'] ) = $db->fetch("select count(*) from ".GD_GOODS_REVIEW." where sno != parent and parent='".$data['sno']."'");
	}

	if ($data[attach]) {
		$data_path = "../../data/review";
		for($ii=0;$ii<10;$ii++){
			if($ii == 0){
				$name = 'RV'.sprintf("%010s", $data[sno]);
			} else {
				$name = 'RV'.sprintf("%010s", $data[sno]).'_'.$ii;
			}
			if(file_exists($data_path.'/'.$name)){
				$data[image] .= "<img src='".$data_path."/".$name."'  name='rv_attach_image[]' border='0'>\n";
			}
		}
		$data[contents] = $data[image].$data[contents];
		$data[subject] = $data[subject].'<img src="../img/icon_attach.gif" border=0>';
	}
?>
<?if ( $data['parent']==$data['sno'] ){ ?>
<div style="border-top-width:1px; border-top-style:solid; border-top-color:#DCD8D6;">
<table width="100%" cellpadding="0" cellspacing="0" onclick="view_content(this, event);" class="hand">
<tr><td height="4" colspan="11"></td></tr>
<tr height="25" align="center" onmouseover="this.style.background='#F7F7F7'" onmouseout="this.style.background=''">
	<td width="60" class="noline"><input type="checkbox" name="confirmyn" value="<?=$data['sno']?>"></td>
	<td width="60"><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td width="70">
		<?php if ($data['goodsno']) { ?>
		<a href="../../goods/goods_view.php?goodsno=<?=$data['goodsno']?>" target="_blank"><?=goodsimg($data['img_s'],40,"style='border:1px solid #efefef'",1)?></a>
		<?php } ?>
	</td>
	<td align="left" style="line-height:17px">
	<?php if ($data['goodsno']) { ?>
	<div style="color:#999999"><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',825,800)" style="color:#0074BA;" class="small">[ <?=$data['goodsnm']?> ]</a></div>
	<?php } ?>
	<font color="#333333"><?=$data['subject']?></font><font class="ver8" color="#FF6709">(<?=$data['replecnt']?>)</font>
	</td>
	<td width="80">
		<?php if($data['m_no'] && $data['m_id']){ ?>
			<?php if($data['dormant_regDate'] == '0000-00-00 00:00:00'){ ?>
				<div><span id="navig" name="navig" m_id="<?=$data[m_id]?>" m_no="<?=$data[m_no]?>"><img src="../img/icon_crmlist.gif" /></span><?getlinkPc080($data['phone'],'phone')?><?getlinkPc080($data['mobile'],'mobile')?></div><div><span style="color:#616161;" class=ver8><?=$data[m_id]?></span></div>
			<?php } else { ?>
				<div style="color:#616161;" class="ver8"><?php echo $data[m_id]; ?><br />(�޸�ȸ��)</div>
			<?php } ?>
		<?php }else{ ?>
			<?=$data['m_id']?>
		<?php }?>
	</td>
	<td width="80"><font class="ver8" color="#333333"><?=substr($data['regdt'],0,10)?></font></td>
	<td width="80" align="left"><font class="ver8" color="#ef6d00"><span style="margin-left:10px;"><?=str_repeat( "��", $data['point'] )?></span></td>
	<td width="80" align="right"><font class="ver8" color="#ef6d00"><span style="margin-right:10px;"><?=number_format($data['emoney'])?> ��</span></td>
	<td width="50"><a href="javascript:popup2('../board/customer_register.php?mode=reviewReply&sno=<?echo($data['sno'])?>',800,800,1)"><img src="../img/i_reply.gif" /></a></td>
	<td width="50"><a href="javascript:popup2('../board/goods_review_register.php?mode=modify&sno=<?echo($data['sno'])?>',800,800)"><img src="../img/i_edit.gif" /></a></td>
	<td width="50"><a href="javascript:act_delete_case(<?= $i++?>)"><img src="../img/i_del.gif"></a></td>
</tr>
<tr><td height="4" colspan="11"></td></tr>
</table>
<div style="display:none;padding:5px 10px 10px 130px;"><font color="484848"><?=nl2br($data['contents'])?></font></div>
</div>
<?} else if ( $data['sno'] != $data['parent'] ){ // ���?>
<div style="border-top-width:1px; border-top-style:dotted; border-top-color:#DCD8D6;">
<table width="100%" cellpadding="0" cellspacing="0" onclick="view_content(this, event);" class="hand">
<tr><td height="4" colspan="11"></td></tr>
<tr height="25" align="center" onmouseover="this.style.background='#F7F7F7'" onmouseout="this.style.background=''">
	<td width="60" class="noline"><input type="checkbox" name="confirmyn" value="<?=$data['sno']?>"></td>
	<td width="60"><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td width="70"><? if($data['notice']==1){ ?>����<? }else { ?><img src="../img/btn_reply.gif" /><?}?></td>
	<td align="left" style="line-height:17px"><font color="#333333"><?=$data['subject']?></font></td>
	<td width="80"><?=$data['m_id']?></td>
	<td width="80"><font class="ver8" color="#333333"><?=substr($data['regdt'],0,10)?></font></td>
	<td width="80"></td>
	<td width="80"></td>
	<td width="50"></td>
	<td width="50"><a href="javascript:popup2('../board/goods_review_register.php?mode=modify&sno=<?echo($data['sno'])?>',800,800)"><img src="../img/i_edit.gif" /></a></td>
	<td width="50"><a href="javascript:act_delete_case(<?= $i++?>)"><img src="../img/i_del.gif"></a></td>
</tr>
<tr><td height="4" colspan="11"></td></tr>
</table>
<div style="display:none;padding:5px 10px 10px 130px;"><font color="484848"><?=nl2br($data['contents'])?></font></div>
</div>
<? } ?>
<? } ?>
<div style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCD8D6;width:100%;height:1px;font-size:0px;"></div>
<input type="hidden" name="nolist">
</form>

<div align="center" class="pageNavi"><font class="ver8"><?=$pg->page['navi']?></font></div>

<div style="float:right">
	<img src="../img/btn_notice_s.gif" alt="�����۵��" border="0" align='absmiddle' style="cursor:hand" onclick="javascript:popup2('../board/goods_review_notice.php?mode=noticeRegist',800,600)">
	<a href="javascript:go_excel()"><img src="../img/btn_download_s.gif" align='absmiddle' /></a>
</div>

<div>
<img src="../img/btn_allselect_s.gif" alt="��ü����"  border="0" align="absmiddle" style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javascript:PubAllSordes( 'select', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?> />
<img src="../img/btn_allreselect_s.gif" alt="���ù���"  border="0" align="absmiddle" style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javascript:PubAllSordes( 'reflect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?> />
<img src="../img/btn_alldeselect_s.gif" alt="��������"  border="0" align="absmiddle" style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javascript:PubAllSordes( 'deselect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?> />
<img src="../img/btn_alldelet_s.gif" alt="���û���" border="0" align="absmiddle" style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javaScript:act_delete();"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?> />
</div>

<div style="padding-top:15px"></div>

<div id="MSG01">
<table cellpadding="2" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�ı������� Ŭ���ϸ� �۳����� ������, �ٽ� ������ Ŭ���ϸ� ������ �����Ե˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />��ǰ�̹����� Ŭ���ϸ� ��â�� �Բ� ��ǰ���������� �̵��մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�ۼ��ڸ� Ŭ���Ͻø� ȸ�������� �Բ� ȸ���ֹ����� ���� ���� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script language="javascript">
var preContent;

//2012-01-16 dn �ͽ��÷η� 10 ��ũ��Ʈ ���� ����
function view_content(obj, e)
{
	if ( document.getElementById && ( e.srcElement.tagName == 'A' || e.srcElement.tagName == 'IMG' || e.srcElement.tagName == 'INPUT' ) ) return;
	else if ( !document.getElementById && ( e.target.tagName == 'A' || e.target.tagName == 'IMG' || e.srcElement.tagName == 'INPUT' ) ) return;

	var div = obj.parentNode;

	for (var i=1, m=div.childNodes.length;i<m;i++) {
		if (div.childNodes[i].nodeType != 1) continue;	// text node.
		else if (obj == div.childNodes[ i ]) continue;

		obj = div.childNodes[ i ];
		break;
	}

	if (preContent && obj!=preContent){
		obj.style.display = "block";
		preContent.style.display = "none";
	}
	else if (preContent && obj==preContent) preContent.style.display = ( preContent.style.display == "none" ? "block" : "none" );
	else if (preContent == null ) obj.style.display = "block";

	preContent = obj;
}

function go_excel(){
	if(confirm("�˻��� ����Ʈ�� ���� �Խù��� �ٿ�ε� �Ͻðڽ��ϱ�?")){
		location.href = "../board/goods_review_excel_list.php?<?=$_SERVER['QUERY_STRING']?>";
	}
}

function checkReplyUse(useSet) {
	if(useSet == "y") document.getElementById('reviewFavoriteReplyNo').disabled = false;
	else document.getElementById('reviewFavoriteReplyNo').disabled = true;
}

function act_delete_case(idx){
	if(!confirm("�������� ���� �Ͻø�, �亯�۵� ���� �����˴ϴ�.\n������ ������ �������� �ʽ��ϴ�.")) return;
	fmList.nolist.value = fmList['confirmyn'][idx].value;
	fmList.action = "../board/goods_review_indb.php?mode=delete" ;
	fmList.submit() ;
}

function act_delete(){

	if ( PubChkSelect( fmList['confirmyn'] ) == false ){
		alert( "�����Ͻ� ������ �����Ͽ� �ֽʽÿ�." );
		return;
	}

	if ( confirm( "�������� ���� �Ͻø�, �亯�۵� ���� �����˴ϴ�.\n������ ������ �������� �ʽ��ϴ�." ) == false ) return;

	var idx = 0;
	var codes = new Array();
	var count = fmList['confirmyn'].length;

	if ( count == undefined ) codes[ idx++ ] = fmList['confirmyn'].value;
	else {

		for ( i = 0; i < count ; i++ ){
			if ( fmList['confirmyn'][i].checked ) codes[ idx++ ] = fmList['confirmyn'][i].value;
		}
	}

	fmList.nolist.value = codes.join( ";" );
	fmList.action = "../board/goods_review_indb.php?mode=delete" ;
	fmList.submit() ;
}

window.onload = function() {
	cssRound('MSG01');
	UNM.inner();
	if(document.getElementById('reviewFavoriteReplyUse_n').checked) document.getElementById('reviewFavoriteReplyNo').disabled = true;
};
</script>