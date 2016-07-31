<?
include "../_header.popup.php";
include "../../lib/page.class.php";

$selected[stype][$_GET[stype]] = "selected";

$db_table = "".GD_MEMBER."";

if ($_GET[stype] && $_GET[sword]) $where[] = "$_GET[stype] like '%$_GET[sword]%'";
$where[] = "sms = 'y'";	// 수신을 동의한 주소록만
$where[] = MEMBER_DEFAULT_WHERE;
$pg = new Page($_GET[page],10);
$pg->setQuery($db_table,$where,"m_no desc");
$pg->exec();

$res = $db->query($pg->query);
?>

<script type="text/javascript">
var nsTable_selector = function() {
return {

	last_clicked_el : null,
	data : [],
	table : null,
	_id : null,
	init : function(_id) {

		var self = this;

		self._id = _id;

		self.table = $(self._id);
		var idx = 0;

		$A(self.table.down('tbody').rows).each(function(tr) {
			tr.id = self._id + '-tr-'+ idx;
			self.data[tr.id] = false;
			idx++;
		});

		Event.observe(self.table,'click', nsTable_selector._onClick , false);

		Event.observe(document,'selectstart', function(){
			Event.stop(event);
		}, false);

		self = null;

	},
	_getIdx : function(el) {
		return (el.id) ? parseInt( el.id.replace(this._id + '-tr-','') ) : 0;
	},
	_onClick : function(event) {

		var self = nsTable_selector;

		var el = Element.up(event.srcElement,'tr');

		if (event.shiftKey) {

			if (self.last_clicked_el == null) self.last_clicked_el = self.table.down('tbody').rows[0];

			var c_idx = self._getIdx(el);
			var l_idx = self._getIdx(self.last_clicked_el);

			var _start = _end = _idx = 0;

			if (c_idx > l_idx)
			{
				_start = l_idx;
				_end = c_idx;
			}
			else {
				_start = c_idx;
				_end = l_idx;
			}

			$A(self.table.down('tbody').rows).each(function(tr){

				_idx = self._getIdx(tr);

				if (_idx >= _start && _idx <= _end) {
					tr.style.backgroundColor = '#3399FF';
					self.data[tr.id] = true;
				}
				else {
					tr.style.backgroundColor = '';
					self.data[tr.id] = false;
				}

			});
		}
		else if (event.ctrlKey) {

			self.last_clicked_el = el;

			if (!self.data[el.id]) {
				el.style.backgroundColor = '#3399FF';
			}
			else {
				el.style.backgroundColor = '';
			}
			self.data[el.id] = !self.data[el.id];
		}
		else {

			self.last_clicked_el = el;

			$A(self.table.down('tbody').rows).each(function(tr){
				if (tr == el) {
					tr.style.backgroundColor = '#3399FF';
					self.data[tr.id] = true;
				}
				else {
					tr.style.backgroundColor = '';
					self.data[tr.id] = false;
				}
			});
		}
		self = null;
	},
	add : function() {

		var self = this;

		$A(self.table.down('tbody').rows).each(function(tr) {
			if (self.data[tr.id]) {
				var tds = $(tr.id).childElements();
				parent.sendNumber( tds[3].innerText ,1 );
				// 선택 해제;
				self.data[tr.id] = false;
				tr.style.backgroundColor = '';
			}
		});

		self = null;

	},
	all : function() {
		var self = this;
		$A(self.table.down('tbody').rows).each(function(tr) {
			tr.style.backgroundColor = '#3399FF';
			self.data[tr.id] = true;
		});
		self = null;
	}
}
}();

Event.observe(document, 'dom:loaded', function(){
	nsTable_selector.init('el-sms-list');
}, false);
</script>

<body scroll="no" style="margin:0;">

<div class="extext" style="color: red;">※ 정보통신망법의 기준에 따라 SMS수신여부가 수신거부인 회원은 발송대상에서 제외됩니다.</div>

<form>
<table>
<tr>
	<td>
	<select name=stype>
	<option value="name" <?=$selected[stype][name]?>>이름
	<option value="m_id" <?=$selected[stype][m_id]?>>아이디
	<option value="mobile" <?=$selected[stype][mobile]?>>휴대폰번호
	</select>
	</td>
	<td>
	<input type=text name=sword value="<?=$_GET[sword]?>" class="line">
	</td>
	<td><input type=image src="../img/btn_search_s1.gif" class=null></td>
</tr>
</form>

<table width=100% border=1 bordercolor=#B9B9B9 style="border-collapse:collapse" id="el-sms-list">
<thead>
<tr bgcolor=#F1F1F1 height=25>
	<th width=60><font class=small color=262626><b>번호</th>
	<th width=90><font class=small color=262626><b>이름</th>
	<th width=90><font class=small color=262626><b>아이디</th>
	<th><font class=small color=262626><b>핸드폰번호</th>
</tr>
<col align=center span=4>
</thead>
<tbody>
<?
while ($data=$db->fetch($res)){
	//SMS 발송 실패 여부
	$smsFailCheck = smsFailCheck('single', $data['mobile']);
?>
<tr height=24>
	<td><font class=ver8 color=262626><?=$pg->idx--?></td>
	<td><font class=small color=262626><?=$data[name]?></td>
	<td><font class=ver8 color=262626><?=$data[m_id]?></td>
	<td><a href="javascript:parent.sendNumber('<?=$data[mobile]?>')"><font class="ver8 el-mobile-str"><?=$data[mobile]?></font></a><?php if($smsFailCheck === true){ ?><font class="ver8 el-mobile-str" color="red"> SMS 발송실패 번호</font> <?php } ?></td>
</tr>
<? } ?>
</tbody>
</table>

<div style="padding:3px;">
<a href="javascript:void(0);" onClick="nsTable_selector.all();"><img src="../img/btn_allchoice.gif"></a>
</div>

<div class="pageNavi" align=center><font class=ver8 color=444444><?=$pg->page[navi]?></div>