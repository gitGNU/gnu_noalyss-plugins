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
 * @brief gère la liaison entre lots et copropriétaire
 * table jnt_coprop_lot
 *
 */
class Copro_Lot
{
	function insert($p_array)
	{
		global $cn;
		extract($p_array);
		try
		{
			// find the coprop
			$coprop=new Fiche($cn);
			$coprop->get_by_qcode($w_copro);
			if ($coprop->id == 0) throw new Exception("Ce copropriétaire $w_copro n'existe pas",1);
			$count=$cn->get_value("select count(*) from coprop.coproprietaire where c_fiche_id=$1",
					array($coprop->id));
			if ( $count > 0 )				throw new Exception ("Copropriétaire déjà encodé",2);
			$cn->exec_sql("insert into coprop.coproprietaire(c_fiche_id)values($1)",
					array($coprop->id));
			// Find the lot.
			$str_warning="";$n_warning=0;
			for ($i=0;$i<count($lot);$i++)
			{
				$flot=new Fiche($cn);
				$flot->id=$lot[$i];

				$part=${'lot_per'.$lot[$i]};

				if ( trim($part)<>'' && isNumber($part)==0)
				{
					// avertissement et mettre à 100%
					$part=100;
				}
				// si w_lot pas inseré alors inserer
				$cn->exec_sql("insert into coprop.lot(l_fiche_id,l_part,coprop_fk)".
						" values ($1,$2,$3)",
						array($flot->id,$part,$coprop->id));

				// si w_lot insérer avec nouveau coprop. calculer somme jcl_part si total de jcl_part > 100 alors
				// avertissement
				$tot=$cn->get_value("select sum(l_part) from coprop.lot where l_fiche_id=$1",
							array($flot->id));
				if ($tot > 100 )
				{
					// avertisement affecté à +100%
				} elseif ($tot < 100 )
				{
					// avertisement affecté à - 100%

				}
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
			echo $e->getTraceAsString();

		}


	}
	function add_lot($p_array)
	{
		global $cn;
		extract($p_array);
		try
		{
			// find the coprop
			$coprop=new Fiche($cn);
			$coprop->id=$copro_id;
			if ($coprop->id == 0) throw new Exception("Ce copropriétaire $w_copro n'existe pas",1);
			$count=$cn->get_value("select count(*) from coprop.coproprietaire where c_fiche_id=$1",
					array($coprop->id));
			// Find the lot.
			$str_warning="";$n_warning=0;
			for ($i=0;$i<count($lot);$i++)
			{
				$flot=new Fiche($cn);
				$flot->id=$lot[$i];

				$part=${'Part'.$lot[$i]};

				if ( trim($part)<>'' && isNumber($part)==0)
				{
					// avertissement et mettre à 100%
					$part=100;
				}
				// si w_lot pas inseré alors inserer
				$cn->exec_sql("insert into coprop.lot(l_fiche_id,l_part,coprop_fk)".
						" values ($1,$2,$3)",
						array($flot->id,$part,$coprop->id));

				// si w_lot insérer avec nouveau coprop. calculer somme jcl_part si total de jcl_part > 100 alors
				// avertissement
				$tot=$cn->get_value("select sum(l_part) from coprop.lot where l_fiche_id=$1",
							array($flot->id));
				if ($tot > 100 )
				{
					// avertisement affecté à +100%
				} elseif ($tot < 100 )
				{
					// avertisement affecté à - 100%

				}
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
			echo $e->getTraceAsString();

		}


	}
	function update_lot($p_array)
	{
		global $cn;
		extract($p_array);
		try
		{
			$lot=$cn->get_array("select l_id,l_fiche_id,l_part from coprop.lot where coprop_fk=$1 ",
					array($copro_id));
			// Find the lot.
			$str_warning="";$n_warning=0;
			for ($i=0;$i<count($lot);$i++)
			{
				$flot=new Fiche($cn);
				$flot->id=$lot[$i]['l_fiche_id'];

				$part=${'num'.$lot[$i]['l_id']};

				if ( trim($part)<>'' && isNumber($part)==0)
				{
					// avertissement et mettre à 100%
					$part=100;
				}
				// si w_lot pas inseré alors inserer
				$cn->exec_sql("update coprop.lot set l_part=$1 where l_id=$2",
						array($part,$lot[$i]['l_id']));

				// si w_lot insérer avec nouveau coprop. calculer somme jcl_part si total de jcl_part > 100 alors
				// avertissement
				$tot=$cn->get_value("select sum(l_part) from coprop.lot where l_fiche_id=$1",
							array($flot->id));
				if ($tot > 100 )
				{
					// avertisement affecté à +100%
				} elseif ($tot < 100 )
				{
					// avertisement affecté à - 100%

				}
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
			echo $e->getTraceAsString();

		}


	}


}
?>
