<?php

include_once(ENGINE_PATH.'interface/interfaceLocalExt.php');
include_once(ENGINE_PATH.'class/classProducts.php');

class allProductsBlock implements LocalExtInterface {
    private $products;

    public function __construct(Page $page) {
        if(is_object($page)) {
            $this->products = new Products($page);
        }
    }

    public function __destruct() {
        $this->products = NULL;
    }

    public function parseSettings() {
    }

    public function getResult() {
        $res = $this->products->getProductsFull(TRUE,FALSE);
        return $res;
    }
}
?>