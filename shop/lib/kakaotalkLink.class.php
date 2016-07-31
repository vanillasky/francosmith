<?php
/**
* Copyright (c) 2014 GODO Co. Ltd
* All right reserved.
*
* This software is the confidential and proprietary information of GODO Co., Ltd.
* You shall not disclose such Confidential Information and shall use it only in accordance
* with the terms of the license agreement  you entered into with GODO Co., Ltd
*
* Revision History
* Author            Date              Description
* ---------------   --------------    ------------------
* 윤범열            2014.07.31        2014.07.31
*/

/**
* 카카오톡 링크 API
*
* @author kakaotalkLink.class.php,  bumyul2000, bumyul2000@godo.co.kr
* @version 1.0.0
* @date 2014-07-31
*
*/
class KakaotalkLink
{
	var $serverLinkUrl = 'http://socialmember.godo.co.kr/kakaoSDK/kakaotalkLink/kakaotalkLink.php'; // 중앙서버 스크립트 경로
	var $kakaoServerType;
	var $kakaoLinkApiType;
	var $checkImageUrl;
	var $imageData = array();
	
	var $fileMaxSize = 512000;	//maximum image size 500KB

	/**
	 * 카카오톡 링크 API construct
	 * @author bumyul2000, bumyul2000@godo.co.kr
	 * @param 
	 * @return
	 * @date 2013-07-31
	 */
	function KakaotalkLink()
	{
		//기본설정, 개별설정 적용여부
		$this->kakaoServerType = $this->getKakaoServerType();
	}

	/**
	 * 카카오톡 설정 타입
	 * @author bumyul2000, bumyul2000@godo.co.kr
	 * @param 
	 * @return string n (기본설정), string y (개별설정)
	 * @date 2013-07-31
	 */
	function getKakaoServerType()
	{
		return 'n';
	}

	/**
	 * 카카오톡 링크 API 타입
	 * @author bumyul2000, bumyul2000@godo.co.kr
	 * @param 
	 * string webButton (이미지API), string webLink (텍스트API)
	 * @date 2013-07-31
	 */
	function getKakaoLinkApiType()
	{
		if( $this->imageData['fileExists'] === true && $this->imageData['fileSize'] < $this->fileMaxSize){
			$this->kakaoLinkApiType = 'webButton';
		} else {
			$this->kakaoLinkApiType = 'webLink';
		}
	}

	/**
	 * 이미지 정보
	 * @author bumyul2000, bumyul2000@godo.co.kr
	 * @param 
	 * @return 
	 * @date 2013-07-31
	 */
	function setImageData()
	{
		//파일 존재여부
		$imgInfo = @getimagesize($this->checkImageUrl);
		if($imgInfo[2] && is_array($imgInfo)) $this->imageData['fileExists'] = true;
		if( $this->imageData['fileExists'] === true ){
			$this->imageData['fileWidth']  = ( $imgInfo[0] < 80 ) ? 80 : $imgInfo[0] ;
			$this->imageData['fileHeight'] = ( $imgInfo[1] < 80 ) ? 80 : $imgInfo[1] ;
		}

		if (preg_match('/^http(s)?:\/\//', $this->checkImageUrl)){
			//파일사이즈
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $this->checkImageUrl);
			curl_setopt($ch, CURLOPT_HEADER, true); 
			curl_setopt($ch, CURLOPT_NOBODY, 1); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
			$result = curl_exec($ch); 
			$imageHeader = @explode("\r\n", $result);
			$imageContentLength = @explode(":", $imageHeader[6]);
			$this->imageData['fileSize'] = @trim($imageContentLength[1]);
		} else {
			//파일사이즈
			$this->imageData['fileSize'] = filesize($this->checkImageUrl);
		}
	}

	/**
	 * 카카오톡 링크 API 스크립트 반환
	 * @author bumyul2000, bumyul2000@godo.co.kr
	 * @param 
	 * @return string 
	 * @date 2013-07-31
	 */
	function getKakaoScript($param)
	{
		$this->checkImageUrl = ($param['msg_kakao_AbsoluteImgUrl']) ? $param['msg_kakao_AbsoluteImgUrl'] : $param['msg_kakao_imgUrl'];
		$this->setImageData();

		//카카오링크 API 타입체크
		$this->getKakaoLinkApiType();

		switch($this->kakaoServerType){
			case 'y' :
			
			break;

			case 'n': default:
				$data = $this->setServerData($param);

				return $this->getServerScript($data);
			break;
		}
	}

	/**
	 * 중앙서버 카카오톡 링크 data 가공
	 * @author bumyul2000, bumyul2000@godo.co.kr
	 * @param  array $param
	 * @return string $postData 
	 * @date 2013-07-31
	 */
	function setServerData($param)
	{
		$data			= array();		
		$postDataArray	= array();

		$data['kakaoLinkImgUrl']		= $param['msg_kakao_imgUrl'];												//상품이미지 URL
		$data['kakaoLinkWidth']			= $this->imageData['fileWidth'];											//상품이미지 width (80px 이상)
		$data['kakaoLinkHeight']		= $this->imageData['fileHeight'];											//상품이미지 height (80px 이상)
		$data['kakaoLinkShopName']		= ($param['msg_kakao1']) ? $param['msg_kakao1'] : $_SERVER['HTTP_HOST'];	//쇼핑몰명 (버튼문구)
		$data['kakaoLinkGoodsName']		= iconv("UTF-8", "EUC-KR", urldecode($param['msg_kakao2']));				//상품제목 (1000자 이하)
		$data['kakaoLinkRedirectUrl']	= $param['msg_kakao3'];														//리다이렉트 URL (developer 계정에 등록되어있는 url)

		$data = @array_map('urlencode', (array)$data);
		
		$postDataArray[] = 'kakaoLinkGoodsName=' . $data['kakaoLinkGoodsName'];
		$postDataArray[] = 'kakaoLinkShopName=' . $data['kakaoLinkShopName'];
		$postDataArray[] = 'kakaoLinkRedirectUrl=' . $data['kakaoLinkRedirectUrl'];
		$postDataArray[] = 'kakaoLinkApiType=' . $this->kakaoLinkApiType;
		if($data['kakaoLinkImgUrl']){
			$postDataArray[] = 'kakaoLinkImgUrl=' . $data['kakaoLinkImgUrl'];
		}
		if($data['kakaoLinkWidth']){
			$postDataArray[] = 'kakaoLinkWidth=' . $data['kakaoLinkWidth'];
		}
		if($data['kakaoLinkHeight']){
			$postDataArray[] = 'kakaoLinkHeight=' . $data['kakaoLinkHeight'];
		}
		$postData = @implode('&', $postDataArray);

		return $postData;
	}

	/**
	 * 간편설정 중앙서버 통신
	 * @author bumyul2000, bumyul2000@godo.co.kr
	 * @param string $postData 가공된 데이터
	 * @return string $result
	 * @date 2013-07-31
	 */
	function getServerScript($postData)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->serverLinkUrl);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		$result = curl_exec($ch);

		return base64_decode($result);
	}
}
?>