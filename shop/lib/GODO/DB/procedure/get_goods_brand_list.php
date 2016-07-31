<?
class get_goods_brand_list extends GODO_DB_procedure {

	function execute() {

		$builder = $this->db->builder()->select();
		$builder->from(GD_GOODS_BRAND);
		$builder->order('sort');

		return $this->db->utility()->getAll($builder);

	}

}
?>