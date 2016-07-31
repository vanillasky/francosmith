<?php
require_once '../library.php';
// @todo : 개발시에만 사용할것
if (!defined('G_CONST_DEVELOPER_MODE') || G_CONST_DEVELOPER_MODE == false || !isset($_GET['q'])) exit;
$query = function_exists('gzcompress') ? gzuncompress(base64_decode(($_GET['q']))) : $_GET['q'];
if (!preg_match('/^select/i',($query))) exit('select sql only.');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="utf-8" />
	<title></title>
	<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css" rel="stylesheet">
	<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>

	<style type="text/css">
	/* desert scheme ported from vim to google prettify */ pre.prettyprint { display: block; background-color: #333;padding:10px; } pre .nocode { background-color: none; color: #000 } pre .str { color: #ffa0a0 } /* string  - pink */ pre .kwd { color: #f0e68c; font-weight: bold } pre .com { color: #87ceeb } /* comment - skyblue */ pre .typ { color: #98fb98 } /* type    - lightgreen */ pre .lit { color: #cd5c5c } /* literal - darkred */ pre .pun { color: #fff }    /* punctuation */ pre .pln { color: #fff }    /* plaintext */ pre .tag { color: #f0e68c; font-weight: bold } /* html/xml tag    - lightyellow */ pre .atn { color: #bdb76b; font-weight: bold } /* attribute name  - khaki */ pre .atv { color: #ffa0a0 } /* attribute value - pink */ pre .dec { color: #98fb98 } /* decimal         - lightgreen */  /* Specify class=linenums on a pre to get line numbering */ ol.linenums { margin-top: 0; margin-bottom: 0; color: #AEAEAE } /* IE indents via margin-left */ li.L0,li.L1,li.L2,li.L3,li.L5,li.L6,li.L7,li.L8 { list-style
-type: none } /* Alternate shading for lines */ li.L1,li.L3,li.L5,li.L7,li.L9 { }  @media print {   pre.prettyprint { background-color: none }   pre .str, code .str { color: #060 }   pre .kwd, code .kwd { color: #006; font-weight: bold }   pre .com, code .com { color: #600; font-style: italic }   pre .typ, code .typ { color: #404; font-weight: bold }   pre .lit, code .lit { color: #044 }   pre .pun, code .pun { color: #440 }   pre .pln, code .pln { color: #000 }   pre .tag, code .tag { color: #006; font-weight: bold }   pre .atn, code .atn { color: #404 }   pre .atv, code .atv { color: #060 } }
	</style>
</head>

<body>

<div class="container">
<!-- -->
<h1>SQL Analyzer</h1>
<ul>
	<li><a href="http://dev.mysql.com/doc/refman/5.0/en/explain-extended.html" target="_blank">explain manual</a></li>
</ul>

<hr>

<h4>Explain Extended</h4>
<table class="table table-bordered">
<thead>
<tr>
	<th>ID</th>
	<th>Select Type</th>
	<th>Table</th>
	<th>Access Type</th>
	<th>Possible Keys</th>
	<th>Used Key</th>
	<th>Key Length</th>
	<th>Ref</th>
	<th>Rows Examined</th>
	<th>Filtered</th><!-- rows × filtered / 100  -->
	<th>Extra</th>
</tr>
</thead>
<tbody>
<?
$_query = 'explain extended '.$query;
$rs = $db->query($_query);
while ($row = $db->fetch($rs,1)) {
?>
<tr>
	<td><?=$row['id']?></td>
	<td><?=$row['select_type']?></td>
	<td><?=$row['table']?></td>
	<td><?=$row['type']?></td>
	<td><?=$row['possible_keys']?></td>
	<td><?=$row['key']?></td>
	<td><?=$row['key_len']?></td>
	<td><?=$row['ref']?></td>
	<td><?=number_format($row['rows'])?></td>
	<td><?=number_format($row['filtered'],2)?>%</td>
	<td><?=$row['Extra']?></td>
</tr>
<? } ?>
</tbody>
</table>

<hr>

<h4>Original SQL Statement</h4>
<pre class="prettyprint lang-sql"><?=GODO_DB_formatter::format($query, false)?></pre>

<hr>

<h4>Converted SQL Statement (MySQL Optimizer)</h4>
<? $rs = $db->query('show warnings'); ?>
<? while ($note = $db->fetch($rs,1)) { ?>
<pre class="prettyprint lang-sql"><?=GODO_DB_formatter::format($note['Message'], false)?></pre>
<? } ?>

<!-- -->
</div>
</body>
</html>
