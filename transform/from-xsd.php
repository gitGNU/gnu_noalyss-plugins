<?php

$xmldas=SDO_DAS_XML::create('NewLK-in_v0_7.xsd');
try {
	$doc=$xmldas->createDocument();
	$rdo=$doc->getRootDataObject();
	print($xmldas-saveString($doc));

} catch (Exception $e)
{
	var_dump($e->getMessage());
}


?>
