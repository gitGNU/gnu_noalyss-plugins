function reconcilie(target,dossier_id,p_id,plugin_code)
{
    var qs="?gDossier="+dossier_id+'&plugin_code='+plugin_code+'&act=show&id='+p_id+'&ctl='+target;
    
    var action=new Ajax.Request ( 'ajax.php',
				  {
				      method:'get',
				      parameters:qs,
				      onFailure:error_box,
				      onSuccess:success_box
				  }
				);
    if ( $(target)) 
    {
	removeDiv(target);
    }
    var sx=0;
    var height=document.body.clientHeight/2-100;
    if ( window.scrollY)
    {
        sx=window.scrollY+height;
    }
     else
     {
            sx=document.body.scrollTop+height;
     }
    var str_style="top:"+sx+";";

    var div={id:target, cssclass:'op_detail',style:str_style,html:loading()};
    
    add_div(div);
}

function save_bank_info(obj)
{
    var query_string=obj.serialize();
    var action=new Ajax.Request ( 'ajax.php',
				  {
				      method:'get',
				      parameters:query_string,
				      onFailure:error_box,
				      onSuccess:success_box
				  });
    
    return false;
}
