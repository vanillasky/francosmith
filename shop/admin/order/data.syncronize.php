<?php
$location = "�ֹ����� > �����͵���ȭ";
include "../_header.php";

$integrate_order = Core::loader('integrate_order');

$result = $integrate_order->checkManualSync();

if ($result === false) {
	// ���� �ֹ����� ���ų�, �⺻ ���� �Ǽ� �̸��� ���
	// ���� ����ȭ�� ��ģ ���
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

                        $('el-button-firstrun-' + step).update('����ȭ �Ϸ�');
                        $('el-result-firstrun-' + step).update('�Ϸ�');
                        $('el-button-firstrun-' + step).removeClassName('el-button-firstrun');

                        $$('.el-button-firstrun').each(function(el){
							if (el.hasClassName('default-btn-off')) el.removeClassName('default-btn-off');
							el.addClassName('default-btn');
                            el.writeAttribute('disabled', false);
                        });
                    }
                    else
                        if (res == 'complete') {
                            alert("�ֹ� ������ ����ȭ�� ��� �Ϸ�Ǿ����ϴ�.\n\n���Ŀ� �߰��Ǵ� �ֹ������ʹ� �ڵ����� ����ȭ�Ǿ� ����˴ϴ�.");
                            top.window.location.replace('./list.integrate.php');
                        }

                }
            },
            onCreate: function(){

                Event.observe(window, 'beforeunload', function(e){
                    e.returnValue = '������ ����ȭ��, ������ �̵��� �Ͻø� �ȵ˴ϴ�.';
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

<div class="title title_top" style="position:relative;padding-bottom:15px">������ ����ȭ</div>

<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse; margin-bottom:10px" width="750">
<tr><td style="padding:7 0 10 10;line-height:150%;">

<span style="font-weight:bold;color:red;padding:3px;mar">�� �߿�!!</span>
<br>
�ֹ����հ��� ������ ���� �� �Ǹ� ä�� �� ������ ����ȭ �۾��� �ݵ�� �ʿ��մϴ�.
<br>
������ ����ȭ ���� �߿��� ��� �� ������ �̵��� �Ұ��� �մϴ�. �ٸ� �۾��� ��� �ߴ��Ͽ� �ּ���.
<br>
<span style="color:red;">1ȸ ������ ����ȭ ���� 10,000 �� ���� ���� �Ǹ�, ����ȭ �۾� �ð��� ��� �̻� �ҿ� �� ���� �ֽ��ϴ�.</span>
<br>
<span style="color:red;">���� �����Ͽ� �ֽð�, �ֹ����հ��� �̿뿩�θ� �Ǵ��Ͽ� �����Ͽ� �ּ���~</span>
<br>
���� �ֹ� ������ ����ȭ �Ϸ� ���Ŀ� �߰� �߻��Ǵ� �ֹ������ʹ� ������ ����ȭ �۾��� �ʿ� ���� �ڵ����� ����ȭ�Ǿ� ����˴ϴ�.
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
        <th>����</th>
        <th>�Ⱓ</th>
        <th>����ȭ</th>
        <th>���</th>
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
            <button type="button" disabled class="default-btn-off">����ȭ �Ϸ�</button>
            <? } else { ?>
            <button type="button" onClick="fnFirstRun(<?=$i?>)" id="el-button-firstrun-<?=$i?>" class="el-button-firstrun default-btn">����ȭ ����</button>
            <? } ?>
        </td>
        <td width=60><span class=small4 id="el-result-firstrun-<?=$i?>"><?= $row['result'] ? '�Ϸ�' : '-'?></span></td>
    </tr>
    <tr>
        <td colspan=20 bgcolor=E4E4E4></td>
    </tr>
    <? } ?>
</table>
<? include "../_footer.php"; ?>
