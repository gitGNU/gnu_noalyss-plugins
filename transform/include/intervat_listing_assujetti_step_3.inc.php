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


$request_id=HtmlInput::default_value_post('r_id',null);

if ($request_id == null)
{
    throw new Exception(_('Accès directe incorrecte'), 15);
}


?>
<h2> <?php echo _('Etape 3/3') ?></h2>
<form method="get" action="extension.raw.php">
    
    <?php
    echo HtmlInput::post_to_hidden(array('r_id','ac','gDossier','plugin_code'));
    echo HtmlInput::hidden('act','listing_assujetti');
    echo HtmlInput::submit('get','Fichier XML');
    ?>
</form>
