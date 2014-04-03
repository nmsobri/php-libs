<?php

namespace utility;

/**
 * Class Database
 * @package utility
 */
class Database extends \PDO
{

    /**
     * @var null|\PDO
     */
    protected $pdo = null;


    /**
     * @var null|string
     */
    protected $bind = null;


    /**
     * @var null|string
     */
    protected $query = null;


    /**
     * @var null|string
     */
    protected $where = null;


    /**
     * @var null|string
     */
    protected $order = null;


    /**
     * @var null|string
     */
    protected $group = null;


    /**
     * @var null|string
     */
    protected $limit = null;


    /**
     * @var null|boolean
     */
    protected $count = null;


    /**
     * @param string $dsn db_type:host=localhost;dbname=db_name
     * @param string $username
     * @param string $password
     * @param int $fetch_mode \PDO::FETCH_OBJ | \PDO::FETCH_ASSOC
     * @throws \Exception
     */
    public function __construct( $dsn, $username, $password, $fetch_mode = \PDO::FETCH_OBJ )
    {
        $format = '#[a-zA-Z]+:host=(http://)?[a-zA-Z0-9.]+;dbname=[a-zA-Z0-9]+#';

        if( !preg_match( $format, $dsn ) )
        {
            $error = 'Invalid dsn, dsn should be in the following format';
            $error .= ' [dbtype:host=localhost;dbname=db_name]';
            throw new \PDOException( $error );
        }

        $this->pdo = new \PDO( $dsn, $username, $password );
        $this->pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
        $this->pdo->setAttribute( \PDO::ATTR_DEFAULT_FETCH_MODE, $fetch_mode );
    }


    /**
     * Return Pdo instance so we can use raw power of \Pdo
     *
     * @return null|\PDO
     */
    public function pdo()
    {
        return $this->pdo;
    }


    /**
     * Run raw query
     *
     * @param string $sql
     * @param array $bind
     * @return Database
     *
     * query('select * from user')
     * query('select * from user where age > ? and level > ? ', array( $age, $level ) )
     * query('select * from user where age > :age and level > :level ', array( ':age' => $age, ':level' => $level ) )
     */
    public function query( $sql, $bind = null )
    {
        $this->bind( $bind );
        $this->query = $sql;
        return $this;
    }


    /**
     * Query to select data
     *
     * @param string $table
     * @param string $column
     * @return Database
     */
    public function select( $table, $column = '*' )
    {
        $column = ( is_string( $column ) ) ? $column : implode( ',', $column );
        $this->query = sprintf( 'SELECT %s FROM %s', $column, $table );
        return $this;
    }


    /**
     * Insert a value into a table
     *
     * @param string $table
     * @param array $data
     * @param array $bind
     * @return Database
     *
     * insert('users', array( array( 'username'=> '?', 'password'=> '?' ) ), array( $username, $password ) )
     * insert('users', array( array( 'username'=> ':username', 'password'=> ':password' ) ), array( ':username' => $username, ':password' => $password ) )
     */
    public function insert( $table, $data = array(), $bind = null )
    {
        $columns = null;
        $values = null;
        $this->bind( $bind );

        foreach( $data as $key => $val )
        {
            $columns .= sprintf( '%s,', $key );

            if( preg_match( '#(:.*|\?{1}|.*?\(.*?\))#', $val ) )
            {
                $values .= sprintf( '%s,',$val );
            }
            else
            {
                $values .= sprintf( '"%s",', $val );
            }
        }

        $columns = sprintf( '(%s)', trim( $columns, ',' ) );
        $values = sprintf( '(%s)', trim( $values, ',' ) );

        $this->query = sprintf( 'INSERT INTO %s %s VALUES %s', $table,
            $columns , $values
        );

        return $this;
    }


    /**
     * Update a value in a table
     *
     * @param string $table
     * @param array $data
     * @param array $bind
     * @return Database
     *
     * update('users', array( array( 'username'=> '?', 'password'=> '?' ) ), array( $username, $password ) )
     * update('users', array( array( 'username'=> ':username', 'password'=> ':password' ) ), array( ':username' => $username, ':password' => $password ) )
     */
    public function update( $table, $data = array(), $bind = null )
    {
        $segment = null;
        $this->bind( $bind );

        foreach( $data as $key => $val )
        {
            if( preg_match( '#(:.*|\?{1}|.*?\(.*?\))#', $val ) )
            {
                $segment .= sprintf( '%s=%s,', $key, $val );
            }
            else
            {
                $segment .= sprintf('%s="%s",', $key, $val);
            }
        }

        $segment = trim( $segment, ',' );
        $this->query = sprintf( 'UPDATE %s SET %s', $table, $segment );
        return $this;
    }


