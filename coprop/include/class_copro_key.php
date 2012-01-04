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
    function verify ($p_array,$b_dupl=true)
    {
        global $cn;
        extract ($p_array);
        if (strlen(trim ($cr_name))==0) 
            throw new Exception("Le nom est vide");
        if ($b_dupl )
        {
            $dupl=$cn->count_sql("select * from copro.clef_repartition where cr_name=$1",$cr_name);
            if ( $dupl >0 )
                throw new Exception("Une clef avec ce nom existe déja");
        }
        
    }

	function insert($p_array)
	{
		global $cn;
		extract($p_array);
		try
		{
                    $this->verify($p_array);
			$cn->start();
			$this->cr_id = $cn->get_value("insert into coprop.clef_repartition(cr_note,cr_name)
				values($1,$2) returning cr_id", array( strip_tags($cr_note), strip_tags($cr_name)));
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
                    $this->verify($p_array,false);
			$cn->start();
			$cn->exec_sql("update coprop.clef_repartition set 
				cr_note=$1,cr_name=$2
				where cr_id=$3",
					array( strip_tags($cr_note), strip_tags($cr_name),$this->cr_id));
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