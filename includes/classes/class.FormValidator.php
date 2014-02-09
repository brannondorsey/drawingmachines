<?php 
class FormValidator{

    public $registration_rules = array(
        'first_name'=>array('display'=>'first name', 'type'=>'string',  'required'=>true, 'min'=>2, 'max'=>50, 'trim'=>true),
        'last_name'=>array('display'=>'last name', 'type'=>'string',  'required'=>true, 'min'=>2, 'max'=>50, 'trim'=>true),
        'email'=>array('display'=>'email', 'type'=>'email',  'required'=>true, 'min'=>5, 'max'=>50, 'trim'=>true),
        'url'=>array('display'=>'URL', 'type'=>'url', 'required'=>true, 'min'=>5, 'max'=>70, 'trim'=>true),
        'password'=>array('display'=>'password', 'type'=>'string',  'required'=>true, 'min'=>6, 'max'=>50, 'trim'=>true),
        'password_conf'=>array('display'=>'password confirm', 'type'=>'string',  'required'=>true, 'min'=>6, 'max'=>50, 'trim'=>true),
        'description'=>array('display'=>'description', 'type'=>'string',  'required'=>true, 'min'=>10, 'max'=>140, 'trim'=>true),
        'media'=>array('display'=>'media', 'type'=>'string',  'required'=>true, 'min'=>3, 'max'=>70, 'trim'=>true),
        'tags'=>array('display'=>'tags', 'type'=>'string',  'required'=>true, 'min'=>5, 'max'=>70, 'trim'=>true),
        'zip'=>array('display'=>'zip code', 'type'=>'numeric', 'required'=>true, 'min'=>1, 'max'=>99999999, 'trim'=>true)
    );
    /*
    * @errors array
    */
    public $errors = array();

    /*
    * @the validation rules array
    */
    private $validation_rules = array();

    /*
     * @the sanitized values array
     */
    public $sanitized = array();
     
    /*
     * @the source 
     */
    private $source = array();



    /**
     *
     * @the constructor, duh!
     *
     */
    public function __construct()
    {
    }

    /**
     *
     * @add the source
     *
     * @paccess public
     *
     * @param array $source
     *
     */
    public function addSource($source, $trim=false)
    {
        $this->source = $source;
    }

    public function matchPasswords() {
        //would be better handled with a regex here so that passwords would be automatically matched
        //based on if they had 'password' and 'password_conf' in their name.
    	if($this->source['password'] != $this->source['password_conf']) {
    		$this->errors['pword_match'] = 'passwords did not match';
    	}
    }


    /**
     *
     * @run the validation rules
     *
     * @access public
     *
     */
    public function run()
    {
        /*** set the vars ***/
        foreach( new ArrayIterator($this->validation_rules) as $var=>$opt)
        {
            if($opt['required'] == true)
            {
                $this->is_set($var, $opt['display']);
            }

            /*** Trim whitespace from beginning and end of variable ***/
            if( array_key_exists('trim', $opt) && $opt['trim'] == true )
            {
                $this->source[$var] = trim( $this->source[$var] );
            }

            switch($opt['type'])
            {
                case 'email':
                    $this->validateEmail($var, $opt['required'], $opt['display']);
                    if(!array_key_exists($var, $this->errors))
                    {
                        $this->sanitizeEmail($var);
                    }
                    break;

                case 'url':
                    $this->validateUrl($var, $opt['required'], $opt['display']);
                    if(!array_key_exists($var, $this->errors))
                    {
                        $this->sanitizeUrl($var);
                    }
                    break;

                case 'numeric':
                    $this->validateNumeric($var, $opt['min'], $opt['max'], $opt['required'], $opt['display']);
                    if(!array_key_exists($var, $this->errors))
                    {
                        $this->sanitizeNumeric($var);
                    }
                    break;

                case 'string':
                    $this->validateString($var, $opt['min'], $opt['max'], $opt['required'], $opt['display']);
                    if(!array_key_exists($var, $this->errors))
                    {
                        $this->sanitizeString($var);
                    }
                break;

                case 'float':
                    $this->validateFloat($var, $opt['required'], $opt['display']);
                    if(!array_key_exists($var, $this->errors))
                    {
                        $this->sanitizeFloat($var);
                    }
                    break;

                case 'ipv4':
                    $this->validateIpv4($var, $opt['required'], $opt['display']);
                    if(!array_key_exists($var, $this->errors))
                    {
                        $this->sanitizeIpv4($var);
                    }
                    break;

                case 'ipv6':
                    $this->validateIpv6($var, $opt['required'], $opt['display']);
                    if(!array_key_exists($var, $this->errors))
                    {
                        $this->sanitizeIpv6($var);
                    }
                    break;

                case 'bool':
                    $this->validateBool($var, $opt['required'], $opt['display']);
                    if(!array_key_exists($var, $this->errors))
                    {
                        $this->sanitized[$var] = (bool) $this->source[$var];
                    }
                    break;
            }
        }
    }


