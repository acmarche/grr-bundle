<?php


namespace Grr\GrrBundle\Api;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;

class RoomProvider implements CollectionDataProviderInterface, ItemDataProviderInterface
{
    public function getCollection(string $resourceClass, string $operationName = null)
    {
        // TODO: Implement getCollection() method.
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        // TODO: Implement getItem() method.
    }
}
