
<label for="secret">Sitekey:</label><br />
<input type="text" id="sitekey" name="sitekey"><br /><br />
<label for="secret">Secret:</label><br />
<input type="password" id="secret" name="secret"><br /><br />
<label for="secret">Type:</label><br />
<select name="type" id="type">
    <option value="checkbox">Checkbox</option>
    <option value="invisible">Invisible</option>
</select><br /><br />
<input type="submit" value="SUBMIT" />

<script>
    get_secret();
    jQuery(document).on('click','input[type="submit"]',()=>{
        jQuery.ajax({
            url         :   ajaxurl,
            type        :   'POST',
            data        :   {
                action      :   'keep_secret',
                sitekey     :   jQuery('#sitekey').val(),
                secret      :   jQuery('#secret').val(),
                type        :   jQuery('#type').val()
            },
            success     :   function(response){
                let obj = JSON.parse(response);
                alert(obj.message);
            },
            fail        :   function(e){
                console.log(e);
                alert('Connection Error: Please try again.');
            },
            error       :   function(xhr){
                alert(`${xhr.code} ${xhr.status}`);
            }
        })
    });

    function get_secret(){
        jQuery.ajax({
            url         :   ajaxurl,
            type        :   'GET',
            data        :   {
                action      :   'get_secret',
            },
            success     :   function(response){
                let obj = JSON.parse(response);
                jQuery('#secret').val(obj.secret)
                jQuery('#sitekey').val(obj.sitekey)
                jQuery('#type').val(obj.type)
            },
            fail        :   function(e){
                console.log(e);
                alert('Connection Error: Please try again.');
            },
            error       :   function(xhr){
                alert(`${xhr.code} ${xhr.status}`);
            }
        })
    }
</script>