    /**
     *
     * @add a rule to the validation rules array
     *
     * @access public
     *
     * @param string $varname The variable name
     *
     * @param string $type The type of variable
     *
     * @param bool $required If the field is required
     *
     * @param int $min The minimum length or range
     *
     * @param int $max the maximum length or range
     *
     */
    public function addRule($varname, $type, $required=false, $min=0, $max=0, $trim=false)
    {
        $this->validation_rules[$varname] = array('type'=>$type, 'required'=>$required, 'min'=>$min, 'max'=>$max, 'trim'=>$trim);
        /*** allow chaining ***/
        return $this;
    }


    /**
     *
     * @add multiple rules to teh validation rules array
     *
     * @access public
     *
     * @param array $rules_array The array of rules to add
     *
     */
    public function AddRules(array $rules_array)
    {
        $this->validation_rules = array_merge($this->validation_rules, $rules_array);
    }

    /**
     *
     * @Check if POST variable is set
     *
     * @access private
     *
     * @param string $var The POST variable to check
     *
     */
    private function is_set($var, $display)
    {
        if(!isset($this->source[$var]) ||
            empty($this->source[$var]))
        {
            $this->errors[$var] = $display . ' is not set';
        }
    }



    /**
     *
     * @validate an ipv4 IP address
     *
     * @access private
     *
     * @param string $var The variable name
     *
     * @param bool $required
     *
     */
    private function validateIpv4($var, $required=false, $display)
    {
        if($required==false && strlen($this->source[$var]) == 0)
        {
            return true;
        }
        if(filter_var($this->source[$var], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE)
        {
            $this->errors[$var] = $display . ' is not a valid IPv4';
        }
    }

    /**
     *
     * @validate an ipv6 IP address
     *
     * @access private
     *
     * @param string $var The variable name
     *
     * @param bool $required
     *
     */
    public function validateIpv6($var, $required=false, $display)
    {
        if($required==false && strlen($this->source[$var]) == 0)
        {
            return true;
        }

        if(filter_var($this->source[$var], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE)
        {
            $this->errors[$var] = $display . ' is not a valid IPv6';
        }
    }

    /**
     *
     * @validate a floating point number
     *
     * @access private
     *
     * @param $var The variable name
     *
     * @param bool $required
     */
    private function validateFloat($var, $required=false, $display)
    {
        if($required==false && strlen($this->source[$var]) == 0)
        {
            return true;
        }
        if(filter_var($this->source[$var], FILTER_VALIDATE_FLOAT) === false)
        {
            $this->errors[$var] = $display . ' is an invalid float';
        }
    }

    /**
     *
     * @validate a string
     *
     * @access private
     *
     * @param string $var The variable name
     *
     * @param int $min the minimum string length
     *
     * @param int $max The maximum string length
     *
     * @param bool $required
     *
     */
    private function validateString($var, $min=0, $max=0, $required=false, $display)
    {
        if($required==false && strlen($this->source[$var]) == 0)
        {
            return true;
        }

        if(isset($this->source[$var]) &&
           !empty($this->source[$var]))
        {
            if(strlen($this->source[$var]) < $min)
            {
                $this->errors[$var] = $display . ' is too short';
            }
            elseif(strlen($this->source[$var]) > $max)
            {
                $this->errors[$var] = $display . ' is too long';
            }
            elseif(!is_string($this->source[$var]))
            {
                $this->errors[$var] = $display . ' is invalid';
            }
        }
    }

    /**
     *
     * @validate an number
     *
     * @access private
     *
     * @param string $var the variable name
     *
     * @param int $min The minimum number range
     *
     * @param int $max The maximum number range
     *
     * @param bool $required
     *
     */
    private function validateNumeric($var, $min=0, $max=0, $required=false, $display)
    {
        if($required==false && strlen($this->source[$var]) == 0)
        {
            return true;
        }
        if(filter_var($this->source[$var], FILTER_VALIDATE_INT, array("options" => array("min_range"=>$min, "max_range"=>$max)))===FALSE)
        {
            $this->errors[$var] = $display . ' is not a valid number';
        }
    }

    /**
     *
     * @validate a url
     *
     * @access private
     *
      * @param string $var The variable name
     *
     * @param bool $required
     *
     */
    private function validateUrl($var, $required=false, $display)
    {
        $clean_url = $this->processURLString($this->source[$var]);
        if($required==false && strlen($this->source[$var]) == 0)
        {
            return true;
        }
        if(filter_var($clean_url, FILTER_VALIDATE_URL) === FALSE)
        {
            $this->errors[$var] = $display . ' was filled out incorrectly';
        }
    }

    // this function processes a URL that 'should' be a full url (http://something.com/dfsdfs/)
    // and makes sure it contains the correct format to be included into a href attribute
    public function processURLString($urlString) {
        $urlString = trim($urlString);
        
        if($urlString) {
            $urlString = preg_replace('/https?:\/\//', '', $urlString);
            $urlString = preg_replace('/^www\./i', '', $urlString);
            $urlString = trim($urlString);
            $urlString  = 'http://'.$urlString;
        }
        
        return $urlString;
    }


    /**
     *
     * @validate an email address
     *
     * @access private
     *
     * @param string $var The variable name 
     *
     * @param bool $required
     *
     */
    private function validateEmail($var, $required=false, $display)
    {
        if($required==false && strlen($this->source[$var]) == 0)
        {
            return true;
        }
        if(filter_var($this->source[$var], FILTER_VALIDATE_EMAIL) === FALSE)
        {
            $this->errors[$var] = $display . ' is not a valid email address';
        }
    }


    /**
     * @validate a boolean 
     *
     * @access private
     *
     * @param string $var the variable name
     *
     * @param bool $required
     *
     */
    private function validateBool($var, $required=false, $display)
    {
        if($required==false && strlen($this->source[$var]) == 0)
        {
            return true;
        }
        filter_var($this->source[$var], FILTER_VALIDATE_BOOLEAN);
        {
            $this->errors[$var] = $display . ' is invalid';
        }
    }

    ########## SANITIZING METHODS ############
    

    /**
     *
     * @santize and email
     *
     * @access private
     *
     * @param string $var The variable name
     *
     * @return string
     *
     */
    public function sanitizeEmail($var)
    {
        $email = preg_replace( '((?:\n|\r|\t|%0A|%0D|%08|%09)+)i' , '', $this->source[$var] );
        $this->sanitized[$var] = (string) filter_var($email, FILTER_SANITIZE_EMAIL);
    }


    /**
     *
     * @sanitize a url
     *
     * @access private
     *
     * @param string $var The variable name
     *
     */
    private function sanitizeUrl($var)
    {
        $this->sanitized[$var] = (string) filter_var($this->source[$var],  FILTER_SANITIZE_URL);
    }

    /**
     *
     * @sanitize a numeric value
     *
     * @access private
     *
     * @param string $var The variable name
     *
     */
    private function sanitizeNumeric($var)
    {
        $this->sanitized[$var] = (int) filter_var($this->source[$var], FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     *
     * @sanitize a string
     *
     * @access private
     *
     * @param string $var The variable name
     *
     */
    private function sanitizeString($var)
    {
        $this->sanitized[$var] = (string) filter_var($this->source[$var], FILTER_SANITIZE_STRING);
    }
    
} /*** end of class ***/
?>