<?

$location = "��ǰ���� > �̹���ȣ���� �ϰ���ȯ";
$scriptLoad = '<script src="../imgHostReplace.js"></script>';
include "../_header.php";
include "../../lib/page.class.php";
include "../../lib/imgHostReplace.class.php";
$imgHost = new imgHost($_SESSION['ftpConf']);

list ($total) = $db->fetch("select count(*) from gd_goods");

if (!$_GET[page_num]) $_GET[page_num] = 10;
$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
$checked[open][$_GET[open]] = "checked";
$checked[isToday][$_GET[isToday]] = "checked";

if ($_GET[sCate]){
	$sCategory = array_notnull($_GET[sCate]);
	$sCategory = $sCategory[count($sCategory)-1];
}

if ($_GET[indicate] == 'search'){
	$orderby = "a.goodsno desc";

	if ($_GET[cate]){
		$category = array_notnull($_GET[cate]);
		$category = $category[count($category)-1];
	}

	$db_table = "
	gd_goods a
	left join gd_goods_option b on a.goodsno=b.goodsno and link and go_is_deleted <> '1'
	";

	if ($category){
		$db_table .= "left join gd_goods_link c on a.goodsno=c.goodsno";

		// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
		$where[]	= getCategoryLinkQuery('c.category', $category, 'where');
	}
	if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
	if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);

	$pg = new Page($_GET[page], $_GET[page_num]);
	$pg->field = "distinct a.goodsno,a.goodsnm,a.img_s,a.open,a.regdt,a.longdesc,a.totstock,b.*";
	$pg->setQuery($db_table,$where,$orderby);
	$pg->exec();

	$res = $db->query($pg->query);
}

?>

<script><!--
function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}
--></script>

<div class="title title_top">�̹���ȣ���� �ϰ���ȯ<span>�� ���θ��� ��ǰ�����̹����� �̹���ȣ�������� �ϰ���ȯ�մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=20')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;color:#777777;" id="goodsInfoBox">
<div><font color="#EA0095"><b>�ʵ�! �̹���ȣ���� �ϰ���ȯ�̶�?</b></font></div>
<div style="padding-top:2">���¸��Ͽ� ������ ��ڴ� �ݵ�� �̹���ȣ������ ����ؾ� �մϴ�.</div>
<div style="padding-top:2">�� ������ ����� ��ǰ���� ���� ��� �ϳ��ϳ� �̹���ȣ�������� �����ϴ� �ð��� ���� �ɸ��� �˴ϴ�.</div>
<div style="padding-top:2">�Ʒ� ����� �� ���θ��� �÷��� ��ǰ�����̹����� �̹���ȣ�������� ������ ��ȯ���ִ� ����Դϴ�.</div>
<div style="padding-top:2">�� ����� ����Ϸ��� �̹���ȣ������ ��û�Ǿ� �־�� �մϴ�. <a href="http://hosting.godo.co.kr/imghosting/service_info.php" target=_blank><img src="../img/btn_imghost_infoview.gif" align=absmiddle></a> �� �����ϼ���!</div>

</div>


<!-- ��ǰ������� : start -->
<form name=frmList onsubmit="return chkForm(this)">
<input type="hidden" name="indicate" value="search">

<div style="padding:10 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">��</font> ���� �Ʒ����� �̹���ȣ�������� ��ȯ�� ��ǰ�� �˻��մϴ�.</b></font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�з�����</td>
	<td>
	<script>new categoryBox('cate[]',4,'<?=$category?>');</script>
	<span class="noline" style="margin-left:10px;"><input type=image src="../img/btn_search_s.gif" align="absmiddle"></span>
	</td>
</tr>
<tr>
	<td>�˻���</td>
	<td>
	<select name=skey>
	<option value="goodsnm" <?=$selected[skey][goodsnm]?>>��ǰ��
	<option value="a.goodsno" <?=$selected[skey]['a.goodsno']?>>������ȣ
	<option value="goodscd" <?=$selected[skey][goodscd]?>>��ǰ�ڵ�
	<option value="keyword" <?=$selected[skey][keyword]?>>����˻���
	</select>
	<input type=text name=sword class=lline value="<?=$_GET[sword]?>">
	</td>
</tr>
<tr>
	<td>��ǰ��¿���</td>
	<td class=noline>
	<input type=radio name=open value="" <?=$checked[open]['']?>>��ü
	<input type=radio name=open value="11" <?=$checked[open][11]?>>��»�ǰ
	<input type=radio name=open value="10" <?=$checked[open][10]?>>����»�ǰ
	</td>
</tr>
</table>

<div style="margin-top:20px;">
	<div style="float:left;" class="pageInfo ver8">�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode[total]?></b>��, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</div>
	<div style="float:right;">
		<img src="../img/sname_output.gif" align=absmiddle>
		<select name=page_num onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>�� ���
		<? } ?>
		</select>
	</div>
</div>
</form>
<!-- ��ǰ������� : end -->

<form name="fmList" method="post" onsubmit="return ( imgHost.submit(this) ? false : false );">
<input type=hidden name=mode>
<input type=hidden name=category value="<?=$category?>">

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr height=35 bgcolor=4a3f38>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>����</b></a></th>
	<th><font class=small1 color=white><b>��ȣ</b></th>
	<th></th>
	<th></th>
	<th><font class=small1 color=white><b>��ǰ��</b></th>
	<th style="padding-top:3"><font class=small1 color=white><b>��ȯ�� �ʿ���<br> �̹�������</b></th>
	<th><font class=small1 color=white><b>�����</b></th>
	<th><font class=small1 color=white><b>����</b></th>
	<th><font class=small1 color=white><b>���</b></th>
	<th><font class=small1 color=white><b>����</b></th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=50><col width=40><col width=10><col><col width=120><col width=60><col width=80><col width=55 span=2>
