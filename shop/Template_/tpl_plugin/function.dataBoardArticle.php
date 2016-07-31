<?php

/* Return Board Article Data Function */

function dataBoardArticle( $id, $limit=0 ){

	if ( $id == '' ) return;
	global $db;

	@include dirname(__FILE__).'/../../conf/bd_'.$id.'.php';
	if( $db->tableCheck(GD_BD_ . $id) == true ){
		$board = array();
		//$query = "select * from " . GD_BD_ . $id . " where idx like 'a%' order by no desc";
		$query = "select * from " . GD_BD_ . $id . " where idx like 'a%' and sub='' order by no desc";	# 2007-09-05 메인에서 답글을 제외한 게시글 가져오도록 modify
		if ( $limit > 0 ) $query .= " limit " . $limit;
		$res = $db->query($query);
		while ( $data = $db->fetch( $res, 1 ) ){
			if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
				$data = validation::xssCleanArray($data, array(
					validation::DEFAULT_KEY => 'text',
					'subject' => array($bdUseXss,'ent_noquotes', $bdAllowPluginTag , $bdAllowPluginDomain),
					'contents' => array($bdUseXss,'ent_noquotes', $bdAllowPluginTag , $bdAllowPluginDomain),
				));
			}

			$data = array_merge( array( 'id' => $id ), $data );
			$div = explode("|",$data[new_file]);
			if (@getimagesize(dirname(__FILE__)."/../../data/board/$id/t/$div[0]")){
				$data[imgnm] = $div[0];
				$data[img] = "<img src='../data/board/$id/t/$div[0]' onClick=\"popupImg('../data/board/$id/$div[0]')\" style='cursor:pointer' width=100>";
			}
			$board[] = $data;
		}

		return $board;
	}
}
?>