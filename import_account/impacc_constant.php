<?php

/*
 *   This file is part of PhpCompta.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))     die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief 
 * Contains standard info for the plugins
 */

global $g_impacc;
$g_impacc =dirname(__FILE__);
define ('DIR_IMPORT_ACCOUNT',$g_impacc);
global $adecimal,$athousand,$aseparator,$aformat_date;

$adecimal=array(
		array ('value'=>0,'label'=>' '),
		array ('value'=>1,'label'=>','),
		array ('value'=>2,'label'=>'.')
		);

$athousand=array(
		 array ('value'=>0,'label'=>' '),
		 array ('value'=>1,'label'=>','),
		 array ('value'=>2,'label'=>'.')
		 );
$aseparator=array(
		  array ('value'=>1,'label'=>','),
		  array ('value'=>2,'label'=>';')
		  );
$aformat_date=array(
		    array ('value'=>1,'label'=>'DD.MM.YYYY','format'=>'d.m.Y'),
		    array ('value'=>2,'label'=>'DD/MM/YYYY','format'=>'d/m/Y'),
		    array ('value'=>3,'label'=>'DD-MM-YYYY','format'=>'d-m-Y'),
		    array ('value'=>4,'label'=>'DD.MM.YY','format'=>'d.m.y'),
		    array ('value'=>5,'label'=>'DD/MM/YY','format'=>'d/m/y'),
		    array ('value'=>6,'label'=>'DD-MM-YY','format'=>'d-m-y'),
		    array ('value'=>7,'label'=>'YYYY-MM-DD','format'=>'Y.m.d')
		    );