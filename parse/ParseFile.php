<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

include_once 'ParseRestClient.php';

class ParseFile extends ParseRestClient{

	private $_fileName;
	private $_contentType;

	public function __construct($contentType='',$data=''){
		if($contentType != '' && $data !=''){
			$this->_contentType = $contentType;
			$this->data = $data;
		}
		
		parent::__construct();

	}

	public function save($fileName){
		if($fileName != '' && $this->_contentType != '' && $this->data != ''){
			$request = $this->request(array(
				'method' => 'POST',
				'requestUrl' => 'files/'.rawurlencode($fileName),
				'contentType' => $this->_contentType,
				'data' => file_get_contents($this->data, 'rb'),
				'tmp_name' => $this->data,
				'name' => rawurlencode($fileName)
			));
			return $request;
		}
		else{
			$this->throwError('Please make sure you are passing a proper filename as string (e.g. hello.txt)');
		}
	}

	public function delete($parseFileName){
		if($parseFileName != ''){
			$request = $this->request(array(
				'method' => 'DELETE',
				'requestUrl' => 'files/'.$parseFileName,
				'contentType' => $this->_contentType,
			));
			return $request;
		}
	}

	public function link($parseFileName){
		if($parseFileName != ''){
			return null;
		} else {
			return $this->getParseRoot().'files/'.$this->getAppId();
		}
	}

}


	private $_contentType;

	public function __construct($contentType='',$data=''){
		if($contentType != '' && $data !=''){
			$this->_contentType = $contentType;
			$this->data = $data;
		}
		
		parent::__construct();

	}

	public function save($fileName){
		if($fileName != '' && $this->_contentType != '' && $this->data != ''){
			$request = $this->request(array(
				'method' => 'POST',
				'requestUrl' => 'files/'.$fileName,
				'contentType' => $this->_contentType,
				'data' => $this->data,
			));
			return $request;
		}
		else{
			$this->throwError('Please make sure you are passing a proper filename as string (e.g. hello.txt)');
		}
	}

	public function delete($parseFileName){
		if($parseFileName != ''){
			$request = $this->request(array(
				'method' => 'DELETE',
				'requestUrl' => 'files/'.$parseFileName,
				'contentType' => $this->_contentType,
			));
			return $request;

		}
	}

	public function link($parseFileName){
		if($parseFileName != ''){
			return null;
		} else {
			return $this->getParseRoot().'files/'.$this->getAppId();
		}
	}

}

