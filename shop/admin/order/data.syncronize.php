<?php
$location = "주문관리 > 데이터동기화";
include "../_header.php";

$integrate_order = Core::loader('integrate_order');

$result = $integrate_order->checkManualSync();

if ($result === false) {
	// 기존 주문건이 없거나, 기본 분할 건수 미만인 경우
	// 수동 동기화를 마친 경우
	$integrate_order->doManualSyncComplete();
	go('./list.integrate.php');
	exit;
}

$status = $integrate_order->getManualSyncStatus();
?>
<script>
    function fnFirstRun(step){

        var ajax = new Ajax.Request('./indb.data.syncronize.php', {
            method: "post",
            parameters: 'step=' + step,
            asynchronous: true,
            onComplete: function(response){
                if (response.status == 200) {

                    Event.stopObserving(window, 'beforeunload');

					var res = response.responseText.trim();

                    if (res == 'ok') {

                        $('el-button-firstrun-' + step).update('동기화 완료');
                        $('el-result-firstrun-' + step).update('완료');
                        $('el-button-firstrun-' + step).removeClassName('el-button-firstrun');

                        $$('.el-button-firstrun').each(function(el){
							if (el.hasClassName('default-btn-off')) el.removeClassName('default-btn-off');
							el.addClassName('default-btn');
                            el.writeAttribute('disabled', false);
                        });
                    }
                    else
                        if (res == 'complete') {
                            alert("주문 데이터 동기화가 모두 완료되었습니다.\n\n이후에 추가되는 주문데이터는 자동으로 동기화되어 저장됩니다.");
                            top.window.location.replace('./list.integrate.php');
                        }

                }
            },
            onCreate: function(){

                Event.observe(window, 'beforeunload', function(e){
                    e.returnValue = '데이터 동기화중, 페이지 이동을 하시면 안됩니다.';
                });

                $('el-button-firstrun-' + step).update('<img src="../img/icon_loading.gif">');

                $$('.el-button-firstrun').each(function(el){
					if (el.hasClassName('default-btn')) el.removeClassName('default-btn');
					el.addClassName('default-btn-off');
					el.writeAttribute('disabled', true);
                });
            }
        });

    }
</script>

<div class="title title_top" style="position:relative;padding-bottom:15px">데이터 동기화</div>

<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse; margin-bottom:10px" width="750">
<tr><td style="padding:7 0 10 10;line-height:150%;">

<span style="font-weight:bold;color:red;padding:3px;mar">※ 중요!!</span>
<br>
주문통합관리 페이지 접근 전 판매 채널 별 데이터 동기화 작업이 반드시 필요합니다.
<br>
데이터 동기화 진행 중에는 취소 및 페이지 이동이 불가능 합니다. 다른 작업은 잠시 중단하여 주세요.
<br>
<span style="color:red;">1회 데이터 동기화 양은 10,000 건 으로 제한 되며, 동기화 작업 시간이 몇분 이상 소요 될 수도 있습니다.</span>
<br>
<span style="color:red;">이점 유의하여 주시고, 주문통합관리 이용여부를 판단하여 진행하여 주세요~</span>
<br>
기존 주문 데이터 동기화 완료 이후에 추가 발생되는 주문데이터는 별도의 동기화 작업이 필요 없이 자동으로 동기화되어 저장됩니다.
</td></tr>
</table>


<div style="margin-top:15px;"></div>


<form name="frmFirstRun" method="post" action="./indb.data.syncronize.php" target="_fself">
    <input type="hidden" name="mode" value="firstrun"><input type="hidden" name="step" value="">
</form>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td class="rnd" colspan="20">
        </td>
    </tr>
    <tr class="rndbg">
        <th>순서</th>
        <th>기간</th>
        <th>동기화</th>
        <th>결과</th>
    </tr>
    <tr>
        <td class="rnd" colspan="20">
        </td>
    </tr>
    <?
    for ($i = 0, $max = sizeof($status); $i < $max; $i++) {
        $row = $status[$i];

    ?>
    <tr height=25 align=center>
        <td><font class=ver81 color=616161><?= $i + 1?></font></td>
        <td><?= $row['startdt']?>~ <?= $row['enddt']?></td>
        <td>
        	<? if ($row['result']) { ?>
            <button type="button" disabled class="default-btn-off">동기화 완료</button>
            <? } else { ?>
            <button type="button" onClick="fnFirstRun(<?=$i?>)" id="el-button-firstrun-<?=$i?>" class="el-button-firstrun default-btn">동기화 실행</button>
            <? } ?>
        </td>
        <td width=60><span class=small4 id="el-result-firstrun-<?=$i?>"><?= $row['result'] ? '완료' : '-'?></span></td>
    </tr>
    <tr>
        <td colspan=20 bgcolor=E4E4E4></td>
    </tr>
    <? } ?>
</table>
<? include "../_footer.php"; ?>
