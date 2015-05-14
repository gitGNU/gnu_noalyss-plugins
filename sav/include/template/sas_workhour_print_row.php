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
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief included from Sav_Workhour -> print_row, display a row
 * @param type $name Descriptionara
 */
?>
<tr id="workhour<?php echo $this->workhour_sql->id; ?>">
      <td>
            <?php 
                echo HtmlInput::card_detail($qcode, "",' style="text-decoration:underline;display:inline"', true);
            ?>        
     </td>
      <td>
            <?php 
                echo h($description);
            ?>        
        </td>
        <td>
            <?php
                echo nb($hours);
            ?>
        </td>
      
        <td>
            <?php
               
              echo HtmlInput::anchor(_('Supprimer'), "",'onclick="'.$js.'"');
              ?>
        </td>
    </tr>
