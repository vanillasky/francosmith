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
 * 牢胶鸥弊伐 困连
 *
 * @author insgoWidgetAdmin.class.php workingby <bumyul2000@godo.co.kr>
 * @version 1.0
 * @date 2015-11-26
 */
class insgoWidgetAdmin extends insgoWidget
{
	private $iframeID;
	/**
	 * iframe 积己
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return array
	 * @date 2015-11-26
	 */
	public function getIframe($dataArray)
	{
		try {
			$this->iframeID = 'insgoWidgetIframe_' . time();
			$iframeSize = $iframeUri = $iframeHtml = '';

			//get src
			$iframeUri = self::getIframeUri($dataArray);
			if(!$iframeUri){
				throw new Exception('fail to get iframe uri');
			}

			//get iframe size
			$iframeSize = self::getiframeSize($dataArray);

			//get iframe
			$iframeHtml = self::getIframeHtml($iframeSize, $iframeUri);
			if(!$iframeHtml){
				throw new Exception('fail to get iframe html');
			}

			return array('OK', $iframeHtml);
		}
		catch(Exception $e){
			return array('ERROR', $e->getMessage());
		}
	}

	/**
	 * iframe size 积己
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $dataArray
	 * @return string $iframeSize
	 * @date 2015-11-26
	 */
	private function getiframeSize($dataArray)
	{
		$width = $height = 0;
		$iframeSize = '';
		if($dataArray['insgoWidget_thumbnailSize'] == 'auto'){
			$iframeSize = 'width: 100%;';
		}
		else {
			$width = ($dataArray['insgoWidget_thumbnailSizePx'] * $dataArray['insgoWidget_WidthCount']) + (($dataArray['insgoWidget_imageMargin'] * $dataArray['insgoWidget_WidthCount']) - $dataArray['insgoWidget_imageMargin']) + 5;
			$height = ($dataArray['insgoWidget_thumbnailSizePx'] * $dataArray['insgoWidget_HeightCount']) + (($dataArray['insgoWidget_imageMargin'] * $dataArray['insgoWidget_HeightCount']) - $dataArray['insgoWidget_imageMargin']) + 5;
			if($dataArray['insgoWidget_thumbnailBorder'] == 'y'){
				$width += $dataArray['insgoWidget_thumbnailSizePx'] * 2;
				$height += $dataArray['insgoWidget_thumbnailSizePx'] * 2;
			}
			$iframeSize = 'width: ' . $width . 'px;';
			$iframeSize .= 'height: ' . $height . 'px;';
		}

		return $iframeSize;
	}

	/**
	 * iframe src uri 积己
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $dataArray
	 * @return array $returnArray
	 * @date 2015-11-26
	 */
	private function getIframeUri($dataArray)
	{
		$returnArray = array();
		$returnArray['displayType'] = $dataArray['insgoWidget_displayType'];
		$returnArray['apiUrl'] = self::getApiUrl($dataArray);
		$returnArray['thumbnailType'] = $dataArray['insgoWidget_thumbnailSize'];
		$returnArray['thumbnailSize'] = $dataArray['insgoWidget_thumbnailSizePx'];
		$returnArray['thumbnailBorder'] = $dataArray['insgoWidget_thumbnailBorder'];
		$returnArray['thumbnailMargin'] = $dataArray['insgoWidget_imageMargin'];
		$returnArray['backgroundColor'] = $dataArray['insgoWidget_backgroundColor'];
		$returnArray['thumbnailEffect'] = $dataArray['insgoWidget_overEffect'];
		$returnArray['widthCount'] = $dataArray['insgoWidget_WidthCount'];
		$returnArray['heightCount'] = $dataArray['insgoWidget_HeightCount'];
		$returnArray['iframeID'] = $this->iframeID;

		return self::setUriHash(array_filter($returnArray));
	}

	/**
	 * uri hash
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $_returnArray
	 * @return array $returnArray
	 * @date 2015-11-26
	 */
	private function setUriHash($returnArray)
	{
		global $xxtea;

		if(!$xxtea){
			$xxtea = Core::loader('xxtea');
		}
		$xxtea->setKey($this->secretKey);

		return base64_encode($xxtea->encrypt(serialize($returnArray)));
	}

	/**
	 * iframe html
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $dataArray, string $iframeUri
	 * @return string $iframeHtml
	 * @date 2015-11-26
	 */
	private function getIframeHtml($iframeSize, $iframeUri)
	{
		global $cfg;

		$iframeHtml = '';
		$iframeHtml = '<iframe name="insgoWidgetIframe" id="'.$this->iframeID.'" src="'. $cfg['rootDir'] .parent::$insgoWidgetPath .'?'.$iframeUri.'" allowTransparency="true" frameborder="0" scrolling="no" style="border:none; overflow:hidden; '.$iframeSize.' " ></iframe>';

		return $iframeHtml;
	}

	/**
	 * API URL 府畔
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $dataArray
	 * @return string $apiUrl
	 * @date 2015-11-26
	 */
	private function getApiUrl($dataArray)
	{
		$getUserUrlParameter = $getUserApiUrl = $searchName = $apiUrl = '';
		$getUserResponseData = $urlParameter = array();

		if($dataArray['insgoWidget_type'] == 'user'){
			//get user info
			$getUserUrlParameter = parent::getParameter('getUser', $dataArray);
			$getUserApiUrl = parent::getInsgoWidgetApiUrl('getUser', '', $getUserUrlParameter);
			$getUserResponseData = parent::executeCurl($getUserApiUrl, 'n');

			//get user media
			list($searchName, $urlParameter) = parent::getParameter('user', $dataArray, $getUserResponseData['data'][0]);

			$apiUrl = parent::getInsgoWidgetApiUrl('user', $searchName, $urlParameter);
		}
		else if($dataArray['insgoWidget_type'] == 'tag'){
			//get tag media
			list($searchName, $urlParameter) = parent::getParameter('tag', $dataArray);
			$apiUrl = parent::getInsgoWidgetApiUrl('tag', $searchName, $urlParameter);
		}
		else {
			$apiUrl = '';
		}

		return $apiUrl;
	}
}
?>