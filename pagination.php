<?php

/**
 * Pagination class
 * @author slier
 */

namespace utility;

class Pagination
{

    /**
     *Item per page
     * @var int
     */
    protected $itemsPerPage;


    /**
     *Total item
     * @var int
     */
    protected $totalItems;


    /**
     *Current page
     * @var int
     */
    protected $currentPage;


    /**
     *Previous page
     * @var int
     */
    protected $prevPage;


    /**
     *Next page
     * @var int
     */
    protected $nextPage;


    /**
     *Total page
     * @var int
     */
    protected $totalPages;


    /**
     * Range
     * @var int
     */
    protected $range;


    /**
     * Start range
     * @var int
     */
    protected $start_range;


    /**
     *Mid range
     * @var int
     */
    protected $midRange;


    /**End range
     * @var int
     */
    protected $end_range;


    /**
     * @var string
     */
    protected $return;


    /**
     * @var mixed
     */
    protected $queryString;


    /**
     * @var int
     */
    protected $limit;


    /**
     * Constructor Method
     *
     * @param int $totalItems
     * @param int $limit
     */
    public function __construct( $totalItems, $limit )
    {
        $this->currentPage = 1;
        $this->midRange = 7;
        $this->totalItems = $totalItems;
        $this->limit = $limit;
        $this->itemsPerPage = ( !empty( $_GET['ipp'] ) ) ? $_GET['ipp'] : $this->limit;
        $this->init();
        $this->paginate();
    }


    /**
     * Setup all necessary variables
     *
     * @return void
     */
    protected function init()
    {
        if( !is_numeric( $this->itemsPerPage ) OR $this->itemsPerPage <= 0 ){
            $this->itemsPerPage = $this->limit;
        }

        $this->totalPages = ceil( $this->totalItems / $this->itemsPerPage );

        $this->currentPage = ( !empty( $_GET['page'] ) ) ? $_GET['page'] : 1;


        if( $this->currentPage < 1 Or !is_numeric( $this->currentPage ) ){
            $this->currentPage = 1;
        }

        if( $this->currentPage > $this->totalPages && $this->totalPages > 0 ){
            $this->currentPage = $this->totalPages;
        }


        $this->prevPage = $this->currentPage - 1;
        $this->nextPage = $this->currentPage + 1;

        if( $_GET ){
            $args = explode( "&", $_SERVER['QUERY_STRING'] );

            foreach( $args as $arg ){
                $keyval = explode( "=", $arg );

                if( $keyval[0] != "page" And $keyval[0] != "ipp" ){
                    $this->queryString .= "&" . $arg;
                }
            }
        }
    }


    /**
     * Method to create pagination link
     *
     * @return string
     */
    protected function paginate()
    {
        if( $this->totalPages > 1 ){
            if( $this->currentPage != 1 And $this->totalItems >= 1 ){
                $this->return = '<a class="paginate" href="' . $_SERVER['PHP_SELF'] . '?page=' . $this->prevPage . '&ipp=' . $this->itemsPerPage . $this->queryString . '">&laquo;Previous</a>';
            }
            else{
                $this->return = '<span class="inactive">&laquo;Previous</span>';
            }

            $this->start_range = $this->currentPage - floor( $this->midRange / 2 );
            $this->end_range = $this->currentPage + floor( $this->midRange / 2 );

            if( $this->start_range <= 0 ){
                $this->start_range = 1;
                $this->end_range += abs( $this->start_range ) + 1;
            }

            if( $this->end_range > $this->totalPages ){
                $this->start_range -= $this->end_range - $this->totalPages;
                $this->end_range = $this->totalPages;
            }

            $this->range = range( $this->start_range, $this->end_range );

            for( $i = 1; $i <= $this->totalPages; $i++ ){
                if( $this->range[0] > 2 And $i == $this->range[0] ){
                    $this->return .= " ... ";
                }

                if( $i == 1 Or $i == $this->totalPages Or in_array( $i, $this->range ) ){
                    if( $i == $this->currentPage ){
                        $this->return .= '<a title="Go to page ' . $i . ' of ' . $this->totalPages . '" class="current" href="#">' . $i . '</a>';
                    }
                    else{
                        $this->return .= '<a class="paginate" title="Go to page ' . $i . ' of ' . $this->totalPages . '" href="' . $_SERVER['PHP_SELF'] . '?page=' . $i . '&ipp=' . $this->itemsPerPage . $this->queryString . '">' . $i . '</a>';
                    }
                }

                if( @$this->range[$this->midRange - 1] < $this->totalPages - 1 And $i == @$this->range[$this->midRange - 1] ){
                    $this->return .= " ... ";
                }
            }
            if( $this->currentPage != $this->totalPages And $this->totalItems >= 1 ){
                $this->return .= '<a class="paginate" href="' . $_SERVER['PHP_SELF'] . '?page=' . $this->nextPage . '&ipp=' . $this->itemsPerPage . $this->queryString . '">Next&raquo;</a>';
            }
            else{
                $this->return .= '<span class="inactive">Next&raquo;</span>';
            }
        }
    }


    /**
     * Method to create selection menu for item per page
     *
     * @return string
     */
    public function showItemPerPage()
    {
        $items = '';
        $ipp_array = array( 10, 25, 50, 100 );

        if( !in_array( $this->limit, $ipp_array ) ){
            array_push( $ipp_array, $this->limit );
            sort( $ipp_array );
        }

        if( $this->totalItems > $this->limit ){
            foreach( $ipp_array as $ipp_opt ){
                if( $ipp_opt == $this->itemsPerPage ){
                    $items .= '<option selected value="' . $ipp_opt . '">' . $ipp_opt . '</option>';
                }
                else{
                    $items .= '<option value="' . $ipp_opt . '">' . $ipp_opt . '</option>';
                }
            }
            var_dump( $this->queryString );
            return '<span class="paginate">Items per page:</span><select class="paginate" onchange="window.location=' . "'" . $_SERVER['PHP_SELF'] . "?page=1&ipp='+this[this.selectedIndex].value+'" . $this->queryString . "';return false" . '">' . $items . '</select>';
        }
    }


    /**
     * Method to create selection menu for jump to page
     *
     * @return string
     */
    public function showJumpMenu()
    {
        if( $this->totalPages > 1 ){
            $option = '';
            for( $i = 1; $i <= $this->totalPages; $i++ ){
                if( $i == $this->currentPage ){
                    $option .= '<option value="' . $i . '" selected>' . $i . '</option>';
                }
                else{
                    $option .= '<option value="' . $i . '">' . $i . '</option>';
                }
            }
            return '<span class="paginate">Page:</span><select class="paginate" onchange="window.location=' . "'" . $_SERVER['PHP_SELF'] . "?page='+this[this.selectedIndex].value+'&ipp=" . $this->itemsPerPage . $this->queryString . "';return false" . '">' . $option . '</select>';
        }
    }


    /**
     * Method to show the pagination link
     *
     * @return string
     */
    public function showPaginator()
    {
        return $this->return;
    }


    /**
     * Method to get current page number
     * Used in sql query for pagination
     *
     * @return int
     */
    public function getPageStart()
    {
        return ( $this->currentPage - 1 ) * $this->itemsPerPage;
    }


    /**
     * Method to get current page number
     * Used in sql query for pagination
     *
     * @return int
     */
    public function getPageLimit()
    {
        return $this->itemsPerPage;
    }


}


?>


