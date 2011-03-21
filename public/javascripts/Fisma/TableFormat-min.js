Fisma.TableFormat={greenColor:"lightgreen",yellowColor:"yellow",redColor:"pink",green:function(a){a.style.backgroundColor=Fisma.TableFormat.greenColor},yellow:function(a){a.style.backgroundColor=Fisma.TableFormat.yellowColor},red:function(a){a.style.backgroundColor=Fisma.TableFormat.redColor},securityAuthorization:function(b,a,c,d){b.innerHTML=d;dateParts=d.split("-");if(3==dateParts.length){authorizedDate=new Date(dateParts[0],dateParts[1],dateParts[2]);greenDate=new Date();greenDate.setMonth(greenDate.getMonth()-30);yellowDate=new Date();yellowDate.setMonth(yellowDate.getMonth()-36);if(authorizedDate>=greenDate){Fisma.TableFormat.green(b.parentNode)}else{if(authorizedDate>=yellowDate){Fisma.TableFormat.yellow(b.parentNode)}else{Fisma.TableFormat.red(b.parentNode)}}}},selfAssessment:function(b,a,c,d){b.innerHTML=d;dateParts=d.split("-");if(3==dateParts.length){assessmentDate=new Date(dateParts[0],dateParts[1],dateParts[2]);greenDate=new Date();greenDate.setMonth(greenDate.getMonth()-8);yellowDate=new Date();yellowDate.setMonth(yellowDate.getMonth()-12);if(assessmentDate>=greenDate){Fisma.TableFormat.green(b.parentNode)}else{if(assessmentDate>=yellowDate){Fisma.TableFormat.yellow(b.parentNode)}else{Fisma.TableFormat.red(b.parentNode)}}}},contingencyPlanTest:function(b,a,c,d){Fisma.TableFormat.selfAssessment(b,a,c,d)},yesNo:function(b,a,c,d){b.innerHTML=d;if("YES"==d){Fisma.TableFormat.green(b.parentNode)}else{if("NO"==d){Fisma.TableFormat.red(b.parentNode)}}},editControl:function(d,c,e,f){var a=document.createElement("img");a.src="/images/edit.png";var b=document.createElement("a");b.href=f;b.appendChild(a);d.appendChild(b)},deleteControl:function(d,c,e,f){var a=document.createElement("img");a.src="/images/del.png";var b=document.createElement("a");b.href=f;b.appendChild(a);d.appendChild(b)},formatHtml:function(a,b,c,d){a.innerHTML=d.replace(/&amp;/g,"&").replace(/&lt;/g,"<").replace(/&gt;/g,">")},overdueFinding:function(i,k,f,b){overdueFindingSearchUrl="/finding/remediation/list/queryType/advanced";var a=k.getData("System");if(a){overdueFindingSearchUrl+="/organization/textExactMatch/"+escape(a)}var d=k.getData("Status");if(d){d=PHP_JS().html_entity_decode(d);overdueFindingSearchUrl+="/denormalizedStatus/textExactMatch/"+escape(d)}var j=f.formatterParameters;if(j.source){overdueFindingSearchUrl+="/source/textExactMatch/"+escape(j.source)}var h=null;if(j.from){fromDate=new Date();fromDate.setDate(fromDate.getDate()-parseInt(j.from));h=fromDate.getFullYear()+"-"+(fromDate.getMonth()+1)+"-"+fromDate.getDate()}var g=null;if(j.to){toDate=new Date();toDate.setDate(toDate.getDate()-parseInt(j.to));g=toDate.getFullYear()+"-"+(toDate.getMonth()+1)+"-"+toDate.getDate()}if(h&&g){overdueFindingSearchUrl+="/nextDueDate/dateBetween/"+g+"/"+h}else{if(h){overdueFindingSearchUrl+="/nextDueDate/dateBefore/"+h}else{var e=new Date();e.setDate(e.getDate()-1);var c=e.getFullYear()+"-"+(e.getMonth()+1)+"-"+e.getDate();overdueFindingSearchUrl+="/nextDueDate/dateBefore/"+c}}i.innerHTML="<a href="+overdueFindingSearchUrl+">"+b+"</a>"},completeDocTypePercentage:function(b,a,c,d){percentage=parseInt(d);if(d!=null){b.innerHTML=d+"%";if(percentage>=95&&percentage<=100){Fisma.TableFormat.green(b.parentNode)}else{if(percentage>=80&&percentage<95){Fisma.TableFormat.yellow(b.parentNode)}else{if(percentage>=0&&percentage<80){Fisma.TableFormat.red(b.parentNode)}}}}},incompleteDocumentType:function(c,b,d,e){var a="";if(e.length>0){a+="<ul><li>"+e.replace(/,/g,"</li><li>")+"</li></ul>"}c.innerHTML=a},formatCheckbox:function(c,a,d,e){if(a.getData("deleted_at")){c.parentNode.style.backgroundColor="pink"}else{var b=document.createElement("input");b.type="checkbox";b.className=YAHOO.widget.DataTable.CLASS_CHECKBOX;b.checked=e;if(c.firstChild){c.removeChild(el.firstChild)}c.appendChild(b)}}};