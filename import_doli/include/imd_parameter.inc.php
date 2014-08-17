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

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/**
 * @file
 * @brief matching between tva and rate
 *
 */
global $cn;
if (isset($_POST['ftvaadd']))
{
	extract($_POST);
	try
	{
		if (isNumber($pt_rate) == 0)
			throw new Exception("le taux n'est pas un nombre");
		if ($pt_rate < 0 || $pt_rate > 100)
			throw new Exception("le taux est invalide");
		$tva = new Acc_Tva($cn, $tva_id);
		if ($tva->load() == -1)
			throw new Exception('Cette tva est invalide');
		$sql = "insert into impdol.parameter_tva(tva_id,pt_rate) values ($1,$2)";
		$cn->exec_sql($sql, array($_POST['tva_id'], $_POST['pt_rate']));
	}
	catch (Exception $e)
	{
		alert($e->getMessage());
	}
}
if (isset($_POST['mod']))
{
	extract ($_POST);
	$aparm = $cn->get_array("select pt_id from impdol.parameter_tva");
	try
	{
		for ($i = 0; $i < count($aparm); $i++)
		{
			if (isset(${'tva_' . $aparm[$i]['pt_id']}))
			{
				$pt_rate = ${'rate' . $aparm[$i]['pt_id']};
				$tva_id = ${'tva_' . $aparm[$i]['pt_id']};
				if (isNumber($pt_rate) == 0)
					throw new Exception("le taux n'est pas un nombre");
				if ($pt_rate < 0 || $pt_rate > 100)
					throw new Exception("le taux est invalide");
				$tva = new Acc_Tva($cn, $tva_id);
				if ($tva->load() == -1)
					throw new Exception('Cette tva est invalide');
				$sql = "update impdol.parameter_tva set tva_id = $1, pt_rate = $2 where pt_id=$3";
				$cn->exec_sql($sql, array($tva_id, $pt_rate,$aparm[$i]['pt_id']));
			}
		}
	}
	catch (Exception $e)
	{
		alert($e->getMessage());
	}
}
/**
 * get data from database
 */
$atva = $cn->get_array("select * from impdol.parameter_tva order by pt_rate");
require 'template/parameter_tva_add.php';
echo '<form method="POST">';
require 'template/parameter.php';
echo HtmlInput::submit("mod", "Modification");

echo '</form>';
?>
