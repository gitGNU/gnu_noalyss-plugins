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
 * \brief let you generate the accounting for the paid off for a selected
 *  year
 */
require_once('class_am_generate.php');
require_once('class_amortissement_sql.php');
global $cn;

$am = new Am_Generate();

if (isset($_POST['generate']))
{
	try
	{
		/*
		 * propose writing
		 */
		if ($am->propose_writing($_POST) == false)
		{
			echo '<div class="content" style="width:80%;margin-left:10%">';
			echo $am->input($_POST);
			echo '</div>';
		}
		exit();
	}
	catch (Exception $e)
	{
		var_dump($e->getTraceAsString());
	}
}

if (isset($_POST['save']))
{
	$ledger = new Acc_Ledger($cn, $_POST['p_jrn']);
	try
	{
		$cn->start();
		$ledger->save($_POST);

		$jr_id = $cn->get_value("select jr_id from jrn where jr_internal=$1", array($ledger->internal));

		$m = $am->save($_POST, $ledger->internal);
		/*
		 * if $m is not empty, some mat. were already encoded
		 */
		if ($m != '')
		{
			throw new Exception($m);
		}
		echo '<div class="content" style="width:80%;margin-left:10%">';

		$p_mesg = "Opération sauvée : " . $ledger->internal;
		echo sprintf('<A class="detail" style="display:inline;text-decoration:underline" HREF="javascript:modifyOperation(%d,%d)">%s</A>', $jr_id, dossier::id(), $p_mesg);

		echo '</div>';
		$cn->commit();
		exit();
	}
	catch (Exception $e)
	{
		echo alert($e->getMessage());
		$cn->rollback();
	}
}

echo '<div class="content" style="width:80%;margin-left:10%">';
echo $am->input($_POST);


echo '</div>';