<?php
/*
 Abstract Db Adapter interface
*/
interface DbAdapter {

    //function _connect();

    function initAdapter($settings);

    //function _quote($value);

    function query($sql, $params);

    function execute($sql, $params);

    function lastInsertId();

    function closeConnection();
}
?>