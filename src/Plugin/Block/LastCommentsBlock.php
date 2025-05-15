<?php

namespace Drupal\vaterno_comments\Plugin\Block;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\vaterno_comments\Helper\CommentCacheHelper;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\vaterno_comments\Repository\CommentsRepositoryInterface;

/**
 * Last comments block
 *
 * @Block(
 *     id = "vaterno_comments_last_comments_block",
 *     admin_label = @Translation("Last vaterno comments block")
 * )
 */
class LastCommentsBlock extends BlockBase implements ContainerFactoryPluginInterface
{
    public function __construct(
        array $configuration,
              $plugin_id,
              $plugin_definition,
        protected CommentsRepositoryInterface $commentsRepository
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
    }

    public static function create(
        ContainerInterface $container,
        array $configuration,
        $plugin_id,
        $plugin_definition
    ) {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('vaterno_comments.comments_repository'),
        );
    }

    public function defaultConfiguration(): array
    {
        return [
            'maxLastCommentsToShow' => 5,
        ];
    }

    public function blockForm($form, FormStateInterface $form_state)
    {
        $config = $this->getConfiguration();

        $form['maxLastCommentsToShow'] = [
            '#type' => 'number',
            '#title' => $this->t('Maximum last number of comments to show.'),
            '#default_value' => $config['maxLastCommentsToShow'],
        ];

        return $form;
    }

    public function blockSubmit($form, FormStateInterface $form_state)
    {
        $this->configuration['maxLastCommentsToShow'] = $form_state->getValue('maxLastCommentsToShow');
    }

    public function build()
    {
        $limit = (
            empty($this->configuration['maxLastCommentsToShow']) ||
            $this->configuration['maxLastCommentsToShow'] <= 0
        ) ? 5 : $this->configuration['maxLastCommentsToShow'];
        $comments = $this->commentsRepository->getLastPublishedComments($limit);
        $commentsTags = CommentCacheHelper::createCacheTags($comments);

        return [
            '#theme' => 'vaterno_comments_last_comments',
            '#comments' => $comments,
            '#cache' => [
                'tags' => array_merge($commentsTags['comments'], $commentsTags['users']),
                'max-age' => Cache::PERMANENT,
            ],
        ];
    }
}
