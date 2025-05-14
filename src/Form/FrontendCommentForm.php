<?php

namespace Drupal\vaterno_comments\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\vaterno_comments\Entity\Comment;
use Drupal\vaterno_comments\Helper\ViolationHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class FrontendCommentForm extends FormBase
{
    public function __construct(
        protected EntityTypeManagerInterface $entityTypeManager,
    ) {
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('entity_type.manager')
        );
    }

    public function getFormId()
    {
        return 'vaterno_comments_frontend_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL)
    {
        $form['#prefix'] = '<div id="vaterno_comments_frontend_form_wrapper">';
        $form['#suffix'] = '</div>';

        $form['comment_text'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Your comments'),
            '#required' => TRUE,
        ];

        $form['node_id'] = [
            '#type' => 'value',
            '#value' => $node->id(),
        ];

        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Send'),
            '#ajax' => [
                'callback' => [static::class, 'ajaxSubmitCallback'],
                'wrapper' => 'vaterno_comments_frontend_form_wrapper',
            ],
        ];

        return $form;
    }

    public static function ajaxSubmitCallback(array &$form, FormStateInterface $form_state)
    {
        if ($form_state->isSubmitted()) {
            $response = new AjaxResponse();

            try {
                /** @var Comment $comment */
                $comment = Comment::create([
                    'comment_text' => $form_state->getValue('comment_text'),
                    'node_id' => $form_state->getValue('node_id'),
                ]);

                ViolationHelper::getEntityMessages($comment);
                $comment->save();

                $response->addCommand(new HtmlCommand('#vaterno_comments_frontend_form_wrapper', ''));
                $response->addCommand(new MessageCommand('Your comment has been added.', '#vaterno_comments_frontend_form_wrapper'));
            } catch (ValidationFailedException $e) {
                $response->addCommand(new MessageCommand($e->getValue(), '#vaterno_comments_frontend_form_wrapper', ['type' => 'error']));
            }  catch (\Throwable $e) {
                $response->addCommand(new MessageCommand($e->getMessage(), '#vaterno_comments_frontend_form_wrapper', ['type' => 'error']));
            }

            return $response;
        }

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {}
}
