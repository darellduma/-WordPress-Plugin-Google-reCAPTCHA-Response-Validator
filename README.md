# [WordPress Plugin]Google-reCAPTCHA-Response-Validator
A WordPress Plugin for Google ReCAPTCHA Response Validation

## Before You Use

This plugin is on early beta phase. Do not use this on your live site. 

This plugin is recommended for WordPress developers who wants to validate Google reCAPTCHA response from the front end. 

Google doesn't accept localhost as domain so use the loopback address (127.0.0.1) instead

## How To Use
1. Download the entire folder.
2. Paste it in your plugins directory file.
3. Activate the plugin.
4. Enter the credentials (site key and secret) and provide the type of captcha (checkbox or invisible) in the settings. (You can get your credentials here: https://www.google.com/recaptcha/admin/)
5. To test your keys, create a new page and insert [test_response] as shortcode.
6. To use it as a validator, 
    
    6.a. Include it in your custom plugin (yoursite/wp-content-plugins/GRCV/GRCV.php)
    
    6.b. Invoke GRCV::verify_response($response) method and place the result in a variable.
  
  Example
  ```
    include_once(plugin_dir_path(__FILE__) . '../GRCV/GRCV.php');
    $result = GRCV::verify_response($response);
  ```
  Return:
    
    success   : (bool)true|false
    
    message   : (string)'Response is Valid!' if success, list of errors if unsuccessful.

## Report Issues here: 
https://github.com/darellduma/-WordPress-Plugin-Google-reCAPTCHA-Response-Validator/issues

## Useful Links:
How to Integrate Google reCAPTCHA on the front end: https://developers.google.com/recaptcha/docs/display

Information on Verifying the User's Response: https://developers.google.com/recaptcha/docs/display
