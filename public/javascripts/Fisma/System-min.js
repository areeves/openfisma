Fisma.System={uploadDocumentCallback:function(a){window.location.href=window.location.href},showInformationTypes:function(){document.getElementById("addInformationTypes").style.display="block"},addInformationType:function(b,a,c,d){b.innerHTML="<a href='/system/add-information-type/id/"+a.getData("system")+"/sitId/"+d+"'>Add</a>"},removeInformationType:function(b,a,c,d){b.innerHTML="<a href='/system/remove-information-type/id/"+a.getData("system")+"/sitId/"+d+"'>Remove</a>"}};