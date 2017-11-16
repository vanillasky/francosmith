<?

$location = "상품관리 > 메인페이지 상품진열";
include "../_header.php";
include "../../conf/design.main.php";
@include "../../conf/design_main.".$cfg['tplSkinWork'].".php";

$mainAutoSort = Core::loader('mainAutoSort');

//최대 상품수
$mainAutoSort_sortLimit = $mainAutoSort->getSortLimit();

$query = "
select
	distinct a.mode,a.goodsno,b.goodsnm,b.img_s,c.price
from
	".GD_GOODS_DISPLAY." a,
	".GD_GOODS." b,
	".GD_GOODS_OPTION." c
where
	a.goodsno=b.goodsno
	and a.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
	".$strSQLWhere."
order by sort
";
$res = $db->query($query);
while ($data=$db->fetch($res)) $loop[$data[mode]][] = $data;

foreach ($cfg_step as $k=>$v) {
	$add_table = $add_query = '';
	if ($v['sort_type'] != '1') {
		//현재 사용중인 테이블
		$mainAutoSort_useTable = $mainAutoSort->getUseTable($v['sort_type']);

		if((string)$v['sort_type'] === '5'){
			$sortNum = $mainAutoSort_useTable.".auto_goodsno DESC";
		}
		else {
			$sortNum = 'sort'.$v['sort_type']."_".$v['select_date'];
		}
		$orderby = 'order by '.$sortNum;

		list($add_table, $add_where, $add_group) = $mainAutoSort->getSortTerms($v, $sortNum);

		$query = "
			SELECT
				".GD_GOODS.".goodsno,".GD_GOODS.".goodsnm,".GD_GOODS.".img_s
			FROM
				".$mainAutoSort_useTable."
				".$add_table."
			where
				".GD_GOODS.".open AND ".GD_GOODS_OPTION.".link=1
				".$add_where."
				".$add_group." ".$orderby."
			limit
				".$mainAutoSort_sortLimit."
		";

		$res = $db->query($query);
		while ($data=$db->fetch($res)) $auto_loop[$k][] = $data;
	}
}

$ar_display_type = array(1 => '갤러리형');
$ar_display_type[] = '리스트형';
$ar_display_type[] = '리스트 그룹형';
$ar_display_type[] = '상품이동형';
$ar_display_type[] = '롤링';
$ar_display_type[] = '스크롤';
$ar_display_type[] = '탭';
$ar_display_type[] = '선택강조';
$ar_display_type[] = '이미지';
$ar_display_type[] = '말풍선';
$ar_display_type[] = '장바구니';

//진열 상품선정 기준
$ar_sort_type = array(1 => '직접진열');
$ar_sort_type[] = '인기순(판매금액)';
$ar_sort_type[] = '인기순(판매개수)';
$ar_sort_type[] = '상품평가순';
$ar_sort_type[] = '해시태그';

// 기본 설정값 지정
foreach ($cfg_step as $k => $v) {
	if (empty($cfg_step[$k]['alphaRate'])) $cfg_step[$k]['alphaRate'] = 70;
	if (empty($cfg_step[$k]['sort_type'])) $cfg_step[$k]['sort_type'] = 1;
	if (empty($cfg_step[$k]['select_date'])) $cfg_step[$k]['select_date'] = 7;
	if (empty($cfg_step[$k]['regdt'])) $cfg_step[$k]['regdt'] = '';
foreach($ar_display_type as $_k => $_v) {
	if (empty($cfg_step[$k]['dOpt'.$_k])) $cfg_step[$k]['dOpt'.$_k] = 1;
}}
?>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/lib/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/lib/js/jquery-ui.js"></script>
<link href="<?php echo $cfg['rootDir']; ?>/lib/js/jquery-ui-1.10.4.custom.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/proc/hashtag/hashtagControl.js?actTime=<?php echo time(); ?>"></script>
<script type="text/javascript">jQuery.noConflict();</script>
<script src="../../lib/js/categoryBox.js"></script>
<script>

	var r_list = new Array('step0','step1','step2','step3','step4');

<?
	$cfg_step_keys = array_keys($cfg_step);
	for ($i=5,$max=sizeof($cfg_step_keys);$i<$max;$i++) {
		echo "r_list.push('step".$cfg_step_keys[$i]."');\n";
	}
