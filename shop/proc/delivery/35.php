<?
	# GSM 국제택배 http://idn.inlos.com/CST/CST2/CST2044.aspx?Hawb=
	$out = str_replace('src="/W','src="http://idn.inlos.com/W',$out);
	$out = str_replace('onclick="javascript:window.close();" src="http://idn.inlos.com/WebCommon/images/button/btn_015.gif" align="absMiddle" border="0"','',$out);
	$out = str_replace('<IMG onmouseover="this.style.cursor=','<span style=display:none; onmouseover="this.style.cursor=',$out);
	$out = str_replace('<tr>
					<td bgColor="#d5d5d5" height="1"></td>
				</tr>','',$out);
	$out = str_replace('<tr>
								<td class="title" height="25"><IMG height="22" src="http://idn.inlos.com/WebCommon/images/button/bullet04.gif" width="26" align="absMiddle">
									<STRONG>&nbsp;House정보</STRONG><!---CST2043-->
								</td>
								<td><IMG height="15" src="http://idn.inlos.com/WebCommon/images/button/help_btn.gif" width="47" align="right"></td>
							</tr>','',$out);

	echo $out;
?>