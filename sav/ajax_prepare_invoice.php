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

$repair_id=HtmlInput::default_value_get('repair_id', '-1');

if ( $repair_id == -1 || isNumber($repair_id ) == 0 ) throw new Exception(_('Appel invalide'), APPEL_INVALIDE);

require_once 'include/class_sav_transform_invoice.php';

$sav=new Service_After_Sale();

$sav->set_card_id($repair_id);

$transform=new Sav_Transform_Invoice($sav);
if ( $sav->get_card_id() == -1 )    throw new Exception(_('Carte réparation inexistante'),APPEL_INVALIDE);

?>        
<form method="POST" action="do.php">
    <?php
        echo Dossier::hidden();
        echo $transform->form();
    ?>
    <input type="submit" class="input_text" value="<?php echo _('Facture') ?>">
</form>