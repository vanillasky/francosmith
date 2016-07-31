<script>
	<?if($fn == 'M'){?>
		alert('수정이 완료 되었습니다.');
	<?}else{?>
		alert('코디 등록이 완료되었습니다. \n등록된 코디는 진열상태 NO인 상태로 코디 리스트에 등록됩니다. \n코디 리스트에서 코디 등록여부를 확인하신 후진열상태를 YES로 \n변경해 주세요.');
	<?}?>
	parent.opener.location.reload();
	parent.window.close();
</script>