<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class ParseRestClient{

	private $_appid = '';
	private $_masterkey = '';
	private $_restkey = '';
	private $_parseurl = '';

	public $data;
	public $requestUrl = '';
	public $returnData = '';

	public function __construct(){
        // Codeigniter specific stuff
        $ci =& get_instance();
        $ci->config->load('parse');
		$this->_appid = $ci->config->item('parse_appid');
    	$this->_masterkey = $ci->config->item('parse_masterkey');
    	$this->_restkey = $ci->config->item('parse_restkey');
    	$this->_parseurl = $ci->config->item('parse_parseurl');
        // end changes

		if(empty($this->_appid) || empty($this->_restkey) || empty($this->_masterkey)){
			$this->throwError('You must set your Application ID, Master Key and REST API Key');
		}

		$version = curl_version();
		$ssl_supported = ( $version['features'] & CURL_VERSION_SSL );

		if(!$ssl_supported){
			$this->throwError('CURL ssl support not found');	
		}

	}

	public function getParseRoot(){
		return $this->_parseurl;
	}

	public function getAppId(){
		return $this->_appid;
	}

	/*
	 * All requests go through this function
	 * 
	 *
	 */	
	public function request($args){
		$isFile = false;
		$c = curl_init();
		curl_setopt($c, CURLOPT_TIMEOUT, 30);
		curl_setopt($c, CURLOPT_USERAGENT, 'parse.com-php-library/2.0');
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLINFO_HEADER_OUT, true);
		if(substr($args['requestUrl'],0,5) == 'files'){
			curl_setopt($c, CURLOPT_HTTPHEADER, array(
				'Content-Type: '.$args['contentType'],
				'X-Parse-Application-Id: '.$this->_appid,
				'X-Parse-Master-Key: '.$this->_masterkey
			));
			$isFile = true;
		}
		else if(
			(
				(substr($args['requestUrl'],0,5) == 'users') ||
				(substr($args['requestUrl'],0,8) == 'sessions') ||
				(substr($args['requestUrl'],0,20) == 'requestPasswordReset')
			)&& 
			isset($args['sessionToken'])){
			curl_setopt($c, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'X-Parse-Application-Id: '.$this->_appid,
				'X-Parse-REST-API-Key: '.$this->_restkey,
				'X-Parse-Session-Token: '.$args['sessionToken']
			));
		}
		else{
			curl_setopt($c, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'X-Parse-Application-Id: '.$this->_appid,
				'X-Parse-REST-API-Key: '.$this->_restkey,
				'X-Parse-Master-Key: '.$this->_masterkey
			));
		}
		curl_setopt($c, CURLOPT_CUSTOMREQUEST, $args['method']);
		$url = $this->_parseurl . $args['requestUrl'];

		if($args['method'] == 'PUT' || $args['method'] == 'POST'){
			if($isFile){
				$postData = $args['data'];
				curl_setopt($c, CURLOPT_POST, 1);
				curl_setopt($c, CURLOPT_BINARYTRANSFER, 1);
				//curl_setopt($c, CURLOPT_SAFE_UPLOAD, 1);
				//curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
				//curl_setopt($c, CURLOPT_VERBOSE, 1); 
				//curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); 
			}
			else{
				$postData = json_encode($args['data']);
			}
			curl_setopt($c, CURLOPT_POSTFIELDS, $postData );
		}

		if($args['requestUrl'] == 'login'){
			$urlParams = http_build_query($args['data'], '', '&');
			$url = $url.'?'.$urlParams;
		}
		if(array_key_exists('urlParams',$args)){
			$urlParams = http_build_query($args['urlParams'], '', '&');
    		$url = $url.'?'.$urlParams;
		}

		curl_setopt($c, CURLOPT_URL, $url);

		$response = curl_exec($c);
		$responseCode = curl_getinfo($c, CURLINFO_HTTP_CODE);

		$expectedCode = array('200');
		if($args['method'] == 'POST' && substr($args['requestUrl'],0,4) != 'push'){
			// checking if it is not cloud code - it returns code 200
			if(substr($args['requestUrl'],0,9) != 'functions'){
				$expectedCode = array('200','201');
			}
		}

		//BELOW HELPS WITH DEBUGGING		
		/*
		//if(!in_array($responseCode,$expectedCode)){
			//////////////////////////////////////////////////
			ob_start();
			var_dump($args);
			var_dump($url);
			var_dump($response);
			$varDumpResult = ob_get_clean();
			echo "<br><br><br>" . $varDumpResult . "<br>";
			//////////////////////////////////////////////////
		//}
		*/
		return $this->checkResponse($c, $response,$responseCode,$expectedCode);
	}

	public function dataType($type,$params){
		if($type != ''){
			switch($type){
				case 'date':
					$return = array(
						"__type" => "Date",
						"iso" => date("c", strtotime($params))
					);
					break;
				case 'bytes':
					$return = array(
						"__type" => "Bytes",
						"base64" => base64_encode($params)
					);			
					break;
				case 'pointer':
					$return = array(
						"__type" => "Pointer",
						"className" => $params[0],
						"objectId" => $params[1]
					);			
					break;
				case 'geopoint':
					$return = array(
						"__type" => "GeoPoint",
						"latitude" => floatval($params[0]),
						"longitude" => floatval($params[1])
					);			
					break;
				case 'file':
					$return = array(
						"__type" => "File",
						"name" => $params[0],
					);			
					break;
				case 'increment':
					$return = array(
						"__op" => "Increment",
						"amount" => $params[0]
					);
					break;
				case 'decrement':
					$return = array(
						"__op" => "Decrement",
						"amount" => $params[0]
					);
					break;
				default:
					$return = false;
					break;	
			}
			
			return $return;
		}	
	}

	public function throwError($msg,$code=0){
		throw new ParseLibraryException($msg,$code);
	}

	private function checkResponse($c, $response,$responseCode,$expectedCode){
		//TODO: Need to also check for response for a correct result from parse.com

		if(!in_array($responseCode,$expectedCode)){
			/*var_dump(curl_getinfo($c));
			var_dump(curl_error($c));
			var_dump($response);
			var_dump($responseCode);
			var_dump($expectedCode);
			die();
			*/
			$error = json_decode($response);
			//var_dump($error);die();
			//this line throws an error if there is no connection
			$this->throwError($error->error,$error->code);
		}
		else{
			//check for empty return
			if($response == '{}'){
				return true;
			}
			else{
				return json_decode($response);
			}
		}
	}
}


class ParseLibraryException extends Exception{
	public function __construct($message, $code = 0, Exception $previous = null) {
		//codes are only set by a parse.com error
		if($code != 0){
			$message = "parse.com error: ".$message;
		}

		parent::__construct($message, $code, $previous);
	}

	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}

}

