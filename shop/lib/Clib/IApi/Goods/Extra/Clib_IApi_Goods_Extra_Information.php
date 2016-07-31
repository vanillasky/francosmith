<?php
/**
 * Clib_IApi_Goods_Extra_Information
 * @author extacy @ godosoft development team.
 */
class Clib_IApi_Goods_Extra_Information
{

	public function generateJson($param)
	{
		if ($param['title'] && $param['descript']) {

			$extra_info = array();
			$keys = array_keys($param['descript']); // 내용 기준
			$key = 0;

			for ($i = min($keys), $m = max($keys); $i <= $m; $i += 2) {

				if (isset($param['title'][$i]) && isset($param['descript'][$i])) {

					for ($j = $i, $k = $i + 1; $j <= $k; $j++) {

						$key++;

						if (isset($param['title'][$j]) && isset($param['descript'][$j])) {

							$param['title'][$j] = addslashes($param['title'][$j]);
							$param['descript'][$j] = addslashes($param['descript'][$j]);
							$param['inerparkCode'][$j] = addslashes($param['inerparkCode'][$j]);
							$param['interparkType'][$j] = addslashes($param['interparkType'][$j]);

							$extra_info[$key] = array(
								'title' => trim($param['title'][$j]),
								'desc' => trim($param['descript'][$j]),
								'inpk_code' => trim($param['inerparkCode'][$j]),
								'inpk_type' => trim($param['interparkType'][$j])
							);

						}

					}

				}

			}

			return gd_json_encode($extra_info);
		}

		return '';
	}

}
