//This file is part of NOALYSS and is under GPL 
//see licence.txt
/**
 *javascript
 */
function add_material(dossier_id,plugin_code,target)
{
    var qs="?gDossier="+dossier_id+'&plugin_code='+plugin_code+'&op=add_mat&t='+target;
    
    var action=new Ajax.Request ( 'ajax.php',
				  {
				      method:'get',
				      parameters:qs,
				      onFailure:error_ajax,
				      onSuccess:success_add_material
				  }
				);
    if ( $(target)) 
    {
	removeDiv(target);
    }

    var sx=0;
    if ( window.scrollY)
    {
            sx=window.scrollY+120;
    }
     else
     {
            sx=document.body.scrollTop+120;
     }
    var str_style="top:"+sx+";width:50%;height:70%";

    var div={id:target, cssclass:'inner_box',style:str_style,html:loading()};
    
    add_div(div);

}
function display_material(dossier_id,f_id,plugin_code,target)
{
    var qs="?gDossier="+dossier_id+'&plugin_code='+plugin_code+'&op=display_modify&t='+target+'&f='+f_id;
    
    var action=new Ajax.Request ( 'ajax.php',
				  {
				      method:'get',
				      parameters:qs,
				      onFailure:error_ajax,
				      onSuccess:success_add_material
				  }
				);
    if ( $(target)) 
    {
	removeDiv(target);
    }

    var sx=0;
    if ( window.scrollY)
    {
            sx=window.scrollY+120;
    }
     else
     {
            sx=document.body.scrollTop+120;
     }
    var str_style="top:"+sx+";width:50%;height:auto";

    var div={id:target, cssclass:'inner_box',style:str_style,html:loading()};
    
    add_div(div);

}
function success_add_material(req)
{
    fill_box(req);

}
function error_ajax() {
    alert('Erreur ajax AMORTIS');
}

/**
*Answer to a post (or get) in ajax
*/
function save_new_material(obj)
{

    var querystring="?"+$(obj).serialize()+'&op=save_new_material&t=bxmat';

    // Create a ajax request to get all the person
    var action = new Ajax.Request ('ajax.php',
				   {
				       method: 'post',
				       parameters: querystring,
				       onFailure: error_ajax,
				       onSuccess: success_save_new_material
				   }
                                  );

    return false;   
}

function success_save_new_material(req)
{
    fill_box(req);
    $('bxmat').style.height='auto';
}
function save_modify(obj)
{
     var querystring="?"+$(obj).serialize()+'&op=save_modify&t=bxmat';
     waiting_box();
    // Create a ajax request to get all the person
    var action = new Ajax.Updater($('source_id'),'ajax.php',
				   {
				       method: 'post',
				       parameters: querystring,
				   }
                                  );

    remove_waiting_box();
    return false;   
   
}

function remove_mat(g_dossier,plugin_code,a_id)
{
    if ( ! confirm('Vous confirmez EFFACEMENT')) { return false;}
    var qs="?gDossier="+g_dossier+"&plugin_code="+plugin_code+"&a_id="+a_id+"&op=rm&t=bxmat";     
    var action=new Ajax.Request ( 'ajax.php',
				  {
				      method:'get',
				      parameters:qs,
				      onFailure:error_ajax,
				      onSuccess:success_add_material
				  }
				);
 

   
}