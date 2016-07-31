<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */

/**
 * GODO_DB_indexer
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
final class GODO_DB_indexer {

	/**
	 * DB 인스턴스
	 * @var GODO_DB
	 */
	private $db;

	/**
	 * Construct
	 * @param GODO_DB
	 * @return void
	 */
	public function __construct(&$db) {
		$this->db = $db;
	}


	/**
	 * 색인 추출 & 저장시 기존 색인 삭제 여부
	 * @var boolean
	 */
	public $skipclear = false;


	/**
	 * 인스턴스 복제
	 * @return GODO_DB_builder
	 */
	public function __clone() {

		return $this;

	}

	/**
	 * 특문, 공백을 제거한 단어를 추출
	 * @toto 유니코드 변환후, 각 range 외 문자 삭제 처리
	 * @param string $words
	 * @return array
	 */
	private function getRawWords($words) {

		// 유니코드로 변환된 한글을 원래 모양으로 수정
		$words = Core::helper('String')->toHan($words);

		// 태그, &xxxx 삭제 및 치환
		$words = strip_tags($words);
		$words = strtr( $words, array_flip( get_html_translation_table( HTML_SPECIALCHARS ) ) );

		// 검색에 필요 없는 특문 치환
		$_replaceFr = array(
			'\n','\r\n','-','+','_','~','!','@','#','$','%','^','&','*','(',')','{','}','[',']',';','\'',':','"',',','.','<','>','/','?','`',
		);

		$words = str_replace($_replaceFr,' ',$words);

		// 공백으로 나눔
		$words = explode(' ',$words);

		// 중복 제거
		$words = array_unique($words);

		return $words;

	}

	/**
	 * 기 저장된 색인을 삭제
	 * @param string $table 삭제할 테이블명
	 * @param mixed $pk 삭제할 primary key
	 * @return void
	 */
	private function clear($table, $pk) {

		$this->db->delete( $this->getTable($table) )->where( 'pk = ?' , $pk )->query();

	}

	/**
	 * 추출된 색인을 저장
	 * @param string $keywords
	 * @param string $table
	 * @param string $column
	 * @param string $pk
	 * @return void
	 */
	private function save($keywords,$table,$column, $pk) {

		$query = array();

		$kw_insert_stmt = $this->db->prepare("insert into gd_indexer_keywords set `word` = ? on duplicate key update `hits` = `hits` + 1");
		$kw_select_stmt = $this->db->prepare("select seq from gd_indexer_keywords where `word` = ?");

		foreach ($keywords as $keyword) {

			$r = $kw_insert_stmt->execute($keyword);

			if (($seq = $kw_insert_stmt->lastID()) < 1) {
				$kw_select_stmt->execute($keyword);
				$tmp = $kw_select_stmt->current();
				$seq = $tmp['seq'];
			}

			//
			$query[] = "('$seq', '$column', '$pk')";
		}

		if (sizeof($query) > 0) {
			$queries = array_chunk($query, 5);

			foreach ($queries as $query) {
				$query = "INSERT INTO ".$this->getTable($table)." (`seq`, `column`, `pk`) VALUES " . implode(',',$query);
				$this->db->query($query);
			}

		}

	}

	/**
	 * 색인 테이블명을 리턴
	 * @param object $table
	 * @return
	 */
	private function getTable($table) {
		return 'gd_indexer_table_'.$table;

	}

	/**
	 * 색인 테이블을 생성
	 * @param object $table
	 * @return
	 */
	private function _createTable($table) {

		return;

		$_table = $this->getTable($table);

		$query = "
		CREATE TABLE IF NOT EXISTS `$_table` (
		  `seq` int(10) unsigned NOT NULL,
		  `column` varchar(32) NOT NULL,
		  `pk` int(10) unsigned NOT NULL,
		  KEY `ix_key` (`seq`,`column`)
		) ENGINE=MyISAM DEFAULT CHARSET=euckr;
		";

	}

	/**
	 * 색인을 추출 하여 저장
	 * @param string $table
	 * @param integer $pk
	 * @param array $data	[optional]
	 * @param array $allow_column	[optional]
	 * @return
	 */
	public function generate($table, $pk, $data=array(), $allow_column=array()) {

		if (!$this->skipclear)
			 $this->clear($table, $pk);

		if (!empty($data) && !empty($allow_column)) {

			if (!is_array($allow_column))
				$allow_column = array($allow_column);

			foreach ($allow_column as $column) {
				$keywords = $this->getKeywords($data[$column], 4);	// 4바이트 고정
				$this->save($keywords, $table, $column, $pk);
			}
		}

	}

	/**
	 * 색인 추출
	 * @param string $word
	 * @param integer $byte [optional]
	 * @return array
	 */
	public function getKeywords($word, $byte = 4) {	// 한글 2 자, 영문 4자

		$words = !empty($word) ? $this->getRawWords($word) : array();

		$keywords = array();

		foreach ($words as $word) {

			$chars = $this->getChars($word);

			if (($m = sizeof($chars)) >= $byte) {

				for ($i=0;$i<$m;$i++) {

					$_keyword = '';
					$_mb = false;

					for ($j=0,$_m=$byte;$j<$_m;$j++) {

						if (!isset($chars[$i + $j])) continue;

						$_keyword .= $chars[$i + $j];

						if (ord($chars[$i + $j]) > 127) {
							$_mb = true;
							$_m--;
						}
					}

					if ($_mb) $_byte = $byte - 1;
					else $_byte = $byte;

					if ($_byte > strlen($_keyword)) continue;

					$keywords[] = $this->convert($_keyword);
				}
			}
			else {
				$keywords[] = $this->convert($word);
			}

		}

		return $this->cleanup($keywords);
	}


	/**
	 * 대문자를 소문자로 변경, 한글을 자소 단위로 분리 하여 리턴
	 * @param string $str
	 * @return string
	 */
	function convert($str) {

		$str = Core::helper('String')->strtolower($str);
		$str = Core::helper('String')->splitKorean($str);

		return $str;

	}


	/**
	 * 단어를 한글자씩 자름
	 * @param string $word
	 * @return array
	 */
	private function getChars($word) {

		$words = array();

		for ($i=0,$m=strlen($word);$i<$m;$i++) {

			$char = $word[$i];

			if (ord($char) > 127) {
				$i++;
				$char = $char . $word[$i];
			}

			$words[] = $char;
		}

		return $words;

	}

	/**
	 * 추출된 색인의 중복 제거
	 * @param array $keywords
	 * @return array 중복 제거된 색인 배열
	 */
	private function cleanup($keywords) {

		for ($i=0,$m=sizeof($keywords);$i<$m;$i++) {

			$_keyword = trim($keywords[$i]);

			if (empty($_keyword) || sizeof(array_unique($this->getChars($_keyword))) < 2) unset($keywords[$i]);

		}

		$keywords = array_unique($keywords);

		return $keywords;

	}

	/**
	 * 검색을 위한 join 구문이 설정된 SQL 빌더를 리턴
	 * @param string $table
	 * @param string $columns
	 * @param string $keyword
	 * @return mixed GODO_DB_builder or false
	 */
	public function search($table, $columns, $keyword) {

		if (!is_array($columns)) $columns = array($columns);
		$keyword = $this->getKeywords($keyword);

		$_columns = array();
		$_keyword = array();

		foreach ($columns as $k) if ($k) $_columns[] = $k;
		foreach ($keyword as $k) if ($k) $_keyword[] = $k;

		if (sizeof($_columns) && sizeof($_keyword)) {

			$builder = $this->db->builder()->select();

			$builder->from(array('map'=> $this->getTable($table)),null);
			$builder->join(array('kw'=> 'gd_indexer_keywords'),'kw.seq = map.seq',null);

			$builder->where('map.`column` IN (?)', array( $_columns ));
			$builder->where('kw.`word` IN (?)', array( $_keyword ));

			$builder->group('map.pk');
			$builder->having('_cnt >= ?', sizeof($_keyword));
			$builder->order('null');

			$builder->columns(array(
				'map.pk',
				'_cnt' => $this->db->expression('COUNT(map.pk)')
			));

			return $builder;

		}

		return false;

	}

}
?>
