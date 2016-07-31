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
 * workingby		 2014.10.30        First Draft.
 */

/**
 * 지역별 배송비
 *
 * @author areaDelivery.class.php workingby
 * @date 2014-10-30
 * @version 1.0
 */
class areaDelivery
{
	private $param, $set;

	function __construct($param)
	{
		global $set, $param;

		$this->param	= $param;
		$this->set		= $set;
	}

	/**
	 * 지역별 적용 배송비
	 * @author workingby
	 * @date 2014-10-30
	 * @param
	 * @return int $_extra_fee
	 */
	public function getPay()
	{
		switch($this->set['delivery']['area_deli_type']){
			case '1' :
				//지역별 배송정책 - 우편번호
				$_extra_fee = $this->getZipcodePay();
			break;

			case '2' :
				//지역별 배송정책 - 지역명/도로명
				$_extra_fee = $this->getNewAreaDeliveryPay();
			break;

			case '0' : default :
				//지역별 배송정책 - 지역명
				$_extra_fee = $this->getAreaDeliveryPay();
			break;
		}

		return $_extra_fee;
	}

	/**
	 * 지역별 배송정책 - 우편번호
	 * @author workingby
	 * @date 2014-10-30
	 * @param
	 * @return int $_extra_fee
	 */
	public function getZipcodePay()
	{
		$arr1 = explode('|',trim($this->set['delivery']['areaZip1']));
		$arr2 = explode('|',trim($this->set['delivery']['areaZip2']));
		$over = explode('|',trim($this->set['delivery']['overAddZip']));
		$zip  = str_replace('-','',$this->param['zipcode']);

		foreach($arr1 as $k => $v){
			if($v <= $zip && $zip <= $arr2[$k]){
				$_extra_fee += $over[$k];
			}
		}

		return $_extra_fee;
	}

	/**
	 * 지역별 배송정책 - 지역명
	 * @author workingby
	 * @date 2014-10-30
	 * @param
	 * @return int $_extra_fee
	 */
	public function getAreaDeliveryPay()
	{
		$arr = explode('|',trim($this->set['delivery']['overZipcode']));
		foreach($arr as $k => $v){
			$idx = count($rzip);
			$arr1 = array_unique(explode(',',$v));
			foreach($arr1 as $v2)if($v2)$rzip[$idx][] = $v2;
		}
		if(!$this->set['delivery']['overAdd']){
			$tmpAdd = explode("|",$this->set['delivery']['over']);
			foreach($tmpAdd as $v){
				$val = $v - $this->set['delivery']['default'];
				if($val >= 0) $overAdd[] = $val;
				else $overAdd[] = $v;
			}
			$over = $overAdd;
		}else{
			$over = explode("|",$this->set[delivery][overAdd]);
		}
		
		$head = substr($this->param['zipcode'],0,3);

		$this->param['zipcode'] = preg_replace('/[^0-9]/','',$this->param['zipcode']);
		$this->param['zipcode'] = sprintf('%s-%s', substr($this->param['zipcode'],0,3), substr($this->param['zipcode'],3,3));
		$tmp = Core::loader('Zipcode')->get( array('keyword' => $this->param['zipcode'], 'where' => 'zipcode' ) )->current();
		$tmpSido = $tmp['sido'];
		$tmpGugun = $tmp['gugun'];
		$tmpThisArea = $tmpSido." ".$tmpGugun;
		@include dirname(__FILE__) . '/../conf/area.delivery.php';
		$areaNameChecker = explode("|", $r_area['deliveryArea']);
		for ($i=0,$imax=count($areaNameChecker);$i<$imax;$i++) $areaNameChecker[$i] = explode(",",$areaNameChecker[$i]);
		for ($i=0;$i<count($rzip);$i++)if(in_array($head,$rzip[$i])&& in_array($tmpThisArea, $areaNameChecker[$i])){
			$key=$i;
			$_extra_fee += $over[$key];
		}

		return $_extra_fee;
	}

