<?
header('Content-Type: text/html; charset=euc-kr');

//쿠키 삭제!!
if( $_COOKIE['Cookie_DivmoveID'] ) SetCookie ("Cookie_DivmoveID","",time()-3600,"/");
//쿠키설정!!

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