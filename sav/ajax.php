<?php
$file=HtmlInput::default_value_get('act',null);

if ($file==null) die(_('No action'));

if ( ! in_array($file, array('spare_part_add'))) 
        {
            die (_('Appel invalide')); 
        }
require_once 'ajax_'.$file.'.php';   