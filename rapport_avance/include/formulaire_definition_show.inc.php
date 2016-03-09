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
 * @brief
 *
 */
echo '<h1>Définition </h1>';
$form = new RAPAV_formulaire($_REQUEST['f_id']);
$form->load_definition();
echo '<form id="form_definition_frm" method="POST" enctype="multipart/form-data" class="print">';
$form->input_formulaire();
$form->input_definition();
echo '<p>';
$delete = new ICheckBox('delete');
echo "Cochez la case pour effacer ce formulaire " . $delete->input();
echo '</p>';
echo HtmlInput::submit('form_def_sub', 'Sauve');
echo '</form>';
?>
