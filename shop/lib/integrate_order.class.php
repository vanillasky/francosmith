<?
require_once (dirname(__FILE__).'/integrate_order_processor.class.php');

class integrate_order {

    var $db;
    var $now; // 현재 시간 (초=timestamp)
    var $uptdt; // 업데이트 시간 (초=timestamp)
    var $cycle; // doSync 주기 (300초)

    var $checkfile;
    var $setupfile;

    function integrate_order() {

        $this->db = &$GLOBALS['db'];
        $this->now = time();
        $this->cycle = 300; // 초(=5분)

        $this->checkfile = dirname(__FILE__).'/../data/chkIntegrateOrderSyncronized';
        $this->setupfile = dirname(__FILE__).'/../data/chkIntegrateOrderSetup';


    }

    function & getInstance() {

        static $ins = null;

        if ($ins === null)
            $ins = & new integrate_order();

        return $ins;
    }

    // 동기화가 필요한지 체크하여 리턴 (boolean)
    function _isRequireSync() {

        $_sync = false;

        if (!file_exists($this->checkfile) || !file_exists($this->setupfile)) {
            echo '
			<script type="text/javascript">
			if (confirm("주문 통합 관리를 사용하시려면, 이전 주문의 데이터를 모두 동기화 하셔야 합니다.\n\n확인을 누르시면 동기화 페이지로 이동합니다.")) {
				top.window.location.replace("./data.syncronize.php");
			}
			else {
				window.history.back();
			}
			</script>
			';
            exit;
        } else {

            $file = @file($this->checkfile);
            $mtime = (int) $file[0];

            if (($mtime + $this->cycle) <= $this->now) {

				$this->uptdt = date('Y-m-d H:i:s', $mtime);

		// 옥션iPay 주문건중 입금확인, 배송준비중, 배송중인 주문건들을 모두 싱크대상으로 변경
		$uncompletedIpayOrdno = array();
		$uncompletedIpayOrderRes = $this->db->query("SELECT ordno FROM ".GD_INTEGRATE_ORDER." WHERE channel='ipay' AND ord_status IN(1, 2, 3)");
		while ($uncompletedIpayOrderData = $this->db->fetch($uncompletedIpayOrderRes, 1)) {
			$uncompletedIpayOrdno[] = $uncompletedIpayOrderData['ordno'];
		}
		if (count($uncompletedIpayOrdno) > 0) {
			$this->db->query("UPDATE ".GD_AUCTIONIPAY." SET uptdt_=NOW() WHERE auctionpayno IN(".implode(',', $uncompletedIpayOrdno).")");
		}

                // 이미 동기화 된 데이터 中, 변경되어 다시 동기화 해야 하는 데이터 처리
                $this->db->query("UPDATE ".GD_ORDER." SET sync_ = 0 WHERE sync_ = 1 AND uptdt_ > '".$this->uptdt."'");
                $this->db->query("UPDATE ".GD_AUCTIONIPAY." SET sync_ = 0 WHERE sync_ = 1 AND uptdt_ > '".$this->uptdt."'");
                $this->db->query("UPDATE ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." SET sync_ = 0 WHERE sync_ = 1 AND uptdt_ > '".$this->uptdt."'");
				$this->db->query("UPDATE ".GD_MARKET_ORDER." SET sync_ = 0 WHERE sync_ = 1 AND uptdt_ > '".$this->uptdt."'");
                $_sync = true;
            }
        }

        return $_sync;
    }

    function checkManualSync() {

        $status = $this->getManualSyncStatus();

        if ($status === false)
            return false;
        else {

            if (is_array($status) && sizeof($status) > 0)
                foreach ($status as $step)
                    if ($step['result'] == 0)
                        return true;
        }

        return false;

    }


    function doManualSyncComplete() {

        if ($fp = @fopen($this->setupfile, 'w')) {
            fwrite($fp, '');
            fclose($fp);
            @chmod($this->setupfile, 0707);
        }

        if ($fp = @fopen($this->checkfile, 'w')) {
            fwrite($fp, $this->uptdt);
            fclose($fp);
            @chmod($this->checkfile, 0707);
        }
    }


    function getManualSyncStatus() {

        $status = &integrate_order_processor::getManualSyncStatus($this->setupfile);

        return $status;
    }

    // 최초 실행 (모든 데이터 동기화)
    function doManualSync($step) {

        $status = $this->getManualSyncStatus();

        if ($step !== false && (int) $status[$step]['result'] === 0) {

            $_sync = &integrate_order_processor::doSync($status[$step]);

            $status[$step]['result'] = (int) $_sync;

        }

        if ($fp = @fopen($this->setupfile, 'w')) {
            fwrite($fp, serialize($status));
            fclose($fp);
            @chmod($this->setupfile, 0707);
        }

        $result = $this->checkManualSync();

        if ($result === false) {

            $this->doManualSyncComplete();
            return false;
        }

        return true;

    }


    // 데이터 동기화
    function doSync($var = null) {

        if ($this->_isRequireSync() === false)
            return;

        $_sync = &integrate_order_processor::doSync($var);

        if ($_sync && ($fp = @fopen($this->checkfile, 'w'))) {
            fwrite($fp, $this->now);
            fclose($fp);
            @chmod($this->checkfile, 0707);
        }
    }

    // 동기화 예약
    function reserveSync() {

        $reserve = $this->now - ($this->cycle + 60); // 60 : network delay

        $file = @file($this->checkfile);
        $mtime = (int) $file[0];

        $tmp = $this->now - ($mtime + $this->cycle);
        if ($tmp >= 0)
            $reserve = $reserve - $tmp;

        if (($fp = @fopen($this->checkfile, 'w'))) {
            fwrite($fp, $reserve);
            fclose($fp);
            @chmod($this->checkfile, 0707);
        }

    }

    /*
     * 주문 단계별 처리
     */
    function setOrder($channel = null, $ordno = null, $status = null, $extra = null) {

        $class = &integrate_order_processor::getInstance();
        if (is_object($class))
            $class->setOrder($channel, $ordno, $status, $extra);

    }

    function getOrderStatus($status, $cs = 'n') {
        $class = &integrate_order::getInstance();
        if (is_object($class))
            return $class->_getOrderStatus($status, $cs);
    }

    function _getOrderStatus($status, $cs = 'n') {

        global $integrate_cfg; // lib.func.php 에 내용이 있음

        $mod = 0;
        // y : 클레임 처리 中, f : 클레임 처리 완료, n or '' : 클레임 상태 아님
        if (($cs == 'y' || $cs == 'f') && $status < 10) {

            switch ((int) $status) {
                case 0:
                    $status = 10;
                    break;
                case 1:
                    $status = 20;
                    break;
                case 2:
                    $status = 20;
                    break;
                case 3:
                    $status = 30;
                    break;
                case 4:
                    $status = 30;
                    break;

            }
            if ($cs == 'f')
                $status++;
        }

        return $integrate_cfg['step'][$status];

    }

    function getCSStatus($status, $channel) {
        $class = &integrate_order::getInstance();
        if (is_object($class))
            return $class->_getCSStatus($status, $channel);
    }

    function _getCSStatus($status, $channel) {

        global $integrate_cfg;

        return $channel == 'checkout' ? $status : $integrate_cfg['claim_code'][$channel][$status];

    }

}
?>
