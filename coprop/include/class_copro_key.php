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
 * @brief Gère les clefs
 *
 */
class Copro_key
{

	function insert($p_array)
	{
		global $cn;
		extract($p_array);
		try
		{
			$cn->start();
			$this->cr_id = $cn->get_value("insert into coprop.clef_repartition(cr_start,cr_end,cr_note,cr_name)
				values(to_date($1,'DD.MM.YYYY'),to_date($2,'DD.MM.YYYY'),$3,$4) returning cr_id", array(strip_tags($cr_start), strip_tags($cr_end), strip_tags($cr_note), strip_tags($cr_name)));
			for ($i = 0; $i < count($f_id); $i++)
			{
				if (${"part" . $f_id[$i]} == '')
					${"part" . $f_id[$i]} = 0;
				$cn->exec_sql("insert into coprop.clef_repartition_detail(lot_fk,crd_amount,cr_id) values($1,$2,$3)", array($f_id[$i], ${"part" . $f_id[$i]}, $this->cr_id));
			}
			$cn->commit();
		}
		catch (Exception $exc)
		{
			$cn->rollback();
			echo $exc->getTraceAsString();
		}
	}

	function update($p_array)
	{
		global $cn;
		extract($p_array);
		try
		{
			$cn->start();
			$cn->exec_sql("update coprop.clef_repartition set cr_start=to_date($1,'DD.MM.YYYY'),
				cr_end=to_date($2,'DD.MM.YYYY'),
				cr_note=$3,cr_name=$4
				where cr_id=$5",
					array(strip_tags($cr_start), strip_tags($cr_end), strip_tags($cr_note), strip_tags($cr_name),$this->cr_id));
			$cn->exec_sql("delete from coprop.clef_repartition_detail where cr_id=$1",array($this->cr_id));
			for ($i = 0; $i < count($f_id); $i++)
			{
				if (${"part" . $f_id[$i]} == '')
					${"part" . $f_id[$i]} = 0;
				$cn->exec_sql("insert into coprop.clef_repartition_detail(lot_fk,crd_amount,cr_id) values($1,$2,$3)", array($f_id[$i], ${"part" . $f_id[$i]}, $this->cr_id));
			}
			$cn->commit();
		}
		catch (Exception $exc)
		{
			$cn->rollback();
			echo $exc->getTraceAsString();
		}
	}

}

?>
