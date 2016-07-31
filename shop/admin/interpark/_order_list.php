<script language="javascript"><!--
function isInpkClaim()
{
	var img = document.getElementsByTagName('img');
	for (i = 0; i < img.length; i++)
	{
		if (img[i].src.match(/inflow_interpark.gif/))
		{
			var imgAtag = img[i].parentNode;
			if (imgAtag.tagName == 'A')
			{
				var idnm = 'claim'+ i;
				var elem = imgAtag.parentNode.insertBefore(document.createElement('span'), imgAtag.nextSibling);
				elem.innerHTML = '<span id="'+ idnm +'" class="small1"></span>';

				var ordno = imgAtag.parentNode.parentNode.parentNode.cells[0].childNodes[0].value;

				var urlStr = "../interpark/indb.php?mode=isClaim&ordno=" + ordno + "&dummy=" + new Date().getTime();
				var ajax = new Ajax.Updater( idnm, urlStr, { method: "get" } );
			}
		}
	}
}

if ("<?=$inpkCfg['use']?>" == 'Y' || "<?=$inpkOSCfg['use']?>" == 'Y') isInpkClaim();
--></script>