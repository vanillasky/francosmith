<?php

$location = '����Ȯ�� �������� > �޴�������Ȯ�� ����';
include '../_header.php';

$hpauth = Core::loader('Hpauth');

$hpauthConfig = $hpauth->loadConfig();

if (!$hpauthConfig['serviceCode']) $hpauthConfig['serviceCode'] = 'mcerti';
$currentHpauthConfig = $hpauth->loadServiceConfig($hpauthConfig['serviceCode']);

?>

<script type="text/javascript">
window.onload = function()
{
	var useyn = "<?php echo $currentHpauthConfig['useyn']; ?>";
	chgifrm("<?php echo $hpauthConfig['serviceCode']; ?>");
	if (useyn === "y") setHpauth("<?php echo $hpauthConfig['serviceCode']; ?>");
};

function chgifrm(name)
{
	var pgIfrm = document.getElementById("pgifrm");
	if (pgIfrm) {
		if (window.useTab) window.useTab.className = "";
		pgIfrm.src = "adm_member_auth.hpauth." + name + ".php";
		window.useTab = document.getElementById("tab-" + name);
		if (window.useTab) window.useTab.className = "active";
	}
}
function setHpauth(name)
{
	if (window.useHpauth) window.useHpauth.innerHTML = "";
	window.useHpauth = document.getElementById("use-" + name);
	if (window.useHpauth) window.useHpauth.innerHTML = "[�����]";
}
</script>

<style type="text/css">
#pgtab td{
	cursor: pointer;
	font-weight: bold;
	background: #ffffff;
	color: #627dce;
	border: solid #627dce 1px;
}
#pgtab td.active{
	background: #627dce;
	color: #ffffff;
}
#pgtab .use-sign{
	color: #ff0000;
	font-size: 11px;
	margin-left: 3px;
}
#pgtab .active .use-sign{
	color: #ffffff;
}
</style>

<div class="title title_top">
	�޴�������Ȯ�� ���� <span>�޴��� ����Ȯ�� ���񽺿� �ʿ��� ������ ���� �մϴ�.</span>
	<a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=member&no=21')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<table style="border-collapse: collapse; border: solid 5px #627dce;" width="100%">
	<tr>
		<td colspan="10" align="center" style="padding: 10px 0px 10px 12px; color: #627dce">����Ͻ� �޴����������� ���� ��ü �� ���� Ŭ���� �� ���� ������ �Է��ϼ���.</td>
	</tr>
	<tr align="center" height="40" id="pgtab">
		<td id="tab-mcerti" width="50%" onclick="chgifrm('mcerti');">Mcerti<span id="use-mcerti" class="use-sign"></span></td>
		<td id="tab-dream" width="50%" onclick="chgifrm('dream');">�帲��ť��Ƽ<span id="use-dream" class="use-sign"></span></td>
	</tr>
</table>

<div style="padding-top: 20px"></div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<iframe id="pgifrm" width="100%" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="10" scrolling="no"></iframe>
		</td>
	</tr>
</table>

<?php

include '../_footer.php';

?>