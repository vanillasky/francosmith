<?

$location = "ȸ������ > ȸ��Ż��/��������";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/fieldset.php";

$rejoindt = date('Ymd', time() - (($joinset['rejoin']-1)*86400));

list ($total) = $db->fetch("select count(*) from ".GD_LOG_HACK); # �� ���ڵ��

### �����Ҵ�
if (!$_GET['page_num']) $_GET['page_num'] = 10; # ������ ���ڵ��
$selected['page_num'][$_GET['page_num']] = "selected";
$selected['srejoin'][$_GET['srejoin']] = "selected";
$selected['sactor'][$_GET['sactor']] = "selected";

$orderby = ($_GET['sort']) ? $_GET['sort'] : "regdt desc"; # ���� ����
$selected['sort'][$orderby] = "selected";

$selected['skey'][$_GET['skey']] = "selected";

### ���
$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->field = "sno, m_id, name, ip, regdt, actor, if(date_format( regdt, '%Y%m%d' ) >= ".$rejoindt.", 1, 0) as rejoin ";
$db_table = "".GD_LOG_HACK."";

if ($_GET['skey'] && $_GET['sword']){
	if ( $_GET['skey']== 'all' ){
		$tmp = str_replace( "-", "", $_GET['sword'] );
	}

	if ( $_GET['skey']== 'all' ){
		$where[] = "( concat( m_id, name, ip ) like '%".$_GET['sword']."%' )";
	}
	else $where[] = $_GET['skey']." like '%".$_GET['sword']."%'";
}

if ($_GET['sactor']!='') $where[] = "actor='".$_GET['sactor']."'";
if ($_GET['srejoin']!='') $where[] = " if(date_format( regdt, '%Y%m%d' ) >= ".$rejoindt.", 1, 0)='".$_GET['srejoin']."'";

if ($_GET['sregdt'][0] && $_GET['sregdt'][1]) $where[] = "regdt between date_format(".$_GET['sregdt'][0].",'%Y-%m-%d 00:00:00') and date_format(".$_GET['sregdt'][1].",'%Y-%m-%d 23:59:59')";

$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);

?>

<form name="frmList">
<div class="title title_top">ȸ��Ż��/��������<span>Ż�� �Ǵ� ������ ȸ������ ������ Ȯ���մϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=5')"><img src="../img/btn_q.gif" hspace="2" align="absmiddle" /></a></div>
<table class="tb">
<col class="cellC" /><col class="cellL" style="width:250" />
<col class="cellC" /><col class="cellL" />
<tr>
	<td>Ű����˻�</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected['skey']['all']?>> ���հ˻� </option>
	<option value="m_id" <?=$selected['skey']['m_id']?>> ���̵� </option>
	<option value="name" <?=$selected['skey']['name']?>> �̸� </option>
	<option value="ip" <?=$selected['skey']['ip']?>> ������ </option>
	</select> <input type="text" NAME="sword" value="<?=$_GET['sword']?>" class="line" />
	</td>
	<td width=170>�簡�Կ��� <font class="small" color="#444444">I</font> ó������</td>
	<td>
	<select name="srejoin">
	<option value="" <?=$selected['srejoin']['']?>> ��ü </option>
	<option value="1" <?=$selected['srejoin']['1']?>> �Ұ��� </option>
	<option value="0" <?=$selected['srejoin']['0']?>> ���� </option>
	</select>
	<select name="sactor">
	<option value="">= ó������ =</option>
	<option value="1" <?=$selected['sactor']['1']?>> ����Ż�� </option>
	<option value="0" <?=$selected['sactor']['0']?>> �������� </option>
	</select>
	</td>
</tr>
<tr>
	<td>Ż��/������</td>
	<td colspan=3>
	<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][0]?>" onclick="calendar(event)" class="cline" /> ~
	<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][1]?>" onclick="calendar(event)" class="cline" />
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
	</td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>

