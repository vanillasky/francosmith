<?

class Graph{

	var $type;
	var $query;
	var $key;
	var $out;
	var $spare;
	var $arrKey;
	var $calcuKey;
	var $ea;
	var $date;

	var $max;
	var $barSize;
	var $barMax;
	var $color = array();

	var $head = '';
	var $display_idx = false;


	function Graph(){
		$this->max		= 0;
		$this->barSize	= 14;
		$this->barMax	= 100;
		$this->color	= array(
						"#F56E00",
						"#CC371E",
						);
	}

	function reset(){
		$this->max = $this->sum = 0;
		unset($this->query);
		unset($this->key);
		unset($this->link);
		unset($this->out);
		unset($this->spare);
		unset($this->arrKey);
		unset($this->calcuKey);
		unset($this->ea);
		unset($this->date);
	}

	function getMaxQry(){
		global $db;
		$result = $db->query($this->query);
		while ($data=$db->fetch($result)){
			$tmpKey = ($this->arrKey[$data[0]]) ? $this->arrKey[$data[0]] : $data[0];
			$this->key[]		= $tmpKey;
			$this->out[$tmpKey]	= $data[1];
			$this->spare[$tmpKey]	= $data[2];
			if ($this->max<=$data[1]) $this->max = $data[1];
			$sum += $data[1];
		}
		$this->sum = $sum;
	}

	function getMax(){
		$this->key = @array_keys($this->out);
		if (!$this->sum) $this->sum = @array_sum($this->out);
		for ($i=0;$i<count($this->key);$i++){
			if ($this->max<=$this->out[$this->key[$i]]) $this->max = $this->out[$this->key[$i]];
		}
	}

	function drawGraph(){

		if ($this->query) $this->getMaxQry();
		else $this->getMax();
		$last = ($this->ea) ? $this->ea : count($this->key);

		$_page = $_GET['page'] ? $_GET['page'] : 1;
		$idx = $last * ($_page - 1);

		for ($i=0;$i<$last;$i++){
			$key	= ($this->ea) ? $i : $this->key[$i];
			if ($this->calcuKey){
				$tmp = "\$key = $key $this->calcuKey;";
				eval($tmp);
			}
			$bar_ici= @round($this->out[$key]/$this->max*$this->barMax);

			if (count($this->color)<=2){
				$color	= ($this->max==$this->out[$key]) ? $this->color[1] : $this->color[0];
			} else {
				$c_idx = floor(round($this->out[$key]/$this->sum*100)/10);
				$color = $this->color[$c_idx%count($this->color)];
			}

			if ($this->date){
				$yoil	= substr(date("D",mktime(0,0,0,substr($this->date,4,2),substr($key,-2),substr($this->date,0,4))),0,2);
				if ($yoil=="Su") $yoil = "<b style='font:8pt verdana; color:red'>$yoil</b>";
				$v_yoil = "<tr><td align=center style='font:8pt verdana'>$yoil</td></tr>";
			}

			if ($this->type){

				$bgcolor = ($i%2) ? "#f7f7f7" : "#ffffff";
				$key_link = ($this->link) ? "<a href='#.' onClick=\"detail('$key')\">$key</a>" : $key;

				$list .= "
				<tr bgcolor=$bgcolor>
					".($this->display_idx ? '<td width=40 align="center" style="font:9pt verdana">'.number_format(++$idx).'</td>' : '')."
					<td width=140 nowrap style='font:9pt verdana'>$key_link</td>
					<td  style='font:7pt verdana'>
						<span style='display:inline-block;width:$bar_ici;height:$this->barSize;background-color:$color'></span>
						&nbsp; ".$this->out[$key]."
					</td>
					<td width='40' align=right>
					".@round($this->out[$key]/$this->sum*100,1)."<span style='font:7pt verdana'>%</span>
					</td>
				</tr>
				";
			} else {
				$bar = ($bar_ici) ? "<div style='width:$this->barSize;height:$bar_ici;background-color:$color;font:1pt verdana'></div>" : "";
				$list .= "
				<td width=".(100/$last)."% align=center valign=bottom>

				<table cellpadding=0 cellspacing=0>
					<col span=2 align=center>
					<tr>
						<td>
							<div style='font:7pt tahoma'>".$this->out[$key]."</div>
							$bar
						</td>
					</tr>
					<tr>
						<td style='font:9pt ;padding-top:8'>".$key."</td>
					</tr>
					$v_yoil
				</table>

				</td>
				";
			}
		}
		echo ($this->type) ?
		"<table width=100% cellpadding=0 cellspacing=0>
			<col style='padding-right:10' align=right>
			".$this->head."
			$list
			<tr><td height=3></td></tr>
			<tr bgcolor='#f7f7f7'>
				".($this->display_idx ? '<td>&nbsp;</td>' : '' )."
				<td><b>Total</b></td>
				<td><b>".$this->sum."</b></td>
				<td>&nbsp;</td>
			</tr>
		</table>" :
		"<table width=100% cellpadding=0 cellspacing=0><tr>$list</tr></table>";
	}
}

?>
