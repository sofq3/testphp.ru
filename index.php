<?php

class Db 
{
    public function __construct()
    {
        $this->db = new PDO("mysql:dbname=test_php;host=localhost", "root", "");
    }
    
    function get_cat()
    {
        $query = $this->db->prepare("SELECT * FROM cat");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $return = array();
        foreach ($result as $value) 
        { 
            $return[$value['parent_id']][] = $value;
        }
        return $return;
        
    }
}

class Tree extends Db
{
     
    function make_tree($data, $parent = 0, $level  = 0)
    {
        $array = $data[$parent];
        echo '<ul>';
            for($i = 0; $i < count($array); $i++) 
            {
                echo "<li><a href=''>" . $array[$i]['name'] . "</a>";
                $level++;

                if(isset($data[$array[$i]['id']])) 
                {
                    $this->make_tree($data, $array[$i]['id'], $level);
                }
                
                echo'</li>';
                $level--;
            }
            
        echo '</ul>';
       
    }
}

$db = new Db();
$data = $db->get_cat();
$tree = new Tree();
echo $tree->make_tree($data);

?>