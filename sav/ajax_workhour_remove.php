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

// Copyright 2014 Author Dany De Bontridder danydb@aevalys.eu

// require_once '.php';
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief 
 * @param type $name Descriptionara
 */
require_once 'include/class_sav_workhour.php';

$workhour=HtmlInput::default_value_get('workhour_id', -1);


try
{
    if ( $workhour == -1 || isNumber($workhour)==0)
        throw new Exception("Invalid parameter",APPEL_INVALIDE);
    
    $workhour=new Sav_WorkHour($workhour);
    $erreur="ok";

    $workhour->remove();
}
catch (Exception $exc)
{
    $erreur=$exc->getCode();
}
if (! headers_sent() )    header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data> 
<code>{$erreur}</code>
</data>
EOF;
?>       