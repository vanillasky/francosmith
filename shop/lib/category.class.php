<?
final class category {

	/**
	 * 1 depth 당 사용 가능한 자릿수
	 */
	const DEPTH_PER = 3;

	/**
	 * 최대 depth
	 */
	const DEPTH_MAX = 4;

	/**
	 * 입력 카테고리의 n번째 카테고리를 리턴
	 * @param string $category
	 * @param integer $depth [optional]
	 * @return string
	 */
	public static function div($category, $depth=1) {

		return substr($category, 0, self::DEPTH_PER * $depth);

	}

	/**
	 * 입력카테고리의 depth 를 리턴
	 * @param string $category [optional]
	 * @return
	 */
	public static function getDepth($category = '') {

		if (($len = strlen($category)) >= 3)
			return ceil($len / self::DEPTH_PER);
		else
			return 0;

	}

	/**
	 * 입력 카테고리의 하위 카테고리를 리턴
	 * @param string $category
	 * @return
	 */
	public static function getSubCategory($category) {

		$db = Core::loader('GODO_DB');

		$depth = self::getDepth($category) + 1;

		$builder = $db->builder()->select();
		$builder
			->from(GD_CATEGORY, $db->expression('MAX(category)'))
			->where("category like ?", $db->wildcard($category,1))
			->where("depth = ?", $depth)
		;

		list($max) = $builder->fetch();
		if (!$max) $max = $category.str_repeat('0', self::DEPTH_PER);

		$_offset = ($depth - 1) * self::DEPTH_PER;

		$_parent = substr($max, 0, $_offset);
		$_child  = substr($max, $_offset,self::DEPTH_PER);

		//$_child_max = pow(36, self::DEPTH_PER) - 1;	// 36 진수
		$_child_max = pow(10, self::DEPTH_PER) - 1;	// 10 진수

		if (++$_child >= $_child_max)
			return false;	// 더이상 하위 카테고리를 생성할 수 없음
		else
			return $_parent.sprintf('%0'.self::DEPTH_PER.'s', $_child);

	}

}
?>