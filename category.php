<?php

/**
 *  CREATE TABLE IF NOT EXISTS `category` (
 *  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 *  `parent` int(10) unsigned DEFAULT NULL,
 *  `title` varchar(255) DEFAULT NULL,
 *  `description` tinytext,
 *   PRIMARY KEY (`id`)
 *   ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;
 */

namespace utility;

class Category
{

    /**
     * Store Pdo instance
     * @var Pdo
     */
    protected $db;

    /**
     * Store table name for categories
     * @var string
     */
    protected $tableName;

    /**
     * Later..
     * @var PDOStatement
     */
    protected $categories;


    /**
     * @param Pdo $db
     * @param string $tableName
     */
    public function __construct( Pdo $db, $tableName )
    {
        $this->db = $db;
        $this->tableName = $tableName;
        $this->categories = $this->db->query( "SELECT * FROM {$this->tableName} order by parent, id " );
    }


    /**
     * Build multidimensional array of categories to pass to Form object
     * @return mixed
     */
    public function build()
    {
        static $collections = array();
        $rootCategory = $this->rootNodes();

        foreach ( $rootCategory as $x => $parent ) {
            foreach ( $this->categories as $y => $child ) {
                if ( $parent['id'] == $child['parent'] ) {
                    if ( @is_null( $collections[$parent['title']] ) ) {
                        $collections[$parent['title']] = array();
                    }
                    if ( $this->hasChild( $child['id'] ) ) {
                        $collections[$parent['title']][$this->padding( 3 ) . $child['title']] = $this->buildChild( $child['id'], 3, 1 );
                    }
                    else {
                        $collections[$parent['title']][$child['title']] = $child['title'];
                    }
                }
            }
            /* This part is for root category that dosent have child */
            if ( @is_null( $collections[$parent['title']] ) ) {
                $collections[$parent['title']] = $parent['title'];
            }
        }
        return $collections;
    }


    /**
     * Add new category
     * @access public
     * @param string $title
     * @param string $description
     * @param int $parent
     * @return int
     */
    public function add( $title, $description, $parent = null )
    {
        $parent = ( is_null( $parent ) ) ? 'NULL' : $parent;
        $sql = "INSERT INTO {$this->tableName} ( parent, title, description ) VALUES ( {$parent}, '{$title}', '{$description}' )";
        return $this->db->query( $sql );
    }


    /**
     * Update category
     * @param int $id
     * @param string $title
     * @param string $description
     * @param int $parent
     * @notes pass php null, to pass NULL datatypes for mysql
     * @return mixed
     */
    public function update( $id, $title, $description, $parent )
    {
        $parent = ( is_null( $parent ) ) ? 'NULL' : $parent;
        $sql = "UPDATE {$this->tableName} SET parent ={$parent}, title ='{$title}', description='{$description}' WHERE id = {$id} ";
        return $this->db->query( $sql );
    }


    /**
     * Delete category
     * @param int $id
     * @return int
     */
    public function delete( $id )
    {
        $childs = $this->nodeChild( $id );
        array_unshift( $childs, $id );
        $childs = implode( ',', $childs );
        $sql = "DELETE FROM {$this->tableName} WHERE id ={$id} or parent IN({$childs})";
        return $this->db->query( $sql );
    }


    /**
     * Check if current node has child
     * @param int $id node
     * @return boolean
     */
    protected function hasChild( $id )
    {
        foreach ( $this->categories as $key => $val ) {
            if ( $id == $val['parent'] ) {
                return true;
            }
        }
        return false;
    }


    /**
     * Get root category
     * @return mixed
     */
    protected function rootNodes()
    {
        $rootCategory = array();

        foreach ( $this->categories as $key => $val ) {
            if ( is_null( $val['parent'] ) ) {
                $rootCategory[$key] = $this->categories[$key];
            }
        }
        return $rootCategory;
    }


    /**
     * Build child for root category
     * @param int $id
     * @param $parentPad
     * @param $childPad
     * @return mixed
     */
    protected function buildChild( $id, $parentPad, $childPad )
    {
        $stack = array();

        foreach ( $this->categories as $key => $val ) {
            if ( $id == $val['parent'] ) {
                if ( $this->hasChild( $val['id'] ) ) {
                    $stack[$this->padding( $parentPad + 2 ) . $val['title']] = $this->buildChild( $val['id'], $parentPad + 2, $childPad + 2 );
                }
                else {
                    $stack[$val['title']] = $this->padding( $childPad ) . $val['title'];
                }
            }
        }
        return $stack;
    }


    /**
     * Get all child under this node
     * @param int $id node
     * @return mixed
     */
    protected function nodeChild( $id )
    {
        static $childs = array();

        foreach ( $this->categories as $key => $val ) {
            if ( $id == $val['parent'] ) {
                if ( $this->hasChild( $val['id'] ) ) {
                    array_push( $childs, $val['id'] );
                    $this->nodeChild( $val['id'] );
                }
            }
        }
        return $childs;
    }


    /**
     * Padding Nodes
     * @param int $times
     * @param string $pad
     * @return string
     */
    protected function padding( $times, $pad = "&nbsp;" )
    {
        return str_repeat( $pad, $times );
    }


}

?>