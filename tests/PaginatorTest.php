<?php

namespace Tests;

use Pagination\Paginator;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{

    protected $paginator;

    protected $totalItem = 100;

    protected $perPage = 10;

    protected $currentPage = 5;

    public function setUp()
    {
        $this->paginator = new Paginator($this->totalItem, $this->perPage, $this->currentPage);
    }

    public function testGetTPageCount(): void
    {
        $pageCount = ceil($this->totalItem / $this->perPage);

        $this->assertEquals($pageCount,
            $this->paginator->getPageCount()
        );
    }

    public function testGetPerPage(): void
    {
        $this->assertEquals($this->perPage,
            $this->paginator->perPage()
        );
    }

    public function testGetFirstPage(): void
    {
        $firstPage = ($this->currentPage - 1) * $this->perPage;

        $this->assertEquals($firstPage,
            $this->paginator->firstPage()
        );
    }

    public function testGetPreviousPage(): void
    {
        $this->assertEquals($this->currentPage - 1,
            $this->paginator->previousPage()
        );
    }

    public function testGetNextPage(): void
    {
        $this->assertEquals($this->currentPage + 1,
            $this->paginator->nextPage()
        );
    }

    public function testGetLastPage(): void
    {
        $this->paginator->setPageCount($this->perPage);

        $this->assertEquals($this->paginator->getPageCount(),
            $this->paginator->lastPage()
        );
    }

    public function testGetCurrentPage(): void
    {
        $this->assertEquals($this->currentPage,
            $this->paginator->currentPage()
        );
    }

}


