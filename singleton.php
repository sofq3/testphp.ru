<?php

class User
{
    protected $_db;
    protected static $_instance = null;
    private $_storage;
    
    protected function __construct($id)
    {
        $this->_db = new PDO("mysql:dbname=test_php;host=localhost", "root", "");
        $query = $this->_db->prepare("SELECT storage FROM `user` WHERE `id` = ? ");
        $query->execute(array($id));
        $result = $query->fetch(PDO::FETCH_OBJ);
        $this->_storage = unserialize($result->storage);
        var_dump($this->_storage);
    }
    
    protected function __clone(){}
    
    public static function getInstance($id)
    {
        if(!self::$_instance instanceof self)
        {
            self::$_instance = new User($id);
            return self::$_instance;
        }
    }
    
    public function get($data)
    {
        $key = explode("\\", $data);
        $array = $this->_storage;
        foreach($key as $val)
        {
            if(array_key_exists($val,$array))
            {
                $array = $array[$val];
            }
        }
       return($array);
    }
    
    public function set($path, $data)
    {
        $key = explode("\\", $path);
        $array = $this->_storage;
        $keys = array();
        
        foreach($key as $val)
        {
           if(array_key_exists($val,$array))
            {
                $array = $array[$val];
                $keys[] = $val;
            }
            else
            {
                $new_key = $val;
            }
        }
        
        if($new_key)
        {
            $input_array[$new_key] = $data;         
            $s_array =  $this->make_array($keys,$keys[0],$input_array);
            $out_array = array_merge_recursive($this->_storage,$s_array);
        }
        else
        {
            $s_array =  $this->make_array($keys,$keys[0],$data);
            $out_array = array_replace_recursive($this->_storage,$s_array);
        }
        return($out_array);
    }
    
    function make_array($data, $parent = 0, $input_array )
    {
        $array = array();
        $count = count($data);
        $i = 0;
        foreach ($data as $key => $val)
        {
            $i++;
            if($key == $parent)
            {
                
                $array[$val] = $this->make_array($data,$key+1, $input_array);
                if($i == $count)
                {
                    $array[$val] = $input_array;
                }
            }
        }
        return $array;
    }
}

$user = User::getInstance ( 1 );
print_r($user->get('age'));
echo "<br \>";
print_r($user->get('home\location'));
echo "<br \>";
print_r($user->get('work\main'));
echo "<br \>";
echo "---------SET-----------";
echo "<br \>";
print_r($user->set('work\new', ['role'=>'new_work','address'=>'homeland']));
echo "<br \>";
print_r($user->set('work\main', ['role'=>'new_work','address'=>'homeland']));
?>