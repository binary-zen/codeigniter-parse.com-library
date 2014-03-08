PHP parse.com API library - Ported to a Codeigniter library
===========================
This is a fork of apotropaic/parse.com-php-library that has been modified to easily integrate with the Codeigniter framework. It uses parse.com's rest api.

Why Codeigniter? I needed a php framework that had a wide range of server compatibility. The particular app that this was designed for will be going on a server at my university, where they are strict about what is installed.

This library requires that the curl library for php be installed.


SETUP
=========================

**Instructions** Create a new config file called parse.php and place it in the application/config directory of your application.

### sample of parse.php ###

Fill in the appropriate keys. An exception will be thrown if they are not set.

```
<?php
/**
 * Parse keys
 */
 
$config['parse_appid'] = '';
$config['parse_masterkey'] = '';
$config['parse_restkey'] = '';
$config['parse_parseurl'] = 'https://api.parse.com/1/';

?>


```



EXAMPLES
=========================

### Using ParseObject ###

```
      $this->load->library('parse');
      $testObj = $this->parse->ParseObject('testObj');
      $testObj->data = array("testcol" => "it works");
      $testObj->save();
```
