IFRAME ���� ������¡ ��ũ��Ʈ
<script language="javascript">
<!--
var name = "<?=$_GET['name']?>";
var height = "<?=$_GET['height']?>";
if (name !='' && height !='' && parent.parent.document.getElementsByName(name)[0])
{
	parent.parent.document.getElementsByName(name)[0].height = height;
}
-->
</script>