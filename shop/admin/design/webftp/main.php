<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: Webftp Main
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/

include_once dirname( __file__ ) . '/conf.php';
?>

<SCRIPT LANGUAGE="JavaScript"> var curr_path = '<?=$curr_path;?>'; var webftpid = '<?=$webftpid;?>'; </SCRIPT>
<SCRIPT LANGUAGE="JavaScript" SRC="<?=$curr_path?>webftp.js"></SCRIPT>


<table border="0" cellspacing="0" cellpadding="0" align=center>
  <tr>
    <td><a href="javascript:;" onclick="file_upload();"><img src="<?=$img_path;?>webftp/top_menu_01.gif" border="0" align="absmiddle"></a></td>
	<!--td width=3></td>
	<td><a href="javascript:;" onclick="file_gdcopy();"><img src="<?=$img_path;?>webftp/top_menu_02.gif" border="0" align="absmiddle"></a></td-->
	<td width=3></td>
	<td><a href="javascript:;" onclick="mkdir();"><img src="<?=$img_path;?>webftp/top_menu_03.gif" border="0" align="absmiddle"></a></td>
	<td width=3></td>
	<td><a href="javascript:;" onclick="file_modity();"><img src="<?=$img_path;?>webftp/top_menu_04.gif" border="0" align="absmiddle"></a></td>
	<td width=3></td>
	<td><a href="javascript:;" onclick="file_delete();"><img src="<?=$img_path;?>webftp/top_menu_05.gif" border="0" align="absmiddle"></a></td>
  </tr>
</table>

<div style="padding-top:13px"></div>


<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr valign="top">
  	<td width="190">
  	  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top:1px #333333 solid;border-left:1px #333333 solid;border-right:3px #333333 solid;border-bottom:3px #333333 solid;">
  	    <tr>
  	      <td height="61" style="background-color:#f6f6f6;padding-bottom:10px;">
            <!-- 글로벌 메뉴 : Start -->
            <table border="0" cellspacing="0" cellpadding="0" align="center">
              <tr>
              <td><a href="javascript:;" onclick="dPathCookie( get_nowhighDir() );"><img src="<?=$img_path;?>webftp/left_menu_01.gif" border="0" align="absmiddle"></a></td>
              <td><a href="javascript:;" onclick="frame_search();"><img src="<?=$img_path;?>webftp/left_menu_02.gif" border="0" align="absmiddle"></a></td>
              <td><a href="javascript:;" onclick="frame_tree();"><img src="<?=$img_path;?>webftp/left_menu_03.gif" border="0" align="absmiddle"></a></td>
              </tr>
            </table>
            <!-- 글로벌 메뉴 : End -->
          </td>
        </tr>
        <tr>
          <td valign="top">
            <!-- 글로벌 프레임 : Start -->
            <iframe name="global_frame" src="" frameborder="0" width="100%" height="500" scrolling="auto"></iframe>
            <!-- 글로벌 프레임 : End -->
          </td>
        </tr>
      </table>
  	</td>
  	<td width="10"></td>
  	<td rowspan="2">
  	  <!-- 파일목록 프레임 : Start -->
  	  <iframe id="folder_frame" name="folder_frame" src="" frameborder="0" width="100%" height="800" scrolling="no"></iframe>
  	  <!-- 파일목록 프레임 : End -->
  	</td>
  </tr>
</table>

<script language="javascript"> frame_load(); </script>