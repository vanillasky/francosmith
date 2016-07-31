<?
include "../lib.php";
include "../../lib/json.class.php";
header('Content-Type: text/html; charset=euc-kr');

$_GET[contents] = iconv("UTF-8", "EUC-KR//IGNORE", rawurldecode($_GET[contents]));

if( $_GET['mode'] == "write" ){

	$Query = "
	insert into
		".GD_MEMBER_CRM."
	set
		m_no = '$_GET[m_no]',
		counsel_id = '$_GET[counsel_id]',
		contents = '$_GET[contents]',
		regdt = '$_GET[regdt]',
		counsel_Type = '$_GET[counsel_Type]'
	";
	$db->query($Query);
	$insert_sno=$db->lastID();

	$_value_array = array();
	$_value_array['mode'] = $_GET['mode'];
	$_value_array['insert_sno'] = $insert_sno;

	$json = new Services_JSON();
	$output = $json->encode($_value_array);
	echo $output;
	exit();
}

if( $_GET['mode'] == "list" ){

	$Query = "
	select
		*,
		date_format(regdt,'%Y.%m.%d %H:%i:%s') regdt
	FROM
		".GD_MEMBER_CRM."
	where
		m_no = '$_GET[m_no]'
	order by sno desc
	";

		//페이지 안에 게시물 수
		$limit="5";
		$page=sprintf("%d",$_GET['page']);
		if(!$page) $page=sprintf("%d",1);
		$offset=$page*$limit-$limit;

		$Sql_cnt= $db->query($Query);
		$numrows = mysql_num_rows($Sql_cnt);

		// 쿼리 실행
		$Query.=" limit $offset,$limit";
		$Sql= $db->query($Query);

		//글번호 시작
		$lastpageNum=$numrows-$offset;
		if($offset==0){
		$myno = $numrows;	//첫페이지
		}else if($limit>$lastpageNum){
			$myno=$lastpageNum ;
		}else{
			$PageCount=$limit*($page-1);
			$myno=$numrows-$PageCount; }
		//글번호 끝

	$_info = array();
	$no = 0;
	while( $row = $db->fetch($Sql)){
		$_info[$no] = array_merge( array( 'myno'=>$myno ), $row );
		$no++;
		$myno--;
	}

	//페이징 계산 start
	$page_array = array();
	$page_per_block=10;
	$total_page = ceil( $numrows / $limit );
	$total_block = ceil( $total_page / $page_per_block );
	$cur_block   = ceil( $page / $page_per_block );

	$page_array = array($total_page,$page,$page_per_block,$total_block,$cur_block);
	$_value_array = array('mode'=>$_GET['mode'], 'totalCnt' =>$numrows, "pageing"=>$page_array, "data"=>$_info );
	//페이징 계산 end

	$json = new Services_JSON();
	$output = $json->encode($_value_array);
	echo $output;
}

if( $_GET['mode'] == "view" ){

	$Query = "
	select
		*,
		date_format(regdt,'%Y.%m.%d %H:%i:%s') regdt
	FROM
		".GD_MEMBER_CRM."
	where
		sno = '$_GET[sno]' and
		m_no = '$_GET[m_no]'
	";
	$row = $db->fetch($Query);

	$_value_array = array();
	$_value_array['data'] = $row;
	$_value_array['mode'] = $_GET['mode'];

	$json = new Services_JSON();
	$output = $json->encode($_value_array);
	echo $output;
	exit();
}

if( $_GET['mode'] == "change" ){
	$Query = "
	update
		".GD_MEMBER_CRM."
	set
		contents = '$_GET[contents]',
		regdt = now(),
		counsel_Type = '$_GET[counsel_Type]'
	where
		sno = '$_GET[sno]' and
		m_no = '$_GET[m_no]'
	";
	$db->query($Query);

	$_value_array = array();
	$_value_array['mode'] = $_GET['mode'];
	$_value_array['page'] = $_GET['page'];

	$json = new Services_JSON();
	$output = $json->encode($_value_array);
	echo $output;
	exit();
}
?>