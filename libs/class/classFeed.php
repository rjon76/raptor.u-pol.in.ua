<?php

class Feed {

    private $FeedTitle;
    private $FeedAtom;
    private $FeedItem;
    private $FeedItems;
	private $rssData = array();
	private $display 	= 20;
    private $title 		= 'Products';
   
	private $langs		= array('en','de','fr','jp','es','it','ru');

    public function __construct() {
	
		$this->FeedTitle = new ArrayObject(
	    array(
	    	'title' => '',
			'link' => '',
			'description' => '',
			'copyright' => 'Copyright '.date('Y').' '.$_SERVER['HTTP_HOST'],
			'generator' => 'VENGINSE Feed Generator',
			'language' => 'en',
			'image' =>'
			<url>http://'.$_SERVER['HTTP_HOST'].'/images/logo.gif</url>
            <title>Garbagecat Software Logo</title>
            <link>http://www.Garbagecat.com</link>
            <width>143</width>
            <height>41</height>'
	    )
	);

	$this->FeedAtom = new ArrayObject(
	    array('href' => '',
		  'rel' => 'self',
		  'type' => 'application/rss+xml'
	    )
	);

	$this->FeedItem = new ArrayObject(
	    array('title' => '',
		'link' => '',
		'description' => '',
		'guid' => '',
		'isPermaLink' => FALSE,
		'pubDate' => ''
	    )
	);

	$this->FeedItems = new ArrayObject();
    }

