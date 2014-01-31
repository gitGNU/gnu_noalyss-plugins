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

?>
<table>
    <tr>
        <td>
            <?php echo _('Nom') . "</td><td>" . $h_name->input(); ?>
        </td>
    <tr>
    <tr>
        <td>
            <?php echo _('Rue') . "</td><td>" . $h_street->input(); ?>
        </td>
    <tr>
    <tr>
        <td>
            <?php echo _('Code postal') . "</td><td>" . $h_postcode->input(); ?>
        </td>
    <tr>
    <tr>
        <td>
            <?php echo _('Ville') . "</td><td>" . $h_city->input(); ?>
        </td>
    <tr>
    <tr>
        <td>
            <?php echo _('Code Pays') . "</td><td>" . $h_countrycode->input(); ?>
        </td>
    <tr>
    <tr>
        <td>
            <?php echo _('email') . "</td><td>" . $h_email->input(); ?>
        </td>
    <tr>
    <tr>
        <td>
            <?php echo _('Téléphone') . "</td><td>" . $h_phone->input(); ?>
        </td>
    <tr>
    <tr>
        <td>
            <?php echo _('N° TVA') . "</td><td>" . $h_vatnumber->input(); ?>
        </td>
    <tr>
    

</table>

