<script LANGUAGE="JavaScript" type="text/javascript" src="/javascripts/jquery/jquery.form.js"></script>

<script>
    $(document).ready(function(){
        $("#validateLDAP").click(function(){
            $(".cform").ajaxSubmit({url:"/config/ldapvalid/format/html",
                                success: ldapResult});
        });
    });

    function ldapResult(responseText, statusText)
    {
        message(responseText);
    }
</script>
<div class="barleft">
<div class="barright">
<p><b>LDAP Configurations <a href="/panel/config/">Back</a></b> 
<button id="validateLDAP">Test the configuration</button></p>
</div>
</div>

<div class="block">
<?php
    $this->form->setAttrib('class','cform');
    echo $this->form;
?>
</div>
