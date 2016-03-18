<?php
class MyDatabase{

    private $link = null;

    /**
     *构造函数.
     */
    public function __construct(){
        try {
            $this->link = new mysqli("localhost","root","your password","your database");
        } catch ( Exception $e ) {
            die( 'Unable to connect to database' );
        }
    }

    /**
     *析构函数
     */
    public function __destruct()
    {
        if( $this->link)
        {
            $this->link->close();
        }
    }

    /**
     * 创建数据库
     * @param $dbName
     * @return bool
     */
    public function createDB($dbName){
        if(mysqli_select_db($this->link,$dbName)){
            mysqli_select_db($this->link,"my_db");
            echo "database already exist<br>";
            return false;
        }
        echo $dbName;
        $sql = "CREATE DATABASE ".$dbName;
        $r = $this->link->query($sql);
        if($r){
            echo $sql."success<br>";
            return true;
        }
        else{
            echo $sql."failed<br>";
            return false;
        }

    }

    /**
     * 判断表是否存在
     * @param $tableName
     * @return bool
     */
    public function table_exists($tableName )
    {
        $sql =  "SELECT * FROM ".$tableName;
        echo $sql."<br>";
        $check = $this->link->query($sql);

        if($check !== false)
        {
            if( $check->num_rows > 0 )
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * 向表中插入数据
     * Example:
     * $user_data = array(
     *      'name' => 'Kawhi Leonard',
     *      'email' => 'email@address.com',
     *      'active' => 1
     * );
     * $database->insert( 'users_table', $user_data );
     *
     * @access public
     * @param string tableName
     * @param array userData
     * @return bool
     *
     */
    public function insert( $tableName, $userData = array() )
    {
        if( empty( $userData ) ) {
            echo "Data cannot be null";
            return false;
        }
        $sql = "INSERT INTO ".$tableName;
        $fields = array();
        $values = array();
        foreach( $userData as $field => $value ) {
            $fields[] = $field;
            $values[] = "'".$value."'";
        }
        $fields = ' (' . implode(', ', $fields) . ')';
        $values = '('. implode(', ', $values) .')';
        $sql .= $fields .' VALUES '. $values;
        echo $sql."<br>";
        $this->link->query( $sql );
        if( $this->link->error )
            return false;
        else
            return true;
    }

    /**
     * 更新表中的数据
     * Example:
     * $info = array("user"=>"Kawhi Leonard","age"=>30);
     * $where = array("id"=>1);
     * $database->update("book",$info,$where)
     * @param $tableName
     * @param array $userData
     * @param array $where
     * @param string $limit
     * @return bool
     */
    public function update($tableName, $userData=array(), $where=array(), $limit=''){

        if( empty( $userData ) )
        {
            return false;
        }
        $updates = array();
        $clause = array();
        $sql = "UPDATE ". $tableName ." SET ";
        foreach( $userData as $field => $value )
        {
            $updates[] = "`$field` = '$value'";
        }
        $sql .= implode(', ', $updates);
        if( !empty( $where ) )
        {
            foreach( $where as $field => $value )
            {
                $clause[] = "$field = '$value'";
            }
            $sql .= ' WHERE '. implode(' AND ', $clause);
        }

        if( !empty( $limit ) )
        {
            $sql .= ' LIMIT '. $limit;
        }
        $this->link->query( $sql );
        if( $this->link->error )
            return false;
        else
            return true;
    }

    /**
     * 删除表中的数据
     * Example:
     * $where = array("id"=>100);
     * $database->delete("book",$where)
     * @param $tableName
     * @param array $where
     * @param string $limit
     * @return bool
     */
    public function delete($tableName, $where=array(), $limit=''){
        if( empty( $where ) )
        {
            return false;
        }
        $clause = array();
        $sql = "DELETE FROM ". $tableName;
        foreach( $where as $field => $value )
        {
            $clause[] = "$field = '$value'";
        }
        $sql .= " WHERE ". implode(' AND ', $clause);

        if( !empty( $limit ) )
        {
            $sql .= " LIMIT ". $limit;
        }
        echo $sql;
        $this->link->query( $sql );
        if( $this->link->error )
        {
            return false;
        }
        else
        {
            return true;
        }
    }


    /**
     * 查询表中的数据
     * Example:
     * $users = $database->select("book",'','');
     * foreach($users as $user){
     * echo $user["id"]."    ".$user["user"].'   '. $user['age']."<br>";
     * }
     * @param $tableName
     * @param $field
     * @param array $where
     * @return array|bool|null
     */
    public function select($tableName, $field, $where=array()){
        $row = null;
        $results = null;
        $fields = empty($field)? '*':implode(',',$field);
        $sql = "SELECT ".$fields." FROM ".$tableName;
        if(empty($where)){
            echo $sql."<br>";
            $results = $this->link->query( $sql );
            if( $this->link->error ) {
                echo "this->link->error"."<br>";
                return null;
            }
            else {
                $row = array();
                while( $r = $results->fetch_assoc() )
                {
                    $row[] = $r;
                }
                if(empty($row))
                    echo " row is empty";
                return $row;
            }
        }
        $sql .= " WHERE ";
        $clause = array();
        foreach( $where as $field => $value )
        {
            $clause[] = "$field = '$value'";
        }
        $sql .= implode(' AND ', $clause);
        echo $sql."<br>";
        $results = $this->link->query( $sql );
        if( $this->link->error ) {
            echo "this->link->error";
            return false;
        }
        else {
            $row = array();
            while( $r = $results->fetch_assoc() ) {
                $row[] = $r;
            }
            if(empty($row))
                echo " row is empty";
            return $row;
        }

    }

}
