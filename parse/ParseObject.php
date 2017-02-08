<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

include_once 'ParseRestClient.php';

class ParseObject extends ParseRestClient{

	public $_includes = array();
	private $_className = '';

	public function __construct($name){
		parent::__construct();

		if($name != ''){
			$this->_className = $name;
		}
		else{
			$this->throwError('include the className when creating a ParseObject');
		}

	}

	public function __set($name,$value){
		if($name != '_className'){
			$this->data[$name] = $value;
		}
	}

	public function create(){
		if(count($this->data) > 0 && $this->_className != ''){
			$request = $this->request(array(
				'method' => 'POST',
				'requestUrl' => 'classes/'.$this->_className,
				'data' => $this->data,
			));
			return $request;
		}
	}

	public function get($id=''){
		$urlParams = array();
		if($this->_className != ''){
			if(!empty($this->_includes)){
				$urlParams['include'] = implode(',',$this->_includes);
			}
			if (!empty($id)){
				$request = $this->request(array(
					'method' => 'GET',
					'requestUrl' => 'classes/'.$this->_className.'/'.$id,
					'urlParams' => $urlParams
				));
			} else {
				$request = $this->request(array(
					'method' => 'GET',
					'requestUrl' => 'classes/'.$this->_className,
					'urlParams' => $urlParams
				));
			}
			// var_dump($request);
			return $request;
		}
	}

	public function update($id=''){
		if(count($this->data) > 0 && $this->_className != ''){
			if (!empty($id)){
				$request = $this->request(array(
					'method' => 'PUT',
					'requestUrl' => 'classes/'.$this->_className.'/'.$id,
					'data' => $this->data,
				));			
			} else {
				$request = $this->request(array(
					'method' => 'PUT',
					'requestUrl' => 'classes/'.$this->_className,
					'data' => $this->data,
				));
			}

			return $request;
		}
	}

	public function increment($field,$amount){
		$this->data[$field] = $this->dataType('increment', $amount);
	}

	public function decrement($id){
		$this->data[$field] = $this->dataType('decrement', $amount);
	}


	public function delete($id){
		if($this->_className != '' && !empty($id)){
			$request = $this->request(array(
				'method' => 'DELETE',
				'requestUrl' => 'classes/'.$this->_className.'/'.$id
			));

			return $request;
		}		
	}

	public function addInclude($name){
		$this->_includes[] = $name;
	}
}

