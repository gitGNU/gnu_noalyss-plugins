<?php

/*
 * Copyright (C) 2014 dany
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * Testing of class_intervat
 * phpunit --bootstrap bootstrap.php class_testintervat.php 
 * @author dany
 */
require_once 'class_intervat.php';
class class_intervatTest extends  PHPUnit_Framework_TestCase
{

    /**
     * Content the XML Document
     * @var DOMDocument
     */
    var $domdoc;

    function __construct()
    {
        $this->domdoc = new DOMDocument('1.0', 'ISO-8859-1');
    }

    function create_root()
    {
        $ns = 'http://www.minfin.fgov.be/ClientListingConsignment';
        $t = $this->domdoc->createElementNS($ns, 'ns2:ClientListingConsignment');

        $root = $this->domdoc->appendChild($t);
        $xmls = $root->setAttribute("xmlns", "http://www.minfin.fgov.be/InputCommon");

        $listing = $this->domdoc->appendChild($root);
        
        $nb = $this->domdoc->createAttribute('ClientListingsNbr');
        $nb->value = 1;
        $listing->appendChild($nb);

        $listing = $this->domdoc->appendChild($root);
    }

}
