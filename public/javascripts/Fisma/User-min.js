Fisma.User={userInfoPanelList:{},generatePasswordBusy:false,checkAccountBusy:false,displayUserInfo:function(c,b){var a;if(typeof Fisma.User.userInfoPanelList[b]=="undefined"){a=Fisma.User.createUserInfoPanel(c,b);Fisma.User.userInfoPanelList[b]=a;a.show()}else{a=Fisma.User.userInfoPanelList[b];if(a.cfg.getProperty("visible")){a.hide()}else{a.bringToTop();a.show()}}},createUserInfoPanel:function(e,d){var b=350;var c,a;c=d+"InfoPanel";a=new YAHOO.widget.Panel(c,{width:b+"px",modal:false,close:true,constraintoviewport:true});a.setHeader("User Profile");a.setBody("Loading user profile for <em>"+d+"</em>...");a.render(document.body);Fisma.Util.positionPanelRelativeToElement(a,e);YAHOO.util.Connect.asyncRequest("GET","/user/info/username/"+escape(d),{success:function(f){a.setBody(f.responseText);Fisma.Util.positionPanelRelativeToElement(a,e)},failure:function(f){a.setBody("User information cannot be loaded.");Fisma.Util.positionPanelRelativeToElement(a,e)}},null);return a},generatePassword:function(){if(Fisma.User.generatePasswordBusy){return}Fisma.User.generatePasswordBusy=true;var a=document.getElementById("generate_password");a.className="yui-button yui-push-button yui-button-disabled";var b=new Fisma.Spinner(a.parentNode);b.show();YAHOO.util.Connect.asyncRequest("GET","/user/generate-password/format/html",{success:function(c){document.getElementById("password").value=c.responseText;document.getElementById("confirmPassword").value=c.responseText;Fisma.User.generatePasswordBusy=false;a.className="yui-button yui-push-button";b.hide()},failure:function(c){b.hide();alert("Failed to generate password: "+c.statusText)}},null);return false},checkAccount:function(){if(Fisma.User.checkAccountBusy){return}Fisma.User.checkAccountBusy=true;var c=document.getElementById("username").value;var a="/user/check-account/format/json/account/"+encodeURIComponent(c);var b=document.getElementById("checkAccount");b.className="yui-button yui-push-button yui-button-disabled";var d=new Fisma.Spinner(b.parentNode);d.show();YAHOO.util.Connect.asyncRequest("GET",a,{success:function(k){var h=YAHOO.lang.JSON.parse(k.responseText);message(h.msg,h.type,true);var e=new Array("nameFirst","nameLast","phoneOffice","phoneMobile","email","title");var f=new Array("givenname","sn","telephonenumber","mobile","mail","title");if(h.accountInfo!=null){for(var g in f){if(!f.hasOwnProperty(g)){continue}var j=h.accountInfo[f[g]];if(j!=null){document.getElementById(e[g]).value=j}else{document.getElementById(e[g]).value=""}}}Fisma.User.checkAccountBusy=false;b.className="yui-button yui-push-button";d.hide()},failure:function(e){d.hide();alert("Failed to check account password: "+e.statusText)}},null)},showCommentPanel:function(){var g=YAHOO.util.Dom.get("locked");if(g===null||parseInt(g.value)===0){YAHOO.util.Dom.getAncestorByTagName("save-button","form").submit();return false}var c=document.createElement("div");var e=document.createElement("p");var b=document.createTextNode("Comments (OPTIONAL):");e.appendChild(b);c.appendChild(e);var f=document.createElement("textarea");f.id="commentTextarea";f.name="commentTextarea";f.rows=5;f.cols=60;c.appendChild(f);var d=document.createElement("div");d.style.height="10px";c.appendChild(d);var a=document.createElement("input");a.type="button";a.id="continueButton";a.value="continue";c.appendChild(a);Fisma.HtmlPanel.showPanel("Add Comment",c.innerHTML);YAHOO.util.Dom.get("continueButton").onclick=Fisma.User.submitUserForm},submitUserForm:function(){if(YAHOO.env.ua.ie){var a=YAHOO.util.Dom.get("commentTextarea").innerHTML}else{var a=YAHOO.util.Dom.get("commentTextarea").value}var b=YAHOO.util.Dom.getAncestorByTagName("save-button","form");YAHOO.util.Dom.get("comment").value=a;b.submit()}};