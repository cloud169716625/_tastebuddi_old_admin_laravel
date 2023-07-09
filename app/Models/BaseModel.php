<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BaseModel extends Model
{

    protected $errors    = [];

    // optional model properties
    // you will need the properties below if you want to create
    // your own pagination system

    protected $total     = 0;
    protected $pages     = 1;
    protected $page      = 1;
    protected $limit     = 20;
    protected $order_by  = '';
    protected $order_direction  = 'ASC';
    protected $offset    = 1;

    protected $collection    = null;
    protected $noLimit = false;

    /**
     * @var Illuminate\Database\Query\Builder
     */
    protected $query;
    protected $fields = [];

    public static function factory()
    {
        return new static;
    }

    /**
     * Alternative use of pagination in case you don't want to use the builder pagination() method
     * Can come in handy on vue based pagination. No need to use vue pagination components
     *
     * Sets the limit, page and order by values
     *
     * @param Request $r
     */
    public function setLpo(Request $r)
    {
        $this->limit            = $r->limit ? $r->limit : 20;
        $this->page             = $r->page ? $r->page : 1;
        $this->offset           = ($this->page-1) * $this->limit;
        $this->order_by         = $r->order_by ? $r->order_by :  $this->primaryKey;
        $this->order_direction  = $r->order_direction ? $r->order_direction : 'ASC';
        $this->noLimit          = $r->noLimit ?? false;
    }

    /**
     * Assigns the limit, page and order by value
     * to the query builder
     *
     * @return Illuminate\Database\Query\Builder
     */

    public function assignLpo()
    {
        if (! $this->noLimit) {
            $this->query->limit($this->limit);
            $this->query->offset($this->offset);
        }

        $this->query->orderBy($this->order_by, $this->order_direction);

        return $this->query;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Current page on the collection list
     * will set the offset of the sql query
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->page;
    }

    /**
     * returns an array of errors or the first error
     *
     * @param bool $first_only
     * @return array|string
     */
    public function getErrors($first_only = false)
    {
        if ($first_only) {
            return isset($this->errors[0]) ? $this->errors[0] : '';
        }
        return $this->errors;
    }

    /**
     * Return the errors in HTML form
     *
     * @return string
     */
    public function displayErrors()
    {
        if (! count($this->errors)) {
            return '';
        }

        $html = '<ul>';
        foreach ($this->errors as $e) {
            $html .= '<li>'.$e.'</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * Alias of find()
     * Handy use for IDE's to identify current Class returned
     *
     * @param array|string $id
     * @return static
     */
    public function f($id)
    {
        return $this->find($id);
    }

    /**
     * List of pages in array form for pagination
     *
     * @param $current_page
     * @param $total_pages
     * @return array
     */
    public function pageList()
    {
        $current_page  = $this->page;
        $total_pages   = ceil($this->getTotal() / $this->limit) ;

        $total_pages = ceil($total_pages);

        $delta       = 1;

        $left        = $current_page - $delta;
        $right       = $current_page + $delta + 1;
        $range       = [];

        $rangeWithDots = [];
        $l = 0 ;

        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == 1 || $i == $total_pages || $i >= $left && $i < $right) {
                $range[] = $i;
            }
        }

        foreach ($range as $i) {
            if ($l) {
                if ($i - $l === 2) {
                    $rangeWithDots[] = $l + 1;
                } elseif ($i - $l !== 1) {
                    $rangeWithDots[] = '...';
                }
            }
            $rangeWithDots[] =  $i;
            $l = $i;
        }

        $last = ( count($rangeWithDots) -1 );
        if (isset($rangeWithDots[ $last ])) {
            if ($rangeWithDots[$last] < $total_pages) {
                $rangeWithDots[] = '...';
                $rangeWithDots[] = $total_pages;
            }
        }

        return $rangeWithDots;
    }

    /**
     * Add a error message to the errors array
     *
     * @param $error_message
     */

    public function addError($error_message)
    {
        $this->errors[] = $error_message;
    }

    /**
     * Check if pagination still have next page
     *
     * @return boolean
     */
    public function hasNextPage()
    {
        return ($this->limit * $this->page) < $this->total;
    }
}
