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
$act = HtmlInput::default_value_get('act', null);
global $cn;
switch ($act)
{
    case 'listing_assujetti_xml':
        $request_id = HtmlInput::default_value_get('r_id', null);

        if ($request_id == null)
        {
            throw new Exception(_('Accès directe incorrecte'), 15);
        }
        require_once 'include/class_transform_declarant.php';
        require_once 'include/class_transform_representative.php';
        require_once 'include/class_transform_intervat.php';
        require_once 'include/class_transform_client.php';

        $declarant = new Transform_Declarant();
        $representative = new Transform_Representative;

        $declarant->from_db($request_id);
        $representative->from_db($request_id);
        $client = new Transform_Client;



        $xml = new Transform_Intervat;

        $xml->append_root();
        if ($representative->name != "")
        {
            $xml->append_representative($representative);
        }
        $xml->append_client_listing($declarant);
        $file = "listing_assujetti" . date('d.m.y.hi') . ".xml";
        $ref = $_ENV['TMP'] . "/" . $file;
        $xml->domdoc->save($ref);
        header('Content-type: application/bin');
        header('Pragma: public');
        header('Content-Disposition: attachment;filename="' . $file . '"', FALSE);
        $file_xml = fopen($ref, 'r');
        $in = fread($file_xml, filesize($ref));
        echo $in;
        break;
    case 'listing_assujetti_csv':
        $request_id = HtmlInput::default_value_get('r_id', null);

        if ($request_id == null)
        {
            throw new Exception(_('Accès directe incorrecte'), 15);
        }
        $aclient=$cn->get_array("select 
             c_name,c_vatnumber,c_amount_vat,c_amount_novat
             from
                transform.intervat_client join transform.intervat_declarant using (d_id)
                where
                r_id=$1"
                ,array($request_id)
                );

        $file = "listing_assujetti" . date('d.m.y.hi') . ".csv";
        $ref = $_ENV['TMP'] . "/" . $file;
        header('Content-type: application/csv');
        header('Pragma: public');
        header('Content-Disposition: attachment;filename="' . $file . '"', FALSE);
        $nb=count($aclient);
        $handle_file=fopen('php://output','w');
        for ($i=0;$i<$nb;$i++)
        {
            fputcsv($handle_file, $aclient[$i], ";");
        }
        break;
}
