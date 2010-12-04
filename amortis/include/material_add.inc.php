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
 * \brief add material
 */
$date=new IDate('p_date');
$p_year=new INum('p_year');
$p_number=new INum('p_number');
$p_card=new ICard('p_card');
$p_card->size=50;
$p_card->typecard=7;
$p_deb=new IPoste('p_deb');
$p_deb->set_attribute('jrn',0);
$p_deb->set_attribute('account','p_deb');
$p_deb->set_attribute('label','p_deb_label');
$deb_span=new ISpan('p_deb_label');

$p_cred=new IPoste('p_cred');
$p_cred->set_attribute('jrn',0);
$p_cred->set_attribute('account','p_cred');
$p_cred->set_attribute('label','p_cred_label');
$cred_span=new ISpan('p_cred_label');

$p_amount=new INum('p_amount');

require_once ('template/material_add.php');
