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
 * @brief Matching between code from Noalyss and TVA code from file
 *
 */

require_once DIR_IMPORT_ACCOUNT."/include/class_impacc_tva.php";

$tva = new Impacc_TVA();
// If some data are submitted we must save them before displaying the list
$save=HtmlInput::default_value_post("save", "#");
////////////////////////////////////////////////////////////
// Save modification
////////////////////////////////////////////////////////////
if ( $save != "#")
{
    $id=HtmlInput::default_value_post("pt_id", "0");
    $tva_code=HtmlInput::default_value_post("tva_code", "");
    $tva_id=HtmlInput::default_value_post("tva_id", 0);
    if ( $tva_id != 0 &&
         $tva_code !=""&&
         $id!=0
        )
    {
        if ($id < 0 ) $tva->insert($tva_id,$tva_code);
        else
            $tva->update($id,$tva_id,$tva_code);
    }
    
}
////////////////////////////////////////////////////////////
// Delete
////////////////////////////////////////////////////////////
$delete = HtmlInput::default_value_post("delete","#");
if ( $delete !="#")        
{    
    $id=HtmlInput::default_value_post("pt_id", "0");
    $tva->delete($id);
}
$tva->display_list();


?>
