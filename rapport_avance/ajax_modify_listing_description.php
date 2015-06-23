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

// Copyright 2015 Author Dany De Bontridder danydb@aevalys.eu

// require_once '.php';
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

global $cn;

$id=HtmlInput::default_value_get('d_id', 0);
if ($id == 0 ) return;

$comment=HtmlInput::default_value_get('comment', NULL);
$comment=strip_tags($comment);

require_once 'include/class_rapport_avance_sql.php';

$list=new RAPAV_Listing_Compute_SQL($id);

$list->l_description=$comment;

$list->update();
?>        