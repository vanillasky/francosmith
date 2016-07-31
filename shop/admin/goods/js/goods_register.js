var nsAdminGoodsForm = function()
{
	return {
		openMemo : function(goodsno)
		{
			nsAdminForm.dialog.open({
				type : 'url',
				contents : '../goods/adm_popup_goods_memo.php',
				width : 500,
				height : 500
			});
		},
		openColorTable : function(idx,bu) {
			var hrefStr = '../proc/help_colortable.php?iconidx='+idx+'&target='+bu;
			var win = popup_return( hrefStr, 'colortable', 400, 400, 600, 200, 0 );
			win.focus();
		},
		checkDuplicatedValue : function(val, column, goodsno)
		{
			if (val.trim() == '') {
				return;
			}

			if (!goodsno) {
				goodsno = '';
			}

			var ajax = nsAdminForm.ajax('../goods/_indb_adm_goods_form.php', 'action=checkUniqueValue&value=' + val + '&column=' + column + '&goodsno=' + goodsno, function()
			{
				var json = ajax.transport.responseText.evalJSON(true);
				if (json == true) {
					alert('사용이 가능한 상품코드 입니다.');
				}
				else {
					alert('상품코드가 존재합니다.');
				}
			});

		},
		validate : function(obj)
		{

			if ( typeof (obj['category[]']) == 'undefined') {
				if (document.getElementsByName('cate[]')[0].value)
					nsAdminGoodsForm.category.add();
				else {
					alert("카테고리를 등록해주세요");
					document.getElementsByName('cate[]')[0].focus();
					return false;
				}
			}
			if (!nsAdminGoodsForm.checkExtrainfoTitle()) {
				alert('항목명은 중복될 수 없습니다.');
				return false;
			}

			// 등록 모드 일때
			if (obj['mode'].value != 'modify' && obj['use_option'].value == '1') {
				if (nsAdminGoodsForm.option.optionList.length < 1) {
					alert('가격옵션 추가/등록 하기에 입력한 옵션값이 있습니다. 확인해주세요.');
					return false;
				}
			}

			// 삭제, 출력
			if (obj['use_option'].value == '1') {

				if ($('el-option-list').down('tbody').childElements().size() > 0) {

					if ($$('input[name^=option_is_display]:checked').size() < 1) {
						alert('출력여부는 옵션 1개이상 출력 필수. 옵션 출력여부를 확인해 주세요.');
						return false;
					}

					if ($$('input[name^=option_is_deleted]:checked').size() == $$('input[name^=option_is_deleted]').size()) {
						alert('옵션 1개이상 등록/출력 필수. 옵션삭제 여부를 확인해 주세요.');
						return false;
					}

					// 대표 가격 지정 옵션은 삭제 할 수 없음.
					try {

						var _display = $('el-option-list').down('tbody').select('input[name^="option_is_display"]');
						var _deleted = $('el-option-list').down('tbody').select('input[name^="option_is_deleted"]');

						var _idx = false;

						_display.each(function(el, i) {
							if (_idx === false && el.checked) {
								_idx = i;
								if (_deleted[_idx].checked) {
									throw {};
								}
							}
						});
					}
					catch (e) {
						alert('대표가격 지정 옵션은 삭제할 수 없습니다.');
						return false;
					}

				}
				else {
					obj['use_option'].value = '0';
				}

			}

			// 구매수량 설정 (묶음 주문 단위가 입력 된 경우, 최소/최대 구매수량은 묶음 주문 단위의 배수여야 한다)
			if (obj['sales_unit'].value) {

				var salse_unit = parseInt(obj['sales_unit'].value);

				try {
					$w('min_ea max_ea').each(function(k)
					{
						if (obj[k].value) {
							if (parseInt(obj[k].value) % salse_unit > 0) {
								obj[k].focus();
								throw {};
							}
						}
					});
				}
				catch (e) {
					alert('최소/최대 구매수량 설정 값을 묶음개수 단위에 맞게 조정해 주세요.');
					return false;
				}

			}

			// 폼 체크
			if (!chkForm(obj))
				return false;
			if (!nsAdminGoodsForm.information.formValidator())
				return false;

			// 관련 상품 정보
			nsAdminGoodsForm.relate.make();

			// hide anchor helper
			try {
				nsAdminForm.anchorHelper.hide();
			}
			catch (e) {
			}

			// loading
			nsGodoLoadingIndicator.init({
				psObject : $$('iframe[name="ifrmHidden"]')[0],
				elWidth : 280,
				elHeight : 80,
				elMsg : '<img src="../img/progress_bar.gif">'
			});

			nsGodoLoadingIndicator.show();

			obj.submit();
			return true;

		},
		checkExtrainfoTitle : function()
		{
			var obj = document.getElementsByName('title[]');
			for (var i = 0; i < obj.length; i++) {
				for (var j = 0; j < obj.length; j++) {
					if (i != j && obj[i].value == obj[j].value && obj[i].value && obj[j].value) {
						return false;
					}
				}
			}
			return true;
		},
		setDeliveryType : function()
		{
			var obj = document.getElementsByName('delivery_type');
			/*
			[0] : 기본 배송 정책에 따름
			[1] : 무료배송
			[4] : 고정 배송비
			[5] : 수량별 배송비
			[3] : 착불 배송비
			*/
			// 배송비 필드 숨김
			var k = 0;
			$w('0 1 3 4 5').each(function(v)
			{
				if ($('gdi' + v))
					$('gdi' + v).setStyle({
						display : (obj[k].checked == true) ? 'inline' : 'none'
					});
				k++;
			});
			return;
		},
		buyable : {
			openMemberGroupSelector : function()
			{
				var str = $('buyable_member_group').value;

				nsAdminForm.dialog.open({
					type : 'url',
					title : '구매가능 회원그룹',
					contents : '../goods/adm_popup_goods_buyable_member_group_selector.php?str=' + str,
					width : 500,
					height : 500
				});
			},
			setMemberGroup : function(str)
			{
				$('buyable_member_group').value = str;

			}
		},
		tax : {
			vat : function()
			{
				var rate, vat;
				var form = $('goods-form');
				var price = parseFloat($F(form['price']));

				// 과세
				if (nsAdminForm.getRadioValue(form['tax']) == '1') {
					if ($F(form['vat_rate'])) {
						rate = $F(form['vat_rate']);
					}
					else {
						Form.Element.setValue(form['vat_rate'], 10);
						rate = 10;
					}
					rate = $F(form['vat_rate']) || 10;
					vat = price - Math.round(price / (1 + parseFloat(rate) / 100));
				}
				// 비과세
				else {
					rate = 0;
					vat = 0;
				}
				// 판매가격 입력시, 상품가격과 세액을 계산
				$('el-vat-indicator-price').update(comma(price - vat));
				$('el-vat-indicator-vat').update(comma(vat));

			}
		},
		category : {
			_getTr : function(val, txt)
			{
				var tr = '';

				tr += '<tr>';
				tr += '	<td>' + txt + '</td>';
				tr += '	<td>';
				tr += '		<input type="text" name="category[]" value="' + val + '" style="display:none;" />';
				tr += '		<input type="hidden" name="sort[]" value="' + (Math.round((new Date()).getTime() / 1000)) + '" class="sortBox right" maxlength=10 />';
				tr += '	</td>';
				tr += '	<td>';
				tr += '		<a href="javascript:void(0);" onclick="nsAdminGoodsForm.category.del(event);"><img src="../img/i_del.gif" align="absmiddle" /></a>';
				tr += '	</td>';
				tr += '</tr>';

				return tr;

			},
			_isAdded : function(val)
			{
				try {
					$$('input[name="category[]"]').each(function(el)
					{
						if (el.value == val)
							throw {};
					});

				}
				catch (e) {
					return true;
				}
				return false;

			},
			add : function(multi)
			{

				var form = $('goods-form');
				var self = this;

				var tbody = $('objCategory').down('tbody');

				var txt, val, textVal;

				// 상품분류 연결방식 전환 여부에 따른 처리
				if (typeof(form['_CATEGORY_NEW_METHOD_']) != 'undefined') {
					var _CATEGORY_NEW_METHOD_	= true;
				} else {
					var _CATEGORY_NEW_METHOD_	= false;
				}

				// 일괄 선택창 열려 있는지 체크.
				if (!multi) {
					var cate = form['cate[]'];
					txt = [];

					for (var i = 0, m = cate.length; i < m; i++) {
						if (cate[i].value) {
							txt[txt.length] = cate[i][cate[i].selectedIndex].text;
							val = cate[i].value;

							if (_CATEGORY_NEW_METHOD_ == true) {
								textVal	= txt.join(' > ');

								if (!self._isAdded(val)) {
									tbody.insert({
										bottom : self._getTr(val, textVal)
									});
								}
							}
						}
					}

					if (!val) {
						alert('카테고리를 선택해주세요');
						return;
					}

					if (_CATEGORY_NEW_METHOD_ == false) {
						txt = txt.join(' > ');

						if (!self._isAdded(val)) {
							tbody.insert({
								bottom : self._getTr(val, txt)
							});
						}
					}
				}
				else {
					multi.each(function(el)
					{
						txt = el.up('label').getText();
						val = el.value;

						if (!self._isAdded(val)) {
							tbody.insert({
								bottom : self._getTr(val, txt)
							});
						}
					});

					try {
						nsAdminForm.dialog.close();
					}
					catch (e) {
					}

				}
			},
			del : function(event)
			{
				var target = Event.element(event);
				target.up('tr').remove();
			},
			openCategorySelector : function()
			{
				var cate = [];

				$$('input[name="category[]"]').each(function(el){
					cate.push(el.value);
				});

				nsAdminForm.dialog.open({
					type : 'url',
					title : '상품분류 일괄선택',
					contents : '../goods/adm_popup_goods_category_selector.php?cate=' + cate.join(','),
					width : 800,
					height : 250
				});
			}
		},
		option : {
			mode : null,
			pageSize : 10,
			currentPage : 1,
			optionTemp : [],
			optionList : [],
			rawData : [],
			_colSize : 0,
			get_js_compatible_key : function(str) {	// @see : lib.func.php

				str = str.replace(/&/g, "&amp;").replace(/\"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

				var _key = "";

				for (var i=0,m=str.length;i<m;i++) {
					_key += str.charAt(i) != '|' ? str.charCodeAt(i) : '|';
				}

				return _key.toUpperCase();
			},
			setStockFields : function()
			{
				var val = nsAdminGoodsForm.purchase.getType();

				$$('input[name^="option_stock\["]').each(function(el) {
					if (val == 2) {
						el.value = '';
						el.disabled = true;
					}
					else {
						el.disabled = false;
					}
				});

				try {
					if (val == 2) {
						$('pchsno').disabled = true;
						$('pchs_pchsdt').disabled = true;
					}
					else {
						$('pchsno').disabled = false;
						$('pchs_pchsdt').disabled = false;
					}
				}
				catch (e) {}

			},
			bindPricing : function()
			{
				var self = this;

				var options = $('el-option-list').down('tbody').childElements();

				if (options.size()) {

					options.each(function(row){

						row.select('input').each(function(el){

							if (el.name.indexOf('option_') > -1 && el.name.indexOf('stock') == -1) {

								if (el.type == 'text') {
									el.observe('keyup', self.pricing);
									el.observe('pricing:changed', self.pricing);
								}
								else if (el.type == 'checkbox') {
									el.observe('click', self.pricing);
								}
							}
						});

					});

					this.pricing();

				}

			},
			pricing : function(event)
			{

				// 출력 체크박스의 맨 처음 인덱스
				var idx = false;

				try {
					$('el-option-list').down('tbody').select('input[name^="option_is_display"]').each(function(el, i) {

						if (el.checked && idx === false) {
							idx = i;
							el.up('tr').addClassName('goods-option-highlight');
						}
						else {
							el.up('tr').removeClassName('goods-option-highlight');
						}

					});
				}
				catch (e) {}

				if (idx === false) {
					return;
				}

				var from = 'goods';

				if (event) {
					var target = Event.element(event);
					var tmp = target.name.match(/^(option_([a-zA-Z_]+)\[)(.+)?\]$/);
					if (tmp != null) {
						from = 'option';
					}
				}

				var tmp, o;

				$('el-option-list').down('tbody').childElements()[idx].select('input[type=text]').each(function(el){

					tmp = el.name.match(/^(option_([a-zA-Z]+)\[)(.+)?\]$/);
					if (tmp != null) {
						o = $$('input[name="goods_' + tmp[2] + '"]').first();

						if (o) {
							if (from == 'option') {
								o.value = el.value;
							}
							else {
								el.value = o.value;
							}
						}
					}
				});

			},
			toggle : function(b)
			{
				var o = $('el-option-form');

				if ( typeof b != 'boolean') {

					if (o.getStyle('display') == 'none') {
						b = true;
					}
					else {
						b = false;
					}
				}

				o.setStyle({
					display : b ? '' : 'none'
				});
				$('totstock').disabled = b;
				try {
					$('pchs_stock').disabled = b;
				} catch (e) {};
				$('use_option').value = b ? '1' : '0';

				// 이미지 제어
				$('el-use-option-toggle-button').src = b ? '../img/btn_priceopt_reset.gif' : '../img/btn_priceopt_add.gif';
				$('el-use-option-toggle-help').update( b ? '가격옵션 출력되지 않음' : '이 상품의 옵션이 여러개인경우 등록하세요 (색상, 사이즈 등)');

			},
			init : function(option)
			{
				var defaultOption = {
					pageSize : 10,
					mode : 'register'
				};

				var options = Object.extend(defaultOption, option || {});

				this.mode = options.mode;
				this.pageSize = options.pageSize;

				if (this.mode == 'modify') {
					this.displayTableBody($('el-option-list'));
					this.drawPaging();
				}

				this.bindPricing();

			},
			getOptionSize : function()
			{
				if (this.mode == 'modify') {
					return $('el-option-list').down('tbody').childElements().size();
				}
				else {
					return this.optionList.size();
				}
			},
			preset : {
				save : function()
				{
					if (! nsAdminGoodsForm.option.validateOptionForm()) {
						return false;
					}

					nsAdminForm.dialog.open({
						type : 'url',
						title : '옵션바구니 저장',
						contents : '../goods/adm_popup_goods_option_preset_save.php',
						width : 250,
						height : 150
					});

				},
				load : function()
				{
					nsAdminForm.dialog.open({
						type : 'url',
						title : '옵션바구니 적용하기',
						contents : '../goods/adm_popup_goods_option_preset_list.php',
						width : 500,
						height : 500
					});
				},
				set : function(data)
				{
					var tbody = $('el-option-table').down('tbody');

					// remove additional fields;
					tbody.childElements().each(function(el, idx)
					{
						if (idx >= 1)
							el.remove();
					});

					var addFieldCount = Object.keys(data).length - 1;
					for (var i = 0; i < addFieldCount; i++) {
						nsAdminGoodsForm.option.add();
					}

					for (var i = 0, m = Object.keys(data).length; i < m; i++) {
						$$('input[name="option_name[]"]')[i].value = Object.keys(data)[i];
						$$('input[name="option_value[]"]')[i].value = data[Object.keys(data)[i]];
					}

				},
				openForm : function(sno)
				{
					var url = '../goods/adm_popup_goods_option_preset_form.php' + ( sno ? '?sno=' + sno : '');
					window.location.href = url;

				},
				openPresetSample : function()
				{
					nsAdminForm.dialog.open({
						type : 'url',
						title : '옵션값 샘플',
						contents : '../goods/adm_popup_goods_option_preset_sample.php',
						width : 780,
						height : 750
					});
				},
				del : function(sno)
				{
					// confirm
					ifrmHidden.location.replace('../goods/indb_adm_popup_option_preset_form.php?action=delete&sno=' + sno);
				}
			},
			combineArray : function(array, index)
			{
				if (!index) {
					index = 0;
				}
				var arraySize = array.size();
				var nextIndex = index + 1;
				var row;

				for (var i = 0, m = array[index].size(); i < m; i++) {
					this.optionTemp[index] = array[index][i];

					if (array[nextIndex]) {
						this.combineArray(array, nextIndex);
					}
					if (arraySize == nextIndex) {
						row = this.optionTemp.slice(0);
						// like clone
						this.optionList.push(row);
					}
				}
			},
			setOptionListFromArray : function(array)
			{
				// 관련값 초기화후 생성
				this.optionTemp = [];
				this.optionList = [];
				this.combineArray(array);

			},
			validateOptionForm :  function()
			{
				try {
					$$('input[name="option_name[]"]').each(function(el, idx)
					{
						if (el.value.trim() == '') {
							throw {};
						}
					});

					$$('input[name="option_value[]"]').each(function(el, idx)
					{
						if (el.value.trim() == '') {
							throw {};
						}
					});

				}
				catch (e) {
					alert('옵션명, 옵션값을 입력해 주세요.');
					return false;
				}

				return true;
			},
			generateOptionArray : function()
			{
				var data = [];
				var _data;

				if (! this.validateOptionForm()) {
					return false;
				}

				$$('input[name="option_value[]"]').each(function(el)
				{
					// 공백 제거
					_data = [];
					el.value.split(',').each(function(val)
					{
						val = val.trim();
						if (val) {
							_data.push(val);
						}
					});
					data.push(_data);
				});

				return data;
			},
			drawTable : function()
			{
				var table = $('el-option-list');

				this.drawTableHead(table);
				this.drawTableBody(table);
				this.displayTableBody(table);
				this.drawTableFoot(table);
				this.drawPaging();
			},
			drawTableHead : function(table)
			{
				var tr = '<tr>';

				// 옵션명
				$$('input[name="option_name[]"]').each(function(el)
				{
					tr += '<th>' + el.value + '</th>';
				});

				tr += '	<th style="width:80px;">재고</th>';
				tr += '	<th style="width:80px;">옵션판매금액</th>';
				tr += '	<th style="width:80px;">정가</th>';
				tr += '	<th style="width:80px;">매입가</th>';
				tr += '	<th style="width:80px;">적립금</th>';
				tr += '	<th style="width:30px;">출력</th>';
				tr += '	<th style="width:30px;">삭제</th>';
				tr += '</tr>';

				table.down('thead').update(tr);

				// 총 컬럼수
				this._colSize = $$('input[name="option_name[]"]').size() + 7;

			},
			drawTableFoot : function(table)
			{
				var tr = '';

				// 일괄 처리
				tr += '<tr class="ac">';
				tr += '	<td colspan="' + (this._colSize - 7) + '">일괄적용</td>';
				tr += '	<td>';
				tr += '		<input type="text" name="all_option_stock" style="width:60px;" >';
				tr += '		<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.allChange(\'option_stock\');"><img src="../img/buttons/btn_seting.gif"></a>';
				tr += '	</td>';
				tr += '	<td>';
				tr += '		<input type="text" name="all_option_price" style="width:60px;" >';
				tr += '		<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.allChange(\'option_price\');"><img src="../img/buttons/btn_seting.gif"></a>';
				tr += '	</td>';
				tr += '	<td>';
				tr += '		<input type="text" name="all_option_consumer" style="width:60px;" >';
				tr += '		<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.allChange(\'option_consumer\');"><img src="../img/buttons/btn_seting.gif"></a>';
				tr += '	</td>';
				tr += '	<td>';
				tr += '		<input type="text" name="all_option_supply" style="width:60px;" >';
				tr += '		<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.allChange(\'option_supply\');"><img src="../img/buttons/btn_seting.gif"></a>';
				tr += '	</td>';
				tr += '	<td>';
				tr += '		<input type="text" name="all_option_reserve" style="width:60px;" >';
				tr += '		<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.allChange(\'option_reserve\');"><img src="../img/buttons/btn_seting.gif"></a>';
				tr += '	</td>';
				tr += '	<td>';
				tr += '		<input type="checkbox" value="1" checked name="all_option_is_display" onclick="nsAdminGoodsForm.option.allChange(\'option_is_display\');">';
				tr += '	</td>';
				tr += '	<td>';
				tr += '		<input type="checkbox" value="1" name="all_option_is_deleted" onclick="nsAdminGoodsForm.option.allChange(\'option_is_deleted\');">';
				tr += '	</td>';
				tr += '</tr>';

				table.down('tfoot').update(tr);

			},
			drawTableBody : function(table)
			{
				var tr;

				table.down('tbody').update('');

				this.optionList.each(function(opt, gidx)
				{
					tr = '<tr class="ac" style="display:none;">';

					opt.each(function(el, idx)
					{
						tr += '<td>' + el + '<input type="hidden" name="opt[' + idx + '][' + gidx + ']" value="' + el + '" /></td>';
					});

					tr += '	<td><input type="text" name="option_stock[' + gidx + ']" value="" style="width:60px;" ></td>';
					tr += '	<td><input type="text" name="option_price[' + gidx + ']" value="" style="width:60px;" ></td>';
					tr += '	<td><input type="text" name="option_consumer[' + gidx + ']" value="" style="width:60px;" ></td>';
					tr += '	<td><input type="text" name="option_supply[' + gidx + ']" value="" style="width:60px;" ></td>';
					tr += '	<td><input type="text" name="option_reserve[' + gidx + ']" value="" style="width:60px;" ></td>';
					tr += '	<td><input type="checkbox" name="option_is_display[' + gidx + ']" value="1" checked ></td>';
					tr += '	<td><input type="checkbox" name="option_is_deleted[' + gidx + ']" value="1" ></td>';
					tr += '</tr>';

					table.down('tbody').insert({
						bottom : tr
					});

				});

			},
			drawPaging : function()
			{
				var paging = '';
				// 페이징
				if (this.getOptionSize() > this.pageSize) {
					paging = this.getPaging();
				}

				$('el-option-list-paging').update(paging);

			},
			displayTableBody : function(table)
			{
				var offset = {};

				offset.start = (this.currentPage - 1) * this.pageSize;
				offset.end = this.currentPage * this.pageSize;

				table.down('tbody').childElements().each(function(el, idx)
				{
					if (idx >= offset.start && idx < offset.end) {
						el.setStyle({
							display : ''
						});
					}
					else {
						el.setStyle({
							display : 'none'
						});
					}
				});
			},
			_getOptionImageTable : function(idx)
			{
				var self = this;
				var html = '';

				html += '<table class="admin-form-table">';
				html += '<tbody>';

				this.rawData[idx].each(function(val, i)
				{

					html += '<tr>';
					html += '	<th>'+val+' 아이콘</th>';
					html += '	<td>';
					html += '		<div class="IF_option_image_type[' + idx + ']_IS_img">';
					html += '		<input type="file" name="option_icon_' + idx + '[' + self.get_js_compatible_key(val) + ']" class="opt gray" />';
					html += '		</div>';
					html += '		<div class="IF_option_image_type[' + idx + ']_IS_color" style="display:none;">';
					html += '		색상값 입력 : #<input type="text" name="option_color_' + idx + '[' + self.get_js_compatible_key(val) + ']" value="" size="8" maxlength="6" /><a href="javascript:nsAdminGoodsForm.openColorTable(\''+ self.get_js_compatible_key(val) +'\',\'option_color_' + idx + '\');"><img src="../img/codi/btn_colortable_s.gif" border="0" alt="색상표 보기" align="absmiddle"></a>';
					html += '		</div>';
					html += '	</td>';

					if (idx == 0) {
						html += '	<th>상품이미지</th>';
						html += '	<td>';
						html += '		<input type=file name="option_image[' + self.get_js_compatible_key(val) + ']" class="opt gray" />';
						html += '	</td>';
					}

					html += '</tr>';

				});

				html += '</tbody>';
				html += '</table>';

				return html;

			},
			drawImageTable : function()
			{
				var self = this;
				var html = '';

				var div = $('el-option-image');

				$$('input[name="option_name[]"]').each(function(el, idx)
				{
					html += '▼ <b>' + el.value + ' 이미지/색상 설정</b>';
					html += '&nbsp;&nbsp;';
					html += '<label class="extext"><input type="radio" name="option_image_type[' + idx + ']" value="img" class="null" checked onclick="nsAdminForm.toggle.is(event, \'img\');nsAdminForm.toggle.is(event, \'color\');" />이미지</label>';
					html += '<label class="extext"><input type="radio" name="option_image_type[' + idx + ']" value="color" class="null"  onclick="nsAdminForm.toggle.is(event, \'color\');nsAdminForm.toggle.is(event, \'img\');" />색상타입 사용</label>';

					html += self._getOptionImageTable(idx);

				});

				div.update(html);

			},
			isGenerated : false,
			generate : function()
			{
				this.isGenerated = false;

				try {
					this.rawData = this.generateOptionArray();

					this.currentPage = 1;

					this.setOptionListFromArray(this.rawData);
					this.isGenerated = true;

					this.drawTable();
					this.drawImageTable();

					this.setStockFields();

					this.bindPricing();
				}
				catch (e) {
					// 오류 발생
				}
			},
			page : function(page)
			{
				this.currentPage = page;
				this.displayTableBody($('el-option-list'));
				this.drawPaging();
			},
			getPaging : function()
			{
				var totalPage = Math.ceil(this.getOptionSize() / this.pageSize);

				var pageMove, pageStart;
				var navi = '';

				if (totalPage && this.currentPage > totalPage) {
					this.currentPage = totalPage;
				}
				pageStart = (Math.ceil(this.currentPage / 10) - 1) * 10;

				if (this.currentPage > 10) {
					navi += '<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.page(1)" class=navi>[1]</a> <a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.page(' + pageStart + ')" class=navi>이전</a>';
				}
				var i = 0;

				while (i + pageStart < totalPage && i < 10) {
					i++;
					pageMove = i + pageStart;
					navi += (this.currentPage == pageMove) ? ' <b>' + pageMove + '</b> ' : ' <a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.page(' + pageMove + ')" class=navi>[' + pageMove + ']</a>';
				}
				if (totalPage > pageMove) {
					pageMove++;
					navi += ' <a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.page(' + pageMove + ')" class=navi>다음</a> <a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.page(' + totalPage + ')" class=navi>[' + totalPage + ']</a>';
				}
				return navi;

			},
			add : function()
			{

				var table = $('el-option-table').down('tbody');

				if (table.childElements().size() > 1) {
					alert('2개 이상 등록할 수 없습니다.');
					return false;
				}

				var tr = '';
				tr += '<tr>';
				tr += '	<th><input type="text" name="option_name[]" value="" /></th>';
				tr += '	<td>';
				tr += '		<div style="width:100%;padding-right:100px;box-sizing:border-box;">';
				tr += '			<div class="field-wrapper" style="float:left;">';
				tr += '				<input type="text" name="option_value[]" value="" />';
				tr += '			</div>';
				tr += '			<div style="float:left;margin:2px -100px 0 5px;">';
				tr += '				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.del(event);"><img src="../img/i_del.gif"></a>';
				tr += '			</div>';
				tr += '		</div>';
				tr += '	</td>';
				tr += '</tr>';

				table.insert({
					bottom : tr
				});

			},
			del : function(event)
			{
				var target = Event.element(event);
				target.up('tr').remove();
			},
			// 하나의 메서드 대신, 태그, 타입 별로 나눈다.
			allSelect : function(field, targetName)
			{
				$$('select[name^="' + targetName + '"]').each(function(el)
				{
					if (!el.disabled) {
						el.selectedIndex = field.selectedIndex;
					}
				});

			},
			// @todo : radio 선택 가능하도록 구현 필요.
			allCheckRadio : function(field, targetName)
			{
				$$('input[name^="' + targetName + '"]').each(function(el)
				{
				});

			},
			allCheck : function(field, targetName)
			{
				$$('input[name^="' + targetName + '"]').each(function(el)
				{
					if (!el.disabled) {
						el.checked = field.checked;
					}
				});

			},
			allInput : function(field, targetName)
			{
				$$('input[name^="' + targetName + '"]').each(function(el, idx)
				{
					if (! el.disabled) {
						el.value = field.value;

						if (idx == 0) {
							el.fire('pricing:changed');
						}
					}
				});

			},
			allChange : function(targetName)
			{
				var field = $('goods-form')['all_'+targetName];

				switch (field.tagName.toUpperCase()) {
					case 'SELECT':
						this.allSelect(field, targetName);
						break;

					case 'INPUT':

						switch (field.readAttribute('type').toUpperCase()) {
							case 'RADIO':
								this.allCheckRadio(field, targetName);
								break;
							case 'CHECKBOX':
								this.allCheck(field, targetName);
								break;
							case 'TEXT':
								this.allInput(field, targetName);
								break;
						}
						break;

				}
			},
			checkSku : function()
			{
				var skus = [];

				try {
					$$('input[name^="option_sku"]').each(function(el)
					{
						if (el.value) {
							skus.each(function(sku)
							{
								if (el.value == sku) {
									throw {};
								}
							});

							skus.push(el.value);
						}
					});
				}
				catch (e) {
					alert('중복코드가 존재합니다.');
					return false;
				}
				alert('사용이 가능한 옵션코드 입니다.');
				return true;
			},
			insertRow : function(data)
			{
				var data = JSON.parse(data);

				var totalPage = Math.ceil(this.getOptionSize() / this.pageSize);
				this.page(totalPage);

				var tr = '<tr class="ac">';

				$$('input[name="option_name[]"]').each(function(el, idx)
				{
					tr += '<td><div class="field-wrapper"><input type="text" name="opt[' + idx + '][]" value="' + (typeof data.opt != 'string' ? data.opt[idx] : data.opt) + '" /></div></td>';
				});

				tr += '	<td><input type="text" name="option_stock[]" value="' + data.option_stock + '" style="width:60px;" ></td>';
				tr += '	<td><input type="text" name="option_price[]" value="' + data.option_price + '" style="width:60px;" ></td>';
				tr += '	<td><input type="text" name="option_consumer[]" value="' + data.option_consumer + '" style="width:60px;" ></td>';
				tr += '	<td><input type="text" name="option_supply[]" value="' + data.option_supply + '" style="width:60px;" ></td>';
				tr += '	<td><input type="text" name="option_reserve[]" value="' + data.option_reserve + '" style="width:60px;" ></td>';
				tr += '	<td><input type="checkbox" name="option_is_display[]" value="1" ' + (data.option_is_display ? 'checked' : '') + ' ></td>';
				tr += '	<td><input type="checkbox" name="option_is_deleted[]" value="1"></td>';
				tr += '</tr>';

				$('el-option-list').down('tbody').insert({
					bottom : tr
				});

			},
			insert : function(goodsno)
			{
				nsAdminForm.dialog.open({
					type : 'url',
					contents : '../goods/adm_popup_goods_option_insert_form.php?goodsno=' + goodsno,
					width : 850,
					height : 400
				});

			},
			reset : function(goodsno)
			{
				alert('옵션(품목) 새로 등록하기 페이지로 이동합니다.\n새로 등록/수정 완료 시 기존의 옵션정보는 복구되지 않습니다.');

				nsAdminForm.dialog.open({
					type : 'url',
					contents : '../goods/adm_popup_goods_option_reset.php?goodsno=' + goodsno,
					width : 1000,
					height : 600
				});

			},
			sort : function(goodsno, type)
			{
				if (!$('opttype_' + type).checked) {
					if (!confirm('옵션 출력방식을 ' + (type == 'double' ? '분리형' : '일체형') + '으로 변경하시겠습니까?')) {
						return false;
					}

					$('opttype_' + type).checked = true;
				}

				var opt = {
					type : 'url',
					title : '옵션 출력순서 지정',
					contents : '../goods/adm_popup_goods_option_sort.php',
					width : 250,
					height : 500
				};

				if (type == 'double') {
					opt.contents = '../goods/adm_popup_goods_option_sort_double.php';
					opt.width = 500;
					opt.height = 500;
				}

				opt.contents = opt.contents + '?goodsno=' + goodsno;

				nsAdminForm.dialog.open(opt);
			}
		},
		addOption : {
			selectable : {
				_idx : null,
				getIdx : function(tbody)
				{
					if (this._idx == null) {
						this._idx = tbody.childElements().size();
					}
					else {++this._idx;
					}

					return this._idx;
				},
				add : function(event)
				{
					var tbody = $('el-add-option').down('tbody');

					var idx = this.getIdx(tbody);

					var tr = '';
					tr += '<tr>';
					tr += '	<td>';
					tr += '		<input type="hidden" name="additional_option[selectable][_idx][]" value="' + idx + '" />';
					tr += '		<input type="text" name="additional_option[selectable][name][' + idx + ']" value="" />';
					tr += '		<a href="javascript:void(0);" onclick="nsAdminGoodsForm.addOption.selectable.del(event);"><img src="../img/i_del.gif" align="absmiddle"></a>';
					tr += '	</td>';
					tr += '	<td colspan="2">';
					tr += '		<table class="nude padding-midium">';
					tr += '		<tr>';
					tr += '			<td>';
					tr += '			<input type="hidden" name="additional_option[selectable][sno][' + idx + '][]" />';
					tr += '			<input type="text" name="additional_option[selectable][value][' + idx + '][]" value="" style="width:270px" />';
					tr += '			<a href="javascript:void(0)" onclick="nsAdminGoodsForm.addOption.selectable.addSub(event);"><img src="../img/i_add.gif" align="absmiddle" border="0"></a>';
					tr += '			</td>';
					tr += '			<td>';
					tr += '			<select name="additional_option[selectable][addprice_operator][' + idx + '][]">';
					tr += '				<option value="+">+</option>';
					tr += '				<option value="-">-</option>';
					tr += '			</select>';
					tr += '			<input type="text" name="additional_option[selectable][addprice][' + idx + '][]" size=9 value="" /><input type="hidden" name="additional_option[selectable][addno][' + idx + '][]" value="" />';
					tr += '			</td>';
					tr += '		</tr>';
					tr += '		</table>';
					tr += '	</td>';
					tr += '	<td class="ac"><input type="checkbox" name="additional_option[selectable][require][' + idx + ']" value="o" /></td>';
					tr += '</tr>';

					tbody.insert({
						bottom : tr
					});
				},
				del : function(event)
				{
					var target = Event.element(event);
					target.up('tr').remove();
				},
				addSub : function(event)
				{
					var target = Event.element(event);
					var tbody = target.up('tbody');
					var idx = tbody.up('tr').select('input[type="hidden"]')[0].value;

					var tr = '';
					tr += '<tr>';
					tr += '	<td>';
					tr += '	<input type="hidden" name="additional_option[selectable][sno][' + idx + '][]" />';
					tr += '	<input type="text" name="additional_option[selectable][value][' + idx + '][]" value="" style="width:270px" />';
					tr += '	<a href="javascript:void(0)" onclick="nsAdminGoodsForm.addOption.selectable.del(event);"><img src="../img/i_del.gif" align="absmiddle" border="0"></a>';
					tr += '	</td>';
					tr += '	<td>';
					tr += '	<select name="additional_option[selectable][addprice_operator][' + idx + '][]">';
					tr += '		<option value="+">+</option>';
					tr += '		<option value="-">-</option>';
					tr += '	</select>';
					tr += '	<input type="text" name="additional_option[selectable][addprice][' + idx + '][]" size=9 value="" /><input type="hidden" name="additional_option[selectable][addno][' + idx + '][]" value="" />';
					tr += '	</td>';
					tr += '</tr>';

					tbody.insert({
						bottom : tr
					});
				},
				delSub : function(event)
				{
					var target = Event.element(event);
					target.up('tr').remove();
				},
				addSubPreset : function(tbody_idx)
				{
					var up_tbody = $('el-add-option').down('tbody');
					var tbody = up_tbody.down('tbody', tbody_idx);
					var idx = tbody.up('tr').select('input[type="hidden"]')[0].value;
					var tr = '';
					tr += '<tr>';
					tr += '	<td>';
					tr += '	<input type="hidden" name="additional_option[selectable][sno][' + idx + '][]" />';
					tr += '	<input type="text" name="additional_option[selectable][value][' + idx + '][]" value="" style="width:270px" />';
					tr += '	<a href="javascript:void(0)" onclick="nsAdminGoodsForm.addOption.selectable.del(event);"><img src="../img/i_del.gif" align="absmiddle" border="0"></a>';
					tr += '	</td>';
					tr += '	<td>';
					tr += '	<select name="additional_option[selectable][addprice_operator][' + idx + '][]">';
					tr += '		<option value="+">+</option>';
					tr += '		<option value="-">-</option>';
					tr += '	</select>';
					tr += '	<input type="text" name="additional_option[selectable][addprice][' + idx + '][]" size=9 value="" /><input type="hidden" name="additional_option[selectable][addno][' + idx + '][]" value="" />';
					tr += '	</td>';
					tr += '</tr>';

					tbody.insert({
						bottom : tr
					});
				}
			},
			inputable : {
				_idx : null,
				getIdx : function(tbody)
				{
					if (this._idx == null) {
						this._idx = tbody.childElements().size();
					}
					else {++this._idx;
					}

					return this._idx;
				},
				add : function(event)
				{
					var tbody = $('el-add-input-option').down('tbody');

					var idx = this.getIdx(tbody);

					var tr = '';
					tr += '<tr>';
					tr += '	<td>';
					tr += '		<input type="hidden" name="additional_option[inputable][_idx][]" value="' + idx + '" />';
					tr += '		<input type="text" name="additional_option[inputable][name][' + tbody.childElements().size() + ']" value="" />';
					tr += '		<a href="javascript:void(0);" onclick="nsAdminGoodsForm.addOption.inputable.del(event);"><img src="../img/i_del.gif" align="absmiddle"></a>';
					tr += '	</td>';
					tr += '	<td>';
					tr += '		<input type="hidden" name="additional_option[inputable][sno][' + tbody.childElements().size() + '][]" />';
					tr += '		<input type="text" name="additional_option[inputable][value][' + tbody.childElements().size() + '][]" value="" style="width:50px" /> 자';
					tr += '	</td>';
					tr += '	<td>';
					tr += '	<select name="additional_option[inputable][addprice_operator][' + tbody.childElements().size() + '][]">';
					tr += '		<option value="+">+</option>';
					tr += '		<option value="-">-</option>';
					tr += '	</select>';
					tr += '	<input type="text" name="additional_option[inputable][addprice][' + tbody.childElements().size() + '][]" size=9 value="" /><input type="hidden" name="additional_option[inputable][addno][' + tbody.childElements().size() + '][]" value="" />';
					tr += '	</td>';
					tr += '	<td class="ac"><input type="checkbox" name="additional_option[inputable][require][' + tbody.childElements().size() + ']" value="o" /></td>';
					tr += '</tr>';

					tbody.insert({
						bottom : tr
					});
				},
				del : function(event)
				{
					var target = Event.element(event);
					target.up('tr').remove();
				}
			}
		},
		discount : {
			addGroup : function()
			{
				var tbody = $('el-goods-discount-by-term').down('tbody');
				var tr = tbody.down('tr', 0).clone(true);
				tr.down('a').writeAttribute('onclick', 'nsAdminGoodsForm.discount.delGroup(event);');
				tr.down('img').writeAttribute('src', '../img/i_del.gif');

				tbody.insert({
					bottom : tr
				});

			},
			delGroup : function(event)
			{
				var target = Event.element(event);
				target.up('tr').remove();
			}
		},
		relate : {
			data : [],
			_originalData : [],
			goodsno : null,
			register : function()
			{
				nsAdminForm.dialog.open({
					type : 'url',
					contents : './popup.related.register.php?goodsno=' + this.goodsno,
					width : 750,
					height : 600
				});
			},
			init : function(goodsno, data)
			{

				//Event.observe(this.element, 'blur', this.onBlur.bindAsEventListener(this));
				//Event.observe(this.element, 'keydown', this.onKeyPress.bindAsEventListener(this));


				Event.observe($('el-related-goodslist'), 'click', nsAdminGoodsForm.relate.sort._set.bindAsEventListener(this));

				//$('el-related-goodslist').observe('click', nsAdminGoodsForm.relate.sort._set.bindAsEventListener(event));



				/*$('el-related-goodslist').observe('click', function(event)
				{
					nsAdminGoodsForm.relate.sort._set(event);
				});*/

				document.observe('keydown', function(event)
				{
					nsAdminGoodsForm.relate.sort.move(event);
				});

				this.goodsno = goodsno;
				this.data = data;

			},
			list : function()
			{
				var el = $('el-related-goodslist');
				var i = 0;

				$A(el.down('tbody').rows).each(function(tr)
				{
					Element.remove(tr);
				});

				if (this.data.size() > 0) {

					var _tpl = '';
					_tpl += '<tr align="center">';
					_tpl += '	<td class="noline"><input type="checkbox" name="related_chk[]" value="#{goodsno}" /></td>';
					_tpl += '	<td><a href="javascript:void(0);" onclick="nsAdminGoodsForm.relate.changetype(event);"><img src="../img/icn_#{type}.gif"></a></td>';
					_tpl += '	<td><a href="../../goods/goods_view.php?goodsno=#{goodsno}" target=_blank>#{img}</a></td>';
					_tpl += '	<td align="left">';
					_tpl += '		#{goodsnm}';
					_tpl += '		<p style="margin:0;"><b>#{price}</b></p>';
					_tpl += '	</td>';
					_tpl += '	<td>#{range}</td>';
					_tpl += '	<td>#{r_regdt}</td>';
					_tpl += '	<td><a href="javascript:void(0);" onclick="nsAdminGoodsForm.relate.del(event);"><img src="../img/btn_delete_new.gif"></a></td>';
					_tpl += '</tr>';

					var _row = new Template(_tpl);

					var r;

					for ( i = 0, m = this.data.size(); i < m; i++) {
						r = this.data[i];

						// 데이터 가공
						r.type = r.r_type == 'couple' ? '1' : '0';
						r.img = '<img src="../../data/goods/' + r.img_s + '" width=40 >';
						r.price = comma(r.price);
						r.range = '';

						if (!r.r_start && !r.r_end)
							r.range = '지속노출';
						else {
							if (r.r_start)
								r.range = r.r_start;
							r.range += ' ~ ';
							if (r.r_end)
								r.range += r.r_end;
						}
						// 삽입
						el.down('tbody').insert({
							bottom : _row.evaluate(r)
						});
					}

				}

				$('el-related-goods-count').update(this.data.size());
			},
			undo : function()
			{
				this.data = this._originalData;
				this.list();
			},
			range : function()
			{
				var chks = $$('input[name="related_chk[]"]:checked');

				if (chks.size() < 1) {
					alert('기간 설정할 관련상품을 선택해 주세요.');
					return false;
				}
				var param = 'goodsno=' + this.goodsno;

				chks.each(function(chk)
				{
					param += '&chk[]=' + chk.value;
				});

				nsAdminForm.dialog.open({
					type : 'url',
					contents : '../goods/popup.related.range.php?' + param,
					width : 400,
					height : 230
				});
			},
			isExist : function(data)
			{
				for (var i = 0, m = this.data.size(); i < m; i++) {
					if (data.goodsno == this.data[i].goodsno)
						return true;
				}
				return false;

			},
			add : function(data)
			{
				var noti = false;

				if (data.length > 0) {
					for (var i = 0, m = data.length; i < m; i++) {
						if (!this.isExist(data[i])) {
							this.data.push(data[i]);
							noti = true;
						}
					}
				}
				if (noti)
					alert('추가되었습니다.');

				this.list();
			},
			set : function(data)
			{
				var noti = false;

				if (data.length > 0) {
					for (var i = 0, m = this.data.size(); i < m; i++) {
						for (var j = 0, n = data.length; j < n; j++) {
							if (this.data[i].goodsno == data[j].goodsno)
								Object.extend(this.data[i], data[j]);

						}
					}
				}
				this.list();

			},
			del : function(event, act)
			{
				if (act == 'multi')
					var chks = $$('input[name="related_chk[]"]:checked');
				else {
					var tr = Element.up(Event.element(event), 'tr');
					var chks = Selector.findChildElements(tr, ['input[name="related_chk[]"]']);
				}
				for (var j = 0, n = chks.size(); j < n; j++) {
					for (var i = 0, m = this.data.size(); i < m; i++) {
						if (this.data[i].goodsno == chks[j].value) {
							this.data[i] = {};
							chks[j].up(1).remove();
						}
					}
				}
				$('el-related-goods-count').update(parseInt($('el-related-goods-count').getText()) - n);
			},
			changetype : function(event, act, typ)
			{
				var img;

				if (act == 'multi') {
					var chks = $$('input[name="related_chk[]"]:checked');
				}
				else {
					var tr = Element.up(Event.element(event), 'tr');
					var chks = Selector.findChildElements(tr, ['input[name="related_chk[]"]']);

				}
				for (var j = 0, n = chks.size(); j < n; j++) {
					for (var i = 0, m = this.data.size(); i < m; i++) {
						if (this.data[i].goodsno == chks[j].value) {
							if (typ)
								this.data[i].r_type = typ;
							else
								this.data[i].r_type = (this.data[i].r_type == 'couple') ? 'single' : 'couple';
							img = Selector.findChildElements(chks[j].up(1), ['img[src*="/img/icn_"]']);
							img[0].src = '../img/icn_' + (this.data[i].r_type == 'couple' ? '1' : '0') + '.gif';
						}
					}
				}
			},
			select : function()
			{
				var i = 0;
				var b_checked = false;
				$$('input[name="related_chk[]"]').each(function(chk)
				{
					if (i == 0)
						b_checked = !chk.checked;
					chk.checked = b_checked;
					i++;
				});
			},
			make : function()
			{
				if (this.data.length > 0) {
					var json = Object.toJSON(this.data);
				}
				else {
					var json = '';
				}

				$('el-relation').setValue(json);

			},
			sort : {
				_row : null,
				_set : function(event)
				{
					// click event;
					var self = nsAdminGoodsForm.relate;

					var el = Element.up(Event.element(event), 'tr');
					if (el.rowIndex != 0) {
						if (self.sort._row == el) {
							el.setStyle({
								backgroundColor : ''
							});
							self.sort._row = null;
						}
						else {
							if (self.sort._row != null) {
								self.sort._row.setStyle({
									backgroundColor : ''
								});
							}
							el.setStyle({
								backgroundColor : '#FFF4E6'
							});
							self.sort._row = el;
						}
					}
					self = null;
				},
				move : function(event)
				{
					// keydown event;
					var self = nsAdminGoodsForm.relate;

					var _k = event.keyCode;
					if (self.sort._row != null && (_k != 38 || _k != 40)) {
						// 이동
						var table = $('el-related-goodslist');
						var _oidx = self.sort._row.rowIndex;
						var _nidx = _oidx + (_k == 38 ? -1 : 1);
						if (_nidx >= table.rows.length)
							_nidx = 1;
						else if (_nidx < 1)
							_nidx = table.rows.length - 1;

						if ( typeof table.moveRow == 'undefined') {
							return;
						}
						else {	// IE only.
							table.moveRow(self.sort._row.rowIndex, _nidx);
						}
						// relation 재 정렬
						_nidx = _nidx - 1;
						_oidx = _oidx - 1;

						if (_oidx == 0 && _nidx == (self.data.size() - 1)) {
							self.data.push(self.data[_oidx]);
							self.data.shift();
						}
						else if (_oidx == (self.data.size() - 1) && _nidx == 0) {
							self.data.unshift(self.data[_oidx]);
							self.data.pop();
						}
						else {
							var tmp = self.data[_nidx];
							self.data[_nidx] = self.data[_oidx];
							self.data[_oidx] = tmp;
						}
						Event.stop(event);
					}
					self = null;
				}
			}
		},
		color : {
			// 스마트 검색 : rgb코드 -> 16진수코드
			convert : function(colorCode)
			{
				if (colorCode.toLowerCase().indexOf('rgb') == 0) {
					colorCode = colorCode.toLowerCase().replace(/rgb/g, '');
					colorCode = colorCode.toLowerCase().replace(/\(/g, '');
					colorCode = colorCode.toLowerCase().replace(/\)/g, '');
					colorCode = colorCode.toLowerCase().replace(/ /g, '');

					colorCode_tempList = colorCode.split(',');
					colorCode = '';

					for ( i = 0; i < colorCode_tempList.length; i++) {
						tmpCode = parseInt(colorCode_tempList[i]).toString(16);
						if (String(tmpCode).length == 1)
							tmpCode = '0' + tmpCode;
						colorCode += tmpCode;
					}
					colorCode = '#' + colorCode;
				}
				return colorCode;
			},
			// 스마트 검색 : 색 선택
			select : function(targetColor)
			{
				targetColor = nsAdminGoodsForm.color.convert(targetColor);

				targetColor = targetColor.toUpperCase();
				tempColor = $("color");

				if (tempColor.value.indexOf(targetColor) != -1)
					return false;
				else
					tempColor.value = tempColor.value + targetColor;

				if (tempColor.value)
					nsAdminGoodsForm.color.toHtml('selectedColor');
			},
			// 스마트 검색 : 선택된 색상을 표시
			toHtml : function(targetID)
			{
				var colorTag = $(targetID);
				var colorText = $("color").value;
				var tempColor = '';

				colorTag.innerHTML = '';
				for ( i = 0; i < colorText.length; i = i + 7) {
					tempColor = colorText.substr(i, 7);
					if (tempColor)
						colorTag.innerHTML += '<div href="javascript:void(0);" style="background-color:' + tempColor + '" class="paletteColor_selected" ondblclick="nsAdminGoodsForm.color.del(\'' + targetID + '\', this.style.backgroundColor);"></div>\n';
				}
				if (colorTag.innerHTML) {
					colorTag.innerHTML += '<div style="clear:left;"></div>';
				}
				else {
					colorTag.innerHTML = '&nbsp;';
				}
			},
			// 스마트 검색 : 색상 제거
			del : function(targetID, delColor)
			{
				delColor = nsAdminGoodsForm.color.convert(delColor);

				delColor = delColor.toUpperCase();
				$("color").value = $("color").value.toUpperCase();
				$("color").value = $("color").value.replace(delColor, "");
				nsAdminGoodsForm.color.toHtml(targetID);
			}
		},
		imageUpload : {
			toggleForm : function()
			{
				var m, obj = document.fm.image_attach_method;

				for (var i = 0; i < obj.length; i++) {
					if (obj[i].checked)
						var m = obj[i].value;
				}
				if (m == 'file') {
					$('image_attach_method_upload_wrap').style.display = 'block';
					$('image_attach_method_link_wrap').style.display = 'none';
				}
				else {
					$('image_attach_method_upload_wrap').style.display = 'none';
					$('image_attach_method_link_wrap').style.display = 'block';
				}
			},
			toggleImg : function()
			{
				var m, obj = document.fm.use_mobile_img;

				for (var i = 0; i < obj.length; i++) {
					if (obj[i].checked)
						var m = obj[i].value;
				}
				if (m == 1) {
					jQuery('.use_mobile_img_1').show();
					jQuery('.use_mobile_img_0').hide();
				}
				else {
					jQuery('.use_mobile_img_1').hide();
					jQuery('.use_mobile_img_0').show();
				}
			},
			// 자동리사이즈
			chkImgCopy : function(fobj)
			{
				var exist = false;
				for (var i = 0; i < document.getElementsByName('img_l[]').length; i++) {
					if (document.getElementsByName('img_l[]')[i].value != '') {
						exist = true;
						break;
					}
					else if (document.getElementsByName('del[img_l][' + i + ']')[0] != null && document.getElementsByName('del[img_l][' + i + ']')[0].checked == false) {
						exist = true;
						break;
					}
				}
				if (exist == false) {
					alert('원본이미지 먼저 등록하세요.');
					return false;
				}
				var limgTable = _ID('tb_l').parentNode.parentNode.parentNode.parentNode;
				if (fobj.copy_i.checked || fobj.copy_s.checked || fobj.copy_m.checked) {
					if (limgTable.style.outline != null)
						limgTable.style.outline = 'solid 5px #627DCE';
					else
						limgTable.style.border = 'solid 5px #627DCE';
				}
				else {
					if (limgTable.style.outline != null)
						limgTable.style.outline = 'none';
					else
						limgTable.style.border = 'solid 1px #EBEBEB';
				}
				for (var i = 0; i < document.getElementsByName('img_m[]').length; i++)
					document.getElementsByName('img_m[]')[i].disabled = fobj.copy_m.checked;
				for (var i = 0; i < document.getElementsByName('img_s[]').length; i++)
					document.getElementsByName('img_s[]')[i].disabled = fobj.copy_s.checked;
				for (var i = 0; i < document.getElementsByName('img_i[]').length; i++)
					document.getElementsByName('img_i[]')[i].disabled = fobj.copy_i.checked;
				for (var i = 0; i < document.getElementsByName('img_mobile[]').length; i++)
					document.getElementsByName('img_mobile[]')[i].disabled = fobj.copy_mobile.checked;
			},
			chkMobileImgCopy : function(fobj)
			{
				var exist = false;
				for (var i = 0; i < document.getElementsByName('img_z[]').length; i++) {
					if (document.getElementsByName('img_z[]')[i].value != '') {
						exist = true;
						break;
					}
					else if (document.getElementsByName('del[img_z][' + i + ']')[0] != null && document.getElementsByName('del[img_z][' + i + ']')[0].checked == false) {
						exist = true;
						break;
					}
				}
				if (exist == false) {
					alert('원본이미지 먼저 등록하세요.');
					return false;
				}
				var limgTable = _ID('tb_mobile_z').parentNode.parentNode.parentNode.parentNode;
				if (fobj.copy_w.checked || fobj.copy_x.checked || fobj.copy_y.checked) {
					if (limgTable.style.outline != null)
						limgTable.style.outline = 'solid 5px #627DCE';
					else
						limgTable.style.border = 'solid 5px #627DCE';
				}
				else {
					if (limgTable.style.outline != null)
						limgTable.style.outline = 'none';
					else
						limgTable.style.border = 'solid 1px #EBEBEB';
				}
				for (var i = 0; i < document.getElementsByName('img_y[]').length; i++)
					document.getElementsByName('img_y[]')[i].disabled = fobj.copy_m.checked;
				for (var i = 0; i < document.getElementsByName('img_x[]').length; i++)
					document.getElementsByName('img_x[]')[i].disabled = fobj.copy_s.checked;
				for (var i = 0; i < document.getElementsByName('img_w[]').length; i++)
					document.getElementsByName('img_w[]')[i].disabled = fobj.copy_i.checked;
			},
			preview : function(obj)
			{
				var tmp = obj.up('tr').select('td')[2];

				var img = new Element('img', {
						'src': obj.value,
						'width': 20,
						'style':'border:1 solid #cccccc;',
						'onload':'if(this.height>this.width){this.height=20}',
						'onclick':'popupImg(this.src,\'../\');',
						'class':'hand'
				});

				tmp.update(img);
			},
			addfld : function(obj)
			{
				var tb = $(obj);
				oTr = tb.insertRow(-1);
				oTd = oTr.insertCell(-1);
				oTd.innerHTML = '<a href="javascript:void(0)" onclick="nsAdminGoodsForm.imageUpload.delfld(this)"><img src="../img/i_del.gif" align="absmiddle"></a>	<span>' + tb.rows[0].cells[0].getElementsByTagName('span')[0].innerHTML + '</span>';
				oTd.getElementsByTagName('input')[0].value = '';
				oTd = oTr.insertCell(-1);
				oTd = oTr.insertCell(-1);
			},
			delfld : function(obj)
			{
				var tb = obj.parentNode.parentNode.parentNode.parentNode;
				tb.deleteRow(obj.parentNode.parentNode.rowIndex);
			},
			chkImgBox : function(obj, fobj)
			{
				fobj.copy_m.checked = obj.checked;
				fobj.copy_s.checked = obj.checked;
				fobj.copy_i.checked = obj.checked;
				var res = nsAdminGoodsForm.imageUpload.chkImgCopy(fobj);
				if (res === false) {
					obj.checked = fobj.copy_m.checked = fobj.copy_s.checked = fobj.copy_i.checked = false;
				}
			},
			chkMobileImgBox : function(obj, fobj)
			{
				fobj.copy_y.checked = obj.checked;
				fobj.copy_x.checked = obj.checked;
				fobj.copy_w.checked = obj.checked;
				var res = nsAdminGoodsForm.imageUpload.chkMobileImgCopy(fobj);
				if (res === false) {
					obj.checked = fobj.copy_y.checked = fobj.copy_x.checked = fobj.copy_w.checked = false;
				}
			}
		},
		information : {
			adding : false,
			rowidx : 0,
			init : function(n)
			{
				this.rowidx = n;
			},
			overview : function()
			{
				popup2('./information.by.goods.php', 600, 660, '0');
			},
			_addrow : function(size)
			{
				if (this.adding == true)
					return;

				this.adding = true;

				var o = $('el-extra-info-table');

				// size = 4 or 2;
				var tr = o.insertRow(-1), td;

				switch (size) {
					case 4:

						this.rowidx++;

						td = tr.insertCell(-1);
						td.innerHTML = '<input type="text" name="extra_info_title[' + this.rowidx + ']" style="width:100%" />';

						td = tr.insertCell(-1);
						td.innerHTML = '<input type="text" name="extra_info_desc[' + this.rowidx + ']" style="width:100%" />';

						this.rowidx++;

						td = tr.insertCell(-1);
						td.innerHTML = '<input type="text" name="extra_info_title[' + this.rowidx + ']" style="width:100%" />';

						td = tr.insertCell(-1);
						td.innerHTML = '<input type="text" name="extra_info_desc[' + this.rowidx + ']" style="width:100%" />';

						break;
					case 2:

						this.rowidx++;

						td = tr.insertCell(-1);
						td.innerHTML = '<input type="text" name="extra_info_title[' + this.rowidx + ']" style="width:100%" />';

						td = tr.insertCell(-1);
						td.innerHTML = '<input type="text" name="extra_info_desc[' + this.rowidx + ']" style="width:100%" />';
						td.colSpan = 3;

						this.rowidx++;

						//

						break;
				}
				td = tr.insertCell(-1);
				td.innerHTML = '<a href="javascript:void(0);" onclick="nsAdminGoodsForm.information.delrow(event);"><img src="../img/i_del.gif"></a>';

				this.adding = false;

			},
			delrow : function(event)
			{
				/*
				 idx = el.rowIndex;
				 var obj = $('objCategory');
				 obj.deleteRow(idx);
				 */
				var o = $('el-extra-info-table');
				//var tr = event.srcElement.up('tr');
				var tr = Event.element(event).parentElement.parentElement.parentElement;
				o.deleteRow(tr.rowIndex);
			},
			add4row : function()
			{
				this._addrow(4);
			},
			add2row : function()
			{
				this._addrow(2);
			},
			formValidator : function()
			{
				try {
					$$('input[name^="extra_info_title"], input[name^="extra_info_desc"]').each(function(el)
					{
						if (!el.value.trim()) {
							el.focus();
							throw 'error';
						}
					});
				}
				catch (e) {
					alert('상품필수정보에 누락된 항목이 없는지 확인해 주세요.');
					return false;
				}
				return true;
			}
		},
		purchase : {
			type : 1,
			openSelector : function () {

				var form = $('goods-form');

				try {
					if(form['pchsno'].disabled == false) {
						nsAdminForm.dialog.open({
							type : 'url',
							contents : '../goods/popup.purchase_find.php',
							width : 640,
							height : 450
						});

						//window.open('../goods/popup.purchase_find.php', 'purchaseSearchPop', 'width=640,height=450');
					}
				}
				catch (e) {}
			},
			setType : function(val) {
				this.type = val;
				nsAdminGoodsForm.option.setStockFields();
			},
			getType : function(val) {
				return this.type;
			}

		},
		presetExtend : {
				load : function()
				{
					nsAdminForm.dialog.open({
						type : 'url',
						title : '추가옵션바구니 저장',
						contents : '../goods/popup.dopt_extend_list.php',
						width : 850,
						height : 600
					});
				},
				set : function(data)
				{

					var tbody = $('el-add-option').down('tbody');

					// remove additional fields;
					tbody.childElements().each(function(el, idx)
					{

						if (idx >= 1)
							el.remove();
					});

					var addFieldCount = Object.keys(data).length;

					for (var i = 0; i < addFieldCount; i++) {

						if (i > 0) {
							nsAdminGoodsForm.addOption.selectable.add();
						}

						$$('input[name="additional_option[selectable][name][' + i + ']"]')[0].value = data[i].name;
						$$('input[name="additional_option[selectable][require][' + i + ']"]')[0].checked = data[i].require;
						var sub_tbody = tbody.down('tbody', i);

						// remove additional fields;
						sub_tbody.childElements().each(function(el, idx)
						{
							if (idx >= 1)
								el.remove();
						});

						var value_data = data[i].options;

						var addValueCount = Object.keys(value_data).length;

						for (var j = 0; j < addValueCount; j++) {
							if (j > 0) {
								nsAdminGoodsForm.addOption.selectable.addSubPreset(i);
							}

							$$('input[name="additional_option[selectable][value][' + i + '][]"]')[j].value = value_data[j].name;

							if(value_data[j].price < 0) {
								$$('select[name="additional_option[selectable][addprice_operator][' + i + '][]"]')[j].value = '-';
							}
							else {
								$$('select[name="additional_option[selectable][addprice_operator][' + i + '][]"]')[j].value = '+';
							}

							$$('input[name="additional_option[selectable][addprice][' + i + '][]"]')[j].value = Math.abs(value_data[j].price);

						}
					}
				}
			}

	};
}();

var nsInformationByGoods = function()
{
	return {
		init : function(n)
		{
			nsAdminGoodsForm.information.init(n);
		},
		overview : function()
		{
			nsAdminGoodsForm.information.overview();
		},
		delrow : function(event)
		{
			nsAdminGoodsForm.information.delrow(event);
		},
		add4row : function()
		{
			nsAdminGoodsForm.information.add4row();
		},
		add2row : function()
		{
			nsAdminGoodsForm.information.add2row();
		},
		formValidator : function()
		{
			return nsAdminGoodsForm.information.formValidator();
		}
	};
}();
