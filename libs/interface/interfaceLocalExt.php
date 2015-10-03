<?php
/*
 Инетерфейс для классов экстеншенов
*/
interface LocalExtInterface {
    /*
     Настройки экстеншена, выборка настроек из ini-файла
    */
    function parseSettings();
    /*
     Возвращает результат работы экстеншена
    */
    function getResult();
}

?>