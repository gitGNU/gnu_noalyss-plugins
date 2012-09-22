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
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/**
 * @file
 * @brief liste des déclarations + modification, création...
 *
 */
require_once 'class_rapav_formulaire.php';
if ( isset($_POST['form_def_sub']))
{
	RAPAV_formulaire::save_definition($_POST);
	exit();
}
if (isset ($_POST['form_param_sub']))
{
	RAPAV_Formulaire::save_parameter($_POST);
}
if (isset ($_POST['add_form']))
{
	$form= new RAPAV_Formulaire();
	$form->f_title=trim($_POST['titre']);
	$form->f_description=trim($_POST['description']);
	$form->insert();
}
RAPAV_formulaire::listing();

?>
