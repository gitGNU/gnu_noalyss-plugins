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
<div style="margin-left:10%;width: 80%;margin-right: 10%">
<h1>Listing Assujetti Intervat </h1>
<?php
$step = HtmlInput::default_value_request('st_transf', 0);
$error=0;$errmsg="";
if ($step == 0)
{
    require 'intervat_listing_assujetti_step_1.inc.php';
}
if ($step == 1)
{
    try
    {
        require 'intervat_listing_assujetti_step_2.inc.php';
    } catch (Exception $e)
    {
        $error=$e->getCode();
        $errmsg=$e->getMessage();
        echo '<p class="notice">' . $e->getMessage() . '</p>';
        if ($e->getCode() != 3)
        {
            require 'intervat_listing_assujetti_step_1.inc.php';
        }
    }
}
if ($step == 2)
{
    try
    {
        require 'intervat_listing_assujetti_step_3.inc.php';
    } catch (Exception $e)
    {
        echo '<p class="notice">' . $e->getMessage() . '</p>';
        require 'intervat_listing_assujetti_step_1.inc.php';
    }
}
?>
</div>