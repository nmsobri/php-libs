<?php

/**
 * New and improve pagination class
 * @author slier
 */
class Pagination
{

    /**
     *
     * @var int
     * @access protected
     */
    protected $itemsPerPage;



    /**
     *
     * @var int
     * @access protected
     */
    protected $totalItems;



    /**
     *
     * @var int
     * @access protected
     */
    protected $currentPage;



    /**
     *
     * @var int
     * @access protected
     */
    protected $totalPages;



    /**
     *
     * @var int
     * @access protected
     */
    protected $midRange;



    /**
     *
     * @var string
     * @access protected
     */
    protected $return;



    /**
     *
     * @var mixed
     * @access protected
     */
    protected $queryString;



    /**
     *
     * @var int
     * @access protected
     */
    protected $limit;




    /**
     *
     * Constructor Method
     * @param int $totalItems
     * @param int $limit
     */
    public function __construct( $totalItems, $limit )
    {
        $this->currentPage = 1;
        $this->midRange = 7;
        $this->totalItems = $totalItems;
        $this->limit = $limit;
        $this->itemsPerPage = (!empty( $_GET[ 'ipp' ] ) ) ? $_GET[ 'ipp' ] : $this->limit;
        $this->init();
        $this->paginate();
    }




    /**
     * Setup all necessary variables
     * @access protected
     */
    protected function init()
    {
        if ( !is_numeric( $this->itemsPerPage ) OR $this->itemsPerPage <= 0 )
        {
            $this->itemsPerPage = $this->limit;
        }

        $this->totalPages = ceil( $this->totalItems / $this->itemsPerPage );

        $this->currentPage = (!empty( $_GET[ 'page' ] ) ) ? $_GET[ 'page' ] : 1;


        if ( $this->currentPage < 1 Or !is_numeric( $this->currentPage ) )
        {
            $this->currentPage = 1;
        }

        if ( $this->currentPage > $this->totalPages && $this->totalPages > 0 )
        {
            $this->currentPage = $this->totalPages;
        }


        $prev_page = $this->currentPage - 1;
        $next_page = $this->currentPage + 1;

        if ( $_GET )
        {
            $args = explode( "&", $_SERVER[ 'QUERY_STRING' ] );

            foreach ( $args as $arg )
            {
                $keyval = explode( "=", $arg );

                if ( $keyval[ 0 ] != "page" And $keyval[ 0 ] != "ipp" )
                {
                    $this->queryString .= "&" . $arg;
                }
            }
        }
    }




    /**
     *
     * Method to create pagination link
     * @access protected
     * @return string
     */
    protected function paginate()
    {
        if ( $this->totalPages > 1 )
        {
            $this->return = ($this->currentPage != 1 And $this->totalItems >= 1) ? "<a class=\"paginate\" href=\"{$_SERVER[ 'PHP_SELF' ]}?page=$prev_page&ipp=$this->itemsPerPage$this->queryString\">&laquo; Previous</a> " : "<span class=\"inactive\" href=\"#\">&laquo; Previous</span> ";

            $this->start_range = $this->currentPage - floor( $this->midRange / 2 );
            $this->end_range = $this->currentPage + floor( $this->midRange / 2 );

            if ( $this->start_range <= 0 )
            {
                $this->end_range += abs( $this->start_range ) + 1;
                $this->start_range = 1;
            }

            if ( $this->end_range > $this->totalPages )
            {
                $this->start_range -= $this->end_range - $this->totalPages;
                $this->end_range = $this->totalPages;
            }

            $this->range = range( $this->start_range, $this->end_range );

            for ( $i = 1; $i <= $this->totalPages; $i++ )
            {
                if ( $this->range[ 0 ] > 2 And $i == $this->range[ 0 ] )
                {
                    $this->return .= " ... ";
                }

                if ( $i == 1 Or $i == $this->totalPages Or in_array( $i, $this->range ) )
                {
                    $this->return .= ( $i == $this->currentPage ) ? "<a title=\"Go to page $i of $this->totalPages\" class=\"current\" href=\"#\">$i</a> " : "<a class=\"paginate\" title=\"Go to page $i of $this->totalPages\" href=\"{$_SERVER[ 'PHP_SELF' ]}?page=$i&ipp=$this->itemsPerPage$this->queryString\">$i</a> ";
                }

                if ( $this->range[ $this->midRange - 1 ] < $this->totalPages - 1 And $i == $this->range[ $this->midRange - 1 ] )
                {
                    $this->return .= " ... ";
                }
            }

            $this->return .= ( ($this->currentPage != $this->totalPages And $this->totalItems >= 1) ) ? "<a class=\"paginate\" href=\"{$_SERVER[ 'PHP_SELF' ]}?page=$next_page&ipp=$this->itemsPerPage$this->queryString\">Next &raquo;</a>\n" : "<span class=\"inactive\" href=\"#\">Next &raquo;</span>\n";
        }
    }




    /**
     *
     * Method to create selection menu for item per page
     * @access public
     * @return string
     */
    public function showItemPerPage()
    {
        $items = '';
        $ipp_array = array( 10, 25, 50, 100 );

        if ( !in_array( $this->limit, $ipp_array ) )
        {
            array_push( $ipp_array, $this->limit );
            sort( $ipp_array );
        }

        if ( $this->totalItems > $this->limit )
        {
            foreach ( $ipp_array as $ipp_opt )
            {
                $items .= ( $ipp_opt == $this->itemsPerPage) ? "<option selected value=\"$ipp_opt\">$ipp_opt</option>\n" : "<option value=\"$ipp_opt\">$ipp_opt</option>\n";
            }

            return "<span class=\"paginate\">Items per page:</span><select class=\"paginate\" onchange=\"window.location='{$_SERVER[ 'PHP_SELF' ]}?page=1&ipp='+this[this.selectedIndex].value+'$this->queryString';return false\">$items</select>\n";
        }
    }




    /**
     *
     * Method to create selection menu for jump to page
     * @access public
     * @return string
     */
    public function showJumpMenu()
    {
        if ( $this->totalPages > 1 )
        {
            $option = '';

            for ( $i = 1; $i <= $this->totalPages; $i++ )
            {
                $option .= ( $i == $this->currentPage) ? "<option value=\"$i\" selected>$i</option>\n" : "<option value=\"$i\">$i</option>\n";
            }

            return "<span class=\"paginate\">Page:</span><select class=\"paginate\" onchange=\"window.location='{$_SERVER[ 'PHP_SELF' ]}?page='+this[this.selectedIndex].value+'&ipp=$this->itemsPerPage$this->queryString';return false\">$option</select>\n";
        }
    }




    /**
     *
     * Method to show the pagination link
     * @access public
     * @return string
     */
    public function showPaginator()
    {
        return $this->return;
    }




    /**
     *
     * Method to get current page number
     * Used in sql query for pagination
     * @access public
     * @return int
     */
    public function getPageStart()
    {
        return ( $this->currentPage - 1 ) * $this->itemsPerPage;
    }




    /**
     *
     * Method to get current page number
     * Used in sql query for pagination
     * @access public
     * @return int
     */
    public function getPageLimit()
    {
        return $this->itemsPerPage;
    }




}