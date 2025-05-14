<?php

namespace Drupal\vaterno_comments\Helper;

use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ViolationHelper
{
    /**
     * @param EntityInterface $entity
     * @param bool $isException
     * @throws \Symfony\Component\Validator\Exception\ValidationFailedException
     * @return array
     */
    public static function getEntityMessages(EntityInterface $entity, bool $isException = true): array
    {
        $messages = [];
        $violations = $entity->validate();

        if (!empty($violations->count())) {
            $messages = array_reduce($violations->getIterator()->getArrayCopy(), function ($carry, $item) {
                $carry[] = ((string)$item->getMessage());
                return $carry;
            });

            if ($isException) {
                throw new ValidationFailedException(implode("\n", $messages), $violations);
            }
        }

        return $messages;
    }
}
