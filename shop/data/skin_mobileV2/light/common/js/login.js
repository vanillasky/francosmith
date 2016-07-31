var cookie_id = decodeBase64(localStorage.getItem('Cookie_shop_ID_'+shop_key));

if(cookie_id){
	document.login_form.m_id.value = cookie_id;
	document.login_form.save_id.checked = true;
}

function chk_save(frm){

	chk_save_id(frm.save_id.checked);

	return true;
}

function chk_save_id(chk){

	var frm = document.login_form;
	if(chk && frm.m_id.value)	localStorage.setItem('Cookie_shop_ID_'+shop_key,encodeBase64(frm.m_id.value));
	else			localStorage.setItem('Cookie_shop_ID_'+shop_key,'');
}