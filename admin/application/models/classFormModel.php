<?php
/*----------------------------------*/
//	classFormModel class
//	version 1.0.0.0
//	start 25/06/2012
//	last update 25/06/2012
//	autor garbagecat76

include_once('classValidator.php');
class classFormModel 
{
	private static $_names=array();
	public $errors= array();
	public $attributes=array();

	/*---------------*/
	public function attributeNames(){
	    $className=get_class($this);
	    if(!isset(self::$_names[$className])){
	        $class=new ReflectionClass(get_class($this));
    	    $names=array();
    		 foreach($class->getProperties() as $property){
	            $name=$property->getName();
	            if($property->isPublic() && !$property->isStatic())
	                $names[]=$name;
	        }
        	return self::$_names[$className]=$names;
    	}
	    else
    	    return self::$_names[$className];
	}
/*---------------*/
	public function attributeLabels()
	{
		return array();
	}
/*---------------*/
	public function generateAttributeLabel($name)
	{
		return ucwords(trim(strtolower(str_replace(array('-','_','.'),' ',preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $name)))));
	}
/*---------------*/	
	public function getAttributeLabel($attribute)
	{
		$labels=$this->attributeLabels();
		if(isset($labels[$attribute]))
			return $labels[$attribute];
		else
			return $this->generateAttributeLabel($attribute);
	}
	
/*---------------*/
	public function validate()
	{
		$validator = new CValidator;
		$validator->validate($this);
		
	}
/*---------------*/
	public function clearErrors()
	{
		$this->errors = array();
	}
/*---------------*/
	public function hasErrors()
	{
		return !empty($this->errors);
	}
/*---------------*/
	public function addError($name, $message)
	{
		$this->errors[$name] = $message;
	}
	
	public function addErrors($messages)
	{
		foreach($messages as $message){
			$this->addError($message[0], $message[1]);
		}
	}
	public function getErrors()
	{
		return $this->errors;
	}
	public function getError($name)
	{
		return (isset($this->errors[$name])) ? $this->errors[$name] : false;
	}

	
	public function printErrors()
	{
		foreach($this->errors as $key=>$val){
			printf("<p>%s</p>", $val);	
		}
	}

//	/*--------------*/
	public function getAttributes($names=null)
	{
		$values=array();
		foreach($this->attributeNames() as $name)
			$values[$name]=$this->$name;

		if(is_array($names))
		{
			$values2=array();
			foreach($names as $name)
				$values2[$name]=isset($values[$name]) ? $values[$name] : null;
			return $values2;
		}
		else
			return $values;
	}
	/*--------------*/
	public function getAttribute($name){
		return (isset($this->attributes[$name])) ? $this->attributes[$name] : false;
	}

	/*--------------*/
	public function setAttributes($values, $safe=true)
	{
		if(!is_array($values))
        	return;
		if (!$safe)
			$this->attributes = array();	
	    $attributenames = array_flip($this->attributeNames());
	    foreach($values as $name=>$value)
    	{
        	if(isset($attributenames[$name])){
            	$this->attributes[$name] = $value;
				$this->$name = $value;				
			}
	    }
	}

	
	public function setAttribute($name, $value)
	{
		$attributenames = array_flip($this->attributeNames());
		if(isset($attributenames[$name])){
            	$this->attributes[$name] = $value;
				$this->$name = $value;				
		}
    	return true;
	}




	/*--------------*/
	public function unsetAttributes($names=null)
	{
		if($names===null)
			$names=$this->attributeNames();
		foreach($names as $name){
			$this->attributes[$name] = $this->$name = null;
		}
	}

}