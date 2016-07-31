var nsAdminGoodsList = function()
{
	return {
		openMemo : function(goodsno)
		{
			nsAdminForm.dialog.open({
				type : 'url',
				title : '메모하기',
				contents : '../goods/adm_popup_goods_memo.php?goodsno=' + goodsno,
				width : 413,
				height : 247
			});
		},
		toggleOpen : function(goodsno, value)
		{

			// via ajax.
			var ajax = nsAdminForm.ajax('../goods/_indb_adm_goods_list.php', 'action=toggleOpen&goodsno=' + goodsno + '&value=' + value, function()
			{

				var json = ajax.transport.responseText.evalJSON(true);
				if (json == true) {
					nsAdminForm.loading.close();
				}
				else {
					alert('실패.');
					nsAdminForm.loading.close();
				}

			});

		},
		_unlinkCategory : function(event, sno)
		{
			var target = Event.element(event);

			// via ajax.
			var ajax = nsAdminForm.ajax('../goods/_indb_adm_goods_list.php', 'action=unlinkCategory&sno=' + sno, function()
			{

				var json = ajax.transport.responseText.evalJSON(true);
				if (json == true) {
					target.up('li').remove();
					nsAdminForm.loading.close();
				}
				else {
					alert('삭제 실패.');
					nsAdminForm.loading.close();
				}

			});
		},
		unlinkCategory : function(event, sno)
		{
			if (confirm('삭제하면 복구할 수 없습니다.\n삭제하시겠습니까?')) {
				this._unlinkCategory(event, sno);
			}
		},
		setSoldout : function()
		{
			var param = '';
			$$('input[name="goodsno[]"]:checked').each(function(el)
			{
				param += '&goodsno[]=' + el.value;
			});

			if (param == '') {
				alert('선택한 상품이 없습니다.');
				return false;
			}
			else if (!confirm('품절처리 하시겠습니까?')) {
				return false;
			}

			// via ajax.
			var ajax = nsAdminForm.ajax('../goods/_indb_adm_goods_list.php', 'action=setSoldout' + param, function()
			{

				var json = ajax.transport.responseText.evalJSON(true);
				if (json == true) {
					window.location.reload();
				}
				else {
					alert('실패.');
					nsAdminForm.loading.close();
				}

			});

		},
		del : function()
		{
			var param = '';
			$$('input[name="goodsno[]"]:checked').each(function(el)
			{
				param += '&goodsno[]=' + el.value;
			});

			if (param == '') {
				alert('선택한 상품이 없습니다.');
				return false;
			}
			else if (!confirm('상품을 삭제하면 복구할 수 없습니다.\n삭제하시겠습니까?')) {
				return false;
			}

			// via ajax.
			var ajax = nsAdminForm.ajax('../goods/_indb_adm_goods_list.php', 'action=delete' + param, function()
			{

				var json = ajax.transport.responseText.evalJSON(true);
				if (json == true) {
					window.location.reload();
				}
				else {
					alert('실패.');
					nsAdminForm.loading.close();
				}

			});

		},
		copy : function()
		{

			var param = '';
			$$('input[name="goodsno[]"]:checked').each(function(el)
			{
				param += '&goodsno[]=' + el.value;
			});

			if (param == '') {
				alert('선택한 상품이 없습니다.');
				return false;
			}
			else if (!confirm('동일한 상품을 하나더 자동등록 합니다. (시스템 상품코드 다름)')) {
				return false;
			}

			// via ajax.
			var ajax = nsAdminForm.ajax('../goods/_indb_adm_goods_list.php', 'action=copy' + param, function()
			{

				var json = ajax.transport.responseText.evalJSON(true);
				if (json == true) {
					window.location.reload();
				}
				else {
					alert('실패.');
					nsAdminForm.loading.close();
				}

			});

		},
		edit : function(goodsno)
		{
			nsAdminForm.openWindow('../goods/adm_popup_goods_form.php?mode=modify&popup=1&goodsno=' + goodsno, 1000, 600);
		},
		sort : function(sort)
		{
			var fm = document.frmList;
			fm.sort.value = sort;
			fm.submit();
		},
		sortInit : function(sort)
		{
			if (!sort)
				return;
			sort = sort.replace(" ", "_");
			var obj = document.getElementsByName('sort_' + sort);
			if (obj.length) {
				div = obj[0].src.split('list_');
				for ( i = 0; i < obj.length; i++) {
					chg = (div[1] == "up_off.gif") ? "up_on.gif" : "down_on.gif";
					obj[i].src = div[0] + "list_" + chg;
				}
			}
		},
		excel : {
			request : function(name, form)
			{
				if (confirm('다운 다 되면, 다운로드 목록에서 받을 수 있을꺼임')) {

					var query = '';

					if (form) {
						query = encodeURIComponent(form.serialize());
					}

					var ajax = nsAdminForm.ajax('../goods/_indb_adm_goods_excel_downloader.php', 'name=' + name + '&query=' + query, function()
					{
						nsAdminForm.loading.close();
					});
				}

			},
			download : function()
			{
				var url = '../background/adm_back_popup_dnxls.php';
				nsAdminForm.dialog.open({
					title : '엑셀(CSV) 다운로드',
					type : 'url',
					contents : url,
					width : 500,
					height : 500
				});
			},
			upload : function(f)
			{

				if (!chkForm(f))
					return false;

				f.target = "ifrmHidden";

				nsGodoLoadingIndicator.init({
					psObject : $$('iframe[name="ifrmHidden"]')[0],
					elWidth : 280,
					elHeight : 80,
					elMsg : '<img src="../img/progress_bar.gif">'
				});

				nsGodoLoadingIndicator.show();

				return true;

			}
		},
		toggleListExtraInfo : function()
		{
			var els = $$('.el-admin-goods-list-extra-info');

			var hide = els.first().getStyle('display') == 'none' ? true : false;

			els.each(function(el) {
				el.setStyle({
					display: hide ? '' : 'none'
				});
			});

			$('el-admin-goods-list-extra-info-toggle-button').src='../img/buttons/brn_' + (hide ? 'cls' : 'open') +'.gif';

			var expire_date = new Date(new Date().getTime() + 31536000000);
			setCookie( 'admin_goods_list_hide_extra_info', !hide, expire_date, '/');
		}

	};
}();
