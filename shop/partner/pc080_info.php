<?
include "../lib/library.php";
@include "../conf/phone.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<HEAD>
<TITLE> 고객정보 </TITLE>
<META NAME="Generator" CONTENT="">
<META NAME="Author" CONTENT="">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">
<style>
body,table,input,select {font:8pt Gulim}
a {text-decoration:none;color:#000000}
input {border:1 solid #cccccc}
</style>
</HEAD>
<body background="../admin/img/bg.jpg">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col width="60" bgcolor=""><col>
<?
$from = $_POST['From'];
$to = $_POST['To'];

$query = "select a.m_id,a.name,a.phone,a.mobile,a.regdt,a.last_login,b.grpnm from ".GD_MEMBER." a left join ".GD_MEMBER_GRP." b on a.level=b.level where replace(phone, '-', '') = '$to' or  replace(mobile, '-', '') = '$to' or email = '$to' limit 1";

$data = $db->fetch($query);

if($data && $from == $set['phone']['pc080_id']){
?>
<tr><td><img src="../admin/img/cs_id01.gif" border=0></td><td><?=$data['m_id']?></td></tr>
<tr><td><img src="../admin/img/cs_name02.gif" border=0></td><td><?=$data['name']?></td></tr>
<tr><td><img src="../admin/img/cs_phone03.gif" border=0></td><td><?=$data['phone']?></td></tr>
<tr><td><img src="../admin/img/cs_mobile04.gif" border=0></td><td><?=$data['mobile']?></td></tr>
<tr><td><img src="../admin/img/icon_regdt.gif" border=0></td><td><?=$data['regdt']?></td></tr>
<tr><td><img src="../admin/img/icon_lastlogin.gif" border=0></td><td><?=$data['last_login']?></td></tr>
<tr><td><img src="../admin/img/icon_mgroup.gif" border=0></td><td><?=$data['grpnm']?></td></tr>
<tr><td colspan="2" height="30">&nbsp;</td></tr>
<tr><td colspan="2" align="center"><a href="../admin/member/list.php" target="_blank"><img src="../admin/img/btn_crmdetail.gif" border=0></a></td></tr>
<?
}else{
?>
<tr><td colspan="2" height="70">&nbsp;</td></tr>
<tr><td colspan="2" align="center">일치하는 회원정보가 없습니다.</td></tr>
<?}?>
</table>
</body>
</html>