    /* Fills standart feed fields title, link and description with values.
      @param $titleValue:  Name of the title param
      @param $linkValue: Value of the title param. Empty by default
      @param $descValue: Value of the title param. Empty by default
      Return TRUE or FALSE as the result of operation.
    */
    public function FillFeedTitle($titleValue, $linkValue, $descValue, $lastBuildDate='') {
		$return = TRUE;
		if (!empty($titleValue)) {
			 $this->FeedTitle->offsetSet('title', htmlspecialchars($titleValue));
			 $this->FeedTitle->offsetSet('image', '<url>http://'.$_SERVER['HTTP_HOST'].'/images/logo.gif</url>
            <title>'.htmlspecialchars($titleValue).'</title>
            <link>'.$linkValue.'</link>
            <width>143</width>
            <height>41</height>');			 
		} else{
			!$return;
		}
		(!empty($linkValue)) ? $this->FeedTitle->offsetSet('link', $linkValue) : !$return;
		(!empty($descValue)) ? $this->FeedTitle->offsetSet('description', $descValue) : !$return;
		(!empty($lastBuildDate))? $this->FeedTitle->offsetSet('lastBuildDate', $lastBuildDate) : !$return;		

		return $return;
    }

    /* Sets feed title param by the key. Use at you own risk!
      @param $titleKey:  Name of the title param
      @param $titleValue: Value of the title param. Empty by default
      Return TRUE or FALSE as the result of operation.
    */
    public function SetFeedTitle($titleKey, $titleValue = '') {
	if (!empty($titleKey)) {
	    $this->FeedTitle->offsetSet($titleKey, $titleValue);
	    return TRUE;
	}
	return FALSE;
    }

    /* Get meaning of feed title values
      @param $titleKey:  Name of the title param. Empty by default
      Return feed title param by the key or full array of whole feed title
    */
    public function GetFeedTitle($titleKey = '') {
	if (!empty($titleKey)) {
	    if ($this->FeedTitle->offsetExists($titleKey)) {
		return $this->FeedTitle->offsetGet($titleKey);
	    }
	    return '';
	}
	return $this->FeedTitle->getArrayCopy();
    }

    /* Set the href parameter of <atom:link> tag.
      @param $atomLink:  Link address. If none is provided - taken from title link.
      Return TRUE or FALSE as the result of operation.
    */
    public function AddFeedAtom($atomLink = '') {
	if (empty($atomLink)) {
	    if ($this->FeedTitle->offsetExists('link')) {
		$this->FeedAtom->offsetSet('href', $this->FeedTitle->offsetGet('link'));
		return TRUE;
	    }
	    return FALSE;
	}
	$this->FeedAtom->offsetSet('href', $atomLink);
	return TRUE;
    }

    /* Add item tag content to the array of items
      @param $itemValues: array('title' => '', 'description' => '',
		'link' => '', 'guid' => '','pubDate'). Array of data for item node
      @param $guidPermaLink: attribute for <guid> tag
      Return void.
    */
    public function AddFeedItem($itemValues, $guidPermaLink = TRUE) {
	$cur_item = clone $this->FeedItem;
	for($iterator = $cur_item->getIterator();
	    $iterator->valid();
	    $iterator->next()) {
	    if (!empty($itemValues[$iterator->key()])) {
		$cur_item->offsetSet($iterator->key(), $itemValues[$iterator->key()]);
	    }
	}
	if ($guidPermaLink) {
	    $cur_item->offsetSet('isPermaLink', TRUE);
	}
	$this->FeedItems->append($cur_item);
    }

    /* Build complete xml file. Data taken from title and items nodes.
      @param none
      Return xml string.
    */
    public function BuildFeed() {
	$xml = '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>

';
	// build channel title
	for($iterator = $this->FeedTitle->getIterator();
		$iterator->valid();
		$iterator->next()) {
		$xml .= '<'.$iterator->key().'>'.$iterator->current().'</'.$iterator->key().'>'."\n";
		if ('title' == $iterator->key()) {
		    $xml .= $this->BuildFeedAtom();
		}
	}
	// build channel items
	$iterator =  new RecursiveArrayIterator($this->FeedItems);
	while($iterator->hasChildren()) {
	    $xml .= '<item>'."\n";
	    for($sub_iterator = $iterator->current()->getIterator();
		$sub_iterator->valid();
		$sub_iterator->next()) {
		if ('guid' == $sub_iterator->key()) {
		    $xml .= '<'.$sub_iterator->key().' isPermaLink ="'.(
			$sub_iterator->offsetGet('isPermaLink')
			? 'true'
			: 'false' ).'">'.
		    $sub_iterator->current().'</'.$sub_iterator->key().'>'."\n";
		}
		elseif ('isPermaLink' != $sub_iterator->key()) {
		    $xml .= '<'.$sub_iterator->key().'>'.$sub_iterator->current().'</'.$sub_iterator->key().'>'."\n";
		}
	    }
	    $xml .= '</item>'."\n";
	    $iterator->next();
	}
	$xml .= '</channel>
</rss>';
	return $xml;
    }

    /* Build <atom:link> tag.
      @param empty
      Return <atom:link> tag.
    */
    private function BuildFeedAtom() {
	$atom_tag = '';
	if ($this->FeedAtom->offsetGet('href')) {
	    $atom_tag = '<atom:link';
	    for($iterator = $this->FeedAtom->getIterator();
		$iterator->valid();
		$iterator->next()) {
		$atom_tag .= ' '.$iterator->key().'="'.$iterator->current().'"';
	    }
	    $atom_tag .= ' />';
	}
	//return '';
	return $atom_tag;
    }
/*-----------------------*/
public function createRss($platform = 0)
{
	if($_SERVER["REQUEST_URI"] != ''){
	
		$dom 	= $_SERVER['HTTP_HOST'];
		$product='';
		$isProduct 	= false;
		$this->type = isset($_GET['type']) ? $_GET['type'] : 'xml';
        $url 		= pathinfo($_SERVER["REQUEST_URI"]);
        $ex 		= explode('-',$url['filename']);
        $this->lang 		= $ex[0];
		if (!in_array($this->lang, $this->langs)){
			$this->lang = 'en';	
		}
        $countIn 	= count($ex);

        for($i = 1; $i < $countIn; $i++) {
           $product .= $ex[$i];

            if(($i + 1) != $countIn) {
                $product .= '-';
            }
        }
    

    // get db table name and domain adress
    	$constData 	= new ConstData();
		$db	= $constData->getConst('langsDb');

	    $query = 'SELECT p_id,
                     p_title,
                     p_download,
                     p_cat,
                     p_platform,
					 p_build,
					 p_page_link,

                     pb_version,
                     pb_build,
                     pb_id,
                     pb_date_release,

                     pc_text,
                     pc_order

	        FROM '.$db.'.product_changelog_'.$this->lang.'
    	    LEFT JOIN '.$db.'.product_builds ON pc_pbid = pb_id
	        LEFT JOIN '.$db.'.products ON pb_pid = p_id
			WHERE ((p_platform = '.$platform.' and '.$platform.' > 0) or ('.$platform.' = 0))';

    	    if($product != 'products' &&
        	   $product != 'mac-software' &&
	           $product != 'flash-software' &&
        	   $product != 'system-utilities') {
		
            	$query .= ' and p_nick = "'.$product.'"';
	            $isProduct = true;
    	    }

        	$query .= ' ORDER BY pb_date_release DESC, pc_order';

		
		    DB::executeQuery($query, 'pr');
    		$rows = DB::fetchResults('pr');

		    foreach($rows as $key => $val){
    		    if($isProduct){
            		$this->title = $val['p_title'];
		        }

		        $this->rssData[$val['pb_build']]['version'] 					= $val['pb_version'];
    		    $this->rssData[$val['pb_build']]['title'] 					= $val['p_title'];
	    	    $this->rssData[$val['pb_build']]['chLog'][$val['pc_order']] 	= $val['pc_text'];
	    	    $this->rssData[$val['pb_build']]['pb_date_release'] 			= $val['pb_date_release'] ;
				$this->rssData[$val['pb_build']]['page_link'] 				= $val['p_page_link'] ;

				$this->rssData[$val['pb_build']]['download'] =  'http://'.$dom.'/download/'.$val['p_download'];

	    	    if(count($this->rssData) >= $this->display){
    	    	    break;
	    	    }
		    }
		
		if(	(strpos($_SERVER['HTTP_USER_AGENT'],'Chrome') !== false or
			strpos($_SERVER['HTTP_USER_AGENT'],'Opera') !== false or
			strpos($_SERVER['HTTP_USER_AGENT'],'Safari') !== false) && ($this->type!=='rss')){
		
			$this->outputXML();
			
		}else{
			$this->outputRSS();
		}
	}
}
/*------------------------*/
private function outputXML()
{
		echo'<?xml version="1.0" encoding="utf-8"?>';
    	?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->lang; ?>" lang="<?php echo $this->lang; ?>">
		<head>
		<title><?php echo $this->title; ?> Updates Notification</title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['HTTP_HOST'];?>/styles/rss.css" />        
		</head>
		<body>
			<h1>Garbagecat <?php echo $this->title; ?> Updates Notification <?php if ($this->type!=='rss') echo "<a href='?type=rss' class='subscribe'>Subscribe</a>"; ?></h1><hr>
			<h2>Products update notification feed from Garbagecat Software company.</h2>
			<br/><br/>

	<?php
	foreach($this->rssData as $build => $data)
	{ ?>
		<h3><a href="<?php  echo $data['page_link']; ?>"><?php echo htmlentities($data['title'].' '.$data['version']); ?></a></h3>
		<?php echo $data['pb_date_release']; ?><br>
		<b>Build: <?php echo $build; ?></b>
        <?php echo ($data['download']) ? ' - <a href="'.$data['download'].'">Download.</a>' : ''; ?>
        <ul>
        <?php
	    foreach ($data['chLog'] as $order => $text)
	    {
	    	echo '<li>'.$text.'</li>';
    	}
		?>
		</ul><br>
		<?php
	}
	?>
	</body>
	</html>	
    <?php
}
/*------------------------*/
	private function outputRSS()
	{
		$lastBuildDate = '';
		$i=0;
    	foreach($this->rssData as $build => $data){

			$item['description'] 	= '<![CDATA[';
    	    $item['pubDate'] 		= gmdate('D, j M Y H:i:s \G\M\T', strtotime($data['pb_date_release']));
        	$item['title'] 			= htmlentities($data['title'].' '.$data['version']);

        	if ($i==0){
				$lastBuildDate = $item['pubDate'];
			}
			$i++;
        	
	        $desc = '<b>Build: '.$build.'</b> ';
	        
	        if($data['download']){
	        	$desc .= ' - <a href='.$data['download'].'>Download</a>';
	        }
	        
			if( count( $data['chLog'] ) != 1 )
			{
				$desc .= '<ul>';
        		foreach ($data['chLog'] as $order => $text)
        		{
            		$desc .= '<li>'.$text.'</li>';
        		}
        		$desc .= '</ul>';
			}
			else
			{
				$desc .= '<ul>';
        		foreach ($data['chLog'] as $order => $text)
        		{
            		$desc .= '<li>'.$text.'</li>';
        		}
        		$desc .= '</ul>';
			}

			$desc = str_replace('<ul><li><ul>','<ul>',$desc);
			$desc = str_replace('</ul></li></ul>','</ul>',$desc);
			
       		$item['description'] .= $desc;
       		$item['description'] .= ']]>';

			$item['link'] = $item['guid'] = $data['page_link'];     		
       		
        	$this->AddFeedItem($item, true);
    	}
    	$this->FillFeedTitle('Garbagecat '.$this->title.' Updates Notification',
                         		'http://'.$_SERVER['HTTP_HOST'],
                         		'Products update notification feed from '.$_SERVER['HTTP_HOST'].' company.',
								$lastBuildDate);
		$this->AddFeedAtom('http://'.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"]);
		header('Content-type: application/xml');
    	echo $this->BuildFeed();
	}
}

?>