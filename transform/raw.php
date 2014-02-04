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

switch ($act)
{
    case 'listing_assujetti':
        $request_id = HtmlInput::default_value_get('r_id', null);

        if ($request_id == null)
        {
            throw new Exception(_('Accès directe incorrecte'), 15);
        }
        require_once 'include/class_transform_declarant.php';
        require_once 'include/class_transform_representative.php';
        require_once 'include/class_transform_intervat.php';

        $declarant = new Transform_Declarant();
        $representative = new Transform_Representative;

        $declarant->from_db($request_id);
        $representative->from_db($request_id);

        $xml = new Transform_Intervat;

        $xml->append_root();
        $xml->append_client_listing($declarant);
        $file = "listing_assujetti" . date('d.m.y.h.mi').".xml";
        $ref = $_ENV['TMP'] . "/" . $file;
        echo $xml->domdoc->save($ref);
        header('Content-type: application/bin');
        header('Pragma: public');
        header('Content-Disposition: attachment;filename="'.$ref.'"',FALSE);
        $file_xml=fopen($ref,'r');
        while ($in=fread($file_xml,8192)) { echo $in; }
        break;
}