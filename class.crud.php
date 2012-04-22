<?php

class Crud extends PDO
{

    protected $db = null;
    protected $bind = null;
    protected $query = null;
    protected $where = null;
    protected $order = null;
    protected $limit = null;


    /**
     * Constructor method
     * @access public
     * @param string $dsn mysql:host=localhost;dbname=db_name
     * @param string $username
     * @param string $password
     */
    public function __construct( $dsn, $username, $password )
    {
        $this->connect( $dsn, $username, $password );
    }

    /**
     * @Connect to the database and set the error mode to Exception
     * @access private
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @return mixed 
     */
    protected function connect( $dsn, $username, $password )
    {
        if( !$this->db instanceof PDO )
        {
            $this->db = new PDO( $dsn, $username, $password );
            $this->db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }
    }


    /**
     * Runn raw query
     * @access public
     * @param string $sql
     * @example $obj->query('select * from user')
     * @return if $return=='y'=return result set
     * @return mixed
     */
    public function query( $sql, $bind = null )
    {
        $this->bind = $this->bind( $bind );
        $this->query = $sql;
        return $this;
    }


    /**
     * Query to select data
     * @access public
     */
    public function select( $table, $column )
    {
        $column = ( is_string( $column ) ) ? $column : implode( ',', $column );
        $this->query = 'SELECT ' . $column . ' FROM ' . $table;
        return $this;
    }


    /**
     * Insert a value into a table
     * @access public
     */
    public function insert( $table, $data = array(), $bind = null )
    {
        $columns = null;
        $values = null;
        $this->bind = $this->bind( $bind );

        foreach( $data as $key => $val )
        {
            $columns .= $key . ',';
            $values .= '"' . $val . '"' . ',';
        }

        $columns = '(' . trim( $columns, ',' ) . ')';
        $values = '(' . trim( $values, ',' ) . ')';

        $this->query = 'INSERT INTO ' . $table . $columns . ' VALUES ' . $values;
        return $this;
    }


    /**
     * Update a value in a table
     * @access public
     */
    public function update( $table, $data = array(), $bind = null )
    {
        $segment = null;
        $this->bind = $this->bind( $bind );

        foreach( $data as $key => $val )
        {
            $segment .= $key . '="' . $val . '",';
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


    /**
     * 
     * Setup where clause
     */
    public function where( $where, $bind = null )
    {
        $this->bind = $this->bind( $bind );
        $this->where = ' WHERE ' . $where;
        return $this;
    }


    /**
     * 
     *Setup order by clause 
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
     *Execute the query 
     */
    public function execute()
    {
        try
        {
            $sql = $this->query . $this->where . $this->order . $this->limit;
            $stmt = $this->db->prepare( $sql );
            $stmt->execute( $this->bind );
            $this->query = $this->where = $this->order = $this->limit = null;

            if( preg_match( '/^sel/i', trim( $sql ) ) )
            {
                return $stmt->fetchAll( PDO::FETCH_ASSOC );
            }
            else
            {
                return $stmt->rowCount();
            }
        }
        catch ( PDOException $e )
        {
            echo $e->getMessage();
        }
    }


    /**
     * 
     * Build bind parameter
     */
    protected function bind( $bind )
    {
        if( !is_array( $bind ) )
        {
            if( !empty( $bind ) )
                $bind = array( $bind );
            else
                $bind = array();
        }
        return $bind;
    }


}

?>