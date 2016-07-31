<script>

var iciRow, preRow;

function spoit(obj)
{
	iciRow = obj;
	iciHighlight();
}

function iciHighlight()
{
	if (preRow) preRow.style.backgroundColor = "";
	iciRow.style.backgroundColor = "#FFF4E6";
	preRow = iciRow;
}

function moveTree(idx)
{
	var objTop = iciRow.parentNode.parentNode;
	var nextPos = iciRow.rowIndex+idx;
	if (nextPos==objTop.rows.length) nextPos = 0;
	if (objTop.moveRow) {
		objTop.moveRow(iciRow.rowIndex,nextPos);
	} else {
		if(idx > 0 && nextPos != 0) nextPos += idx;
		var beforeRow = objTop.rows[nextPos];
		iciRow.parentNode.insertBefore(iciRow, beforeRow);
	}
	window.focus();
}

function keydnTree(e)
{
	if (iciRow==null) return;
	e = e ? e : event;
	switch (e.keyCode){
		case 38: moveTree(-1); return false;
		case 40: moveTree(1); return false;
	}
}

function chkBoxAll(El,mode)
{
	if (!El || !El.length) return;
	for (i=0;i<El.length;i++){
		if (El[i].disabled) continue;
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
	}
}
document.onkeydown = keydnTree;

</script>
<?
@include "../../conf/orderXls.php";

if(!${$mode})${$mode} = $default[$mode];
else ${$mode} = getdefault($mode);

$tmp = ${$mode};

switch($mode) {
	case 'orderXls' : $title = "�ֹ��� �ٿ�ε� ���� ����"; break;
	case 'orderGoodsXls': $title = "�ֹ� ��ǰ�� �ٿ�ε� ���� ����"; break;
	case 'orderTodayGoodsXls': $title = "�����̼��ֹ� ��ǰ�� �ٿ�ε� ���ϼ���(�ǹ���ǰ)"; break;
	case 'orderTodayCouponXls': $title = "�����̼��ֹ� ��ǰ�� �ٿ�ε� ���ϼ���(������ǰ)"; break;
}
?>
<div class="title title_top"><?=$title?> <span>�ٿ�ε� ������ �ֹ����� �׸��� �����ϽǼ� �ֽ��ϴ�.</span> </div>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��������� <font color=0074BA>�׸��� ������� Ŭ���Ͻð� Ű������ ���� Ű</font>�� �̿��Ͽ� ������ �������ּ���</td></tr>
<tr><td><img src="http://guide.godo.co.kr/guide/img/sa_orderarticle_change.gif"></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA>���ö��� üũ�� �Ǿ� �ִ� �׸�</font>�� �������Ϸ� �ٿ�ε� �޾����ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�׸� ������ ���ÿ��θ� ���� �Ŀ��� �ϴ��� <font color=0074BA>�����ư�� ������ ������ ������ ����</font>�Ǿ� ���ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
<div style="padding-top:15px"></div>
<form method=post action="indb.php">
<input type=hidden name=mode value="<?=$mode?>">
<table width=100% border=0 bordercolor=#dfdfdf style="border-collapse:collapse">
<tr bgcolor=#313131 style="color:#ffffff" height=30>
	<th width=40>��ȣ</th>
	<th width=40><a href="javascript:void(0)" onClick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class=white>����</a></th>
	<th width=150>�׸�</th>
	<th>����</th>
</tr>
</table>
<table width=100% border=1 bordercolor=#dfdfdf style="border-collapse:collapse" frame=hsides rules=rows>
<? foreach($tmp as $k => $v){ ?>
<tr onclick="spoit(this)" height=30>
	<input type=hidden name=sval[] value="<?=$v[0]?>^<?=$v[1]?>^<?=$v[2]?>">
	<td align=center bgcolor=#f7f7f7 width=40 nowrap><font class=small1 color=444444><?=++$idx?></font></td>
	<td align=center width=40><input type=checkbox name='chk[]' value='<?=$v[1]?>' class=null <?=$v[3]?>>
	<td style="padding-left:5px;" width=150><b><?=$v[0]?></b></td>
	<td style="padding-left:10px" nowrap><font class=ver8 color=444444> - <?=$v[2]?></td>
</tr>
<? } ?>
</table>

<div class=button>
<input type=image src="../img/btn_save.gif">
</div>

</form>


