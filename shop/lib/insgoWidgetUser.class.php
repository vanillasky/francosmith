<?php
/**
 * Copyright (c) 2015 GODO Co. Ltd
 * All right reserved.
 *
 * This software is the confidential and proprietary information of GODO Co., Ltd.
 * You shall not disclose such Confidential Information and shall use it only in accordance
 * with the terms of the license agreement  you entered into with GODO Co., Ltd
 *
 * Revision History
 * Author            Date              Description
 * ---------------   --------------    ------------------
 * workingby         2015.11.26        First Draft.
 */

/**
 * 인스타그램 위젯
 *
 * @author insgoWidgetUser.class.php workingby <bumyul2000@godo.co.kr>
 * @version 1.0
 * @date 2015-11-26
 */
class insgoWidgetUser extends insgoWidget
{
	/**
	 * get api data
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postData
	 * @return array $imageData
	 * @date 2015-11-26
	 */
	public function getInsgoWidgetData($postData)
	{
		global $cfg;

		$apiUrl = '';
		$responseCount = $requestCount = $idx = 0;
		$dataArray = $responseData = $imageData = array();
		$endWhile = false;
		$dataArray = self::getUriHash($postData['queryString']);
		$requestCount = $dataArray['widthCount'] * $dataArray['heightCount'];

		do {
			if(!$apiUrl){
				$apiUrl = $dataArray['apiUrl'];
			}

			$responseData[$idx] = parent::executeCurl($apiUrl, 'n');
			$responseCount += count($responseData[$idx]['data']);
			if(count($responseData[$idx]['data']) < 1){
				break;
			}

			//max data size - 33, because retry api communicate
			if($responseData[$idx]['pagination']['next_url'] && $requestCount > $responseCount){
				$apiUrl = self::getNextUrl($responseData[$idx]['pagination']['next_url'], $requestCount, $responseCount);
			}

			if($responseCount >= $requestCount || !$responseData[$idx]['pagination']['next_url']){
				$endWhile = true;
			}
			$idx++;
		} while ($endWhile == false);

		$idx=0;
		$imageData['displayType'] = $dataArray['displayType'];
		foreach($responseData as $array){
			foreach($array['data'] as $value){
				$imageData['thumbnails'][$idx]['image'] = $value['images'];
				$imageData['thumbnails'][$idx]['viewUrl'] = $value['link'];
				$idx++;
			}
		}
		$imageData['data'] = $dataArray;

		return $imageData;
	}

	/**
	 * next api url
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $url, inteager $requestCount, inteager $responseCount
	 * @return string $url
	 * @date 2015-11-26
	 */
	private function getNextUrl($url, $requestCount, $responseCount)
	{
		$count = 0;
		$prefixStr = 'count=';
		$parseUrl = $queryString = array();

		$parseUrl =  parse_url($url);
		parse_str($parseUrl['query'], $queryString);
		$count = (int)$requestCount-(int)$responseCount;
		$url = str_replace($prefixStr.$queryString['count'], $prefixStr.$count, $url);

		return $url;
	}

	/**
	 * 전달받은 uri 복호화
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $queryString
	 * @return array $queryString
	 * @date 2015-11-26
	 */
	private function getUriHash($queryString)
	{
		global $xxtea;

		if(!$xxtea){
			$xxtea = Core::loader('xxtea');
		}
		$xxtea->setKey($this->secretKey);

		return unserialize($xxtea->decrypt(base64_decode($queryString)));
	}
}
?>