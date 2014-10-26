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
 * @brief class for the table coprop.parameter
 *
 */
class Copro_Parameter
{
    // constructor
    function __construct() {
		global $cn;
        $Res=$cn->exec_sql("select * from coprop.parameter ");
        for ($i = 0;$i < Database::num_row($Res);$i++)
        {
            $row=Database::fetch_array($Res,$i);
            $key=$row['pr_id'];
            $elt=$row['pr_value'];
            // store value here
            $this->{"$key"}=$elt;
        }

    }
    /*!
     **************************************************
     * \brief  save the parameter into the database by inserting or updating
     *
     *
     * \param $p_attr give the attribut name
     *
     */
    function save($p_option,$p_value)
    {
		global $cn;
        // check if the parameter does exist
        if ( $cn->get_value('select count(*) from coprop.parameter where pr_id=$1',array($p_option)) != 0 )
        {
            $Res=$cn->exec_sql("update coprop.parameter set pr_value=$1 where pr_id=$2",
                                     array($p_value,$p_option));
        }
        else
        {

            $Res=$cn->exec_sql("insert into coprop.parameter (pr_id,pr_value) values( $1,$2)",
                                     array($p_option,$p_value));

        }

    }

}
?>
