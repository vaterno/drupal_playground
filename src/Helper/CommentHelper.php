<?php

namespace Drupal\vaterno_comments\Helper;

use Drupal\vaterno_comments\Entity\CommentInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

class CommentHelper
{
    public static function getPublishedStatusText(CommentInterface $entity): TranslatableMarkup
    {
        return $entity->isPublished()
            ? t('Published')
            : t('Unpublished');
    }

    public static function getAllowedNodeBundles(): array
    {
        $result = [];
        $machineNames = array_keys(\Drupal::entityTypeManager()
            ->getStorage('node_type')
            ->loadMultiple()
        );

        foreach ($machineNames as $machineName) {
            if ($machineName != 'page') {
                $result[$machineName] = $machineName;
            }
        }

        return $result;
    }
}
