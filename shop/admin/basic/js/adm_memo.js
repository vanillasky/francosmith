jQuery(document).ready(function(){
	jQuery('#memoContent').html( jQuery('#memoContent').html().replace(/\n/g, "<br />") );
	jQuery('#memoSave').click(function () {
		jQuery('input:hidden[name=miniMemo]').val(jQuery('#memoContent').html());
		jQuery("form[name=fm_memo]").submit();
	});

	jQuery('#memoDel').click(function () {
		if(confirm("삭제 된 메모는 복구할 수 없습니다.\n삭제하시겠습니까?")){
			jQuery('#memoContent').html("");
		}
	});
});