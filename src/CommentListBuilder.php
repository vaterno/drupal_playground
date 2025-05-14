<?php

namespace Drupal\vaterno_comments;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\vaterno_comments\Entity\CommentInterface;
use Drupal\vaterno_comments\Helper\CommentHelper;

class CommentListBuilder extends EntityListBuilder
{
    public function buildHeader()
    {
        $header = [
            'id' => $this->t('ID'),
            'label' => $this->t('Label'),
            'is_published' => $this->t('Published'),
            'created' => $this->t('Created'),
        ] + parent::buildHeader();

        return $header;
    }

    public function buildRow(EntityInterface $entity)
    {
        /** @var CommentInterface $entity */
        $row = [
            'id' => $entity->id(),
            'label' => $entity->label(),
            'is_published' => CommentHelper::getPublishedStatusText($entity),
            'created' => $entity->getCreatedTime()->format('Y-m-d H:i:s'),
        ] + parent::buildRow($entity);

        return $row;
    }

    protected function getEntityListQuery(): QueryInterface
    {
        $query = parent::getEntityListQuery();
        $query->sort('id', 'DESC');

        return $query;
    }
}
