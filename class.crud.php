<?php

class Database extends PDO
{

    protected $db = null;
    protected $bind = null;
    protected $query = null;
    protected $where = null;
    protected $order = null;
    protected $limit = null;
    protected $count = null;



    /**
     * Constructor method
     * @access public
     * @param string $dsn mysql:host=localhost;dbname=db_name
     * @param string $username
     * @param string $password
     */
    public function __construct( $dsn, $username, $password )
    {
        try
        {
            $this->db = new PDO( $dsn, $username, $password );
            $this->db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }
        catch( PDOException $e )
        {
            $msg = "Message: {$e->getMessage()}";
            $msg .="\nFile:   {$e->getFile()}";
            $msg .="\nLine:   {$e->getLine()}";
            echo nl2br( $msg );
            exit(); /* cause pdo is stupid , its not stopping execution flow on exception */
        }
    }



    /**
     * Runn raw query
     * @access public
     * @param string $sql raw query
     * @param mixed $bind value to bind
     * @example query('select * from user')
     * @example query('select * from user where age > ? and level > ? ', array( $age, $level ) )
     * @example query('select * from user where age > :age and level > :level ', array( ':age' => $age, ':level' => $level ) )
     * @return Database for chaining
     */
    public function query( $sql, $bind = null )
    {
        $this->bind( $bind );
        $this->query = $sql;
        return $this;
    }



    /**
     * Query to select data
     * @access public
     */
    public function select( $table, $column = '*' )
    {
        $column = ( is_string( $column ) ) ? $column : implode( ',', $column );
        $this->query = 'SELECT ' . $column . ' FROM ' . $table;
        return $this;
    }



    /**
     * Insert a value into a table
     * @access public
     * @param string $table table name
     * @param mixed $data data to insert
     * @param mixed $bind value to bind
     * @example insert('users', array( array( 'username'=> '?', 'password'=> '?' ) ), array( $username, $password ) )
     * @example insert('users', array( array( 'username'=> ':username', 'password'=> ':password' ) ), array( ':username' => $username, ':password' => $password ) )
     * @return Database for chaining
     */
    public function insert( $table, $data = array( ), $bind = null )
    {
        $columns = null;
        $values = null;
        $this->bind( $bind );

        foreach ( $data as $key => $val )
        {
            $columns .= $key . ',';
            if ( preg_match( '#(:.*|\?{1}|.*?\(.*?\))#', $val ) )
            {
                $values .= $val . ',';
            }
            else
            {
                $values .= '"' . $val . '"' . ',';
            }
        }

        $columns = '(' . trim( $columns, ',' ) . ')';
        $values = '(' . trim( $values, ',' ) . ')';

        $this->query = 'INSERT INTO ' . $table . $columns . ' VALUES ' . $values;
        var_dump($this->query);
        return $this;
    }



    /**
     * Update a value in a table
     * @access public
     * @param string $table table name
     * @param mixed $data data to update
     * @param mixed $bind value to bind
     * @example update('users', array( array( 'username'=> '?', 'password'=> '?' ) ), array( $username, $password ) )
     * @example update('users', array( array( 'username'=> ':username', 'password'=> ':password' ) ), array( ':username' => $username, ':password' => $password ) )
     */
    public function update( $table, $data = array( ), $bind = null )
    {
        $segment = null;
        $this->bind( $bind );

        foreach ( $data as $key => $val )
        {
            if ( preg_match( '#(:.*|\?{1}|.*?\(.*?\))#', $val ) )
            {
                $segment .= $key . '=' . $val . ',';
            }
            else
            {
                $segment .= $key . '="' . $val . '",';
            }
        }

        $segment = substr( $segment, 0, -1 );
        $this->query = 'UPDATE ' . $table . ' SET ' . $segment;
        return $this;
    }



    /**
     * Delete a record from a table
     * @access public
     */
    public function delete( $table )
    {
        $this->query = 'DELETE FROM ' . $table;
        return $this;
    }



    public function totalrow()
    {
        $this->count = true;
        return $this;
    }



    /**
     *
     * Setup where clause
     * @access public
     * @param string $where raw sql condition
     * @param mixed $bind value to bind
     * @example where( "username = ? and password = ?", array( $username, $password ) )
     * @example where( "username = :username and password = :password", array( ':username' => $username, ':password' => $password ) )
     */
    public function where( $where, $bind = null )
    {
        if ( preg_match( '/where/i', $this->query ) )
        {
            throw new Exception( 'There is a where clause already in the sql statement' );
        }
        else
        {
            $this->bind( $bind );
            $this->where = ' WHERE ' . $where;
            return $this;
        }
    }



    /**
     *
     * Setup order by clause
     */
    public function orderby( $order )
    {
        $this->order = ' ORDER BY ' . $order;
        return $this;
    }



    /**
     *
     * Setup limit clause
     */
    public function limit( $start, $limit )
    {
        $this->limit = ' LIMIT ' . $start . ',' . $limit;
        return $this;
    }



    /**
     * Method to get last insert id From insert statement
     * @access public
     * @return int
     */
    public function getLastInsertId()
    {
        return $this->db->lastInsertId();
    }



    /**
     *
     * Execute the query
     */
    public function execute()
    {
        try
        {
            $sql = $this->query . $this->where . $this->order . $this->limit;
            $stmt = $this->db->prepare( $sql );
            $stmt->execute( $this->bind );
            $count = $this->count; //cache this value cause if use directly, statement below will always make $this->count = null
            $this->query = $this->where = $this->order = $this->limit = $this->count = $this->bind = null;

            if ( preg_match( '/^sel/i', trim( $sql ) ) )
            {
                return ($count) ? count( $stmt->fetchAll( PDO::FETCH_ASSOC ) ) : $stmt->fetchAll( PDO::FETCH_ASSOC );
            }
            else
            {
                return $stmt->rowCount();
            }
        }
        catch( PDOException $e )
        {
            $msg = "Message: {$e->getMessage()}";
            $msg .="\nFile:   {$e->getFile()}";
            $msg .="\nLine:   {$e->getLine()}";
            echo nl2br( $msg );
            exit();
        }
    }



    /**
     *
     * Build bind parameter
     */
    protected function bind( $bind )
    {
        if ( is_null( $this->bind ) )
        {
            $this->bind = array( );
        }

        if ( !empty( $bind ) )
        {
            if ( is_array( $bind ) )
            {
                foreach ( $bind as $key=>$val )
                {
                    $this->bind[ $key] = $val;
                }
            }
            else
            {
                $this->bind[ ] = $bind;
            }
        }
    }



}




?>