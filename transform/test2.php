<?php
$doc = new DOMDocument();
$doc->formatOutput = true;
//set root element to correct cd prefix _and_ namespace:
$root = $doc->appendChild(
        $doc->createElementNS(
        $cd = 'http://www.crea.si/Schemas/2004/Document/ZBSxml/2.0',
        'cd:Document'));
//this is the bit of obscure magic: it will set the default namespace
$doc->createAttributeNS(
        'http://www.zbs-giz.si/Schemas/2006/ZBSxml/2.2',
        'xmlns');
//now continue as normal
$root->setAttributeNS(
        'http://www.w3.org/2001/XMLSchema-instance',
        'xsi:schemaLocation',
        'http://www.crea.si/Schemas/2004/Document/ZBSxml/2.0/ZbsCreaDoc.xsd');
$data = $root->appendChild($doc->createElementNS($cd,'cd:Data'));
$dataformat = $data->appendChild($doc->createElementNS($cd,'cd:DataFormat'));
$dataformat->appendChild($doc->createElementNS($cd,'cd:MimeType','text/xml'));
$content = $data->appendChild($doc->createElementNS($cd,'cd:Content'));

echo $doc->saveXML();

?>
