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
/* $Revision$ */

// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be

/*!\file
 * \brief renumbered ledger
 */
display_search_receipt($cn);
$act=HtmlInput::default_value_request('act', "");
if ($act=='numbering')
  {
    change_receipt($cn);
  }
if ( $act == 'download') {
    download_receipt($cn);
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
    display_download_receipt();
    display_result_receipt($cn);
  }