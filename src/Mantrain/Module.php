<?php

namespace DeltaX\Mantrain;

abstract class Module {

    /**
     * The data to be processed by every module.
     * Preferably an associative array derived from a JSON string
     *
     * @var array
     */
    protected $data = [];

    /**
     * Some sort of a flag whether to continue allowing
     * the data to be modified or not.
     * You can use HTTP codes for this
     *
     * @var int
     */
    protected $code = 0;

    /**
     * It must always have a data to process,
     * or atleast, an empty array
     *
     * @param array     $data
     */
    public function __construct(array $data = []){
        $this->data = $data;
    }

    /**
     * Returns self with no argument is supplied
     * or when its $code is not 0.
     *
     * @param string $module_class
     * @param array $args
     * @return self|\DeltaX\Mantrain\Module
     */
    public function run(string $module_class = null, array $args = []){

        $this->process();

        if ( $this->code === 0 && !is_null ($module_class)){
            return new $module_class($this->data, ...$args);
        }

        return $this;
    }

    /**
     * Whatever the descendants do. 
     * Might or might not mutate the $inputData.
     * This determines the $code and $data
     * 
     * @return self
     */
    abstract protected function process();

    /**
     * Gets the data in current module
     *
     * @return array
     */
    public function getData(){

        return $this->data;
    }

    /**
     * Gets the code in current module
     *
     * @return int 
     */
    public function getCode(){

        return $this->code;
    }

}
