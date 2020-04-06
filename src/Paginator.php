<?php

namespace Pagination;

use Pagination\Exception\FormatException;

class Paginator
{

    private $totalItems;

    protected $maxPagesToShow = 10;

    protected $url;

    protected $perPage;

    protected $pageCount;

    protected $currentPage;

    protected $previousText = 'Previous';

    protected $nextText = 'Next';

    public function __construct(int $totalItems, $perPage, $currentPage, $url = '')
    {
        $this->init($totalItems, $perPage, $currentPage, $url);
        return $this;
    }

    public function init(int $totalItems, $perPage, $currentPage = 1, $base_url = '')
    {

        if ($totalItems < $perPage)
            throw new FormatException('the total items must be greater than of the total item');

        $this->totalItems = $totalItems;
        $this->perPage = $perPage;
        $this->url = empty($base_url) ? '' : $base_url;
        $this->setPageCount($perPage);
        $this->setCurrentPage($currentPage);
    }

    public function url($page)
    {
        if ($page != null) {
            if ($this->url == null) {
                $protocol = 'http';
                if (isset($_SERVER['HTTPS']))
                    if (strtoupper($_SERVER['HTTPS']) == 'ON')
                        $protocol = 'https';

                return substr("$protocol://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],0,-1).$page;
            }

            return $this->url . '?page=' . $page;
        }

        return null;
    }

    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage > $this->getPageCount() ? $this->getPageCount() : ((int)$currentPage != 0 && $currentPage > 1 ? $currentPage : 1);
    }

    public function perPage()
    {
        return $this->perPage;
    }

    public function firstPage()
    {
        return ($this->currentPage() - 1) * $this->perPage;
    }

    public function previousPage()
    {
        if ($this->currentPage() - 1 > 0) {
            return $this->currentPage() - 1;
        }
        return null;
    }

    public function nextPage()
    {
        if ($this->currentPage() + 1 < $this->getPageCount()) {
            return $this->currentPage() + 1;
        }

        return null;
    }

    public function lastPage()
    {
        return $this->getPageCount();
    }

    public function setPageCount($value)
    {
        $this->pageCount = ceil($this->totalItems / $value);
    }

    public function getPageCount()
    {
        return $this->pageCount;
    }

    public function currentPage()
    {
        return $this->currentPage;
    }

    public function setMaxPagesToShow(int $maxPage)
    {
        $this->maxPagesToShow = $maxPage;
        return $this;
    }

    public function getMaxPagesToShow()
    {
        return $this->maxPagesToShow;
    }

    public function setPreviousText(string $previousText)
    {
        $this->previousText = $previousText;
        return $this;
    }

    public function getPreviousText()
    {
        return $this->previousText;
    }

    public function setNextText(string $nextText)
    {
        $this->nextText = $nextText;
        return $this;
    }

    public function getNextText()
    {
        return $this->nextText;
    }

    private function showPageCountLessThanMaxPageShow(&$html)
    {
        for ($i = 1; $i <= $this->getMaxPagesToShow(); $i++) {
            if ($this->currentPage() == $i) {
                $html .= '<li class="page-item active"><a class="page-link" href="' . $this->url($i) . '">' . $i . '</a></li>';
            } else {
                $html .= '<li class="page-item"><a class="page-link" href="' . $this->url($i) . '">' . $i . '</a></li>';
            }
        }
    }

    private function showPageCountBiggerThanMaxPageShow(&$html)
    {
        if ($this->currentPage() <= $this->getMaxPagesToShow() && $this->currentPage() != $this->getMaxPagesToShow()) {
            $this->showPageCountLessThanMaxPageShow($html);
            $html .= '<li class="page-item page-link">...</li><li class="page-item"><a class="page-link" href="' . $this->url($this->getPageCount()) . '">' . $this->getPageCount() . '</a></li>';
        } else {

            $startPage = $this->currentPage() - 5;
            $endPage = $this->currentPage() + 5 < $this->getPageCount() ? $this->currentPage() + 5 : $this->getPageCount();

            $html .= '<li class="page-item page-link">...</li>';
            for ($i = $startPage; $i <= $endPage; $i++) {
                if ($this->currentPage() == $i) {
                    $html .= '<li class="page-item active"><a class="page-link" href="' . $this->url($i) . '">' . $i . '</a></li>';
                } else {
                    $html .= '<li class="page-item"><a class="page-link" href="' . $this->url($i) . '">' . $i . '</a></li>';
                }
            }
            if ($this->currentPage() + 5 < $this->getPageCount())
                $html .= '<li class="page-item page-link">...</li><li class="page-item"><a class="page-link" href="' . $this->url($this->getPageCount()) . '">' . $this->getPageCount() . '</a></li>';
        }
    }

    private function makeHtml()
    {

        $html = '<nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
        <li class="page-item ' . ($this->currentPage() != 1 ?: 'disabled') . '">
        <a class="page-link " href="' . $this->url . '?page=' . $this->previousPage() . '" tabindex="-1" aria-disabled="true">' . $this->getPreviousText() . '</a>
        </li>';

        if ($this->getPageCount() < $this->getMaxPagesToShow()) {
            $this->showPageCountLessThanMaxPageShow($html);
        } else {
            $this->showPageCountBiggerThanMaxPageShow($html);
        }

        $html .= '<li class="page-item ' . ($this->currentPage() != $this->getPageCount() ?: 'disabled') . '">
        <a class="page-link" href="' . $this->url . '?page=' . $this->nextPage() . '">' . $this->getNextText() . '</a>
        </li>
        </ul>
        </nav>';
        return $html;
    }

    public function links()
    {
        return $this->makeHtml();
    }

    public function toArray()
    {
        return [
            'current_page' => $this->currentPage(),
            'first_page_url' => $this->url(1),
            'from' => $this->firstPage() + 1,
            'next_page_url' => $this->url($this->nextPage()),
            'per_page' => $this->perPage(),
            'prev_page_url' => $this->url($this->previousPage()),
            'to' => $this->lastPage(),
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

}
