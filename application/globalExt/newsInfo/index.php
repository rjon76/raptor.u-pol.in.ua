<?php

include_once(ENGINE_PATH.'class/classNews.php');

class NewsInfo {

    private $news;

    public function __construct() {
        $this->news = new News();
    }

    public function __destruct() {
        $this->news = NULL;
    }

    public function getNews() {
		//var_dump($this->news->getNews());
        return $this->news->getNews();
    }
}

?>