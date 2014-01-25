<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Copyright Author Dany De Bontridder danydb@aevalys.eu

$doc=new DOMDocument('1.0','ISO-8859-1');
$doc->formatOutput = true;

// $ns="http://www.w3.org/2001/XMLSchema";
$ns='http://www.minfin.fgov.be/ClientListingConsignment';
$t=$doc->createElementNS($ns,'ns2:ClientListingConsignment');

$root=$doc->appendChild($t);

// $root->setAttributeNS($ns,'ns2:iso','http://www.minfin.fgov.be/IsoTypes');
$listing=$doc->appendChild($root);
$xmls=$root->setAttribute("xmlns","http://www.minfin.fgov.be/InputCommon");

//$xmls->value="http://www.minfin.fgov.be/InputCommon";
$listing=$doc->appendChild($root);


$nb=$doc->createAttribute('ClientListingsNbr');
$nb->value=1;
$listing->appendChild($nb);

$declarant=$doc->createElementNS('http://www.minfin.fgov.be/ClientListingConsigment','ns2:Declarant');
$vatnumber=$doc->createElement("VATNumber","dany");
$name=$doc->createElement("Name","dany");
$street=$doc->createElement("Street","dany");
$postcode=$doc->createElement("PostCode","dany");
$city=$doc->createElement("City","dany");
$country=$doc->createElement("CountryCode","dany");
$email=$doc->createElement("EmailAddress","dany");
$phone=$doc->createElement("Phone","0000000");



$declarant_xml=$root->appendChild($declarant);
$declarant_xml->appendChild($vatnumber);
$declarant_xml->appendChild($name);
$declarant_xml->appendChild($street);
$declarant_xml->appendChild($postcode);
$declarant_xml->appendChild($city);
$declarant_xml->appendChild($country);
$declarant_xml->appendChild($email);
$declarant_xml->appendChild($phone);

$periode=$doc->createElementNS("http://www.minfin.fgov.be/ClientListingConsignment","ns2Periode","1402");
$root->appendChild($periode);

$test=$doc->saveXML();
$doc->save('test.xml');
echo $test;
echo ' file text.xml created'.PHP_EOL;
$doc->normalizeDocument();
echo $doc->saveXML();
?>
