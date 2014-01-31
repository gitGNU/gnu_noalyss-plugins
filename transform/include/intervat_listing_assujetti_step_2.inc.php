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

// Copyright Author Dany De Bontridder danydb@aevalys.eu
var_dump($_POST);
/**
 * save data into db and display them before creating the XML
 */
global $cn;
require_once 'class_transform_sql.php';
/**
 * Save a new request
 */
$request = new Transform_Request_SQL();
$request->insert();

$representative = new Transform_Representative();
$representative->data->r_id = $request->r_id;
$representative->fromPost();
$representative->insert();

$declarant = new Transform_Declarant();
$declarant->data->r_id = $request->r_id;
$declarant->fromPost();
$declarant->insert();

/*
 * Save the file
 */
if (count($_FILES) == 0)
    throw new Exception(_('Aucun fichier donné'), 1);

$name = $_FILES['client_assujetti']['name'];

if (strlen($_FILES['client_assujetti']['tmp_name'][0]) != 0)
{
    $new_name = tempnam($_ENV['TMP'], 'client_assujetti');
    if (!move_uploaded_file($_FILES['file_upload']['tmp_name'][$i], $new_name))
    {
        throw new Exception(_('Impossible de sauver ce fichier'), 2);
    }
    $file = fopen($new_name, "r");
    $i = 0;
    $o_data=array();
    try
    {
        $cn->start();
        while ($data = fgetcsv($file, 0, ";"))
        {
            $i++;
            if (count($data) != 4)
            {
                $o_data[$i]= _('Ligne non importée'). join(' - ', $data);
                continue;
            }
            /*
             * insert into transform.intervat_client
             */
            $o_data[$i] = new Intervat_Client_SQL();
            $o_data[$i]->d_id = $declarant->data->d_id;
            $o_data[$i]->c_name = $data[0];
            $o_data[$i]->c_issuedby = "BE";
            $o_data[$i]->c_vatnumber = $data[1];
            $o_data[$i]->c_amount_vat = $data[2];
            $o_data[$i]->c_amount_novat = $data[3];
            $o_data[$i]->insert();
        }
        $cn->commit();
    } catch (Exception $ex)
    {
        $cn->rollback();
        throw new Exception(_('Ne peut pas ajouter ').h($o_data[$i]->c_name).'-'.h($o_data[$i]->c_vatnumber),3);
    }
    
}
/**
 * Show the result 
 */
?>

