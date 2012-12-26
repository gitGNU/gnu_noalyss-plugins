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
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/**
 * @file
 * @brief display a declaration but you can't modify it
 */
require_once 'include/class_rapav_declaration.php';
global $cn;
echo HtmlInput::button_action("Retour","$('declaration_list_div').show(); $('declaration_display_div').hide();");
$decl = new Rapav_Declaration();
$decl->d_id = $_GET['d_id'];
$decl->load();
$decl->display();
$ref=HtmlInput::array_to_string(array('gDossier','plugin_code','d_id'),$_GET,'extension.raw.php?');
$ref.="&amp;act=export_decla_csv";
echo HtmlInput::button_anchor("Export CSV",$ref,'export_id');
echo HtmlInput::button_action("Retour","$('declaration_list_div').show(); $('declaration_display_div').hide();");
?>
