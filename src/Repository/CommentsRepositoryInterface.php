<?php

namespace Drupal\vaterno_comments\Repository;

interface CommentsRepositoryInterface
{
    public function getCommentsByEntityId(int $entityId, int $offset = 0, int $itemsPerPage = 5): array;
    public function getTotalCommentsByEntityId(int $entityId): int;
}