?>
	// 진열 타입 추가/삭제 관련
	var setted_idx = [];


	function fnGenerateFormKey() {

		var key = Math.floor(new Date().getTime() / 1000);	// timestamp

        for (k in setted_idx) {
            if (setted_idx[k] === key) {
				return fnGenerateFormKey();
            }
        }

		setted_idx.push(key);

		return key;
	}

	function fnAddDisplayForm() {

		var tpl = new Template( $('extra-display-form-src').innerHTML.unescapeHTML() );

		var idx = fnGenerateFormKey();	// 중복되지 않는 폼 키를 구함(=timestamp 에 근거)

		var data = {
			i : idx
		};

		$('extra-display-form-wrap').insert({ bottom: tpl.evaluate(data) });

		r_list.push('step'+idx);

		table_design_load();

		return false;
	}

	function fnDelDisplayForm(idx) {

		if (confirm('상품 진열 폼을 삭제 하시겠습니까?'))
		{
			$('extra-display-form-' + idx).remove();
		}
	}

	function fnCopyDisplayFormSrc(idx) {
		if ( window.clipboardData ) {
			alert("코드를 복사하였습니다. \n원하는 곳에 붙여넣기(Ctrl+V)를 하시면 됩니다~");
			window.clipboardData.setData("Text", $('extra-display-form-tplsrc-'+idx).innerHTML);
		} else {
			prompt("코드를 클립보드로 복사(Ctrl+C) 하시고. \n원하는 곳에 붙여넣기(Ctrl+V)를 하시면 됩니다~", $('extra-display-form-tplsrc-'+idx).innerHTML);
		}
	}

	// 디스플레이 유형 관련

	var cfg_step_data = <?=$cfg_step ? gd_json_encode($cfg_step) : '{}'?>;
	var _default = {};

	function fnSetExtraOption(gid, tid) {	// 진열 그룹 순번, 진열 타입 번호
		var oTpl = $(tid);

		var data = cfg_step_data[gid] || {};
		data.i = gid;

		data.checked = {};
		$H(data).each(function(pair){
			if (pair.key.indexOf('dOpt') > -1 && pair.value) {
				eval('data.checked.'+ pair.key +' = ["",""];');
				eval('data.checked.'+ pair.key +'['+pair.value+'] = "checked";');
			}
		});

		if (oTpl != null) {
			var tpl = new Template( oTpl.innerHTML.unescapeHTML() );

			if (tid == '탭') {

				data.tab = fnInitTab(gid, data);
				var html = tpl.evaluate(data);

				$('gList_' + gid).style.display = 'none';

			}
			else {

				var html = tpl.evaluate(data);
				$('gList_' + gid).style.display = '';

			}

			$('extra-config-wrap-display-type-' + gid).update( html );
			$('extra-config-display-type-' + gid).style.display = '';

			if (tid == '탭') {
				$('tabNum_'+gid).selectedIndex = parseInt(data.tabNum) - 1;
				var m = $('tabNum_'+gid).getValue();
				var tb;

				for (tb=1;tb<=m ;tb++ )
				{
					// 상품 가져오기
					fnGetTabsGoods(gid, tb);
				}
			}
		}
		else {
			$('extra-config-wrap-display-type-' + gid).update('');
			$('extra-config-display-type-' + gid).style.display = 'none';
		}
		sort_type_view(gid);
	}

	function fnChangeTab(gid, obj) {
		cfg_step_data[gid] = cfg_step_data[gid] || {};
		cfg_step_data[gid].tabNum = obj.value;
		fnSetExtraOption(gid, '탭');
	}

	function fnInitTab(gid, data, obj) {

		var tpl = new Template( $('탭쓰').innerHTML.unescapeHTML() );

		if (obj != undefined) {
			var m = obj.value;
			data = cfg_step_data[gid] || {};
			data.i = gid;
		}
		else
			var m = data.tabNum || 1;

		var tab = '';

		for (var tb=1;tb<=m ;tb++)
		{
			data.tb = tb;
			data.tabName = eval('data.tabName'+tb);
			tab += tpl.evaluate(data);

			var exist = false;

			for (key in r_list) {
				if (r_list[key] == 'step'+gid+'_'+tb) exist = true;
			}

			if (exist == false) r_list.push('step'+gid+'_'+tb);

		}

		return tab;
	}



	function fnGetTabsGoods(gid, tb) {

		var html = '\
			<input type=hidden name=e_step#{gid}_#{tb}[] value="#{goodsno}">\
			<a href="../../goods/goods_view.php?goodsno=#{goodsno}" target=_blank>#{goodsimg}</a>\
		';

		var mode = tb+'_'+gid;

		var ajax = new Ajax.Request('./disp_main_get_goods.php', {
			method: "post",
			parameters: 'mode='+mode,
			asynchronous: false,
			onComplete: function(response) { if (response.status == 200) {

				var json = response.responseText.evalJSON(true);

				var tpl = new Template(html);

				var i, row;
				var json_len = json.length;

				var _html = '';

				for (i=0;i < json_len ;i++ )
				{
					row = json[i];
					row.gid = gid;
					row.tb = tb;
					_html += tpl.evaluate(row);
				}

				$('step'+gid+'_'+tb+'X').insert(_html);
			}}
		});
	}
	function sort_type_view(idx) {
		var value = $$("input:checked[name='sort_type["+idx+"]']")[0].value;
		var tpl_value = $$("input:checked[name='tpl["+idx+"]']").length > 0 ? $$("input:checked[name='tpl["+idx+"]']")[0].value : '';
		var gList_disp = sList_disp = mList_disp1 = mList_disp2 = hList_disp = '';

		if (tpl_value == 'tpl_07') {
			gList_disp = sList_disp = mList_disp = 'none';
		} else {
			if (parseInt(value) == 1) {
				mList_disp = hList_disp = 'none';
				sList_disp = gList_disp = '';
			}
			else if(parseInt(value) == 5){ //해시태그
				mList_disp = gList_disp = 'none';
				sList_disp = hList_disp = '';
			}
			else {
				gList_disp = hList_disp = 'none';
				sList_disp = mList_disp = '';
			}
		}

		$('gList_' + idx).setStyle({display:gList_disp});
		$('step' + idx + 'X').setStyle({display:gList_disp});
		$('sList_' + idx).setStyle({display:sList_disp});

		if(tpl_value == 'tpl_07'){
			$$('.mList_' + idx).each(function(e,key){
				e.setStyle({display:mList_disp});
			});
			$$('.hList_' + idx).each(function(e,key){
				e.setStyle({display:hList_disp});
			});
		}
		else {
			if(parseInt(value) == 5){ //해시태그
				$$('.mList_' + idx).each(function(e,key){
					e.setStyle({display:mList_disp});
				});
				$$('.hList_' + idx).each(function(e,key){
					e.setStyle({display:hList_disp});
				});
			}
			else {
				$$('.hList_' + idx).each(function(e,key){
					e.setStyle({display:hList_disp});
				});
				$$('.mList_' + idx).each(function(e,key){
					e.setStyle({display:mList_disp});
				});
			}
		}
	}
	function disp_main_save() {
		var hashtagName = new Array();
		var ret = true;
		var idx = 0;
		$$("input[type=checkbox][name^='chk']").each(function(e){
			key = e.name.replace(/[^0-9]/g,'');
			if(e.checked === true){
				switch($$("input:checked[name='sort_type["+key+"]']")[0].value){
					case '2' : case '3' : case '4' :
						if (!$$("input[name='categoods["+key+"][]']").length && ret === true) {
							alert("진열 대상 카테고리를 선택해 주세요.");
							document.getElementsByName('step_categoods['+key+'][]')[0].focus();
							ret = false;
						}
					break;

					case '5' :
						if (!$$("input[name='hashtagName["+key+"]']")[0].value) {
							alert("해시태그를 입력해 주세요.");
							document.getElementsByName('hashtagName['+key+']')[0].focus();
							ret = false;
						}
						hashtagName[idx] = {key:key, value:$$("input[name='hashtagName["+key+"]']")[0].value};
						idx++;
					break;

					default :

					break;
				}
			}
		});

		//해시태그 유효성 체크
		if(ret === true && hashtagName.length > 0){
			var ajaxHashtag = new Ajax.Request('../../proc/hashtag/ajax.getHashtagData.php', {
				method: "post",
				asynchronous : false,
				parameters : {
                    'mode' : 'checkLiveHashtag',
                    'hashtagName' : JSON.stringify(hashtagName)
                },
				onComplete: function(res) {
					var dataArray = new Array();
					dataArray = res.responseText.split("|");

					var hashtagData = eval("("+dataArray[1]+")");

					if(hashtagData !== ''){
						alert('존재하지 않는 해시태그 입니다.');
						$$("input[name='hashtagName["+hashtagData+"]']")[0].focus();
						ret = false;
					}
				}
			});
		}

		if (ret === false) return false;
		else {
			var width = document.body.scrollWidth;
			var height = document.body.scrollHeight;
			var imgPosition = document.body.scrollTop + (document.body.clientHeight/2 - 58);
			$('onLoading-hide-layer').setStyle({width:width+'px',height:height+'px',display:'block'});
			$('onLoading-img').setStyle({margin:imgPosition+'px 0 0 0'});
		}
	}
</script>

<style>
#extra-display-form-wrap {}
.display-type-config-tpl {display:none;}
.display-type-wrap {width:94px;float:left;margin:3px;}
.display-type-wrap img {border:none;width:94px;height:72px;}
.display-type-wrap div {text-align:center;}

