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
 * @brief
 *
 */
require_once 'class_sql_impdol.php';
class Impdol_Operation
{

	function save_file()
	{
		if (trim($_FILES['csv_operation']['name']) == '')
		{
			alert('Pas de fichier donné');
			return -1;
		}
		$this->filename = tempnam($_ENV['TMP'], 'upload_');
		move_uploaded_file($_FILES["csv_operation"]["tmp_name"], $this->filename);

		$imp=new Impdol_Import_sql();
		$imp->setp('temp_file',$this->filename);
		$imp->setp('send_file',$_FILES['csv_operation']['name']);
		$imp->insert();
		$this->impid=$imp->getp("id");
	}

	function record()
	{
		$foperation= fopen($this->filename, 'r');
		$this->row_count = 0;
		$max = 0;
		while (($row = fgetcsv($foperation, 0, ";", '"')) !== false)
		{
			if ( count($row) != 10 )
			{
				echo "Attention $row ne contient pas 10 colonnes";
				continue;
			}
			$r=new impdol_Operation_tmp_Sql();
			$r->setp('dolibarr',$row[0]);
			$r->setp('date',$row[1]);
			$r->setp('qcode',$row[2]);
			$r->setp('desc',$row[3]);
			$r->setp('pj',$row[4]);
			$r->setp('amount_unit',$row[5]);
			$r->setp('amount_vat',$row[6]);
			$r->setp('rate',$row[8]);
			$r->setp('amount_total',$row[9]);
			$r->setp("import_id",$this->impid);
			$r->insert();
			$this->row_count++;
		}
		echo "Nombre de lignes enregistrées : ".$this->row_count;
		$import=new impdol_import_sql($this->impid);
		$import->setp("nbrow",$this->row_count);
		$import->update();
	}


}

?>
