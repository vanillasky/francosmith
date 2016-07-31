<?
	### 네덱스 http://61.251.167.147:8080/jsp/tr/detailSheet.jsp?iSheetNo=

	$out = split_betweenStr($out,'<!---타이틀--------->','<!-- 하단 이미지');
?>

<style>
table, td {font: 12px 돋움, Dodum, Dodumche ; color:676663}

input {font-family : 돋움 ; font-size: 12px; color: #606060}
.input {
    border: 1px solid #B9B9AF; font-family : 돋움 ; font-size: 12px; color: #606060 ; height:20px
}
</style>

<script language=javascript>
function goBran(bran_nm, bran_tel) {
	var w       = "300";
	var h       = "180";
	var scroll  = "n";
	var resize  = "n";

	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;

	features = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',resizable='+resize;

	var url = "http://61.251.167.147:8080/jsp/tr/BranInfo.jsp?bran_nm="+bran_nm+"&bran_tel="+bran_tel;

	window.open(url, "", features);
}

function search_pic(t_date, t_sheet_no) {
	var w       = "500";
	var h       = "280";
	var scroll  = "n";
	var resize  = "n";

	var winl = (screen.width - w)  / 2;
	var wint = (screen.height - h) / 2;

	features = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',resizable='+resize;

	var url = "http://61.251.167.147:8080/jsp/tr/DlvImage.jsp?t_date="+t_date+"&t_sheet_no="+t_sheet_no;
	window.open(url, "", features);
}

function goEmp(name, hp, pda) {
	var w       = "300";
	var h       = "200";
	var scroll  = "n";
	var resize  = "n";

	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;

	features = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',resizable='+resize;

	var url = "http://61.251.167.147:8080/jsp/tr/EmpInfo.jsp?name="+name+"&hp="+hp+"&pda="+pda;

	window.open(url, "", features);
}
function goDlvEmp( EmpId, exprdate ) {
	document.location = "http://61.251.167.147:8080/jsp/dlv/listDlvEmp.jsp?iEmpId="+ EmpId + "&iDate="+exprdate;
}
function go_view_img(filepath) {
	var w       = "210";
	var h       = "180";
	var scroll  = "n";
	var resize  = "n";

	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;

	features = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',resizable='+resize;

	var url = "http://61.251.167.147:8080/jsp/tr/viewImage.jsp?filepath="+filepath;

	window.open(url, "", features);
}
function not_image() {

   alert('등록된 배송이미지가 없습니다.');
   return;
}
</script>

<base href="http://61.251.167.147:8080/" target="_blank">
<?=$out[0]?>