<?php

namespace G4\CleanCore\Formatter\Collection;

use G4\CleanCore\Formatter\Collection\CollectionAbstract;
use G4\CleanCore\Paginator\Adapter\Iterator;

abstract class SliceAbstract extends CollectionAbstract
{

    public function format()
    {
        if ($this->_hasItems()) {
            foreach ($this->_getResourceCollection() as $resource) {
                $this->_formatOneResource($resource);
            }
        }

        return $this->_getPaginatorResponse();
    }

    public function isCollectionCountable()
    {
        return $this->_getResourceCollection() instanceof \Countable
            || is_array($this->_getResourceCollection());
    }

    public function isCollectionIterator()
    {
        return $this->_getResourceCollection() instanceof \Iterator;
    }

    protected function _getPaginatorResponse()
    {
        $totalItems = $this->_getTotalItemsCount();

        return array(
            'current_page_number' => !empty($this->_resource) ? $this->_resource['page'] : null,
            'total_item_count'    => $totalItems,
            'item_count_per_page' => !empty($this->_resource) ? $this->_resource['per_page'] : null,
            'current_item_count'  => count($this->_data),
            'page_count'          => !empty($this->_resource) ? ceil($totalItems / $this->_resource['per_page']) : 0,
            'current_items'       => $this->_data
        );
    }

    private function _collectionNotCountable()
    {
        throw new \Exception('Collection does not implement Countable', 500);
    }

    private function _getCollectionCount()
    {
        $collection = $this->_getResourceCollection();

        return $this->isCollectionCountable()
            ? count($collection)
            : $this->_collectionNotCountable();
    }

    private function _hasItems()
    {
        return $this->_getCollectionCount() > 0;
    }

    private function _getTotalItemsCount()
    {
        return is_object($this->_getResourceCollection()) && method_exists($this->_getResourceCollection(), 'getTotalItemsCount')
            ? $this->_getResourceCollection()->getTotalItemsCount()
            : $this->_getCollectionCount();
    }

}