<?
while (is_resource($res) && $data=$db->fetch($res)){
	$stock = $data['totstock'];
	$cnt = $imgHost->imgStatus($data['longdesc']);
?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[goodsno]?>" onclick="iciSelect(this)"></td>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td style="border:1px #e9e9e9 solid;"><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a></td>
	<td></td>
	<td><a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><font class=small1 color=0074BA><?=$data[goodsnm]?></a></td>
	<td align=center><font class="ver81" color="#444444" id="in_<?=$data[goodsno]?>" style="font-weight:bold; font-size:16pt;"><?=number_format($cnt['in'])?></font></td>
	<td align=center><font class="ver81" color="#444444"><?=substr($data[regdt],0,10)?></td>
	<td align=center>
	<font color="#4B4B4B"><font class=ver81 color="#444444"><b><?=number_format($data[price])?></b></font>
	<div style="padding-top:2px"></div>
	<img src="../img/good_icon_point.gif" align=absmiddle><font class=ver8><?=number_format($data[reserve])?></font>
	</td>
	<td align=center><font class="ver81" color="#444444"><?=number_format($stock)?></td>
	<td align=center><img src="../img/icn_<?=$data[open]?>.gif"></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<!-- ���� : start -->
<div style="padding:20 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">��</font> �� ��ǰ����Ʈ���� ������ ��ǰ�� ��ǰ�����̹����� �̹���ȣ�������� ��ȯ�մϴ�.</b></font></div>
<div class="noline" style="padding:0 0 5 5">

	<div style="padding-left:210px;"><input type=image src="../img/btn_confirm.gif" align=top></div>
</div>
<!-- ���� : end -->

</form>

<div style="padding-top:30"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ�����̹�����?
<ul style="margin:0px 0px 0px 34px;">
<li>��ǰ��Ͻ� '��ǰ����'���� �÷��� �̹����� �ǹ��մϴ�.</li>
</ul>
</td></tr>
<tr><td height=4></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�̹���ȣ�����̶�?
<ul style="margin:0px 0px 0px 34px;">
<li>���¸��� �� �����ڰ� ���� �ܺλ���Ʈ���� ����� �����ϵ��� �������� �̹��� ���뼭���� �����ϴ� ���¸��Ͽ� ���� �����Դϴ�.</li>
<li>�� ����� �̿��Ͻ÷��� �̹���ȣ������ ���� ��û�ϼž� �մϴ�. <a href="http://hosting.godo.co.kr/imghosting/service_info.php" target=_blank><img src="../img/btn_imghost_infoview.gif" align=absmiddle></a> �� �����ϼ���!</li>
<li>�̹���ȣ������ �̹��� ���� ��ũ�ּ�(������ URL)�� �̹������� �����ּ�(FTP)�� �ٸ��ϴ�.<br>������ URL : FTPID.godohosting.com / FTP �ּ� : ftp.FTPID.godohosting.com</li>
</ul>
</td></tr>
<tr><td height=4></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ȯ�� �ʿ��� �̹��� ����
<ul style="margin:0px 0px 0px 34px;">
<li>��ǰ�����̹��� �߿��� <u>�̹���ȣ�������� ��ȯ�� �ʿ��� �̹��� ����</u>�� �����ݴϴ�.</li>
</ul>
</td></tr>
<tr><td height=4></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�̿���/����
<ol type="1" style="margin:0px 0px 0px 40px;">
<li><u>��ǰ�����ϱ�</u> : ��ǰ�� �˻��� �� ��ǰ�� ��ȯ�� �ʿ��� �̹��������� �����Ͽ� �̹���ȣ�������� ��ȯ�� ��ǰ�� �����մϴ�.</li>
<div style="padding-top:2"></div>
<li><u>�����û�ϱ�</u> : [Ȯ��] ��ư�� Ŭ���Ͽ� ������ ��û�մϴ�.</li>
<div style="padding-top:2"></div>
<li><u>FTP �Է��ϱ�</u> : �̹���ȣ���� FTP ���������� �Է��մϴ�.</li>
<div style="padding-top:2"></div>
<li><u>�̹�������</u> : ��ǰ�����̹��� �߿��� ��ȯ�� �ʿ��� �̹����� �̹���ȣ�������� ���۵˴ϴ�.<br>
+ <b>'/goods_������' ����</b>�� ���۵˴ϴ�.<br>
+ ������ �̹������� �̹���ȣ���ÿ� �����ϸ� <b>�����</b>�� �˴ϴ�.<br>
+ <span class=color_ffe><b>�����̹����� �ٸ� �������� ����ϰ� ���� �� �����Ƿ� �ڵ� �������� �ʽ��ϴ�.</b></span><br>
&nbsp; '�����ΰ��� > webFTP�̹������� > data > editor'���� �̹���üũ �� ���������ϼ���.
</li>
<div style="padding-top:2"></div>
<li><u>�̹��� �ּ� ����</u> : ��ǰ�����̹����� ��ũ�ּҰ� ���¸����� ������ �̹���ȣ������ ��ũ�ּҷ� ����˴ϴ�.</li>
<div style="padding-top:2"></div>
<li><u>��������</u> : ������ ����Ǹ� [close] ��ư�� Ŭ���մϴ�.</li>
</ol>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script language="javascript"><!--
imgHost.ftp = <?=(session_is_registered('ftpConf') ? 'true' : 'null')?>;
--></script>

<? include "../_footer.php"; ?>