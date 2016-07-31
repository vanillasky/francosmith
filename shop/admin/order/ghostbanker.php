<?php
$location = "�ֹ����� > ��Ȯ�� �Ա��� ����Ʈ ����";
include "../_header.php";



$ghost_cfg = $config->load('ghostbanker');

if (empty($ghost_cfg)) {
	$ghost_cfg['use'] = 0;
	$ghost_cfg['expire'] = 3;
	$ghost_cfg['hide_bank'] = 0;
	$ghost_cfg['hide_money'] = 0;
	$ghost_cfg['bankda_use'] = 0;
	$ghost_cfg['bankda_limit'] = '';
	$ghost_cfg['design_skin'] = 1;
	$ghost_cfg['design_html'] = '';
	$ghost_cfg['banner_skin'] = 2;
	$ghost_cfg['banner_file'] = '';
	$ghost_cfg['banner_skin_type'] = 'select';
	$ghost_cfg['design_skin_type'] = 'select';

}



?>
<script type="text/javascript">
var g_jsonData = {};
var g_page = {
			current : 1,	// ���� ������
			limit : 10,		// �������� ���ڵ�
			pages : 10		// ���� ������ ��������
			};

var nsGhostbanker = function() {

	function $RF(el, radioGroup) {

		if($(el).type && $(el).type.toLowerCase() == 'radio') {
			var radioGroup = $(el).name;
			var el = $(el).form;
		}
		else if ($(el).tagName.toLowerCase() != 'form') {
			return false;
		}

		var checked = $(el).getInputs('radio', radioGroup).find(function(re) {return re.checked;});

		return (checked) ? $F(checked) : null;
	}


	return {
		_form : null,
		init : function() {

			var self = this;

			// ��
			$( 'banner_skin_type_' + $RF('frmGhostbankerconfig','banner_skin_type') ).setStyle({display:'block'});	//
			$( 'design_skin_type_' + $RF('frmGhostbankerconfig','design_skin_type') ).setStyle({display:'block'});	//

			$$('.design-selector > legend > label > input').each(function(el) {
				Event.observe( el , 'click',	function(event) {self._designform(el);});
			});

			Event.observe( $('frmGhostbankerconfig') , 'submit',	function(event) {
				// �� üũ.

				//event.preventDefault();
			});

			self.load();
		}
		,
		_designform : function(el) {

			(['select','direct']).each(function(t){

				if (t == $RF('frmGhostbankerconfig',el.name)) {
					// show
					$( el.name + '_' + t).setStyle({display:'block'});
				}
				else {
					// hide
					$( el.name + '_' + t).setStyle({display:'none'});
				}

			});

		}
		,
		save : function() {

			var self = this;

			var ajax = new Ajax.Request('./ax.indb.ghostbanker.php', {
				method: "post",
				parameters: 'mode=save&' + $('frmGhostbanker').serialize(),
				asynchronous: true,
				onComplete: function(response) {if (response.status == 200) {

					var json = response.responseText.evalJSON(true);

					if (json.result == true)
					{
						self.load();
					}

				}}
			});

			return false;
		}
		,
		config : function() {

			var self = this;

			var ajax = new Ajax.Request('./ax.indb.ghostbanker.php', {
				method: "post",
				parameters: 'mode=config&' + $('frmGhostbankerconfig').serialize(),
				asynchronous: true,
				onComplete: function(response) {if (response.status == 200) {

					var json = response.responseText.evalJSON(true);
					alert(json.body);

				}}
			});

			return false;
		}
		,
		list : function(page) {

			$A($('oGhostBanker').down('tbody').rows).each(function(tr){
				Element.remove(tr);
			});


			// ����¡ ��� �� html ����
				g_page.current = page;
				g_page.total	= Math.ceil(g_jsonData.page.total / g_page.limit);

				if (g_page.total && g_page.current > g_page.total) g_page.current = g_page.total;
				g_page.start		= (Math.ceil(g_page.current/ g_page.pages)-1)*g_page.pages;

				g_page.navi = "";

				if(g_page.current>g_page.pages){
					g_page.navi += ' <a href="javascript:nsGhostbanker.list(1);" class=navi>[1]</a>';
					g_page.navi += ' <a href="javascript:nsGhostbanker.list('+g_page.start+');" class=navi>��</a>';
				}

				var i = 0;

				while(i + g_page.start < g_page.total && i < g_page.pages) {
					i++;
					g_page.move = i + g_page.start;
					g_page.navi += (g_page.current == g_page.move) ? ' <b>'+ g_page.move + '</b>' : ' <a href="javascript:nsGhostbanker.list('+g_page.move+');" class=navi>['+g_page.move+']</a>';
				}

				if(g_page.total>g_page.move){

					g_page.next = g_page.move+1;
					g_page.navi += ' <a href="javascript:nsGhostbanker.list('+g_page.next+');" class=navi>��</a>';
					g_page.navi += ' <a href="javascript:nsGhostbanker.list('+g_page.total+');" class=navi>['+g_page.total+']</a>';

				}



			// ����Ʈ, ����¡ ���
				var start, end, row , i, html;

				_start = (g_page.current - 1) * g_page.limit;
				_end = _start + g_page.limit;

				var no = parseInt(g_jsonData.page.total - _start);

				for (i = _start;i < _end ;i++) {

					row = g_jsonData.body[i];

					if (row != undefined) {

						html = '<tr height="25" align="center">';
						html += '<td class="noline"><input type="checkbox" name="chk[]" value="'+ row.sno +'"></td>';
						html += '<td>'+(no--)+'</td>';
						html += '<td>'+row.date+'</td>';
						html += '<td>'+row.name+'</td>';
						html += '<td>'+row.bank+'</td>';
						html += '<td>'+comma(row.money)+'��</td>';
						html += '</tr>';
						html += '<tr><td colspan=12 class=rndline></td></tr>';

						$('oGhostBanker').down('tbody').insert({ bottom: html });

					}

				}

				$('pageNavi').update( g_page.navi );

		}
		,
		load : function() {

			var self = this;

			var ajax = new Ajax.Request('./ax.indb.ghostbanker.php', {
				method: "post",
				parameters: 'mode=load&' + $('frmGhostbankerListOption').serialize(),
				asynchronous: true,
				onComplete: function(response) {if (response.status == 200) {

					var json = response.responseText.evalJSON(true);

					if (json.result == true)
					{

						$('oGhostBanker').down('tbody').down('tr').remove();

						// ���̺� ���
							g_jsonData = json;
							self.list(1);
					}
				}},
				onCreate : function(){

					$('oGhostBanker').down('tbody').insert({ bottom: '<tr><td colspan="20" align="center"><img src="../img/loading.gif"></td></tr>' });
				}
			});

			return false;
		}
		,
		del : function() {
			var self = this;

			if ($('frmGhostbankerList').serialize() == '')
			{
				alert('������ �׸��� ������ �ּ���.');
				return false;
			}


			var ajax = new Ajax.Request('./ax.indb.ghostbanker.php', {
				method: "post",
				parameters: 'mode=delete&' + $('frmGhostbankerList').serialize(),
				asynchronous: true,
				onComplete: function(response) {if (response.status == 200) {
					var json = response.responseText.evalJSON(true);
					$$('input[name="chk[]"]:checked').each(function(item) {
						item.up('tr').remove();
					});
				}}
			});

		}
		,
		download : function() {

				var self = this;

				var inputs = '<input type="hidden" name="mode" value="download" />';

				$A($('frmGhostbankerListOption').serialize().split('&')).each(function(it){
					var pair = it.split('=');
					inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />';
				});
				var f = new Element('form', {'method': 'post','action' : './ax.indb.ghostbanker.php','target':'ifrmHidden'}).update(inputs);
				document.body.appendChild(f);
				f.submit();
				document.body.removeChild(f);

		}
	}
}();




