content[400]="Filtrer sur quick-code, nom, date d'acquisition ou Année d'achat";
/* This file is part of NOALYSS and is under GPL see licence.txt */
/**
 *javascript
 */
function add_material(dossier_id,plugin_code,target)
{
    var qs="gDossier="+dossier_id+'&plugin_code='+plugin_code+'&op=add_mat&t='+target;

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
    var str_style="top:"+sx+"px;width:80%;height:70%";

    var div={id:target, cssclass:'inner_box',style:str_style,html:loading(),drag:1};

    add_div(div);

}
function display_material(dossier_id,f_id,plugin_code,target)
{
    var qs="gDossier="+dossier_id+'&plugin_code='+plugin_code+'&op=display_modify&t='+target+'&f='+f_id;

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
    var str_style="top:"+sx+"px;width:70%;height:auto";

    var div={id:target, cssclass:'inner_box',style:str_style,html:loading(),drag:1};

    add_div(div);

}
function success_add_material(req)
{
    fill_box(req);

}
function error_ajax() {
    alert_box('Erreur ajax AMORTIS');
}
function confirm_new_material(obj) {
    smoke.confirm('Confirmez ?',function(e)  { if ( e ) {save_new_material(obj);return false;} });
    return false;
}
/**
 * Check the values
 *   -  amrt_date date 
 *   -  p_card
 *   -  p_number
 *   -  p_year
 *   -  p_amount
 *   -  select_type_id
 * @returns 0 if no error , otherwise number of error
 */
function check_material()
{
     var error=0;
    // check date
    if ( check_date ($('amrt_date').value)  == false ) {
        $('amrt_date').addClassName('error');
        error++;
    } else {
        $('amrt_date').removeClassName('error');
    }
    // check card is given
    if ( String.trim($('p_card').value) == "") {
            $('p_card').addClassName('error');
        error++;
    } else {
        $('p_card').removeClassName('error');
    }
    // check amount of year
    if ( String.trim($('p_number').value) == "") {
            $('p_number').addClassName('error');
        error++;
    } else {
        $('p_number').removeClassName('error');
    }
    // check year of buy
    if ( String.trim($('p_year').value) == "" || parseFloat(String.trim($('p_year').value)) < 1970 || parseFloat(String.trim($('p_year').value)) > 2100 )
    {
        $('p_year').addClassName('error');
        error++;
    } else {
        $('p_year').removeClassName('error');
    }
    // check year of buy
    if ( String.trim($('p_amount').value) == "" ) {
        $('p_amount').addClassName('error');
        error++;
    } else {
        $('p_amount').removeClassName('error');
    }
    // check card or accounting
    if ( $('select_type_id').value == -1) {
        $('select_type_id').addClassName('error');
        error++;
    } else {
        $('select_type_id').removeClassName('error');
    }
    return error;
}

/**
*Answer to a post (or get) in ajax
*/
function save_new_material(obj)
{
    
    /*********************************************************************/
    /* Check that all fields are properly set*/
    /*********************************************************************/
   
    
    if ( check_material() != 0 ) {  
        return false;    
    }
    var querystring=$(obj).serialize()+'&op=save_new_material&t=bxmat';

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
    $('bxmat').width='80%';
}

function confirm_save_modify(obj) {
    smoke.confirm('Confirmez ?',function(e)  { if ( e ) {save_modify(obj);return false;} });
    return false;
}
function save_modify(obj)
{
    if ( check_material() != 0 ) { return false;}
     var querystring=$(obj).serialize()+'&op=save_modify&t=bxmat';

    // Create a ajax request to get all the person
    var action = new Ajax.Request ('ajax.php',
				   {
				       method: 'post',
				       parameters: querystring,
				       onFailure: error_ajax,
				       onSuccess: success_save_modify
				   }
                                  );

    return false;

}
function success_save_modify(req)
{
    fill_box(req);

}
function remove_mat(g_dossier,plugin_code,a_id)
{
    confirm_box(null,'Vous confirmez EFFACEMENT',function() {
    var qs="gDossier="+g_dossier+"&plugin_code="+plugin_code+"&a_id="+a_id+"&op=rm&t=bxmat";
    var action=new Ajax.Request ( 'ajax.php',
				  {
				      method:'get',
				      parameters:qs,
				      onFailure:error_ajax,
				      onSuccess:success_add_material
				  }
				) 
                    } );



}

function list_csv(obj)
{
    alert_box ("dossier = "+obj.dossier+" plugin :"+obj.plugin+" year "+obj.year);
    var qs="gDossier="+obj.dossier+"&plugin_code="+obj.plugin+"&list_year=1"+"&year="+obj.year;
    var action=new Ajax.Request ( 'extension.raw.php',
				  {
				      method:'get',
				      parameters:qs,
				      onFailure:null,
				      onSuccess:null
				  }
				);


}

function show_selected_material(obj)
{
    if ( obj.value==0) { $('cred_use_account_tr_id').show();$('deb_use_account_tr_id').show();
        $('cred_use_card_tr_id').hide();$('deb_use_card_tr_id').hide() }
    if ( obj.value==1) { $('cred_use_account_tr_id').hide();$('deb_use_account_tr_id').hide() 
    $('cred_use_card_tr_id').show();$('deb_use_card_tr_id').show()}
    if ( obj.value==-1) { $('cred_use_account_tr_id').hide();$('deb_use_account_tr_id').hide() ;
    $('cred_use_card_tr_id').hide();$('deb_use_card_tr_id').hide()}
}
