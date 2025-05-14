<?php

namespace Drupal\vaterno_comments\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\vaterno_comments\Entity\CommentInterface;

class CommentForm extends ContentEntityForm
{
    public function save(array $form, FormStateInterface $form_state)
    {
        /** @var CommentInterface $entity */
        $entity = $this->entity;
        $status = parent::save($form, $form_state);

        switch($status) {
            case SAVED_NEW:
                $this->messenger()->addMessage($this->t('Created a new product comment.'));
            break;
            default:
                $this->messenger()->addMessage($this->t('Saved changes.'));
            break;
        }

        $form_state->setRedirect('entity.vaterno_comment.canonical', [
            'vaterno_comment' => $entity->id(),
        ]);
    }
}
