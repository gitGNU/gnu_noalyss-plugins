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

require_once 'class_transform_representative.php';
require_once 'class_transform_declarant.php';
$representative=new Transform_Representative();
$declarant=new Transform_Declarant();
?>

<form method="post" enctype="multipart/form-data">
    <h2><?php echo _('Mandataire');?></h2>
<?php
$representative->input();
?>
    <h2><?php echo _('Déclarant');?></h2>
<?php
$declarant->input();
?>
    <p>
    <?php
    $file = new IFile('client_assujetti');
    echo $file->input();
    ?>
    </p>
    <p>
        <?php
        echo HtmlInput::request_to_hidden(array('gDossier', 'ac', 'plugin_code', 'sa'));
        echo HtmlInput::hidden('st_transf',1);
        echo HtmlInput::submit('send_list', 'Valider');
        ?>
    </p>
</form>    