function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFF0" : '';
}

function chkBoxAll(El,mode)
{
	if (!El || !El.length) return;
	for (i=0;i<El.length;i++){
		if (El[i].disabled) continue;
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		iciSelect(El[i]);
	}
}

function _fnInit() {
	nsGhostbanker.init();


}
Event.observe(document, 'dom:loaded', _fnInit, false);

</script>
<style type="text/css">
fieldset.design-selector {padding:10px;margin:0;}
fieldset.design-selector legend {}
fieldset.design-selector legend input {}
fieldset.design-selector div.panel {display:none;}
fieldset.design-selector div.panel div.preview {border:1px solid #F6F6F6;padding:5px;margin-top:10px;}

</style>

<div class="title title_top" style="padding-bottom:15px">��Ȯ�� �Ա��� ����Ʈ ����<span>��Ȯ�� �� �Ա��ڸ� ���/����/�����ϰ�, ������ �ǽð����� ��Ȯ�� �Ա��ڸ� Ȯ���� �� �ֽ��ϴ�.</span>
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=15')"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle"/></a>
</div>

<!-- -->


<div class="title title_top">��Ȯ�� �Ա��� ��� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=15')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
	<form name="frmGhostbanker" id="frmGhostbanker" method="post" action="" onSubmit="return nsGhostbanker.save();">
	<table class=tb>
	<col class=cellC><col class=cellL style="width:250">
	<col class=cellC><col class=cellL>
	<tr>
		<td>�Ա�����</td>
		<td colspan="3">
			<input type="text" name="date" value="<?=date('Ymd')?>" onclick="calendar(event);" class=line>
		</td>
	</tr>
	<tr>
		<td>�Ա���</td>
		<td>
			<input type="text" name="name" value="" class=line>
		</td>
		<td>�Աݳ���</td>
		<td>
			<select name="bank">
			<option value="">=�����=</option>
			<? foreach ( codeitem("bank") as $k=>$v){ ?>
			<option value="<?=$v?>"><?=$v?></option>
			<? } ?>
			</select>
			<input type="text" name="money" value="" class=line> ��

			<input type="image" src="../img/btn_regist_s.gif" border="0" align="absmiddle" style="border:none !important;">
		</td>
	</tr>
	</table>
	</form>

<div style="width:660px;border:solid 1px #cccccc; margin:10px 0 0 0">
<div style="margin:1px; background-color:#f8f8f8; padding:7px 10px; line-height:1.3em;">
	<div style="margin-bottom:2px">
	Ȯ�ε��� ���� �Ա��� ����Ʈ�� ����� �ּ���. <br>
	��ϵ� ��Ȯ�� �Ա��ڸ���Ʈ�� ���θ��� ����Ǿ� �Ա��ڸ� ã�� �� �ֽ��ϴ�.
	</div>
</div>
</div>


<div class="title">��Ȯ�� �Ա��� ����Ʈ <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=15')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
	<form name="frmGhostbankerListOption" id="frmGhostbankerListOption" method="post" action="" onSubmit="return nsGhostbanker.load();">
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>Ű���� �˻�</td>
		<td>
			<select name="skey">
				<option value=""> = ���հ˻� = </option>
				<option value="bank"> ����</option>
				<option value="name"> �Ա���</option>
				<option value="money"> �Աݾ�</option>
			</select>
			<input type="text" name="sword" value="" class="line"/>
			<input type="image" src="../img/btn_search2.gif" align="absmiddle" style="border:none;">
		</td>
	</tr>
	<tr>
		<td>�Ⱓ</td>
		<td>
			<input type="text" name="regdt[]" value="<?=$search['regdt_start']?>" onclick="calendar(event)" size="12" class="line"/> -
			<input type="text" name="regdt[]" value="<?=$search['regdt_end']?>" onclick="calendar(event)" size="12" class="line"/>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle"/></a>
		</td>
	</tr>
	</table>
	</form>

	<br>

	<form name="frmGhostbankerList" id="frmGhostbankerList" method="post"  action="./ax.indb.ghostbanker.php" target="ifrmHidden">
	<table width=100% cellpadding=0 cellspacing=0 border=0 id="oGhostBanker">
	<col width=50><col width=50><col width=15%><col width=15%><col><col>
	<thead>
	<tr><td class=rnd colspan=20></td></tr>
	<tr class=rndbg>
		<th><a href="javascript:void(0)" onClick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class=white>����</a></th>
		<th>��ȣ</th>
		<th>�Ա�����</th>
		<th>����</th>
		<th>����</th>
		<th>�ݾ�</th>
	</tr>
	<tr><td class=rnd colspan=20></td></tr>
	</thead>
	<tbody>
	</tbody>
	</table>

	<div style="padding:10px 10px 3px 10px">
		<a href="javascript:;" onClick="nsGhostbanker.download();"><img src="../img/btn_excel_download.gif"></a>
		<a href="javascript:;" onClick="nsGhostbanker.del();"><img src="../img/btn_s_delete.gif"></a>
	</div>

	<div align="center" class="pageNavi" id="pageNavi">
	</div>

	</form>

<form name="frmGhostbankerconfig" id="frmGhostbankerconfig" method="post" enctype="multipart/form-data" action="./ax.indb.ghostbanker.php" target="ifrmHidden">
<input type="hidden" name="mode" value="config">

<div class="title">��Ȯ�� �Ա��� ���/�˾� ���� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=15')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
	<table class=tb>
	<col class=cellC style="width:150px;"><col class=cellL>
	<tr>
		<td>��� ���</td>
		<td class="noline">
			<label><input type="radio" name="use" value="1" <?=($ghost_cfg['use'] == 1) ? 'checked' : ''?>> ���</label>
			<label><input type="radio" name="use" value="0" <?=($ghost_cfg['use'] == 0) ? 'checked' : ''?>> �̻��</label>
		</td>
	</tr>
	<tr>
		<td>����Ʈ ���� �Ⱓ</td>
		<td class="noline">
			<label><input type="radio" name="expire" value="3" <?=($ghost_cfg['expire'] == 3) ? 'checked' : ''?>> 3��</label>
			<label><input type="radio" name="expire" value="7" <?=($ghost_cfg['expire'] == 7) ? 'checked' : ''?>> 7��</label>
			<label><input type="radio" name="expire" value="14" <?=($ghost_cfg['expire'] == 14) ? 'checked' : ''?>> 14��</label>
			<label><input type="radio" name="expire" value="30" <?=($ghost_cfg['expire'] == 30) ? 'checked' : ''?>> 30��</label>
			<label><input type="radio" name="expire" value="60" <?=($ghost_cfg['expire'] == 60) ? 'checked' : ''?>> 60��</label>
		</td>
	</tr>
	<tr>
		<td>�Ա� ���� ����</td>
		<td class="noline">
			<label><input type="radio" name="hide_bank" value="1" <?=($ghost_cfg['hide_bank'] == 1) ? 'checked' : ''?>> ���</label>
			<label><input type="radio" name="hide_bank" value="0" <?=($ghost_cfg['hide_bank'] == 0) ? 'checked' : ''?>> �̻��</label>
		</td>
	</tr>
	<tr>
		<td>�Ա� �ݾ� ����</td>
		<td class="noline">
			<label><input type="radio" name="hide_money" value="1" <?=($ghost_cfg['hide_money'] == 1) ? 'checked' : ''?>> ���</label>
			<label><input type="radio" name="hide_money" value="0" <?=($ghost_cfg['hide_money'] == 0) ? 'checked' : ''?>> �̻��</label>
		</td>
	</tr>
	<tr>
		<td>��ũ�� �ڵ� ����</td>
		<td class="noline">
			<label><input type="radio" name="bankda_use" value="1" <?=($ghost_cfg['bankda_use'] == 1) ? 'checked' : ''?>> ���</label>
			<label><input type="radio" name="bankda_use" value="0" <?=($ghost_cfg['bankda_use'] == 0) ? 'checked' : ''?>> �̻��</label>
		</td>
	</tr>
	<tr>
		<td>��ũ�� �ڵ� ���� ���ѱݾ�</td>
		<td><input type="text" name="bankda_limit" value="<?=$ghost_cfg['bankda_limit']?>" class=line> ��</td>
	</tr>
	</table>



<div class="title">��Ȯ�� �Ա��� ������ ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=15')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
	<table class=tb>
	<col class=cellC style="width:150px;"><col class=cellL>
	<tr>
		<td>
		���
		</td>
		<td class="noline">
		<!-- -->

		<fieldset class="design-selector">
			<legend>
				<label><input type="radio" name="banner_skin_type" value="select" <?=($ghost_cfg['banner_skin_type'] == 'select' ? 'checked' : '')?>> ���ø����� ����</label>
				<label><input type="radio" name="banner_skin_type" value="direct" <?=($ghost_cfg['banner_skin_type'] == 'direct' ? 'checked' : '')?>> ���� ���ε��ϱ�</label>
			</legend>

			<div class="panel" id="banner_skin_type_select">
				<table border="0" cellpadding="5">
				<tr>
				<td align="center">
					<img src="../../data/ghostbanker/tpl/_banner_1.jpg"><br>
					<input type="radio" name="banner_skin" value="1" <?=($ghost_cfg['banner_skin'] == 1) ? 'checked' : ''?>>
				</td>
				<td align="center">
					<img src="../../data/ghostbanker/tpl/_banner_2.jpg"><br>
					<input type="radio" name="banner_skin" value="2" <?=($ghost_cfg['banner_skin'] == 2) ? 'checked' : ''?>>
				</td>
				<td align="center">
					<img src="../../data/ghostbanker/tpl/_banner_3.jpg"><br>
					<input type="radio" name="banner_skin" value="3" <?=($ghost_cfg['banner_skin'] == 3) ? 'checked' : ''?>>
				</td>
				<td align="center">
					<img src="../../data/ghostbanker/tpl/_banner_4.jpg"><br>
					<input type="radio" name="banner_skin" value="4" <?=($ghost_cfg['banner_skin'] == 4) ? 'checked' : ''?>>
				</td>
				</table>
			</div>

			<div class="panel" id="banner_skin_type_direct">

				<input type="file" name="banner_file"> (jpg, gif, png ������ �̹���)
				<? if ($ghost_cfg['banner_file'] != '') { ?>
				<div id="banner_file_preview" class="preview">
					<img src="../../data/ghostbanker/<?=$ghost_cfg['banner_file']?>" onerror="this.src='../img/bn_blank.gif'">
					<label><input type="checkbox" name="banner_file_delete" value="<?=$ghost_cfg['banner_file']?>"> ����</label>
				</div>
				<? } ?>

			</div>

		</fieldset>

		<!-- -->
		</td>
	</tr>
	<tr>
		<td></td>
		<td>

		<p style="font-weight:normal;">

		{ghostBankerBanner} <img class="hand" src="../img/i_copy.gif" onclick="window.clipboardData.setData('Text', '{ghostBankerBanner}');" alt="�����ϱ�" align="absmiddle"/> <br>

		������ ���ø��� ġȯ�ڵ带 ���ϴ� ������ �����ϸ� ���θ� ���������� Ȯ���� �� �ֽ��ϴ�.


		</p>
		</td>
	</tr>
	<tr>
		<td class="noline">��Ų</td>
		<td class="noline">
		<!-- -->

		<fieldset class="design-selector">
			<legend>
				<label><input type="radio" name="design_skin_type" value="select" <?=($ghost_cfg['design_skin_type'] == 'select' ? 'checked' : '')?>> ���ø����� ����</label>
				<label><input type="radio" name="design_skin_type" value="direct" <?=($ghost_cfg['design_skin_type'] == 'direct' ? 'checked' : '')?>> ���� �Է��ϱ�</label>
			</legend>

			<div class="panel" id="design_skin_type_select">
				<table border="0" style="margin-left:20px" cellpadding="5">
				<tr>
				<td align="center">
					<img src="../../data/ghostbanker/tpl/_list_1.jpg"><br>
					<input type="radio" name="design_skin" value="1" <?=($ghost_cfg['design_skin'] == 1) ? 'checked' : ''?>>
				</td>
				<td align="center">
					<img src="../../data/ghostbanker/tpl/_list_2.jpg"><br>
					<input type="radio" name="design_skin" value="2" <?=($ghost_cfg['design_skin'] == 2) ? 'checked' : ''?>>
				</td>
				<td align="center">
					<img src="../../data/ghostbanker/tpl/_list_3.jpg"><br>
					<input type="radio" name="design_skin" value="3" <?=($ghost_cfg['design_skin'] == 3) ? 'checked' : ''?>>
				</td>
				<td align="center">
					<img src="../../data/ghostbanker/tpl/_list_4.jpg"><br>
					<input type="radio" name="design_skin" value="4" <?=($ghost_cfg['design_skin'] == 4) ? 'checked' : ''?>>
				</td>
				</table>
			</div>

			<div class="panel" id="design_skin_type_direct">
				<? if (is_file( SHOPROOT.'/data/ghostbanker/tpl/src/custom.htm' )) $ghost_cfg['design_html'] = file_get_contents(SHOPROOT.'/data/ghostbanker/tpl/src/custom.htm'); ?>
				<textarea name="design_html" style="width:100%;height:200px" id="design_html" type="editor"><?=$ghost_cfg['design_html']?></textarea><br>
			</div>

		</fieldset>

		<!-- -->
		</td>
	</tr>
	</table>

<div class="button">
<input type="image" src="../img/btn_save.gif">
</div>

</form>


<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������ �Ա� �ֹ� �� �Ա������� ��Ȯ�ϰ� Ȯ�ε��� ���� �ǿ� ���� �����ϴ� ����Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Աݾװ� �ֹ��ݾ��� ���� �ʰų�, �ֹ���ȣ�� �Ա��ڸ��� ��Ȯ���� ���� ��� ��Ȯ�� �Ա��ڸ� ����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ϵ� ��Ȯ�� �Ա��ڴ� ���θ��� ����˴ϴ�. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ó���� ��Ȯ�� �Ա��ڰ��� �����Ͽ� ����ó���� ���ּž� �մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���θ��� ����Ǵ� ��� ���ø��� �����ϰų� ���� ����Ͽ� ����Ͻ� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<!-- // -->
<? include "../_footer.php"; ?>