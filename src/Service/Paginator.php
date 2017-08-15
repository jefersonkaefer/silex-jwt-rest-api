<?php

namespace Service;

use Doctrine\DBAL\Query\QueryBuilder;

class Paginator
{
    private $qb;
    private $currentPage;
    private $itemsPerPage;
    private $itemsCount;
    private $availablePages;

    public function __construct(QueryBuilder $qb, $currentPage, $itemsPerPage)
    {
        $this->setQueryBuilder($qb);
        $this->setItemsPerPage($itemsPerPage);
        $this->fetchItemsCount();
        $this->calculateAvailablePages();
        $this->setCurrentPage($currentPage);
    }

    private function setQueryBuilder(QueryBuilder $qb)
    {
        $this->qb = $qb;
    }

    private function setCurrentPage($currentPage)
    {
        if (!preg_match('/^[1-9]+[0-9]*$/', $currentPage)) {
            throw new \InvalidArgumentException('Current page should be a number, greater than 0.');
        }

        if ($currentPage > $this->availablePages) {
            throw new \InvalidArgumentException(sprintf('Current page should be a number less than %s.', $this->availablePages));
        }

        $this->currentPage = $currentPage;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    private function setItemsPerPage($itemsPerPage)
    {
        if (!preg_match('/^[1-9]+[0-9]*$/', $itemsPerPage)) {
            throw new \InvalidArgumentException('Items per page should be a number.');
        }

        if ($itemsPerPage < getenv('MIN_ITEMS_PER_PAGE')) {
            throw new \InvalidArgumentException(sprintf('Items per page should be a number greater than %s.', getenv('MIN_ITEMS_PER_PAGE')));
        }

        if ($itemsPerPage > getenv('MAX_ITEMS_PER_PAGE')) {
            throw new \InvalidArgumentException(sprintf('Items per page should be a number less than %s.', getenv('MAX_ITEMS_PER_PAGE')));
        }

        $this->itemsPerPage = $itemsPerPage;
    }

    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    private function fetchItemsCount()
    {
        $this->itemsCount = (clone $this->qb)->select('COUNT(*)')->execute()->fetchColumn();
    }

    public function getItemsCount()
    {
        return $this->itemsCount;
    }

    private function calculateAvailablePages()
    {
        $this->availablePages = ceil($this->itemsCount / $this->itemsPerPage);
    }

    public function getAvailablePages()
    {
        return $this->availablePages;
    }

    public function paginate()
    {
        $this->qb->setFirstResult(($this->currentPage > 0) ? $this->currentPage * $this->itemsPerPage - $this->itemsPerPage : 0);
        $this->qb->setMaxResults($this->itemsPerPage);

        return [
            'itemsCount'        => $this->getItemsCount(),
            'currentPage'       => $this->getCurrentPage(),
            'lastPage'          => $this->getAvailablePages(),
            'isPrevPage'        => (($this->currentPage - 1) > 0) ? true : false,
            'isNextPage'        => (($this->currentPage + 1) <= $this->availablePages) ? true : false
        ];
    }
}