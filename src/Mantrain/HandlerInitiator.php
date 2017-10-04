<?php

namespace DeltaX\Mantrain;

use DeltaX\Mantrain\Handler;

class HandlerInitiator {

	protected $data;

	protected $code;

	protected $handler;

	protected $handlerClass;

	protected $handlerArguments;

	protected $handlerAuxilliaries;

	public function __construct($data = [], int $code = 0){
		
		$this->data = $data;
		$this->code = $code;
	}

	public function setHandler($handler, ...$arguments){
		
		// If one of the handlers had sent a non-zero code
		//before this.
		if ( $this->code != 0 ){
			return $this;
		}

		switch ($handler) {

			case $handler instanceof Handler:

				$this->handler = $handler;
				$this->handlerClass = get_class($handler);
				break;
			
			case is_string($handler):

				$this->handlerClass = $handler;
				$this->handlerArguments = $arguments;
				break;
		}

		return $this;
	}

	public function setHandlerArguments(...$arguments){

		$this->handlerArguments = $arguments;

		return $this;
	}

	public function set(string $methodName, ...$arguments){
	
		$this->handlerAuxilliaries[$methodName] = $arguments;

		return $this;

	}

	public function run(){

		return $this
			->initiateHandler()
			->prepareInputData()
			->runHandlerAuxilliaries()
			->runHandler();
	}

	protected function initiateHandler(){

		if ( empty($this->handler) && empty($this->handlerClass) ) {
			return $this;
		}

		$handlerClass = $this->handlerClass;
		$arguments = $this->handlerArguments;

		$this->handler = $this->handler ?? new $handlerClass( ...$arguments );

		return $this;
	}

	protected function prepareInputData(){

		if ( empty($this->handler) ) {
			return $this;
		}

		$this->handler = $this->handler->setInputData($this->data);

		return $this;	
	}

	protected function runHandlerAuxilliaries(){

		if ( empty($this->handler) ) {
			return $this;
		}

		$handler = $this->handler;

		foreach ($this->handlerAuxilliaries as $handlerMethod => $arguments) {

			$handler = $handler->{handlerMethod}(...$arguments);

		}

		$this->handler = $handler;

		return $this;
	}

	protected function runHandler(){

		if ( empty($this->handler) ) {
			return $this;
		}

		return $this->handler->run();
	}
	
	public function getData(){

		return $this->data();
	}
	
	public function getCode(){

		return $this->code();
	}

}
