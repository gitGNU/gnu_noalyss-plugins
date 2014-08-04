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
/* $Revision$ */

// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be

/*!\file
 * \brief contains all the constant for this plugin
 */

global $adecimal,$athousand,$aseparator,$aformat_date,$aheader;
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
		    array ('value'=>1,'label'=>'DD.MM.YYYY'),
		    array ('value'=>2,'label'=>'DD/MM/YYYY'),
		    array ('value'=>3,'label'=>'DD-MM-YYYY'),
		    array ('value'=>4,'label'=>'DD.MM.YY'),
		    array ('value'=>5,'label'=>'DD/MM/YY'),
		    array ('value'=>6,'label'=>'DD-MM-YY'),
		    array ('value'=>7,'label'=>'YYYY-MM-DD')
		    );
$aheader=array(
	       array('value'=>-1,'label'=>'-- Non utilisé --'),
	       array('value'=>0,'label'=>'Date'),
	       array('value'=>1,'label'=>'Montant'),
	       array('value'=>2,'label'=>'Libelle'),
	       array('value'=>3,'label'=>'Numéro opération'),
	       array('value'=>4,'label'=>'Tiers'),
	       array('value'=>5,'label'=>'Info supplémentaire')
	       );
?>
