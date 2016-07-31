<script src="../interpark/js/_goods_form.js"></script>
<script>
IDFG.mode = "<?=$_GET['mode']?>";
IDFG.use = "<?=$inpkOSCfg['use']?>";
IDFG.inpk_prdno = "<?=$goods->getData('inpk_prdno')?>";
IDFG.inpk_regdt = "<?=$goods->getData('inpk_regdt')?>";
IDFG.inpk_moddt = "<?=$goods->getData('inpk_moddt')?>";
IDFG.inpk_dispno = "<?=$goods->getData('inpk_dispno')?>";
IDFG.display();
if(IDFG.use==="Y")
{
	var inpkPrdReqInfo = new window.InterparkProductRequireInfo();
	if(IDFG.inpk_dispno.trim().length)
	{
		inpkPrdReqInfo.setProductGroup(IDFG.inpk_dispno.substring(0, 9));
	}
	inpkPrdReqInfo.displayRequireInfoNotice();
}
</script>