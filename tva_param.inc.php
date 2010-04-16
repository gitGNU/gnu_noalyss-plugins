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
 * \brief set up the parameters
 */
require_once('class_tva_parameter.php');

// save all the parameters
if ( isset ($_REQUEST['RECORD']))  {
  $aCode=$_POST['code'];
  $aValue=$_POST['value'];
  $aAccount=$_POST['account'];
  for ($i=0;$i<count($aCode);$i++) {
    $code=new Tva_Parameter($cn);
    $code->set_parameter('code',$aCode[$i]);
    $code->set_parameter('value',$aValue[$i]);
    if ( isset($aAccount[$i]))
      $code->set_parameter('account',$aAccount[$i]);
    else
      $code->set_parameter('account','');
    $code->update();
  }
}

/* show all the possible parameters */
$cn=new Database(dossier::id());
$tvap=new Tva_Parameter($cn);
require_once('class_itva_popup.php');
$a=new IPopup('popup_tva');
$a->set_title('Choississez la tva qui convient');
echo $a->input();
require_once('class_iposte.php');
echo IPoste::ipopup('ipop_account');

echo '<form method="post">';
echo dossier::hidden();
echo HtmlInput::extension();
echo $tvap->display();
echo HtmlInput::submit('RECORD','Sauve');
echo '</form>';

