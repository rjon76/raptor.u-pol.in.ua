<?php

class localHTMLRenderer {
	private $formData; 

	public function __construct() {
	$page = VBox::get('Page');
        $pageAddress = $page->address['uri_address'];
        $pageLanguage = $page->language;
        $addressPrefix = ($pageLanguage != 'en' ? '/'.$pageLanguage : '');

       $this->formData = array(
            array('name' => $page->getLocalizedString('features'), 'address' => '/pc-inventory.html', 'title'=>$page->getLocalizedString('features_title')),
           array('name' => $page->getLocalizedString('download'), 'address' => '/download.html', 'title'=>$page->getLocalizedString('download_title'),'attr'=>'class="download"'),
		   array('name' => $page->getLocalizedString('pricing'), 'address' => '/pricing.html', 'title'=>$page->getLocalizedString('pricing_title')),
		   array('name' => $page->getLocalizedString('help'), 'address' => 'help.html', 'title'=>$page->getLocalizedString('Help_title')),
		   array('name' => $page->getLocalizedString('contacts'), 'address' => '/contacts.html', 'title'=>$page->getLocalizedString('contacts_title')),
		   array('name' => $page->getLocalizedString('Blog'), 'address' => 'http://blog.clearapps.com', 'title'=>$page->getLocalizedString('Blog_title'))
        );

        for($i = 0; $i < count($this->formData); $i++) {
            if($pageAddress == $this->formData[$i]['address']) {
                $this->formData[$i]['selected'] = TRUE;
            } else {
                $this->formData[$i]['selected'] = FALSE;
            }
        }
    }

    public function __destruct() {
	$this->formData = NULL;
    }


    public function getResult() {
	return $this->formData;
    }
}

?>