	/**
	 * 지역별 배송정책 - 지역명/도로명
	 * @author workingby
	 * @date 2014-10-30
	 * @param
	 * @return int $_extra_fee
	 */
	public function getNewAreaDeliveryPay()
	{
		//도로명주소
		if($this->param['road_address']){
			$newAreaResult	= array();
			$road_address	= trim($this->param['road_address'] . ' ' .  $this->param['address_sub']);
			$tmpRoad_address= $this->getAddressReprocess($road_address);
			$newAreaResult	= $this->getGdAreaDelivery($tmpRoad_address);
		}

		//지번주소
		if($this->param['address']){
			$address	= trim($this->param['address'] . ' ' .  $this->param['address_sub']);
			$tmpAddress	= $this->getAddressReprocess($address);

			// 도로명주소가 없다면 지번주소로 gd_area_delivery 추출
			if(!$this->param['road_address']){
				$newAreaResult	= array();		
				$checkSido		= array('강원도'=>'강원', '경기도'=>'경기', '경상남도'=>'경남', '경상북도'=>'경북', '광주광역시'=>'광주', '대구광역시'=>'대구', '대전광역시'=>'대전', '부산광역시'=>'부산', '서울특별시'=>'서울', '세종특별자치시'=>'세종', '울산광역시'=>'울산', '인천광역시'=>'인천', '전라남도'=>'전남', '전라북도'=>'전북', '제주특별자치도'=>'제주', '충청남도'=>'충남', '충청북도'=>'충북');

				//'시도'의 줄임말을 풀네임으로 변환
				list($tmpAddressSido) = @array_keys(@preg_grep('/' . $tmpAddress[0] . '/', $checkSido));
				$tmpAddress[0] = ($tmpAddressSido) ? $tmpAddressSido : $tmpAddress[0];
				$newAreaResult =  $this->getGdAreaDelivery($tmpAddress);
			}
		}

		$address_cnt			= (@count($tmpRoad_address) > @count($tmpAddress)) ? @count($tmpRoad_address) : @count($tmpAddress);
		$tmp_extra_fee_roadAddr	= false;
		$tmp_extra_fee_addr		= false;
		if(count($newAreaResult) > 0){
			for($i=$address_cnt; $i>0; $i--){
				if($i!=$address_cnt){
					if(@count($tmpRoad_address) >= @count($tmpAddress)) @array_pop($tmpRoad_address);
					if(@count($tmpRoad_address) <= @count($tmpAddress)) @array_pop($tmpAddress);
				}
				foreach($newAreaResult as $newArea){
					$newAreaName	= trim($newArea['areaSido']).trim($newArea['areaGugun']).str_replace(' ', '',$newArea['areaEtc']);

					//도로명주소 비교
					if($tmp_extra_fee_roadAddr === false && implode('', $tmpRoad_address) == $newAreaName){
						$tmp_extra_fee_roadAddr = $newArea['areaPay'];
						$roadAddr_length = @count(@explode(' ', $newArea['areaEtc']));
					}

					//지번주소 비교
					if($tmp_extra_fee_addr === false && implode('', $tmpAddress) == $newAreaName){
						$tmp_extra_fee_addr = $newArea['areaPay'];
						$addr_length = @count(@explode(' ', $newArea['areaEtc']));
					}
					if($tmp_extra_fee_roadAddr !== false && $tmp_extra_fee_addr !== false) break;
				}

				//도로명주소, 지번주소중 행정구역별로 더 상세한 주소에게 배송비 부과
				if($tmp_extra_fee_roadAddr !== false || $tmp_extra_fee_addr !== false){
					$_extra_fee = ($roadAddr_length >= $addr_length) ? (int)$tmp_extra_fee_roadAddr : (int)$tmp_extra_fee_addr;
					break;
				}
			}
		}

		return (int)$_extra_fee;
	}

	/**
	 * 지역명 설정 내역
	 * @author workingby
	 * @date 2014-10-30
	 * @param array $tmpRoad_address
	 * @return array $newAreaResult
	 */
	public function getGdAreaDelivery($tmpRoad_address)
	{
		global $db;

		if($tmpRoad_address[0] != '세종특별자치시') {
			$where = " and areaGugun = '" . $tmpRoad_address[1] . "'";
		}

		$newAreaResult = $db->_select("
			SELECT 
				areaSido, areaGugun, areaEtc, areaPay 
			FROM 
				" . GD_AREA_DELIVERY . " 
			WHERE 
				areaSido = '" . $tmpRoad_address[0] . "' $where and areaPay is not null and LENGTH(areaPay) > 0
			ORDER BY 
				LENGTH(areaEtc) DESC
		");

		return $newAreaResult;
	}

	/**
	 * 주소 배열가공
	 * @author workingby
	 * @date 2014-10-30
	 * @param string $address
	 * @return array $tmpAddress
	 */
	public function getAddressReprocess($address)
	{
		$tmpAddress = @array_values(@array_filter(@array_map('trim', (array)@explode(' ', $address))));

		return $tmpAddress;
	}
}