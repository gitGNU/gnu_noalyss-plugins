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

    var div={id:target, cssclass:'op_detail',style:str_style,html:loading()};
    
    add_div(div);

}
function success_add_material(req)
{
    var answer=req.responseXML;
    var a=answer.getElementsByTagName('ctl');
    var name_ctl=a[0].firstChild.nodeValue;

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

    var querystring="?"+$(obj).serialize()+'&op=save_new_material';
    // Create a ajax request to get all the person
    var action = new Ajax.Request ('ajax.php',
				   {
				       method:			 'get',
				       parameters:			 querystring,
				       onFailure:			 error_ajax,
				       onSuccess:			 success_save_new_material
				   }
                                  );

    return false;   
}

function success_save_new_material(req)
{
    try{
	var answer=req.responseXML;
	var a=answer.getElementsByTagName('ctl');
	var html=answer.getElementsByTagName('code');
	if ( a.length == 0 ) { var rec=req.responseText;alert ('erreur :'+rec);}
	var name_ctl=a[0].firstChild.nodeValue;
	var code_html=getNodeText(html[0]); // Firefox ne prend que les 4096 car.
	code_html=unescape_xml(code_html);
	$(name_ctl).innerHTML=code_html;
    } 
    catch (e) {
	alert(e.message);}
    try{
	code_html.evalScripts();}
    catch(e){
	alert("Impossible executer script de la reponse\n"+e.message);
    }


}