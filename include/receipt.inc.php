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
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/*!\file
 * \brief renumbered ledger
 */
display_search_receipt($cn);
if ( isset($_POST['chg_receipt']) )
  {
    change_receipt($cn);
  }
if (isset($_GET['search']))
  {
    $err=0;
    if (isDate($_GET['dstart'])==0) 
      {
       echo " <p class=\"error\">La date de départ est invalide</p>";
       $err++;
      }
    if (isDate($_GET['dend'])==0) 
      {
       echo " <p class=\"error\">La date de fin est invalide</p>";
       $err++;
      }
    if ( $err != 0 ) return;
    display_numb_receipt();
    display_result_receipt($cn);
  }