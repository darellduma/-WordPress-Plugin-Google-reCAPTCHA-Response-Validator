<?php
    /*
        Plugin Name:    Google reCAPTCHA Response Validator
        Description:    A small plugin that validates your Google reCAPTCHA response sent by the user from the front end.
        Author:         Darell N. Duma
        Author URI:     darellduma@gmail.com
        Version:        1.0.0
        License:        GPL v2 or later
        License URI:    https://www.gnu.org/licenses/gpl-2.0.html
        Text Domain:    google-recaptcha-response-validator

        Google reCAPTCHA Response Validator is free software: you can redistribute it and/or modify
        it under the terms of the GNU General Public License as published by
        the Free Software Foundation, either version 2 of the License, or
        any later version.
        
        Google reCAPTCHA Response Validator is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
        GNU General Public License for more details.
        
        You should have received a copy of the GNU General Public License
        along with Google reCAPTCHA Response Validator. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
    */

    defined('ABSPATH') or die('You are not authorized to access this file');

    if(!class_exists('GRCV')){
        class GRCV{
            function test_response_func($atts){
                $result = $this->retrieve_sitekey();
                if($result->type==='checkbox'){
                    ob_start();
                ?>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                    <script type="text/javascript">
                        var onloadCallback = function() {
                            widgetId1 = grecaptcha.render('grecaptcha',{
                                'sitekey'        :   '<?php echo $result->sitekey; ?>'
                            });
                        };
                    </script>
                    <h4>Instructions:</h4>
                    <p>Check the box and answer the challenges then click the submit button</p>
                    <form id="form">
                    <div id="grecaptcha"></div>
                    <br>
                    <input id="btn_submit_recaptcha" type="submit" value="Submit">
                    </form>
                    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
                    <script>
                        let f1 = document.getElementById('form');
                        f1.addEventListener('submit',(e)=>{
                            e.preventDefault();
                            let response = grecaptcha.getResponse(widgetId1);
                            if(!response){
                                alert('Please verify that you are a human')
                            } else {
                                jQuery('#submit').val('PLEASE WAIT...');
                                jQuery('#submit').attr('disabled',true);
                                let data = {
                                    action  : 'test_token',
                                    token   : response
                                };
                                jQuery.ajax({
                                url     :   'http://127.0.0.1/dev/recaptcha/wp-admin/admin-ajax.php',
                                type    :   'POST',
                                data    :   data,
                                success   : function(response){
                                    let obj = JSON.parse(response);
                                    console.log(obj);
                                    alert(obj.message);
                                    location.reload();
                                },
                                fail      : function(e){
                                    console.log(e);
                                    alert('Connection Error: Please try again.');
                                },
                                error     : function(xhr){
                                    alert(`${xhr.code} ${xhr.status}`);
                                }
                                });
                            }
                        })
                    </script>
                <?php
                return ob_get_clean();
                } else {
                        ob_start();
                        ?>
                            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
                            <script>
                            function onSubmit(token) {
                                jQuery('#submit').val('PLEASE WAIT...');
                                jQuery('#submit').attr('disabled',true);
                                let data = {
                                    action  : 'test_token',
                                    token   : token
                                };
                                jQuery.ajax({
                                url     :   'http://127.0.0.1/dev/recaptcha/wp-admin/admin-ajax.php',
                                type    :   'POST',
                                data    :   data,
                                success   : function(response){
                                    let obj = JSON.parse(response);
                                    console.log(obj);
                                    alert(obj.message);
                                    location.reload();
                                },
                                fail      : function(e){
                                    console.log(e);
                                    alert('Connection Error: Please try again.');
                                },
                                error     : function(xhr){
                                    alert(`${xhr.code} ${xhr.status}`);
                                }
                                });
                                // alert('thanks ' + document.getElementById('field').value);
                            }

                            function validate(event) {
                                event.preventDefault();
                                grecaptcha.execute();
                            }
                        
                            function onload() {
                                var element = document.getElementById('submit');
                                element.onclick = validate;
                            }
                        </script>
                        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                        <!-- invisible -->
                        <!-- Name: (required) <input id="field" name="field"> -->
                        <h3>Google reCAPTCHA Response Test</h3>
                        <h4>Instructions:</h4>
                        <p>Click the submit button then answer the challenges</p>
                        <div id='recaptcha' class="g-recaptcha"
                            data-sitekey="6LcctMAUAAAAANyZctJNGjBtxLqbOwSPXodladUu"
                            data-callback="onSubmit"
                            data-size="invisible"></div>
                        <button id='submit'>submit</button>
                        <script>onload();</script>
                        <?php
                        return ob_get_clean();
                    }
            }

            function activate_func(){
                global $wpdb;
                $charset_collate = $wpdb->get_charset_collate();
                $sql = "CREATE TABLE {$wpdb->prefix}grcv 
                (id smallint unsigned not null auto_increment, 
                sitekey varchar(50) not null, secret varchar(50) not null, 
                PRIMARY KEY (id))";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql );
                $url = plugin_dir_path(__FILE__) . 'admin/settings.php';
                header("Location: $url");
            }

            function grcv_options_page() {
                add_menu_page(
                    'GRCV Settings',
                    'GRCV Settings',
                    'manage_options',
                    plugin_dir_path(__FILE__) . 'admin/settings.php',
                    null,
                    plugin_dir_url(__FILE__) . 'images/grecaptcha.png',
                    20
                );

                // add_submenu_page(
                //     plugin_dir_path(__FILE__) . 'admin/settings.php',
                //     'Test Token',
                //     'Test token',
                //     'manage_options',
                //     plugin_dir_path(__FILE__) . 'admin/test_token.php',
                // );
            }

            function initialize_plugin(){
                add_action( 'admin_menu', [$this,'grcv_options_page'] );
            }

            function keep_secret(){
                $data = json_decode(file_get_contents('php://input'),true);
                $sitekey = sanitize_text_field($_POST['sitekey']);
                $secret = sanitize_text_field($_POST['secret']);
                $type = sanitize_text_field($_POST['type']);
                $success = false;
                if(!$secret){
                    echo json_encode(['success'=>$success,'message'=>'Invalid Secret Key']);
                    wp_die();
                }
                global $wpdb;
                $wpdb->get_results("SELECT * FROM {$wpdb->prefix}grcv");
                $sql = '';
                $action = '';
                if(!$wpdb->num_rows){
                    $wpdb->insert("{$wpdb->prefix}grcv",['secret'=>$secret,'sitekey'=>$sitekey,'type'=>$type]);
                    $action = 'Inserted';
                } else {
                    $sql = "UPDATE {$wpdb->prefix}grcv SET secret = '$secret', sitekey = '$sitekey', type = '$type'";
                    $wpdb->query($sql);
                    $action = 'Updated';
                }
                $success = !$success;
                echo json_encode([
                    'success'=>$success,
                    'message'=>"Secret Key has been $action",
                ]);
                wp_die();
            }

            function get_secret(){
                $pair = $this->get_pair();
                echo json_encode($pair);
                wp_die();
            }

            function get_pair(){
                global $wpdb;
                $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}grcv LIMIT 0,1");
                return $results[0];
            }

            function get_secret_from_db(){
                global $wpdb;
                $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}grcv LIMIT 0,1");
                $secret = '';
                foreach($results as $result){
                    $secret = $result->secret;
                }
                return $secret;
            }

            function verify_token($token){
                $secret = $this->get_secret_from_db();
                $success = false;
                if(!$secret){
                    echo json_encode(['success'=>$success,'message'=>'Secret Key is Unset']);
                    wp_die();
                }
                $payload = array(
                    'secret'    =>  urlencode($secret),
                    'response'  =>  urlencode($token)
                );
                $url_payload = 'secret='.urlencode($secret).'&response='.urlencode($token);
                $ch = curl_init();
                $url = 'https://www.google.com/recaptcha/api/siteverify';
                curl_setopt($ch,CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $url_payload);
              
                $result = json_decode(curl_exec($ch));
                curl_close($ch);
                if($result->success){
                    $message = 'Response is Valid!';
                } else {
                    $message = "The following Errors were encountered: \n";
                    foreach($result->{'error-codes'} as $error){
                        $message .= $this->get_error_description($error) . "\n";
                    }
                }
                return ['success'=>$result->success,'message'=>$message];
                wp_die();
            }

            function get_error_description($code){
                switch($code){
                    case 'missing-input-secret':
                    return 'The secret parameter is missing.';
                    break;
                    case 'invalid-input-secret':
                    return 'The secret parameter is invalid or malformed.';
                    break;
                    case 'missing-input-response':
                    return 'The response parameter is missing.';
                    break;
                    case 'invalid-input-response':
                    return 'The response parameter is invalid or malformed.';
                    break;
                    case 'bad-request':
                    return 'The request is invalid or malformed.';
                    break;
                    case 'timeout-or-duplicate':
                    return 'The response is no longer valid: either is too old or has been used previously.';
                    break;
                    default:
                    return 'Invalid Keys.';
                    break;
                }
            }

            function test_token(){
                $data = json_decode(file_get_contents('php://input'),true);
                $token = sanitize_text_field($_POST['token']);
                if(!$token){
                    echo json_encode(['success'=>false,'message'=>'Empty Token']);
                    wp_die();
                }
                $result = $this->verify_token($token);
                echo json_encode($result);
                wp_die();
            }

            function retrieve_sitekey(){
                global $wpdb;
                $sql = "SELECT sitekey,type from {$wpdb->prefix}grcv limit 0,1";
                $results = $wpdb->get_results($sql);
                return $results[0];
            }

            function get_sitekey(){
                $results = $this->retrieve_sitekey();
                echo json_encode($results);
                wp_die();
            }
        }
    }

    $grcv = new GRCV();
    $grcv->initialize_plugin();
    register_activation_hook( __FILE__, [$grcv,'activate_func'] );

    add_shortcode('test_response',[$grcv,'test_response_func']);

    add_action('wp_ajax_keep_secret',[$grcv,'keep_secret']);
    add_action('wp_ajax_get_secret',[$grcv,'get_secret']);
    add_action('wp_ajax_test_token',[$grcv,'test_token']);
    add_action('wp_ajax_nopriv_test_token',[$grcv,'test_token']);
    add_action('wp_ajax_nopriv_get_sitekey',[$grcv,'get_sitekey']);

?>