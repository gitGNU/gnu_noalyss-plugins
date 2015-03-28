<?php
/*
 * * Copyright (C) 2015 Dany De Bontridder <dany@alchimerys.be>
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.

 * 
 */


/**
 * @brief 
 */
?>
<tr id="spare_part<?php echo $this->spare_part->id; ?>">
        <td>
            <?php 
                echo HtmlInput::card_detail($qcode, "",' style="text-decoration:underline;display:inline"', true);
            ?>        
        </td>
        <td>
            <?php 
                echo h($name);
            ?>        
        </td>
        <td>
            <?php
                echo nb($this->spare_part->quantity);
            ?>
        </td>
      
        <td>
            <?php
               
              echo HtmlInput::anchor(_('Supprimer'), "",'onclick="'.$js.'"');
              ?>
        </td>
    </tr>