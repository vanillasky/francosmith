jQuery(document).ready(function(){
	jQuery('#memoContent').html( jQuery('#memoContent').html().replace(/\n/g, "<br />") );
	jQuery('#memoSave').click(function () {
		jQuery('input:hidden[name=miniMemo]').val(jQuery('#memoContent').html());
		jQuery("form[name=fm_memo]").submit();
	});

	jQuery('#memoDel').click(function () {
		if(confirm("���� �� �޸�� ������ �� �����ϴ�.\n�����Ͻðڽ��ϱ�?")){
			jQuery('#memoContent').html("");
		}
	});
});