<table width="100%">
<tr>
	<td class="pageInfo">
	�� <b><?=number_format($total)?></b>��, �˻� <b><?=number_format($pg->recode['total'])?></b>��, <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
	</td>
	<td align="right">
	<select name="sort" onchange="this.form.submit();">
	<option value="regdt desc" <?=$selected['sort']['regdt desc']?>>- Ż���� ���ġ�</option>
	<option value="regdt asc" <?=$selected['sort']['regdt asc']?>>- Ż���� ���ġ�</option>
	<optgroup label="------------"></optgroup>
	<option value="m_id desc" <?=$selected['sort']['m_id desc']?>>- ���̵� ���ġ�</option>
	<option value="m_id asc" <?=$selected['sort']['m_id asc']?>>- ���̵� ���ġ�</option>
	<option value="name desc" <?=$selected['sort']['name desc']?>>- �̸� ���ġ�</option>
	<option value="name asc" <?=$selected['sort']['name asc']?>>- �̸� ���ġ�</option>
	</select>&nbsp;
	<select name="page_num" onchange="this.form.submit()">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>�� ���</option>
	<? } ?>
	</select>
	</td>
</tr>
</table>
</form>

<form method="post" action="" name="fmList">
<table width=100% cellpadding=0 cellspacing=0>
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th width="60">��ȣ</th>
	<th width="120">���̵�</th>
	<th width="140">�̸�</th>
	<th width="80">ó������</th>
	<th>������</th>
	<th width="100">Ż��/������</th>
	<th width="80">�簡�Կ���</th>
	<th width="60">����</th>
	<th width="50">����</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
<tr><td colspan=10 height=4></td></tr>
<?
while ($data=$db->fetch($res)){
	$regdt = (substr($data[regdt],0,10)!=date("Y-m-d")) ? substr($data[regdt],0,10) : "<font color=#7070B8>".substr($data[regdt],11)."</font>";
	?>
<tr><td height=4 colspan=10></td></tr>
<tr height=25 align="center" onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="">
	<td><font class=ver8 color=616161><?=$pg->idx--?></td>
	<td><font class=ver81 color=0074BA><b><?=$data['m_id']?></b></font></td>
	<td><?=$data['name']?></td>
	<td><font class=extext><b><?=($data[actor] == '1' ? '����Ż��' : '��������')?></b></font></td>
	<td><font class=ver8><?=$data['ip']?></font></td>
	<td><font class=ver81 color=616161><?=$regdt?></font></td>
	<td><font class=small color=616161><?=($data['rejoin'] == '1' ? '�Ұ���' : '����')?></font></td>
	<td><a href="javascript:popupLayer('../member/hack_register.php?mode=modify&sno=<?echo($data['sno'])?>')"><img src="../img/btn_viewbbs.gif"></a></td>
	<td class="noline"><input type=checkbox name=confirmyn value="<?=$data['sno']?>"></td>
</tr>
<tr><td height=4 colspan=10></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>
<INPUT TYPE="hidden" style="width:300" NAME="nolist">
</form>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div align=right style="padding-right:10px;">
<img src="../img/btn_allselect_s.gif" alt="��ü����"  border="0" align="absmiddle" style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javascript:PubAllSordes( 'select', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
<img src="../img/btn_allreselect_s.gif" alt="���ù���"  border="0" align="absmiddle" style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javascript:PubAllSordes( 'reflect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
<img src="../img/btn_alldeselect_s.gif" alt="��������"  border="0" align="absmiddle" style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javascript:PubAllSordes( 'deselect', fmList['confirmyn'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
<img src="../img/btn_alldelet_s.gif" alt="���û���" border="0" align="absmiddle" style="cursor:hand" <?if ( $pg->recode['total'] != 0 ){?>onclick="javaScript:act_delete();"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
</div>

<div style="padding-top:15px"></div>



<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ���̴� ����Ʈ���� ȸ���� �����ϸ� �簡���� �ٷ� �����մϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<div style="padding-top:15px"></div>


<SCRIPT LANGUAGE=JavaScript><!--
/*-------------------------------------
 ����
-------------------------------------*/
function act_delete(){

	if ( PubChkSelect( fmList['confirmyn'] ) == false ){
		alert( "�����Ͻ� ȸ���� �����Ͽ� �ֽʽÿ�." );
		return;
	}

	if ( confirm( "������ ȸ���� ���� �����Ͻðڽ��ϱ�?\n�����Ŀ��� �ش� ȸ���� �簡���� ���������ϴ�." ) == false ) return;

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
	fmList.action = "../member/hack_indb.php?mode=delete" ;
	fmList.submit() ;
}
//--></SCRIPT>


<? include "../_footer.php"; ?>