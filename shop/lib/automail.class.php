<?php

/***
 * 자동메일설정에 의한 메일발송 클래스
*/

class automail {

	var $mail;
	var $tpl;
	var $mode=false;
	var $to_mail=false;
	
	// mail과 tpl클래스로 생성
	function automail() {
		include_once dirname(__FILE__)."/../Template_/Template_.class.php";
		include_once dirname(__FILE__)."/../lib/mail.class.php";
		$this->db = $GLOBALS['db'];
		$this->mail = new Mail($params);
		$this->tpl = new Template_;
	}

	// $mode=어떤메일,$to_mail=받는메일주소,$cfg=쇼핑몰환경설정파일
	function _set($mode,$to_mail,$cfg) {
		$this->mode=$mode;
		$this->to_mail=$to_mail;
		$this->cfg=$cfg;

		$this->tpl->template_dir = dirname(__FILE__)."/../conf/email";
		$this->tpl->compile_dir = dirname(__FILE__)."/../Template_/_compiles/$cfg[tplSkin]/conf/email";
		$this->tpl->define('tpl',"tpl_$mode.php");
		$this->tpl->assign("cfg",$cfg);
	}

	// 변수등록
	function _assign($arg,$arg2=null) {
		if (is_array($arg)) $this->tpl->assign($arg);
		else $this->tpl->assign($arg,$arg2);
	}

	// 발송
	function _send() {
		if($this->mode===false) return false;
		if($this->to_mail===false) return false;

		include dirname(__FILE__)."/../conf/email/subject_{$this->mode}.php";
		$headers['Name']    = $this->cfg[shopName];
		$headers['From']    = $this->cfg[adminEmail];
		$headers['To']		= $this->to_mail;

		return $this->mail->send($headers, $this->tpl->fetch('tpl'));
	}

	function _assign_tpl($ordno) {
		GLOBAL $db;
		GLOBAL $r_settlekind;

		$query="SELECT a.prn_settleprice, a.nameOrder,a.email,a.goodsprice,a.settleprice,a.settlekind,a.zipcode,a.zonecode,a.address,a.nameReceiver,a.phoneReceiver,a.deliverycode,a.delivery,b.deliveryno,b.deliveryurl
		FROM ".GD_ORDER." a LEFT OUTER JOIN ".GD_LIST_DELIVERY." b on a.deliveryno=b.deliveryno WHERE a.ordno='".$ordno."' ";
		$data = $db->fetch($query,1);

		$row[goodslink] = 'http://'.$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir].'/goods/goods_view.php?goodsno=';	//상품상세보기링크

		$query = "
		SELECT a.goodsno,a.goodsnm,a.opt1,a.opt2,a.addopt,a.reserve,a.price,a.ea,b.img_s
		FROM ".GD_ORDER_ITEM." AS a
		INNER join ".GD_GOODS." AS b on a.goodsno=b.goodsno
		where
		a.ordno = '".$ordno."' and istep <= 4 ";
		$res = $db->query($query);
		$orderInfo_body='';
		while ($sub=$db->fetch($res)){
			$goods[goodsno]=$sub[goodsno];
			$goods[goodsimg]=goodsimg($sub[img_s],50,'',4);
			$goods[reserve] =number_format($sub[reserve]*$sub[ea]);	//적립금
			$goods[price]=number_format($sub[price]);	//단일상품가
			$goods[ea]=number_format($sub[ea]);	//갯수
			$goods[sumprice]=number_format($sub[price]*$sub[ea]);	//상품합계
			$goods[goodsinfo]=$sub[goodsnm];	//상품정보
			if($sub[opt1])$goods[goodsinfo].='['.$sub[opt1].']';
			if($sub[opt2])$goods[goodsinfo].='['.$sub[opt2].']';
			if($sub[addopt])$goods[goodsinfo].='<div>['.str_replace("^","] [",$sub[addopt]).']</div>';

			//치환코드
			$orderInfo_body.='<tr><td style="padding:6px 0 4px;border:1px solid #e6e6e6;color:#000" align="left">
			<table><tr><td style="font:12px 돋움;padding-left:10px"><a href="'.$row[goodslink].$goods[goodsno].'" target="_blank">'.$goods[goodsimg].'</a></td>
					<td style="font:12px 돋움;padding-left:20px">'.$goods[goodsinfo];
			$orderInfo_body.='</td>
				</tr></table></td><td style="padding:6px 0 4px;border:1px solid #e6e6e6;color:#000">'.$goods[reserve].' 원</td>
			<td style="padding:6px 0 4px;border:1px solid #e6e6e6;color:#000">'.$goods[price].' </td>
			<td style="padding:6px 0 4px;border:1px solid #e6e6e6;color:#000">'.$goods[ea].' 개</td>
			<td style="padding:6px 0 4px;border:1px solid #e6e6e6;color:#000">'.$goods[sumprice].' 원</td></tr>';
			$totalGoodsPrice+=$sub[price]*$sub[ea];
			$item[] = $goods;
		}
		$row[goodsprice]=number_format($totalGoodsPrice);//상품합계금액
		$row[delivery]=number_format($data[delivery]);//배송비
		$row[totalprice]=number_format($totalGoodsPrice+$data[delivery]);	//총 주문금액
		$row[settleprice]=number_format($data[prn_settleprice]);//결제금액

