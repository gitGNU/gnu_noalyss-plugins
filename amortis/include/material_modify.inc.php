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
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *   02111-1307 USA /
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/*!\file
 * \brief save the modification for a material
 *\verb
array
  'gDossier' => string '48' (length=2)
  'plugin_code' => string 'AMORT' (length=5)
  'op' => string 'save_modify' (length=11)
  't' => string 'bxmat' (length=5)
  'f' => string '210' (length=3)
\endverb
 */
require_once ('class_am_card.php');
$a=$cn->get_value("select a_id from amortissement.amortissement where f_id=$1",
		  array($f));
if ( $cn->count() == 0 )
  {
    echo HtmlInput::anchor_close($t);
    echo '<h2 class="title">'._('Détail de matériel').'</h2>';
    echo "<h2 class=\"error\"> "._("Bien à amortir effacé")." </h2>";
    echo HtmlInput::button('close',_('Fermer'),"onclick=\"removeDiv('bxmat');refresh_window()\" ");
  }
else
  {
    $mat=new Am_Card();
    $mat->set_material($f);
    echo HtmlInput::anchor_close($t);
    $mat->input();
  }
?>