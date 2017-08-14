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

    public function setQueryBuilder(QueryBuilder $qb)
    {
        $this->qb = $qb;
    }

    public function setCurrentPage($currentPage)
    {
        $currentPage = intval($currentPage);

        if ($currentPage < 1) {
            throw new \InvalidArgumentException('Current page should be a number greater than 0.');
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

    public function setItemsPerPage($itemsPerPage)
    {
        $itemsPerPage = intval($itemsPerPage);

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

    public function fetchItemsCount()
    {
        $qb = clone $this->qb;

        $qb
            ->select('COUNT(*)')
        ;

        $this->itemsCount = $qb->execute()->fetchColumn();
    }

    public function getItemsCount()
    {
        return $this->itemsCount;
    }

    public function calculateAvailablePages()
    {
        $this->availablePages = ceil($this->itemsCount / $this->itemsPerPage);
    }

    public function getAvailablePages()
    {
        return $this->availablePages;
    }

    public function paginate()
    {
        $firstResultItemId = ($this->currentPage > 0) ? $this->currentPage * $this->itemsPerPage - $this->itemsPerPage : 0;

        $this->qb->setFirstResult($firstResultItemId);
        $this->qb->setMaxResults($this->itemsPerPage);

        return [
            'itemsCount'        => $this->getItemsCount(),
            'itemsPerPage'      => $this->getItemsPerPage(),
            'availablePages'    => $this->getAvailablePages(),
            'currentPage'       => $this->getCurrentPage()
        ];
    }
}