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
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief Match between Noalyss VAT code and VAT Code from CSV file
 * 
 */
class Impacc_TVA
{
    function display_list()
    {
        $cn=Dossier::connect();
        $sql="select * from impacc.parameter_tva left join public.tva_rate using(tva_id) order by tva_code";
        $ret=$cn->get_array($sql);
        require_once DIR_IMPORT_ACCOUNT."/template/tva_parameter_list.php";
    }
    /// The TVA ID must exist
    function check_exist($tva_id)
    {
        
    }
    /// the TVA Code must be unique
    function check_valid($tva_code,$pt_id)
    {
        $cn=Dossier::connect();
        $count=$cn->get_value(
                "select 
                    count(*) 
                 from 
                    impacc.parameter_tva 
                 where
                    pt_id <> $1
                    and tva_code=$2",
                    array($pt_id,$tva_code)
                );
        return $count;
    }
    function insert($tva_id,$tva_code)
    {
        if ( $this->check_valid($tva_code,-1) > 0)
        {
            throw new Exception(_("Duplicate"));
        }
        $cn=Dossier::connect();
        
        $cn->exec_sql("insert into impacc.parameter_tva(tva_id,tva_code) 
                values ($1,$2)
                returning pt_id", array($tva_id,$tva_code));
    }
    function update($id,$tva_id,$tva_code)
    {
        if ( $this->check_valid($tva_code,$id) > 0)
        {
            throw new Exception(_("Duplicate"));
        }
        $cn=Dossier::connect();
        $cn->exec_sql("update impacc.parameter_tva set tva_id=$1,tva_code=$2 where pt_id=$3",
                array($tva_id,$tva_code,$id));
    }
    function delete($p_id)
    {
        $cn=Dossier::connect();
        $cn->exec_sql("delete from impacc.parameter_tva where pt_id=$1",array($p_id));
    }
    function display_modify($p_tva) 
    {
        $cn=Dossier::connect();
        
        // Load parameter
        $sql="select * from impacc.parameter_tva left join public.tva_rate using(tva_id) where pt_id=$1";
        $ret=$cn->get_array($sql,array($p_tva));
        $tva_id=$ret[0]["tva_id"]; 
        $comment=h($ret[0]['tva_comment']);
        $tva_code=h($ret[0]['tva_code']);
        $id=$ret[0]['pt_id'];
        $label=$ret[0]['tva_label'];
        
        // Display Box
        require_once DIR_IMPORT_ACCOUNT."/template/tva_parameter_detail.php";
    }
    function display_add()
    {
        $tva_id=""; 
        $comment="";
        $tva_code="";
        $id=-1;
        $label="";
        // Display empty Box
        require_once DIR_IMPORT_ACCOUNT."/template/tva_parameter_detail.php";
    }
}
