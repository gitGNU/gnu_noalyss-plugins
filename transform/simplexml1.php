
<?php
$string = <<<XML
<?xml version='1.0'?>
<document>
    <cmd>login</cmd>
    <login>Richard</login>
</document>
XML;
                                                                       
                                          
$xml = simplexml_load_string($string);
print_r($xml);
$login = $xml->login;
echo 1;
print_r($login);
$login = (string) $xml->login;
echo 2;
print_r($login);
$login = (string) $xml->login;
echo 3;
print_r($login);
?> 
