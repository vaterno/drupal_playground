<?php

namespace Drupal\vaterno_comments\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CommentAccessControlHandler extends EntityAccessControlHandler implements EntityHandlerInterface
{
    public function __construct(
        EntityTypeInterface $entity_type,
        protected EntityTypeManagerInterface $entity_type_manager
    ) {
        parent::__construct($entity_type);
    }

    public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type)
    {
        return new static(
            $entity_type,
            $container->get('entity_type.manager')
        );
    }

    protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account)
    {
        $isOwner = ($entity->getOwnerId() == $account->id());

        switch ($operation) {
            case 'view':
                return AccessResult::allowedIf(
                    $account->hasPermission('user view vaterno_comments entities')  ||
                    $account->hasPermission('administer vaterno_comments')
                );
            case 'update':
                return AccessResult::allowedIf(
                    ($account->hasPermission('user edit vaterno_comments entities')  && $isOwner) ||
                    $account->hasPermission('administer vaterno_comments')
                );
            case 'delete':
                return AccessResult::allowedIf(
                    ($account->hasPermission('user delete vaterno_comments entities')  && $isOwner)  ||
                    $account->hasPermission('administer vaterno_comments')
                );
        }

        return AccessResult::neutral();
    }

    protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL)
    {
        return AccessResult::allowedIf(
            $account->hasPermission('user add vaterno_comments entities') ||
            $account->hasPermission('administer vaterno_comments')
        );
    }
}
