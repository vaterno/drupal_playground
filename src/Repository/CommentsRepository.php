<?php

namespace Drupal\vaterno_comments\Repository;

use Drupal\Core\Entity\EntityTypeManagerInterface;

class CommentsRepository implements CommentsRepositoryInterface
{
    protected EntityTypeManagerInterface $entityTypeManager;

    public function __construct(EntityTypeManagerInterface $entityTypeManager)
    {
        $this->entityTypeManager = $entityTypeManager;
    }

    public function getCommentsByEntityId(int $entityId, int $offset = 0, int $itemsPerPage = 5): array
    {
        $storage = $this->entityTypeManager->getStorage('vaterno_comment');

        $query = $storage->getQuery()
            ->condition('node_id', $entityId)
            ->condition('is_published', TRUE)
            ->sort('created', 'DESC')
            ->range($offset, $itemsPerPage)
            ->accessCheck(FALSE);
        $commentIds = $query->execute();

        return $storage->loadMultiple($commentIds);
    }

    public function getTotalCommentsByEntityId(int $entityId): int
    {
        $storage = $this->entityTypeManager->getStorage('vaterno_comment');

        return (int)$storage->getQuery()
            ->condition('node_id', $entityId)
            ->condition('is_published', TRUE)
            ->accessCheck(FALSE)
            ->count()
            ->execute();
    }
}
