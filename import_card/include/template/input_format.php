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
?>

<form accept-charset="utf-8" method="post">
    <?php echo $hidden?>
    <table>
    <tr>
        <td><?php echo _("Catégorie de fiche");?></td>
        <td> <?php echo $fd->input();?></td>
    </tr>
    <tr>
    <tr>
        <td><?php echo _("Délimiteur");?> </td>
        <TD> <?php echo $delimiter->input()?></td>
    </tr>

    <tr>
        <td><?php   echo _("Encodage unicode");?></td>
        <TD> <?php echo $encodage->input()?></td>
    </tr>
    <tr>
        <td><?php echo _("  Texte entouré du signe");?> </td>
        <TD><input type="text" name="rsurround" value='"' size="1"></td>
    </tr>
    <tr>
        <td><?php echo _("Supprimer la première ligne");?></td>
        <td><?php echo $skip_row->input()?></td>
    </tr>
    </table>
    <p>
        Vous pouvez changer le format et l'appliquer pour voir le résultat , ou importer ces fiches
    </p>
    <ul class="aligned-block">
        <li>
            <?php echo HtmlInput::submit("apply_format", _("Appliquer"));?>
        </li>
        <li>
            <?php echo HtmlInput::submit("import_file", _("Importer"));?>

        </li>
    </ul>  
