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

    /**
     * Paginator constructor which set class properties.
     *
     * @param QueryBuilder $qb
     * @param $currentPage
     * @param $itemsPerPage
     */
    public function __construct(QueryBuilder $qb, $currentPage, $itemsPerPage)
    {
        $this->setQueryBuilder($qb);
        $this->setItemsPerPage($itemsPerPage);
        $this->fetchItemsCount();
        $this->calculateAvailablePages();
        $this->setCurrentPage($currentPage);
    }

    /**
     * Method which set qb property as QueryBuilder instance.
     *
     * @param QueryBuilder $qb
     */
    private function setQueryBuilder(QueryBuilder $qb)
    {
        $this->qb = $qb;
    }

    /**
     * Method which set currentPage property.
     *
     * @param $currentPage
     * @throws \InvalidArgumentException if $currentPage is not a valid number.
     * @throws \InvalidArgumentException if $currentPage is a number greater that available pages count.
     */
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

    /**
     * Method which return value of currentPage property.
     *
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Method which set itemsPerPage property.
     *
     * @param $itemsPerPage
     *
     * @throws \InvalidArgumentException if $itemsPerPage is not a valid number.
     * @throws \InvalidArgumentException if $currentPage is a number less than MIN_ITEMS_PER_PAGE.
     * @throws \InvalidArgumentException if $currentPage is a number greater than MAX_ITEMS_PER_PAGE.
     */
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

    /**
     * Method which return value of itemsPerPage property.
     *
     * @return integer
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * Method which calculate query rows amount.
     */
    private function fetchItemsCount()
    {
        $qb = clone $this->qb;

        $this->itemsCount = $qb->select('COUNT(*)')->execute()->fetchColumn();
    }

    /**
     * Method which return value of itemsCount property.
     *
     * @return integer
     */
    public function getItemsCount()
    {
        return $this->itemsCount;
    }

    /**
     * Method which calculate availablePages from itemsCount and itemsPerPage properties.
     */
    private function calculateAvailablePages()
    {
        $this->availablePages = ceil($this->itemsCount / $this->itemsPerPage);
    }

    /**
     * Method which return value of availablePages property.
     *
     * @return integer
     */
    public function getAvailablePages()
    {
        return $this->availablePages;
    }

    /**
     * Method which set QB limit property and return an array of pagination informations.
     *
     * @return array
     */
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