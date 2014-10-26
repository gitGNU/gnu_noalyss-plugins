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

/**
 * @file
 * @brief install the Transform (TRANSFORM plugin)
 *
 */
class Install_Transform
{

    function __construct($cn)
    {
        $this->db = $cn;
    }

    function install()
    {
        $file = dirname(__FILE__);
        $this->db->execute_script($file . '/../sql/install.sql');
    }

    function upgrade($p_version)
    {
        global $cn;
        $cur_version = $cn->get_value('select max(v_id) from transform.version');
        $cur_version++;
        $file = dirname(__FILE__);
        for ($e = $cur_version; $e <= $p_version; $e++)
        {
            $this->db->execute_script($file . '/../sql/upgrade' . $e . '.sql');
        }
    }

}

?>