.display-type-config {width:100%;background:#e6e6e6;border:2px dotted #f54c01;}
.display-type-config  th, .display-type-config  td {font-weight:normal;text-align:left;}
.display-type-config  th {width:100px;background:#f6f6f6;}
.display-type-config  td {background:#ffffff;}
xmp.extra-display-form-tplsrc {margin:0;font-size:11px;}
.add_categoods_box {float:left; background:#f3f3f3; padding:5px; margin-top:5px; display:block; clear:both;}
#onLoading-hide-layer {position:absolute; z-index:100; top:0; left:0; width:100%; height:100%; text-align:center; background:rgb(68, 81, 91); filter:alpha(opacity=70); opacity:0.7; display:none;}
.hashtagInputText { border: 1px #BDBDBD solid; width: 170px; float: left; height: 18px; }
.hashtagInputText input { border: none; height: 16px; width: 150px; }
</style>
<div id="onLoading-hide-layer"><img src="../img/admin_progress.gif" id="onLoading-img" /></div>
<!-- 세부설정 소스 -->
	<textarea id="상품이동형" class="display-type-config-tpl"><table class="display-type-config">
	<tr>
		<th>이동 방향</th>
		<td class="noline">
		<label><input type="radio" name="dOpt4[#{i}]" value="1" #{checked.dOpt4[1]}  />오른쪽에서 왼쪽으로</label>
		<label><input type="radio" name="dOpt4[#{i}]" value="2" #{checked.dOpt4[2]}  />왼쪽에서 오른쪽으로 </label>
		</td>
	</tr>
	</table></textarea>

	<textarea id="롤링" class="display-type-config-tpl"><table class="display-type-config">
	<tr>
		<th>이동 방향</th>
		<td class="noline">
		<label><input type="radio" name="dOpt5[#{i}]" value="1" #{checked.dOpt5[1]}  />위에서 아래로</label>
		<label><input type="radio" name="dOpt5[#{i}]" value="2" #{checked.dOpt5[2]}  />아래에서 위로</label>
		</td>
	</tr>
	</table></textarea>

	<textarea id="스크롤" class="display-type-config-tpl"><table class="display-type-config">
	<tr>
		<th>이동 방향</th>
		<td class="noline">
		<label><input type="radio" name="dOpt6[#{i}]" value="1" #{checked.dOpt6[1]}  />가로형</label>
		<label><input type="radio" name="dOpt6[#{i}]" value="2" #{checked.dOpt6[2]}  />세로형</label>
		</td>
	</tr>
	</table></textarea>

	<textarea id="탭" class="display-type-config-tpl"><table class="display-type-config">
	<tr>
		<th>탭 개수</th>
		<td class="noline">
		<select name="tabNum[#{i}]" id="tabNum_#{i}" onchange="fnChangeTab('#{i}',this);">
		<? for($j = 1; $j <= 7; $j++) { ?>
		<option value="<?=$j?>"><?=$j?></option>
		<? } ?>
		</select> 개
		<font class="extext">탭의 개수 입니다.</font>
		</td>
	</tr>
	<tr>
		<th>탭 방향</th>
		<td class="noline">
		<label><input type="radio" name="dOpt3[#{i}]" value="1" #{checked.dOpt3[1]}  />가로형</label>
		<label><input type="radio" name="dOpt3[#{i}]" value="2" #{checked.dOpt3[2]}  />세로형</label>
		</td>
	</tr>
	#{tab}
	</table></textarea>

	<!-- 탭에 끼워들어 가는 부분이므로, table 로 감싸지 않음. -->
	<textarea id="탭쓰" class="display-type-config-tpl">
	<tr>
		<th>#{tb}번 탭이름</th>
		<td>
		<input type="text" name="tabName#{tb}[#{i}]" value="#{tabName}" class="rline">
		</td>
	</tr>
	<tr>
		<th>진열할 상품선정</th>
		<td>
			<div style="padding-top:5px;"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_step#{i}_#{tb}[]', 'step#{i}_#{tb}X');" align="absmiddle" /> <font class="extext">※주의: 상품선택 후 반드시 하단 저장버튼을 누르셔야 최종 저장이 됩니다.</font></div>
			<div style="position:relative;z-index:1000;">
				<div id=step#{i}_#{tb}X style="padding-top:3px"></div>
			</div>
		</td>
	</tr>
	</textarea>

	<textarea id="선택강조" class="display-type-config-tpl"><table class="display-type-config">
	<tr>
		<th>효과 대상</th>
		<td class="noline">
		<label><input type="radio" name="dOpt8[#{i}]" value="1" #{checked.dOpt8[1]}  />선택한 상품만 흐리게</label>
		<label><input type="radio" name="dOpt8[#{i}]" value="2" #{checked.dOpt8[2]}  />선택한 나머지 상품 흐리게</label>
		</td>
	</tr>
	<tr>
		<th>투명도</th>
		<td>
		<input type="text" name="alphaRate[#{i}]" value="#{alphaRate}" class="rline"> <font class="extext">0%에 가까울수록 투명해 집니다.</font>
		</td>
	</tr>
	</table></textarea>

	<textarea id="말풍선" class="display-type-config-tpl"><table class="display-type-config">
	<tr>
		<th>배경색</th>
		<td class="noline">
		<label><input type="radio" name="dOpt10[#{i}]" value="1" #{checked.dOpt10[1]}  />타입1(Black)</label>
		<label><input type="radio" name="dOpt10[#{i}]" value="2" #{checked.dOpt10[2]}  />타입2(white)</label>
		</td>
	</tr>
	<tr>
		<th>투명도</th>
		<td>
		<input type="text" name="alphaRate[#{i}]" value="#{alphaRate}" class="rline"> <font class="extext">0%에 가까울수록 투명해 집니다.</font>
		</td>
	</tr>
	</table></textarea>
<!-- 세부설정-->


<!-- 진열 타입 소스 -->
	<textarea id="extra-display-form-src" style="display:none;width:100%;height:300px;">
	<div id="extra-display-form-#{i}" class="extra-display-form">
	<div class=title>메인페이지 상품진열 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a> <span><a href="../../main/index.php" target=_blank><font color=0074BA>[메인화면보기]</font></a></span></div>
	<?=$strMainGoodsTitle?>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>설명</td>
		<td><input type=text name=title[#{i}] value="#{title}" class=lline>
		<div style="padding-top:4px"><font class=extext>관련 타이틀이미지는 <a href="../design/codi.php" target=_blank><font class=extext_l>[디자인관리 > 메인페이지디자인 > 본문영역]</font></a> 에서 작성하세요</font></div></td>
	</tr>
	<tr>
		<td>사용유무 <a href="javascript:void(0);" onClick="fnDelDisplayForm(#{i});"><img src="../img/i_del.gif" align="absmiddle"></a></td>
		<td class=noline>
		<input type=checkbox name=chk[#{i}]> 체크시 메인페이지에 출력이됩니다
		</td>
	</tr>
	<tr>
		<td>템플릿 소스코드<div style="padding-top:3px"></div><a href="javascript:void(0);" onClick="fnCopyDisplayFormSrc('#{i}')"><img src="../img/btn_codecopy.gif" align="absmiddle"></a></td>
		<td>
			<xmp id="extra-display-form-tplsrc-#{i}">{ ? _cfg_step[#{i}].chk }
			{ = this->assign( 'loop', dataDisplayGoods( #{i}, _cfg_step[#{i}].img, _cfg_step[#{i}].page_num ) ) }
			{ = this->assign( 'dpCfg', _cfg_step[#{i}] ) }
			{ = this->assign( 'id', 'main_list_#{i}' ) }
			{ = this->assign( 'cols', _cfg_step[#{i}].cols ) }
			{ = this->assign( 'size', _cfg[_cfg_step[#{i}].img] ) }
			{ = include_file( 'goods/list/' + _cfg_step[#{i}].tpl + '.htm' ) }
			{ / }</xmp>
		</td>
	</tr>
	<tr>
		<td>디스플레이유형</td>
		<td>
		<? for ($t=1,$m=sizeof($ar_display_type);$t<=$m;$t++) { ?>
		<div class="display-type-wrap">
			<img src="../img/goodalign_style_<?=sprintf('%02d',$t)?>.gif"  alt="<?=$ar_display_type[$t]?>">
			<div class="noline">
			<input type=radio name=tpl[#{i}] value="tpl_<?=sprintf('%02d',$t)?>" onclick="fnSetExtraOption(#{i},'<?=$ar_display_type[$t]?>')">
			</div>
		</div>
		<? } ?>
		</td>
	</tr>
	<tr id="extra-config-display-type-#{i}" style="display:none;">
		<td>세부 설정</td>
		<td id="extra-config-wrap-display-type-#{i}"></td>
	</tr>
	<tr>
		<td>이미지 선택</td>
		<td>
		<select name=img[#{i}]>
		<option value="img_s"> 리스트이미지 (<?=$cfg[img_s]?>px)
		<option value="img_i"> 메인이미지 (<?=$cfg[img_i]?>px)
		</select>
		<font class=extext>적당한 사이즈의 이미지를 고르세요
		</td>
	</tr>
	<tr>
		<td>메인출력 상품수</td>
		<td><input type=text name=page_num[#{i}] value="" class="rline"> 개 <font class=extext>메인페이지에 보여지는 총 상품수입니다</td>
	</tr>
	<tr>
		<td>라인당 상품수</td>
		<td><input type=text name=cols[#{i}] value="" class="rline"> 개 <font class=extext>한줄에 보여지는 상품수입니다</td>
	</tr>
	<tr id="sList_#{i}">
		<td>진열 상품선정 기준</td>
		<td>
		<? foreach ($ar_sort_type as $key => $value){?>
		<label><input type="radio" name="sort_type[#{i}]" value="<?=$key?>" <?=$key == 1 ? "checked" : ""?> onclick="sort_type_view('#{i}')"><?=$value?></label>
		<? } ?><br />
		<font class=extext>* 디스플레이유형을 ‘탭 진열형‘으로 선택한 경우 직접진열 기준만 사용할 수 있습니다.<br />* 인기순 및 상품평가순 으로 진열 시 상품은 최대 <?=$mainAutoSort_sortLimit?>개 까지만 진열이 됩니다.</font>
		</td>
	</tr>
	<tr class="hList_#{i}" style="display:none;">
		<td>진열 대상<br />해시태그 설정</td>
		<td>
			<div class="hashtagInputText">#<input type="text" name="hashtagName[#{i}]" class="hashtagInputListSearch" maxlength="20" /></div>
		</td>
	</tr>
	<tr class="mList_#{i}" style="display:none;">
		<td>진열 상품선정 기간</td>
		<td>
		최근 <select name="select_date[#{i}]">
		<option value="7" selected>7일</option>
		<option value="15">15일</option>
		</select>
		</td>
	</tr>
	<tr class="mList_#{i}" style="display:none;">
		<td>진열 대상 카테고리</td>
		<td>
			<script>new categoryBox('step_categoods[#{i}][]',4,'','','','category_#{i}');</script>
			<span id="category_#{i}"></span>
			<a href="javascript:addCate('categoods','#{i}');"><img src="../img/i_add.gif" align="absmiddle" /></a>
			<div id="add_categoods_area_#{i}"></div>
		</td>
	</tr>
	<tr class="mList_#{i} hList_#{i}" style="display:none;">
		<td>진열 대상 추가조건</td>
		<td>
			<a onclick="more_terms('#{i}')" class="hand"><img src="../img/disp_btn_open.gif" id="more_terms_btn_#{i}" /></a><br />
			<table id="more_terms_#{i}" class=tb style="display:none;">
			<col class=cellC><col class=cellL>
			<tr>
				<td>진열 상품가격</td>
				<td>
					<input type=text name="price[#{i}][]" onkeydown="onlynumber()" size="15" class="ar"> 원 -
					<input type=text name="price[#{i}][]" onkeydown="onlynumber()" size="15" class="ar"> 원
				</td>
			</tr>
			<tr>
				<td>진열 상품재고수량</td>
				<td>
					<label><input name="stock_type[#{i}]" value="product" type="radio" checked />상품재고(품목재고 합)</label>
					<label><input name="stock_type[#{i}]" value="item" type="radio"  />품목재고</label>
					<div>
						<input type=text name="stock_amount[#{i}][]" onkeydown="onlynumber()" size="15" class="ar"> 개 -
						<input type=text name="stock_amount[#{i}][]" onkeydown="onlynumber()" size="15" class="ar"> 개
					</div>

					<p class="help">
						<font color="blue">상품재고:</font> 상품내 품목(가격옵션)별 재고 총합의 조건을 말합니다. 주문시 재고차감(재고량연동)인 상품만 진열대상이 됩니다. <br/>
						<font color="blue">품목재고:</font> 품목(가격옵션) 개별 재고 조건을 말합니다. 주문시 재고차감(재고량연동)인 상품만 진열대상이 됩니다.
					</p>
				</td>
			</tr>
			<tr>
				<td>진열 상품등록일</td>
				<td>
					최근 <select name="regdt[#{i}]">
						<option value="" selected>전체</option>
						<option value="7">7일</option>
						<option value="15">15일</option>
						<option value="30">30일</option>
						<option value="60">60일</option>
					</select>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr class="mList_#{i} hList_#{i}" style="display:none;">
		<td>진열 상품</td>
		<td>
			<span class="extext">* 관리자 로그인 시 설정 기준으로 진열될 상품이 변경됩니다. (미진열 상품은 제외됩니다.) </span><br />
			<span style="color:#ff0000;">※ 조건설정 후 반드시 하단 [저장]버튼을 누르셔야 상품이 진열됩니다.</span>
		</td>
	</tr>
	<tr id="gList_#{i}">
		<td>진열할 상품선정<div style="padding-top:3px"></div>
		<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_display.html',850,523)"><font class=extext_l>[상품순서변경 방법]</font></a>
		<div style="padding-top:3px"></div>
		<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_display.html',850,523)"><img src="../img/icon_sample.gif" border="0" align=absmiddle hspace=2></a>
		</td>
		<td>
			<div style="padding-top:5px;"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_step#{i}[]', 'step#{i}X');" align="absmiddle" /> <font class="extext">※주의: 상품선택 후 반드시 하단 저장버튼을 누르셔야 최종 저장이 됩니다.</font></div>
			<div style="position:relative;z-index:1000;">
				<div id=step#{i}X style="padding-top:3px"></div>
			</div>
		</td>
	</tr>
	</table>
	</div>
	</textarea>
<!-- 진열 타입 소스 -->

<form id=form action="indb.php" method=post onsubmit="return disp_main_save();" target="ifrmHidden">
<input type=hidden name=mode value="disp_main">
<input type="hidden" name="tplSkinWork" value="<?=$cfg['tplSkinWork']?>">

<?
for ($i=0;$i<5;$i++){
	$checked[tpl][$i][$cfg_step[$i][tpl]] = "checked";
	$checked[sort_type][$i][$cfg_step[$i][sort_type]] = "checked";
	$selected[stock_type][$i][$cfg_step[$i][stock_type]] = "checked";
	$selected[img][$i][$cfg_step[$i][img]] = "selected";
	$selected[select_date][$i][$cfg_step[$i][select_date]] = "selected";
	$selected[regdt][$i][$cfg_step[$i][regdt]] = "selected";
?>
<div class=title <?if(!$i){?>style="margin-top:0"<?}?>>메인페이지 상품진열 <?=$i+1?> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a> <span><a href="../../main/index.php" target=_blank><font color=0074BA>[메인화면보기]</font></a></span></div>
<?=$strMainGoodsTitle?>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>설명</td>
	<td><input type=text name=title[] value="<?=$cfg_step[$i][title]?>" class=lline>
	<div style="padding-top:4px"><font class=extext>관련 타이틀이미지는 <a href="../design/codi.php" target=_blank><font class=extext_l>[디자인관리 > 메인페이지디자인 > 본문영역]</font></a> 에서 작성하세요</font></div></td>
</tr>
<tr>
	<td>사용유무</td>
	<td class=noline>
	<input type=checkbox name=chk[<?=$i?>] <? if ($cfg_step[$i][chk]){ ?>checked<?}?>> 체크시 메인페이지에 출력이됩니다
	</td>
</tr>
<tr>
	<td>디스플레이유형</td>
	<td>
	<? for ($t=1,$m=sizeof($ar_display_type);$t<=$m;$t++) { ?>
	<div class="display-type-wrap">
		<img src="../img/goodalign_style_<?=sprintf('%02d',$t)?>.gif"  alt="<?=$ar_display_type[$t]?>">
		<div class="noline">
		<input type=radio name=tpl[<?=$i?>] value="tpl_<?=sprintf('%02d',$t)?>" <?=$checked[tpl][$i]['tpl_'.sprintf('%02d',$t)]?>  onclick="fnSetExtraOption(<?=$i?>,'<?=$ar_display_type[$t]?>')">
		</div>
	</div>
	<? } ?>
	</td>
</tr>
<tr id="extra-config-display-type-<?=$i?>" style="display:none;">
	<td>세부 설정</td>
	<td id="extra-config-wrap-display-type-<?=$i?>"></td>
</tr>
<tr>
	<td>이미지 선택</td>
	<td>
	<select name=img[]>
	<option value="img_s" <?=$selected[img][$i][img_s]?>> 리스트이미지 (<?=$cfg[img_s]?>px)
	<option value="img_i" <?=$selected[img][$i][img_i]?>> 메인이미지 (<?=$cfg[img_i]?>px)
	</select>
	<font class=extext>적당한 사이즈의 이미지를 고르세요
	</td>
</tr>
<tr>
	<td>메인출력 상품수</td>
	<td><input type=text name=page_num[] value="<?=$cfg_step[$i][page_num]?>" class="rline"> 개 <font class=extext>메인페이지에 보여지는 총 상품수입니다</td>
</tr>
<tr>
	<td>라인당 상품수</td>
	<td><input type=text name=cols[] value="<?=$cfg_step[$i][cols]?>" class="rline"> 개 <font class=extext>한줄에 보여지는 상품수입니다</td>
</tr>
<tr id="sList_<?=$i?>">
	<td>진열 상품선정 기준</td>
	<td>
	<? foreach ($ar_sort_type as $key => $value){?>
		<label><input type="radio" name="sort_type[<?=$i?>]" value="<?=$key?>" <?=$checked[sort_type][$i][$key]?> onclick="sort_type_view('<?=$i?>')"><?=$value?></label>
	<? } ?><br />
	<font class=extext>* 디스플레이유형을 ‘탭 진열형‘으로 선택한 경우 직접진열 기준만 사용할 수 있습니다.<br />* 인기순 및 상품평가순 으로 진열 시 상품은 최대 <?=$mainAutoSort_sortLimit?>개 까지만 진열이 됩니다.</font>
	</td>
</tr>
<tr class="hList_<?php echo $i; ?>">
	<td>진열 대상<br />해시태그 설정</td>
	<td>
		<div class="hashtagInputText">#<input type="text" name="hashtagName[<?php echo $i; ?>]" value="<?php echo $cfg_step[$i]['hashtagName']; ?>" class="hashtagInputListSearch" maxlength="20" /></div>
	</td>
</tr>
<tr class="mList_<?=$i?>">
	<td>진열 상품선정 기간</td>
	<td>
	최근 <select name="select_date[<?=$i?>]">
	<option value="7" <?=$selected[select_date][$i][7]?>>7일</option>
	<option value="15" <?=$selected[select_date][$i][15]?>>15일</option>
	</select>
	</td>
</tr>
<tr class="mList_<?=$i?>">
	<td>진열 대상 카테고리</td>
	<td>
		<script>new categoryBox('step_categoods[<?=$i?>][]',4,'','');</script>
		<a href="javascript:addCate('categoods','<?=$i?>');"><img src="../img/i_add.gif" align="absmiddle" /></a>
		<div id="add_categoods_area_<?=$i?>">
		<? if (is_array($cfg_step[$i]['categoods']) && count($cfg_step[$i]['categoods'])>0){ foreach($cfg_step[$i]['categoods'] as $key => $value){ ?>
		<div id="add_categoods_<?=$i."_".$value?>">
			<div class="add_categoods_box"><?=strip_tags(currPosition($value))?> &nbsp; <a href="javascript:delCate('<?=$i?>','<?=$value?>');"><img src="../img/i_del.gif" align=absmiddle /></a></div>
			<input type="hidden" name="categoods[<?=$i?>][]" value="<?=$value?>">
		</div>
		<? }} ?>
		</div>
	</td>
</tr>
<tr class="mList_<?=$i?>" style="display:none;">
	<td>진열 대상 추가조건</td>
	<td>
		<a onclick="more_terms('<?=$i?>')" class="hand"><img src="../img/disp_btn_<?=array_filter($cfg_step[$i]['price']) || array_filter($cfg_step[$i]['stock_amount']) || $cfg_step[$i]['regdt'] ? "close" :"open"?>.gif" id="more_terms_btn_<?=$i?>" /></a><br />
		<table id="more_terms_<?=$i?>" class=tb style="display:<?=array_filter($cfg_step[$i]['price']) || array_filter($cfg_step[$i]['stock_amount']) || $cfg_step[$i]['regdt'] ? "" :"none"?>;">
		<col class=cellC><col class=cellL>
		<tr>
			<td>진열 상품가격</td>
			<td>
				<input type=text name="price[<?=$i?>][]" value="<?=$cfg_step[$i]['price'][0]?>" onkeydown="onlynumber()" size="15" class="ar"> 원 -
				<input type=text name="price[<?=$i?>][]" value="<?=$cfg_step[$i]['price'][1]?>" onkeydown="onlynumber()" size="15" class="ar"> 원
			</td>
		</tr>
		<tr>
			<td>진열 상품재고수량</td>
			<td>
				<label><input name="stock_type[<?=$i?>]" value="product" type="radio" <?=$selected[stock_type][$i]['product']?> />상품재고(품목재고 합)</label>
				<label><input name="stock_type[<?=$i?>]" value="item" type="radio" <?=$selected[stock_type][$i]['item']?> />품목재고</label>
				<div>
					<input type=text name="stock_amount[<?=$i?>][]" value="<?=$cfg_step[$i]['stock_amount'][0]?>" onkeydown="onlynumber()" size="15" class="ar"> 개 -
					<input type=text name="stock_amount[<?=$i?>][]" value="<?=$cfg_step[$i]['stock_amount'][1]?>" onkeydown="onlynumber()" size="15" class="ar"> 개
				</div>

				<p class="help">
					<font color="blue">상품재고:</font> 상품내 품목(가격옵션)별 재고 총합의 조건을 말합니다. 주문시 재고차감(재고량연동)인 상품만 진열대상이 됩니다. <br/>
					<font color="blue">품목재고:</font> 품목(가격옵션) 개별 재고 조건을 말합니다. 주문시 재고차감(재고량연동)인 상품만 진열대상이 됩니다.
				</p>
			</td>
		</tr>
		<tr>
			<td>진열 상품등록일</td>
			<td>
				최근 <select name="regdt[<?=$i?>]">
					<option value="" <?=$selected[regdt][$i]['']?>>전체</option>
					<option value="7" <?=$selected[regdt][$i][7]?>>7일</option>
					<option value="15" <?=$selected[regdt][$i][15]?>>15일</option>
					<option value="30" <?=$selected[regdt][$i][30]?>>30일</option>
					<option value="60" <?=$selected[regdt][$i][60]?>>60일</option>
				</select>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr class="mList_<?=$i?> hList_<?php echo $i; ?>">
	<td>진열 상품</td>
	<td>
		<span class="extext">* 관리자 로그인 시 설정 기준으로 진열될 상품이 변경됩니다. (미진열 상품은 제외됩니다.) </span><br />
		<? if ($auto_loop[$i]){ foreach ($auto_loop[$i] as $v){ ?>
		<a href="../../goods/goods_view.php?goodsno=<?=$v[goodsno]?>" target=_blank><?=goodsimg($v[img_s], '40,40', '', 1)?></a>
		<? }} else {?>
		<? if ($cfg_step[$i]['sort_type'] && $cfg_step[$i]['sort_type'] != 1) {?><span style="color:#ff0000;">※ 진열 상품선정 기준에 맞는 상품이 없습니다. 직접진열을 선택하시어 진열될 상품을 직접 선택해주세요.</span><br /><? } ?>
		<span style="color:#ff0000;">※ 조건설정 후 반드시 하단 [저장]버튼을 누르셔야 상품이 진열됩니다.</span>
		<? } ?>
	</td>
</tr>
<tr id="gList_<?=$i?>">
	<td>진열할 상품선정<div style="padding-top:3px"></div>
	<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_display.html',850,523)"><font class=extext_l>[상품순서변경 방법]</font></a>
	<div style="padding-top:3px"></div>
	<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_display.html',850,523)"><img src="../img/icon_sample.gif" border="0" align=absmiddle hspace=2></a>
	</td>
	<td>
		<div style="padding-top:5px;"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_step<?=$i?>[]', 'step<?=$i?>X');" align="absmiddle" /> <font class="extext">※주의: 상품선택 후 반드시 하단 저장버튼을 누르셔야 최종 저장이 됩니다.</font></div>
		<div style="position:relative;z-index:1000;">
			<div id=step<?=$i?>X style="padding-top:3px">
			<? if ($loop[$i]){ foreach ($loop[$i] as $v){ ?>
			<input type=hidden name=e_step<?=$i?>[] value="<?=$v[goodsno]?>">
			<a href="../../goods/goods_view.php?goodsno=<?=$v[goodsno]?>" target=_blank><?=goodsimg($v[img_s], '40,40', '', 1)?></a>
			<? }} ?>
			</div>
		</div>
	</td>
</tr>
</table>
<? $t = (int)(array_pop(explode('_',$cfg_step[$i][tpl]))); ?>
<script language="JavaScript">fnSetExtraOption(<?=$i?>,'<?=$ar_display_type[$t]?>')</script>
<? } ?>

<!-- 노출 영역 추가 확장 -->
<div id="extra-display-form-wrap">
	<?
	$_cfg_step = $cfg_step;
	unset($_cfg_step[0], $_cfg_step[1], $_cfg_step[2], $_cfg_step[3], $_cfg_step[4]);

	foreach($_cfg_step as $i => $_foo) {

		$checked[tpl][$i][$cfg_step[$i][tpl]] = "checked";
		$checked[sort_type][$i][$cfg_step[$i][sort_type]] = "checked";
		$selected[stock_type][$i][$cfg_step[$i][stock_type]] = "checked";
		$selected[img][$i][$cfg_step[$i][img]] = "selected";
		$selected[select_date][$i][$cfg_step[$i][select_date]] = "selected";
		$selected[regdt][$i][$cfg_step[$i][regdt]] = "selected";?>

	<div id="extra-display-form-<?=$i?>">
		<div class=title>메인페이지 상품진열 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a> <span><a href="../../main/index.php" target=_blank><font color=0074BA>[메인화면보기]</font></a></span></div>
		<?=$strMainGoodsTitle?>
		<table class=tb>
		<col class=cellC><col class=cellL>
		<tr>
			<td>설명</td>
			<td><input type=text name=title[<?=$i?>] value="<?=$cfg_step[$i][title]?>" class=lline>
			<div style="padding-top:4px"><font class=extext>관련 타이틀이미지는 <a href="../design/codi.php" target=_blank><font class=extext_l>[디자인관리 > 메인페이지디자인 > 본문영역]</font></a> 에서 작성하세요</font></div></td>
		</tr>
		<tr>
			<td>사용유무 <a href="javascript:void(0);" onClick="fnDelDisplayForm(<?=$i?>);"><img src="../img/i_del.gif" align="absmiddle"></a></td>
			<td class=noline>
			<input type=checkbox name=chk[<?=$i?>] <? if ($cfg_step[$i][chk]){ ?>checked<?}?>> 체크시 메인페이지에 출력이됩니다
			</td>
		</tr>
		<tr>

			<td>템플릿 소스코드<div style="padding-top:3px"></div><a href="javascript:void(0);" onClick="fnCopyDisplayFormSrc('<?=$i?>')"><img src="../img/btn_codecopy.gif" align="absmiddle"></a></td>
			<td>
				<xmp class="extra-display-form-tplsrc" id="extra-display-form-tplsrc-<?=$i?>">{ ? _cfg_step[<?=$i?>].chk }
				{ = this->assign( 'loop', dataDisplayGoods( <?=$i?>, _cfg_step[<?=$i?>].img, _cfg_step[<?=$i?>].page_num ) ) }
				{ = this->assign( 'dpCfg', _cfg_step[<?=$i?>] ) }
				{ = this->assign( 'id', 'main_list_<?=$i?>' ) }
				{ = this->assign( 'cols', _cfg_step[<?=$i?>].cols ) }
				{ = this->assign( 'size', _cfg[_cfg_step[<?=$i?>].img] ) }
				{ = include_file( 'goods/list/' + _cfg_step[<?=$i?>].tpl + '.htm' ) }
				{ / }</xmp>
			</td>
		</tr>
		<tr>
			<td>디스플레이유형</td>
			<td>

			<? for ($t=1,$m=sizeof($ar_display_type);$t<=$m;$t++) { ?>
			<div class="display-type-wrap">
				<img src="../img/goodalign_style_<?=sprintf('%02d',$t)?>.gif"  alt="<?=$ar_display_type[$t]?>">
				<div class="noline">
				<input type=radio name=tpl[<?=$i?>] value="tpl_<?=sprintf('%02d',$t)?>" <?=$checked[tpl][$i]['tpl_'.sprintf('%02d',$t)]?>  onclick="fnSetExtraOption(<?=$i?>,'<?=$ar_display_type[$t]?>')">
				</div>
			</div>
			<? } ?>

			</td>
		</tr>
		<tr id="extra-config-display-type-<?=$i?>" style="display:none;">
			<td>세부 설정</td>
			<td id="extra-config-wrap-display-type-<?=$i?>"></td>
		</tr>
		<tr>
			<td>이미지 선택</td>
			<td>
			<select name=img[<?=$i?>]>
			<option value="img_s" <?=$selected[img][$i][img_s]?>> 리스트이미지 (<?=$cfg[img_s]?>px)
			<option value="img_i" <?=$selected[img][$i][img_i]?>> 메인이미지 (<?=$cfg[img_i]?>px)
			</select>
			<font class=extext>적당한 사이즈의 이미지를 고르세요
			</td>
		</tr>
		<tr>
			<td>메인출력 상품수</td>
			<td><input type=text name=page_num[<?=$i?>] value="<?=$cfg_step[$i][page_num]?>" class="rline"> 개 <font class=extext>메인페이지에 보여지는 총 상품수입니다</td>
		</tr>
		<tr>
			<td>라인당 상품수</td>
			<td><input type=text name=cols[<?=$i?>] value="<?=$cfg_step[$i][cols]?>" class="rline"> 개 <font class=extext>한줄에 보여지는 상품수입니다</td>
		</tr>
		<tr id="sList_<?=$i?>">
			<td>진열 상품선정 기준</td>
			<td>
			<? foreach ($ar_sort_type as $key => $value){?>
				<label><input type="radio" name="sort_type[<?=$i?>]" value="<?=$key?>" <?=$checked[sort_type][$i][$key]?> onclick="sort_type_view('<?=$i?>')"><?=$value?></label>
			<? } ?><br />
			<font class=extext>* 디스플레이유형을 ‘탭 진열형‘으로 선택한 경우 직접진열 기준만 사용할 수 있습니다.<br />* 인기순 및 상품평가순 으로 진열 시 상품은 최대 <?=$mainAutoSort_sortLimit?>개 까지만 진열이 됩니다.</font>
			</td>
		</tr>
		<tr class="hList_<?php echo $i; ?>">
			<td>진열 대상<br />해시태그 설정</td>
			<td>
				<div class="hashtagInputText">#<input type="text" name="hashtagName[<?php echo $i; ?>]" value="<?php echo $cfg_step[$i]['hashtagName']; ?>" class="hashtagInputListSearch" maxlength="20" /></div>
			</td>
		</tr>
		<tr class="mList_<?=$i?>">
			<td>진열 상품선정 기간</td>
			<td>
			최근 <select name="select_date[<?=$i?>]">
			<option value="7" <?=$selected[select_date][$i][7]?>>7일</option>
			<option value="15" <?=$selected[select_date][$i][15]?>>15일</option>
			</select>
			</td>
		</tr>
		<tr class="mList_<?=$i?>">
			<td>진열 대상 카테고리</td>
			<td>
				<script>new categoryBox('step_categoods[<?=$i?>][]',4,'','');</script>
				<a href="javascript:addCate('categoods','<?=$i?>');"><img src="../img/i_add.gif" align="absmiddle" /></a>
				<div id="add_categoods_area_<?=$i?>">
				<? if (is_array($cfg_step[$i]['categoods']) && count($cfg_step[$i]['categoods'])>0){ foreach($cfg_step[$i]['categoods'] as $key => $value){ ?>
				<div id="add_categoods_<?=$i."_".$value?>">
					<div class="add_categoods_box"><?=strip_tags(currPosition($value))?> &nbsp; <a href="javascript:delCate('<?=$i?>','<?=$value?>');"><img src="../img/i_del.gif" align=absmiddle /></a></div>
					<input type="hidden" name="categoods[<?=$i?>][]" value="<?=$value?>">
				</div>
				<? }} ?>
			</td>
		</tr>
		<tr class="mList_<?=$i?> hList_<?php echo $i; ?>" style="display:none;">
			<td>진열 대상 추가조건</td>
			<td>
				<a onclick="more_terms('<?=$i?>')" class="hand"><img src="../img/disp_btn_<?=array_filter($cfg_step[$i]['price']) || array_filter($cfg_step[$i]['stock_amount']) || $cfg_step[$i]['regdt'] ? "close" :"open"?>.gif" id="more_terms_btn_<?=$i?>" /></a><br />
				<table id="more_terms_<?=$i?>" class=tb style="display:<?=array_filter($cfg_step[$i]['price']) || array_filter($cfg_step[$i]['stock_amount']) || $cfg_step[$i]['regdt'] ? "" :"none"?>;">
				<col class=cellC><col class=cellL>
				<tr>
					<td>진열 상품가격</td>
					<td>
						<input type=text name="price[<?=$i?>][]" onkeydown="onlynumber()" size="15" value="<?php echo $cfg_step[$i]['price'][0]; ?>" class="ar"> 원 -
						<input type=text name="price[<?=$i?>][]" onkeydown="onlynumber()" size="15" value="<?php echo $cfg_step[$i]['price'][1]; ?>" class="ar"> 원
					</td>
				</tr>
				<tr>
					<td>진열 상품재고수량</td>
					<td>
						<label><input name="stock_type[<?=$i?>]" value="product" type="radio" checked />상품재고(품목재고 합)</label>
						<label><input name="stock_type[<?=$i?>]" value="item" type="radio"  />품목재고</label>
						<div>
							<input type=text name="stock_amount[<?=$i?>][]" onkeydown="onlynumber()" size="15" value="<?php echo $cfg_step[$i]['stock_amount'][0]; ?>" class="ar"> 개 -
							<input type=text name="stock_amount[<?=$i?>][]" onkeydown="onlynumber()" size="15" value="<?php echo $cfg_step[$i]['stock_amount'][1]; ?>" class="ar"> 개
						</div>

						<p class="help">
							<font color="blue">상품재고:</font> 상품내 품목(가격옵션)별 재고 총합의 조건을 말합니다. 주문시 재고차감(재고량연동)인 상품만 진열대상이 됩니다. <br/>
							<font color="blue">품목재고:</font> 품목(가격옵션) 개별 재고 조건을 말합니다. 주문시 재고차감(재고량연동)인 상품만 진열대상이 됩니다.
						</p>
					</td>
				</tr>
				<tr>
					<td>진열 상품등록일</td>
					<td>
						최근 <select name="regdt[<?=$i?>]">
							<option value="" <?=$selected[regdt][$i]['']?>>전체</option>
							<option value="7" <?=$selected[regdt][$i][7]?>>7일</option>
							<option value="15" <?=$selected[regdt][$i][15]?>>15일</option>
							<option value="30" <?=$selected[regdt][$i][30]?>>30일</option>
							<option value="60" <?=$selected[regdt][$i][60]?>>60일</option>
						</select>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr class="mList_<?=$i?> hList_<?php echo $i; ?>">
			<td>진열 상품</td>
			<td>
				<span class="extext">* 관리자 로그인 시 설정 기준으로 진열될 상품이 변경됩니다. (미진열 상품은 제외됩니다.) </span><br />
				<? if ($auto_loop[$i]){ foreach ($auto_loop[$i] as $v){ ?>
				<a href="../../goods/goods_view.php?goodsno=<?=$v[goodsno]?>" target=_blank><?=goodsimg($v[img_s], '40,40', '', 1)?></a>
				<? }} else { ?>
				<? if ($cfg_step[$i]['sort_type'] && $cfg_step[$i]['sort_type'] != 1) {?><span style="color:#ff0000;">※ 진열 상품선정 기준에 맞는 상품이 없습니다. 직접진열을 선택하시어 진열될 상품을 직접 선택해주세요.</span><br /><? } ?>
				<span style="color:#ff0000;">※ 조건설정 후 반드시 하단 [저장]버튼을 누르셔야 상품이 진열됩니다.</span>
				<? } ?>
			</td>
		</tr>
		<tr id="gList_<?=$i?>">
			<td>진열할 상품선정<div style="padding-top:3px"></div>
			<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_display.html',850,523)"><font class=extext_l>[상품순서변경 방법]</font></a>
			<div style="padding-top:3px"></div>
			<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_display.html',850,523)"><img src="../img/icon_sample.gif" border="0" align=absmiddle hspace=2></a>
			</td>
			<td>
				<div style="padding-top:5px;"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_step<?=$i?>[]', 'step<?=$i?>X');" align="absmiddle" /> <font class="extext">※주의: 상품선택 후 반드시 하단 저장버튼을 누르셔야 최종 저장이 됩니다.</font></div>
				<div style="position:relative;z-index:1000;">
					<div id=step<?=$i?>X style="padding-top:3px">
					<? if ($loop[$i]){ foreach ($loop[$i] as $v){ ?>
					<input type=hidden name=e_step<?=$i?>[] value="<?=$v[goodsno]?>">
					<a href="../../goods/goods_view.php?goodsno=<?=$v[goodsno]?>" target=_blank><?=goodsimg($v[img_s], '40,40', '', 1)?></a>
					<? }} ?>
					</div>
				</div>
			</td>
		</tr>
		</table>
	</div>
	<? $t = (int)(array_pop(explode('_',$cfg_step[$i][tpl]))); ?>
	<script language="JavaScript">fnSetExtraOption(<?=$i?>,'<?=$ar_display_type[$t]?>')</script>
	<? } ?>
</div>
<div class=button>
	<input type=image src="../img/btn_save.gif">
	<a href="list.php"><img src='../img/btn_list.gif'></a>
</div>
</form>

<a href="javascript:void(0);" onClick="fnAddDisplayForm();"><img src="../img/btn_goodsadd.gif"></a>


<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>상품선택하기 버튼을 눌러 진열될 상품을 선택해주세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>펼쳐진 창을 닫고 아래의 저장버튼을 누르셔야 최종 저장됩니다. </td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>진열할 수 있는 상품의 최대개수는 300개 입니다. </td></tr>
</table>

</div>
<script>cssRound('MSG01')</script>
<script type="text/javascript">
function addCate(name,idx) {
	var cate = document.getElementsByName("step_"+name+"["+idx+"][]");
	var cate_nm = "";
	var cate_val = "";

	for(var i =0; i< cate.length; i++) {

		if(cate[i].value != "") {
			cate_val = cate[i].value;

			if(i == 0) {
				cate_nm = cate[i].options[cate[i].selectedIndex].text;
			}
			else {
				cate_nm += " > " + cate[i].options[cate[i].selectedIndex].text;
			}
		}
	}

	if(cate_val == "") {
		alert("카테고리를 선택해 주세요.");
		cate[0].focus();
		return;
	}

	var cate_hidden = document.getElementsByName(name+'['+idx+'][]');

	for(var j=0; j< cate_hidden.length; j++) {

		if(cate_hidden[j].value == cate_val) {
			alert("이미 추가된 카테고리 입니다.");
			return;
		}
	}
	
	var html_str = '<div id="add_categoods_'+idx+'_'+cate_val+'">';
	html_str += '<div class="add_categoods_box">'+cate_nm+' &nbsp; <a href="javascript:delCate(\''+idx+'\',\''+cate_val+'\');"><img src="../img/i_del.gif" align=absmiddle /></a></div>';
	html_str += '<input type="hidden" name="categoods['+idx+'][]" value="'+cate_val+'">';
	html_str += '</div>';

	new Insertion.Bottom('add_categoods_area_'+idx,html_str);
}

function delCate(idx,value){
	$('add_categoods_'+idx+'_'+value).remove();
}

function more_terms(num){
	if ($('more_terms_'+num).style.display == 'none') {
		$('more_terms_'+num).style.display = '';
		$('more_terms_btn_'+num).writeAttribute('src','../img/disp_btn_close.gif');
	} else {
		$('more_terms_'+num).style.display = 'none';
		$('more_terms_btn_'+num).writeAttribute('src','../img/disp_btn_open.gif');
	}
}
</script>

<script type="text/javascript">
jQuery(document).ready(HashtagInputListController);
</script>


<? include "../_footer.php"; ?>