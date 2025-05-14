<?php

namespace Drupal\vaterno_comments\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\EntityOwnerTrait;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\vaterno_comments\Helper\CommentHelper;

/**
 * Defines the Comment entity.
 *
 * @ContentEntityType(
 *  id = "vaterno_comment",
 *  label = @Translation("Comment"),
 *  handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\vaterno_comments\CommentListBuilder",
 *     "access" = "Drupal\vaterno_comments\Access\CommentAccessControlHandler",
 *
 *     "form" = {
 *         "default" = "Drupal\vaterno_comments\Form\CommentForm",
 *         "add" = "Drupal\vaterno_comments\Form\CommentForm",
 *         "edit" = "Drupal\vaterno_comments\Form\CommentForm",
 *         "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *         "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *  },
 *  base_table = "vaterno_comment",
 *  admin_permission = "administer site configuration",
 *  entity_keys = {
 *      "id" = "id",
 *      "uuid" = "uuid",
 *      "owner" = "user_id",
 *      "published" = "is_published",
 *  },
 *
 *  links = {
 *      "canonical" = "/admin/structure/vaterno_comments/{vaterno_comment}",
 *      "add-form" = "/admin/structure/vaterno_comments/add",
 *      "edit-form" = "/admin/structure/vaterno_comments/{vaterno_comment}/edit",
 *      "delete-form" = "/admin/structure/vaterno_comments/{vaterno_comment}/delete",
 *      "collection" = "/admin/structure/vaterno_comments",
 *  }
 *)
 */
class Comment extends ContentEntityBase implements CommentInterface, EntityPublishedInterface, EntityOwnerInterface
{
    use EntityOwnerTrait;
    use EntityPublishedTrait;

    public function label()
    {
        return mb_substr($this->get('comment_text')->value ?? '', 0, 30);
    }

    public function getCommentText(): string
    {
        return $this->get('comment_text')->value;
    }

    public function setCommentText($commentText): static
    {
        $this->set('comment_text', $commentText);
        return $this;
    }

    public function getCreatedTime(): \DateTime
    {
        return (new \DateTime())->setTimestamp($this->get('created')->value);
    }

    public function setCreatedTime(int|\DateTime $time): static
    {
        if ($time instanceof \DateTime) {
            $time = $time->getTimestamp();
        }

        $this->set('created', $time);
        return $this;
    }

    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields = parent::baseFieldDefinitions($entity_type);
        $fields += static::ownerBaseFieldDefinitions($entity_type);
        $fields += static::publishedBaseFieldDefinitions($entity_type);

        $fields['is_published']->setDefaultValue(FALSE)
            ->setLabel(t('Published'))
            ->setTranslatable(FALSE)
            ->setRevisionable(FALSE)
            ->setDisplayOptions('form', [
                'label' => 'hidden',
                'type' => 'boolean_checkbox',
            ]);

        $fields['comment_text'] = BaseFieldDefinition::create('text_long')
            ->setLabel(t('Comment text'))
            ->setDescription(t('The comment text.'))
            ->setSettings([
                'type' => 'text_long',
                'text_processing' => 1,
                'allowed_formats' => ['restricted_html'],
            ])
            ->setRequired(TRUE)
            ->setDisplayOptions('view', [
                'label' => 'string',
                'type' => 'string',
            ])
            ->setDisplayOptions('form', [
                'label' => 'hidden',
                'type' => 'string_textarea',
            ])
            ->setDisplayConfigurable('view', TRUE);

        $fields['created'] = BaseFieldDefinition::create('timestamp')
            ->setLabel(t('Created'))
            ->setDescription(t('Time the entity was created'))
            ->setDefaultValueCallback('Drupal\vaterno_comments\Helper\TimeHelper::getCurrentTime')
            ->setDisplayOptions('view', [
                'label' => 'string',
                'type' => 'timestamp',
                'weight' => 0,
            ])
            ->setRequired(TRUE);

        $allowedBundles = CommentHelper::getAllowedNodeBundles();
        $fields['node_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Referenced node'))
            ->setDescription(t('The comment reference to this node.'))
            ->setSetting('target_type', 'node')
            ->setDisplayOptions('form', [
                'type' => 'entity_reference_autocomplete',
                'settings' => [
                    'match_operator' => 'CONTAINS',
                    'autocomplete_type' => 'textfield',
                    'placeholder' => '',
                ],
            ])
            ->setSetting('handler', 'default')
            ->setSetting('handler_settings', [
                'target_bundles' => $allowedBundles,
            ])
            ->setDisplayOptions('view', [
                'label' => 'string',
                'type' => 'string',
            ])
            ->setRequired(TRUE);

        return $fields;
    }

    public function getUserId(): int
    {
        return $this->get('user_id')->target_id;
    }

    public function setUserId(int $userId): static
    {
        $this->set('user_id', $userId);
        return $this;
    }
}
