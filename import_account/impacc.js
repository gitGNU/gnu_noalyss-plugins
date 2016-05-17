
/* This file is part of NOALYSS and is under GPL see licence.txt */

/// Show the TVA detail and modify it
/// on save go to imd_parameter.inc.php
function tva_parameter_modify(p_tva)
{
    waiting_box();
    new Ajax.Request(
            "ajax.php",
            {
                parameters: {tva_id: p_tva, gDossier: dossier, plugin_code: plugin_code, ac: ac, action: "tva_parameter_modify"},
                method: "get",
                onSuccess: function (req)
                {
                    var obj = {id: 'tva_detail_id', "cssclass": "inner_box", "style": "width:auto"};
                    add_div(obj);
                    $('tva_detail_id').innerHTML = req.responseText;
                    req.responseText.evalScripts();
                    remove_waiting_box();
                }
            }
    )
}
/// Check that the parameter for VAT Detail are correct before saving
function check_param_tva()
{
    $("tva_id").style.borderColor = "inherit";
    $("tva_code").style.borderColor = "inherit";
    if ($('tva_id').value == "0") {
        $("tva_id").style.borderColor = "red";
        smoke.alert("Erreur");
        return false;
    }
    if ($('tva_code').value == "") {
        smoke.alert("Erreur");
        $("tva_code").style.borderColor = "red";
        return false;
    }
    return true;
}
/// Add a new matching
function tva_parameter_add()
{
    waiting_box();
    new Ajax.Request(
            "ajax.php",
            {
                parameters: { gDossier: dossier, plugin_code: plugin_code, ac: ac, action: "tva_parameter_add"},
                method: "get",
                onSuccess: function (req)
                {
                    var obj = {id: 'tva_detail_id', "cssclass": "inner_box", "style": "width:auto"};
                    add_div(obj);
                    $('tva_detail_id').innerHTML = req.responseText;
                    req.responseText.evalScripts();
                    remove_waiting_box();
                }
            }
    )
}
/// Delete a TVA Code
function tva_parameter_delete(id)
{
    smoke.confirm("Effacement ?",function (e)
    {
   if ( e) 
   {
       waiting_box();
        new Ajax.Updater($("row"+id),"ajax.php",{
            method:"get",
            parameters:{"pt_id":id,action:"tva_parameter_delete",gDossier: dossier, plugin_code: plugin_code, ac: ac}
        });
        remove_waiting_box();
   }
});
}