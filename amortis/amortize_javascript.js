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
    var div={id:target, cssclass:'op_detail',style:'top:30%;width:50%;margin-left:0;',html:loading()};
    
    add_div(div);

}

function error_ajax() {
    alert('Erreur ajax AMORTIS');
}
function success_add_material(req)
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
	alert("Impossible executer script de la reponse\n"+e.message);}


}
