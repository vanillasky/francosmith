<?php
class extrainfo {

    private $json = null;

    public function __construct() {

        if (!class_exists('Services_JSON', false))
            include_once (dirname(__FILE__) . '/json.class.php');

        $this->json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);

    }

    public function toStr($json) {

        $tmp = (array)$this->json->decode($json);
        $ret = array();

        foreach ($tmp as $k => $v) {

            // = 구분자로 title, desc 분리
            $exp = sprintf('%d:%s|%s', $k, stripslashes($v['title']), stripslashes($v['desc']));
            $ret[] = sprintf('{%s}', $exp);

        }

        return implode(',', $ret);

    }

    public function toJson($str) {

        $tmp = explode(',', $str);
        $ret = array();

        $found = true;

        for ($i = 0, $m = sizeof($tmp); $i < $m; $i++) {

            if ($found)
                $_str = $tmp[$i];
            else
                $_str .= ',' . $tmp[$i];

            if (preg_match('/^\{([0-9]+):(.+)\}$/s', $_str, $matches)) {

                $_no   = $matches[1];
                $_pair = explode('|', $matches[2]);

                $_pair[0] = addslashes($_pair[0]);
                $_pair[1] = addslashes($_pair[1]);

                $ret[$_no]['title'] = $_pair[0];
                $ret[$_no]['desc']  = $_pair[1];

                $found = true;

            } else {
                $found = false;
            }

        }

		if (!empty($ret)) {
			ksort($ret);
		}

        return $this->json->encode($ret);

    }

}
?>
