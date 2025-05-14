<?php

namespace Drupal\vaterno_comments\Helper;

use Drupal\Core\Entity\EntityInterface;

class CommentCacheHelper
{
    public static function createCacheTags(array $comments = []): array
    {
        $tags = [
            'comments' => [],
            'users' => [],
        ];

        if (!empty($comments)) {
            array_reduce($comments, function($carry, $comment) use (&$tags) {
                /** @var \Drupal\vaterno_comments\Entity\CommentInterface|EntityInterface $comment */
                $tags['users'][$comment->getUserId()] = 'user:' . $comment->getUserId();
                $tags['comments'][$comment->id()] = 'vaterno_comment:' . $comment->id();
            });
        }

        $tags['users'][46] = '46';

        return $tags;
    }
}