		$row[zipcode]= ($data['zonecode']) ? $data['zonecode'] : $data['zipcode']; //우편번호
		$row[address]=$data[address];	//집주소
		$row[nameReceiver]=$data[nameReceiver];	//받는사람
		$row[phoneReceiver]=$data[phoneReceiver];	//받는전화번호
		$row[deliverycode]=$data[deliverycode];	//배송코드
		$row[deliveryurl]=$data[deliveryurl];	//배송url
		$row[nameOrder]=$data[nameOrder];	//주문자

		##치환코드##
		//구매 상세 내역정보
		$orderInfo_header='<table style="width:100%;border-bottom:2px solid #dcdcdc;font-family:돋움,dotum;font-size:12px;text-align:center;border-collapse:collapse;width:640px" border="0" cellspacing="0"  >
		<colgroup><col ><col width="10%"><col width="10%"><col width="10%"><col width="10%"></colgroup><thead>
			<tr><th scope="col" style="height: 34px;padding:7px 0 4px;border-top:2px solid #e6e6e6;border-right:1px solid #e6e6e6;border-left:1px solid #e6e6e6;background-color:#F6F6F6;color:#000;font-family:돋움,dotum;font-size:12px;font-weight:bold">상품정보</th>
				<th scope="col" style="height: 34px;padding:7px 0 4px;border-top:2px solid #e6e6e6;border-right:1px solid #e6e6e6;border-left:1px solid #e6e6e6;background-color:#F6F6F6;color:#000;font-family:돋움,dotum;font-size:12px;font-weight:bold">적립금</th>
				<th scope="col" style="height: 34px;padding:7px 0 4px;border-top:2px solid #e6e6e6;border-right:1px solid #e6e6e6;border-left:1px solid #e6e6e6;background-color:#F6F6F6;color:#000;font-family:돋움,dotum;font-size:12px;font-weight:bold">판매가</th>
				<th scope="col" style="height: 34px;padding:7px 0 4px;border-top:2px solid #e6e6e6;border-right:1px solid #e6e6e6;border-left:1px solid #e6e6e6;background-color:#F6F6F6;color:#000;font-family:돋움,dotum;font-size:12px;font-weight:bold">수량</th>
				<th scope="col" style="height: 34px;padding:7px 0 4px;border-top:2px solid #e6e6e6;border-right:1px solid #e6e6e6;border-left:1px solid #e6e6e6;background-color:#F6F6F6;color:#000;font-family:돋움,dotum;font-size:12px;font-weight:bold">합계</th>
		</tr></thead><tbody>';

		$orderInfo_footer='</tbody><tfoot style="background-color:#f5f7f9"><tr><td align="left" colspan="5" style="padding:6px 0 4px;border:1px solid #e6e6e6;color:#000">
						&nbsp;&nbsp;상품합계금액 &nbsp;<B>'.($row[goodsprice]).'</B>원 &nbsp; + &nbsp;
						배송비&nbsp;<B>'.($row[delivery]).'</B>원&nbsp; =&nbsp;총주문금액 &nbsp;<B>'.($row[settleprice]).'</B>원</td></tr></tfoot></table>';

		$orderInfo=$orderInfo_header.$orderInfo_body.$orderInfo_footer;
		$row['str_settlekind'] = $r_settlekind[$data['settlekind']];

		//결제정보
		$settleInfo='<div style="border:1px solid #e6e6e6;width:640px;height:70px;font:12px 돋움;color:#000;line-height:20px;width:640px;padding-top:5px">
			&nbsp;&nbsp;결제방법 : '.$row['str_settlekind'].' <br/>
			&nbsp;&nbsp;주문금액 : '.$row['goodsprice'].' 원 <br/>
			&nbsp;&nbsp;결제금액 : '.$row['settleprice'].' 원
		</div>';
		
		//배송정보
		$deliveryInfo='<div style="border:1px solid #e6e6e6;width:640px;height:70px;font:12px 돋움;color:#000;line-height:20px;padding-top:5px;">
			&nbsp;&nbsp;배송처 : ['.$row['zipcode'].'] '.$row['address'].' <br/>
			&nbsp;&nbsp;받는분 : '.$row['nameReceiver'].' <br/>
			&nbsp;&nbsp;연락처 : '.$row['phoneReceiver'].'
		</div>';
		
		$deliveryLink='<a href="'.$row[deliveryurl].$row[deliverycode].'" target=_blank>'.$row[deliverycode].'</A>';

		$this->_assign($row);
		$this->_assign('item',$item);
		$this->_assign('deliveryLink',$deliveryLink);
		$this->_assign('orderInfo',$orderInfo);
		$this->_assign('settleInfo',$settleInfo);
		$this->_assign('deliveryInfo',$deliveryInfo);
	}
}
?>