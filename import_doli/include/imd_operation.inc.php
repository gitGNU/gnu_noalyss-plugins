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
 * @brief upload operation
 *
 */
require_once 'class_impdol_operation.php';

// step 1, select a file
if ( ! isset ($_POST['upload']) && ! isset ($_POST['check']) && ! isset($_POST['transfer']))
{
	require_once 'template/upload_operation.php';

	exit();
}
// step 2 save file into impdol.operation
if ( isset ($_POST['upload']))
{
	// save the file
	$io=new Impdol_Operation();
	$io->save_file();

	// record the file into the table operation
	$io->record();

	// show the result + error
	$io->check();
	$io->result();

}
// step 3, insert data into the target ledger
if ( isset ($_POST['transfer']))
{

}

?>
