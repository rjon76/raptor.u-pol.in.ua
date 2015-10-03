<?php
class cl_pages_info
{
	public function get_url_mask($start='', $param='')
	{
		return array('start' => $start.'?'.(($param!=='') ? $param.'&':'').'num_page=', 'end' =>'');
//	return array('start' => "index.php?module=$module&page=$page".$param.'&'. ($parent_id == '*' ? '' : "parent_id=$parent_id&") . "num_page=", 'end' => '');
	}
	public function get_url_mask2($module, $page = 'index', $parent_id = '*', $param='', $start='num_page')
	{
		return array('start' => "index.php?module=$module&page=$page".$param.'&'. ($parent_id == '*' ? '' : "parent_id=$parent_id&") .$start. "=", 'end' => '');
	}
	public function get_url_mask_front($page = 'index')
	{
		return array('start' => "$page/", 'end' => '.html');
	}
	
	public function get_pages_text($num_page, $page_count, $url_mask, $link_class = 'nav_page', $current_page_class = 'nav_current_page')
	{
		$limit = IniParser::getInstance()->getSettring('count', 'records_on_page');
		$pages_text = "";		
	//	$pages_text = "<span class='$link_class'>Îòîáðàæàòü ïî</span> <input type=text name='records_on_page' value=$limit style='width:50px'>";
		$from = $num_page - (int)$limit;
		if ($from < 0)
			$from = 0;
		$to = $num_page + (int)$limit;
		if ($to > $page_count - 1)
			$to = $page_count - 1;
		if($num_page > 0)
			$pages_text .= "<a href='".$url_mask[start].($num_page-1).$url_mask[end]."' title='<<' class='prev_$link_class'></a>&nbsp;&nbsp;&nbsp;";
		if ($page_count > 1)
		{
			for ($i = $from; $i <= $to; $i++)
			{
				if ($i != $num_page)
					$pages_text .= "<a href='$url_mask[start]$i$url_mask[end]' title='".($i+1)."' class='$link_class'>".($i+1)."</a>";
				else
					$pages_text .= "<span class='".$current_page_class."'>".($i+1)."</span>";
				if ($i <= $to - 1)
					$pages_text .= "&nbsp;&nbsp;&nbsp;";
			}
		}
		if($num_page < $page_count - 1)
			$pages_text .= "&nbsp;&nbsp;&nbsp;<a href='".$url_mask[start].($num_page+1).$url_mask[end]."' title=' >>' class='next_$link_class' style='text-decoration:none;'></a>";
		
		return $pages_text;
	}
}

?>