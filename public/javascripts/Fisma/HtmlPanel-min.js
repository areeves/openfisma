Fisma.HtmlPanel=function(){return{showPanel:function(c,b){var a=new YAHOO.widget.Panel("panel",{width:"540px",modal:true});a.setHeader(c);a.setBody("Loading...");a.render(document.body);a.center();a.show();if(b!=""){a.setBody(b);a.center()}else{alert("The parameter html can not be empty.")}return a}}}();