<?php
class Clib_Form_Admin_Goods_Register extends Clib_Form_Abstract
{
	// dont't create construct & destruct.

	protected function initialize()
	{
		/*
		 * $this->input(columnName, attributes, $validationOption);
		 *
		 * $validationOption like this :
		 *
		 * array(
		 *       'required' => Clib_Validation::REQUIRED_YES,
		 *       'type' => Clib_Validation::TYPE_STRING,
		 *       'length' => 9,
		 *       'length_type' => Clib_Validation::TYPE_LENGTH_EQUAL
		 * );
		 */

		$this->input('goodscd');
		$this->input('goodsnm', array(
			'style' => 'width:100%;',
			'required' => true,
			'label' => '상품명'
		));
		$this->checkbox('meta_title', array(
			'value' => 1,
			'class' => 'null'
		));

		$this->input('model_name');

		$this->radio('goods_status', array(
			'default_value' => 'N',
			'value' => array(
				'신상품' => 'N',
				'중고상품' => 'U',
				'반품/재고상품' => 'R'
			)
		));

		$brandnoValue = Clib_Application::getCollectionClass('goods_brand')->setOrder('sort')->load();
		$brandnoValue->unshiftItem(Clib_Application::getModelClass('goods_brand')->setData(array('brandnm' => '- 브랜드 선택 -', )));
		$this->select('brandno', array('value' => $brandnoValue));

		$this->input('maker');
		$makerValue = Clib_Application::getCollectionClass('goods_maker')->load();
		$makerValue->unshiftItem(Clib_Application::getModelClass('goods_maker')->setData(array('maker' => '- 목록보기 -', )));
		$this->select('maker_select', array(
			'value' => $makerValue,
			'onchange' => 'this.form.maker.value=this.value;this.form.maker.focus();'
		));

		$this->input('origin');
		$originValue = Clib_Application::getCollectionClass('goods_origin')->load();
		$originValue->unshiftItem(Clib_Application::getModelClass('goods_origin')->setData(array('origin' => '- 목록보기 -', )));
		$this->select('origin_select', array(
			'value' => $originValue,
			'onchange' => 'this.form.origin.value=this.value;this.form.origin.focus();'
		));

		$this->hidden('color');
		$this->input('launchdt', array(
			'type' => 'date',
			'class' => 'test123'
		));
		$this->input('keyword');
		$this->input('goods_price', array(
			'required' => true,
			'option' => 'regNum',
			'onkeyup' => 'nsAdminGoodsForm.option.pricing();'
		));

		$this->input('provider_price');
		$this->input('strprice', array('style' => "width:200px;"));
		$this->input('min_ea');
		$this->input('max_ea');
		$this->input('sales_unit');
		$this->input('totstock');

		$this->input('manufacture_date', array('type' => 'date'));
		$this->input('effective_date_start', array('type' => 'date'));
		$this->input('effective_date_end', array('type' => 'date'));
		$this->input('external_video_width', array('size' => 4));
		$this->input('external_video_height', array('size' => 4));

		$this->radio('external_video_size_type', array(
			'default_value' => '0',
			'value' => array(
				'기본' => 0,
				'사용자' => 1
			)
		));

		$this->input('external_video_url');

		$this->radio('use_external_video', array(
			'default_value' => '0',
			'value' => $this->getOption('boolean'),
			'onclick' => 'nsAdminForm.toggle.is(event, 1);'
		));

		$this->radio('opttype', array(
			'default_value' => 'single',
			'value' => array(
				'일체형' => 'single',
				'분리형' => 'double'
			)
		));

		$this->checkbox('use_stocked_noti', array('value' => 1));

		$this->checkbox('runout', array('value' => 1));
		$this->checkbox('usestock', array('value' => 'o'));

		$this->radio('use_emoney', array(
			'default_value' => '0',
			'value' => array(
				'0',
				'1'
			)
		));
		$this->input('goods_reserve', array('onkeyup' => 'nsAdminGoodsForm.option.pricing();'));

		$this->checkbox('exclude_member_reserve', array('value' => 1));
		$this->checkbox('exclude_member_discount', array('value' => 1));

		$this->radio('use_goods_discount', array(
			'default_value' => '0',
			'value' => array(
				'상품별 할인 사용안함' => '0',
				'상품별 할인 사용' => '1'
			),
			'onclick' => 'nsAdminForm.toggle.is(event, 1);'
		));

		$this->radio('goods_discount_by_term_for_specify_member_group', array(
			'default_value' => '0',
			'value' => array(
				'회원 및 비회원 전체' => '2',
				'회원전체 (비회원 제외)' => '0',
				'회원 그룹지정' => '1'
			),
			'onclick' => 'nsAdminForm.toggle.is(event, 0,1,2);'
		));
		$this->radio('goods_discount_by_term_use_cutting', array(
			'default_value' => '0',
			'value' => array(
				'절사안함' => '0',
				'절사함' => '1'
			)
		));
		$this->select('goods_discount_by_term_cutting_method', array(
			'default_value' => 'f',
			'value' => $this->getOption('cutoff_method')
		));
		$this->select('goods_discount_by_term_cutting_unit', array(
			'default_value' => '0',
			'value' => $this->getOption('cutoff_unit')
		));

		$_use_only_adult = false;

		if ( ! $_use_only_adult) {
			// 휴대폰 인증 서비스 사용시 성인인증 설정 사용 가능
			$hpauth = Core::loader('Hpauth');
			$hpauthConfig = $hpauth->loadConfig();
			$_tmp = $hpauth->loadServiceConfig($hpauthConfig['serviceCode']);
			if ($_tmp['useyn'] == 'y') {
				$_use_only_adult = true;
			}
		}

		if ( ! $_use_only_adult) {
			@include SHOPROOT . '/conf/fieldset.php';
			if ($ipin['nice_useyn'] == 'y' || $ipin['useyn'] == 'y') {
				$_use_only_adult = true;
			}
		}

		$_tmp = array(
			'default_value' => '0',
			'value' => $this->getOption('boolean')
		);
		if ( ! $_use_only_adult) {
			$_tmp['disabled'] = true;
		}

		$this->radio('use_only_adult', $_tmp);

		$this->input('memo', array(
			'type' => 'textarea',
			'style' => 'width:100%;height:60px',
			'class' => 'tline'
		));

		//$this->radio('open', array('default_value'=>'1', 'value'=>array('진열함'=>'1', '진열안함'=>'0', '진열기간설정'=>'2')));
		$this->radio('open', array(
			'default_value' => '0',
			'value' => array(
				'진열함' => '1',
				'진열안함' => '0'
			)
		));
		$this->radio('tax', array(
			'default_value' => '1',
			'value' => array(
				'과세' => '1',
				'비과세' => '0'
			),
		));

		$this->select('display_term_startdate_time', array(
			'default_value' => '1',
			'value' => '1:23'
		));
		$this->select('display_term_enddate_time', array(
			'default_value' => '1',
			'value' => '1:23'
		));

		$this->input('goods_supply', array('onkeyup' => 'nsAdminGoodsForm.option.pricing();'));
		$this->input('goods_consumer', array('onkeyup' => 'nsAdminGoodsForm.option.pricing();'));

		$this->input('delivery_area');

		$this->radio('use_add_option', array(
			'default_value' => '0',
			'value' => $this->getOption('boolean'),
			'onclick' => 'nsAdminForm.toggle.is(event, 1);'
		));
		$this->radio('use_add_input_option', array(
			'default_value' => '0',
			'value' => $this->getOption('boolean'),
			'onclick' => 'nsAdminForm.toggle.is(event, 1);'
		));

		$this->radio('relationis', array(
			'default_value' => '0',
			'value' => array(
				'자동' => '0',
				'수동' => '1'
			),
			'onclick' => 'nsAdminForm.toggle.is(event, 1);'
		));

		$this->radio('image_attach_method', array(
			'default_value' => 'file',
			'value' => array(
				'직접 업로드' => 'file',
				'이미지호스팅 URL 입력' => 'url'
			),
			'onclick' => 'nsAdminGoodsForm.imageUpload.toggleForm();'
		));

		$this->radio('detailView', array(
			'default_value' => 'n',
			'value' => array(
				'사용' => 'y',
				'사용안함' => 'n'
			),
			'onclick' => 'nsAdminForm.toggle.is(event, \'y\');'
		));

		$this->radio('use_extra_field', array(
			'default_value' => '0',
			'value' => $this->getOption('boolean'),
			'onclick' => 'nsAdminForm.toggle.is(event, 1);'
		));

		$this->radio('buyable', array(
			'default_value' => '1',
			'value' => array(
				'전체(비회원+회원)' => '1',
				'회원전용(비회원제외)' => '2',
				'특정 회원그룹 선택/지정' => '3'
			)
		));
		$this->hidden('buyable_member_group');

		$this->hidden('use_option');
		
		$this->radio('speach_description_useyn', array(
		    'default_value' => 'n',
		    'value' => array(
		        '사용안함' => 'n',
		        '사용' => 'y',
		    ),
		));
		
		$this->textarea('speach_description', array(
		    'class' => 'tline',
		    'style' => 'width: 100%;',
		    'rows' => '4',
		    'placeholder' => '이 곳에 입력된 상품 설명은 모바일샵에서 음성으로 고객에게 읽어드립니다.',
		    'maxlength' => '100',
		));

		$this->input('naver_event');
	}

}
