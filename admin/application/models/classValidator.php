<?php

class CValidator{
	private $RequiredValidator;
	private $NumberValidator;
	private $StringValidator;
	private $EmailValidator;	
	
    public function __construct() {
		$this->RequiredValidator = new CRequiredValidator;
		$this->NumberValidator = new CNumberValidator;
		$this->StringValidator = new CStringValidator;
		$this->EmailValidator = new CEmailValidator;										
		
    }

    public function __destruct() {
    }

	public function validate(&$object){
		foreach ($object->rules() as $rule){
			switch($rule[1]) 
			{
			   case 'required': 
			       $this->RequiredValidator->validateAttribute($object, $rule[0]);
			   break;
			   case 'number': 
			       $this->NumberValidator->validateAttribute($object, $rule[0], $rule[2]);
			   break;
			   case 'string': 
			       $this->StringValidator->validateAttribute($object, $rule[0], $rule[2]);
			   break;
			   case 'email': 
			       $this->EmailValidator->validateAttribute($object, $rule[0], $rule[2]);
			   break;
			   
			} 
		}
	}
}

class CRequiredValidator{
	
	public function validateAttribute(&$object, $atributes = NULL)
	{
		$atributes_array = explode(',', $atributes);
		foreach($atributes_array as $attribute)
		{
			$attribute = trim($attribute);
			if (!$object->getAttribute($attribute) || $object->getAttribute($attribute)=='')
			{
				$object->addError($attribute, 'Attribute '.$object->attributeLabels[$attribute].' cannot be blank.');
			}
		}
		return;
	}
}

class CNumberValidator{
	public function validateAttribute(&$object, $atributes = NULL, $params=array())
	{
		$atributes_array = explode(',', $atributes);
		foreach($atributes_array as $attribute){
			$attribute = trim($attribute);
			$value = $object->getAttribute($attribute);
		    if($params['allowEmpty'] && !$value )
	        	return;
			if($params['integerOnly'])
			{		
		        if(!preg_match('/^\s*[+-]?\d+\s*$/',"$value"))
        		{
					$object->addError($attribute, 'Attribute '.$object->attributeLabels[$attribute].' must be an integer.');
		        }
		    }
		    else
		    {
        		if(!preg_match('/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/',"$value"))
		        {
					$object->addError($attribute, 'Attribute '.$object->attributeLabels[$attribute].' must be an number.');
		        }
		    }		
		    if($params['min'] && $value < $params['min'])
		    {
				$object->addError($attribute, 'Attribute '.$object->attributeLabels[$attribute].' is too small (minimum is '.$params['min'].')');				
		    }
		    if($params['max'] && $value > $params['max'])
		    {
				$object->addError($attribute, 'Attribute '.$object->attributeLabels[$attribute].' is too big (maximum is '.$params['max'].')');				
		    }
		}
	}
}

class CStringValidator{
	public function validateAttribute(&$object, $atributes = NULL, $params=array())
	{
		$atributes_array = explode(',', $atributes);
		foreach($atributes_array as $attribute)
		{
			$attribute = trim($attribute);
			$value = $object->getAttribute($attribute);
		    if($params['allowEmpty'] && !$value )
	        	return;
		    if($params['encoding']!==false && function_exists('mb_strlen'))
		        $length=mb_strlen($value, $params['encoding']);
		    else
        		$length=strlen($value);
		    if($params['min'] && $length < $params['min'])
		    {
				$object->addError($attribute, 'Attribute '.$object->attributeLabels[$attribute].'  is too short (minimum is '.$params['min'].' characters)');								
		    }
		    if($params['max'] && $length > $params['max'])
		    {
				$object->addError($attribute, 'Attribute '.$object->attributeLabels[$attribute].' is too long (maximum is '.$params['max'].' characters)');												
		    }
		    if($params['is'] && $length!==$params['is'])
		    {		
				$object->addError($attribute, 'Attribute '.$object->attributeLabels[$attribute].' is of the wrong length (should be '.$params['is'].' characters)');												
		    }	
		}
	}
}
class CEmailValidator{
	public $pattern='/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';
    public $fullPattern='/^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/';
    public $allowName=false;
    public $checkMX=false;
    public $checkPort=false;
    public $allowEmpty=true;
	
	public function validateAttribute(&$object, $atributes = NULL, $params=array())
	{
		$atributes_array = explode(',', $atributes);
		$this->allowName = isset($params['allowEmpty']) ? $params['allowEmpty'] : $this->allowName;
		$this->checkMX = isset($params['checkMX']) ? $params['checkMX'] : $this->checkMX;	
		$this->checkPort = isset($params['checkPort']) ? $params['checkPort'] : $this->checkPort;	
					
		foreach($atributes_array as $attribute)
		{
			$attribute = trim($attribute);
			$value = $object->getAttribute($attribute);
			var_dump($params['allowEmpty'] , !$value);
		    if($params['allowEmpty'] && !$value )
	        	return;
		    if(!$this->validateValue($value))
		    {
				$object->addError($attribute, 'Attribute '.$object->attributeLabels[$attribute].' is not a valid email address.');												
		    }
		}
	}
	public function validateValue($value)
	{
    	$valid=is_string($value) && (preg_match($this->pattern,$value) || $this->allowName && preg_match($this->fullPattern,$value));
	    if($valid)
    	    $domain=rtrim(substr($value,strpos($value,'@')+1),'>');
	    if($valid && $this->checkMX && function_exists('checkdnsrr'))
    	    $valid=checkdnsrr($domain,'MX');
	    if($valid && $this->checkPort && function_exists('fsockopen'))
    	    $valid=fsockopen($domain,25)!==false;
	    return $valid;
	}
	
}

?>