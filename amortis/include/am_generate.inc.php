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
		return;
	}
	catch (Exception $e)
	{
            echo $e->getMessage();
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
	}
}

if (isset($_POST['save']))
{
	try
	{
		$cn->start();
                $p_group=HtmlInput::default_value_post("grouped", -1);
                if ( $p_group == 1)
                {
                    $group=true;
                } else if ($p_group == 0)
                {
                    $group=false;
                }
                if ( isset ($group))
                    $m = $am->save($_POST,$group);
                else
                    throw new Exception (_('Missing parameter grouped'));
		/*
		 * if $m is not empty, some mat. were already encoded
		 */
		if ($m != '')
		{
			throw new Exception($m);
		}
		$cn->commit();
		echo '<div class="content" style="width:80%;margin-left:10%">';

		echo  h2("Opération sauvée") ;
                echo '<ol>';
                for ($i=0;$i < count($am->saved_operation['desc']);$i++)
                {
                    echo '<li>';
                    echo sprintf('<A class="detail" style="display:inline;text-decoration:underline" HREF="javascript:modifyOperation(%d,%d)">%s</A>', $am->saved_operation['jr_id'][$i], dossier::id(), $am->saved_operation['internal'][$i]." ".$am->saved_operation['desc'][$i]);
                    echo '</li>';
                }
                echo '</ol>';
		echo '</div>';
		return;
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