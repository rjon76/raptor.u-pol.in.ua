<?php
/*
unset($_GObjects);
*/
DB::unsetInstance();
print Error::showErrorMessages();
Error::unsetInstance();
IniParser::unsetInstance();

?>