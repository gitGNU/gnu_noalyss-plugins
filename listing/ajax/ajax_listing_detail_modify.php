<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

require_once $g_listing_home.'/include/class_rapav_listing_formula.php';
require_once $g_listing_home.'/include/class_rapav_condition.php';
$id=HtmlInput::default_value_get('id',-1);
if ($id == -1 )
{
    $id=HtmlInput::default_value_get('lp_id',-1);
}
if ($id !=-1)
{
    
    ob_start();
    $obj=new RAPAV_Listing_Param_SQL($id);
    $formula=RAPAV_Listing_Formula::make_object($obj);
    echo HtmlInput::title_box($obj->lp_code,'listing_param_input_div_id');
    echo h2($obj->lp_comment);
    $code=new IText('code_id',$obj->lp_code);
    $comment=new IText('comment',$obj->lp_comment);
    $order=new INum('order',$obj->l_order);
    $atab=array(
        'ATTR'=>'new_attribute_id',
        'ACCOUNT'=>'new_account_id',
        'COMP'=>'compute_id',
        'FORM'=>'formula'
    );
    
    ?>
<?php echo HtmlInput::hidden("listing_id", $obj->l_id); ?>
    <form id="common_frm">

        <table >
            <tr>
                <td>
                    <label>Code</label> 
                   
                </td>
                <td>
                    <?php echo $code->input() ?>
                </tD>
                <td>
                     <p id="code_id_span" class="error"></p>
                </td>
                
                
            </tr>
            <tr>
                <td>
                    <label>Commentaire</label>
                </td>
                <td>
                    <?php echo $comment->input(); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label>Ordre d'apparition</label>
                </td>
                <td>
                    <?php echo $order->input(); ?>
                </td>
            </tr>
            
        </table>
    </form>

 <span class="error" id="info_listing_param_input_div_id"></span>
<?php
    echo '<form method="POST" id="listing_detail_modify_frm" onsubmit="save_param_listing(\'listing_detail_modify_frm\'); return false;">';
    echo $formula->input();
    echo HtmlInput::array_to_hidden(array('act','ac','plugin_code','gDossier'),$_REQUEST);
    echo HtmlInput::hidden('tab',$atab[$formula->sig]);
    echo HtmlInput::hidden('listing_id',$obj->l_id);
    echo HtmlInput::hidden('lp_id',$obj->lp_id);
    
    echo '<p>';
    echo HtmlInput::submit('save_listing_detail_modify',_('Sauver'));
    echo '</p>';
    echo '</form>';
    $response = ob_get_clean();
 } else {
     $response='invalide id';
 }
 $html = escape_xml($response);
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl></ctl>
<code>$html</code>
</data>
EOF;
?>        