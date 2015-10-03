<?php

include_once('classAjaxController.php');

$ajax = new AjaxController();
echo $ajax->getResult();
unset($ajax);

?>