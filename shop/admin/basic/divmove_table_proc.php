<?
header('Content-Type: text/html; charset=euc-kr');

//��Ű ����!!
if( $_COOKIE['Cookie_DivmoveID'] ) SetCookie ("Cookie_DivmoveID","",time()-3600,"/");
//��Ű����!!

$DateStr = explode(",", $_GET['boxdiv']);
$DateCnt = count($DateStr);
$no = 0;

for( $i=0; $i < $DateCnt; $i++ ){

	if( $DateStr[$i] != "" ){ 
		$boxdiv_arr[$no] = $DateStr[$i];
		$no++;
	}
}

$cdi=implode(",",$boxdiv_arr);

setCookie("Cookie_DivmoveID",$cdi,time()+86400*30,"/");

echo "ok";
?>