    /**
     * Delete a record from a table
     *
     * @param string $table
     * @return Database
     */
    public function delete( $table )
    {
        $this->query = sprintf( 'DELETE FROM %s', $table );
        return $this;
    }


    /**
     * Get total fo result of the query
     *
     * @return Database
     */
    public function totalrow()
    {
        $this->count = true;
        return $this;
    }


    /**
     * Setup where clause
     *
     * @param string $where
     * @param array $bind
     * @return Database
     * @throws \PDOException
     *
     * where( "username = ? and password = ?", array( $username, $password ) )
     * where( "username = :username and password = :password", array( ':username' => $username, ':password' => $password ) )
     */
    public function where( $where, $bind = null )
    {
        if( preg_match( '/where/i', $this->query ) )
        {
            $error = 'There is a where clause already inside the sql statement';
            throw new \PDOException( $error );
        }

        $this->bind( $bind );
        $this->where = sprintf(' WHERE %s', $where );
        return $this;
    }


    /**
     * Setup order by clause
     *
     * @param string $order
     * @return Database
     *
     * orderby( 'date Asc')
     */
    public function orderby( $order )
    {
        $this->order = sprintf(' ORDER BY %s', $order );
        return $this;
    }


    /**
     * Setup group by clause
     *
     * @param string $group
     * @return Database
     */
    public function groupby( $group )
    {
        $this->group = sprintf(' GROUP BY %s', $group );
        return $this;
    }


    /**
     * Setup limit clause
     *
     * @param int $start
     * @param int $limit
     * @return Database
     */
    public function limit( $start, $limit )
    {
        $this->limit = sprintf( ' LIMIT %s,%s', $start, $limit );
        return $this;
    }


    /**
     * Method to get last insert id from insert statement
     *
     * @return int
     */
    public function getLastInsertId()
    {
        return $this->pdo->lastInsertId();
    }


    /**
     * Execute the query
     *
     * @return mixed
     * @throws \Exception
     */
    public function execute()
    {
        try
        {
            $count = $this->count;
            $sql = sprintf( '%s%s%s%s%s', $this->query, $this->where,
                $this->order, $this->group,$this->limit
            );

            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute( $this->bind );
            $this->resetProp();
            return $this->result( $stmt, $sql, $count );
        }
        catch( \PDOException $e )
        {
            $e = sprintf( '%s<br><br>Query: %s', $e->getMessage(), $this->query );
            throw new \Exception( $e );
        }
    }


    /**
     * Return result of query
     *
     * @param \PDOStatement $stmt
     * @param string $sql
     * @param int $count
     * @return int|array
     */
    protected function result($stmt, $sql, $count )
    {
        if( preg_match( '/^sel/i', trim( $sql ) ) )
        {
            return ( $count ) ? count( $stmt->fetchAll() ) : $stmt->fetchAll();
        }

        return $stmt->rowCount();
    }


    /**
     * Build bind parameter
     *
     * @param mixed $bind
     * @return void
     */
    protected function bind( $bind )
    {
        if( is_null( $this->bind ) ) $this->bind = array();

        if( !empty( $bind ) )
        {
            if( is_array( $bind ) )
            {
                if( $this->isAssoc( $bind ) )
                {
                    foreach( $bind as $key => $val ) $this->bind[$key] = $val;
                }
                else
                {
                    foreach( $bind as $val ) $this->bind[] = $val;
                }
            }
            else
            {
                $this->bind[] = $bind;
            }
        }
    }


    /**
     * Check if an array is an associative array
     *
     * @access protected
     * @param array $arr
     * @return boolean
     */
    protected function isAssoc( $arr )
    {
        foreach( array_keys( $arr ) as $key )
        {
            if( !is_int( $key ) ) return true;
        }
        return false;
    }


    /**
     * Reset properties
     * @return void
     */
    protected function resetProp()
    {
        $this->query = $this->where = null;
        $this->order = $this->limit = null;
        $this->count = $this->bind = null;
        $this->group = null;
    }

}


?>