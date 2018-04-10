<?php

namespace Drupal\bluecadet_push_subscription\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\bluecadet_push_subscription\Entity\PushSubscriptionInterface;

/**
 * Class PushSubscriptionController.
 *
 *  Returns responses for Push subscription routes.
 */
class PushSubscriptionController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Push subscription  revision.
   *
   * @param int $push_subscription_revision
   *   The Push subscription  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($push_subscription_revision) {
    $push_subscription = $this->entityManager()->getStorage('push_subscription')->loadRevision($push_subscription_revision);
    $view_builder = $this->entityManager()->getViewBuilder('push_subscription');

    return $view_builder->view($push_subscription);
  }

  /**
   * Page title callback for a Push subscription  revision.
   *
   * @param int $push_subscription_revision
   *   The Push subscription  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($push_subscription_revision) {
    $push_subscription = $this->entityManager()->getStorage('push_subscription')->loadRevision($push_subscription_revision);
    return $this->t('Revision of %title from %date', ['%title' => $push_subscription->label(), '%date' => format_date($push_subscription->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Push subscription .
   *
   * @param \Drupal\bluecadet_push_subscription\Entity\PushSubscriptionInterface $push_subscription
   *   A Push subscription  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(PushSubscriptionInterface $push_subscription) {
    $account = $this->currentUser();
    $langcode = $push_subscription->language()->getId();
    $langname = $push_subscription->language()->getName();
    $languages = $push_subscription->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $push_subscription_storage = $this->entityManager()->getStorage('push_subscription');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $push_subscription->label()]) : $this->t('Revisions for %title', ['%title' => $push_subscription->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all push subscription revisions") || $account->hasPermission('administer push subscription entities')));
    $delete_permission = (($account->hasPermission("delete all push subscription revisions") || $account->hasPermission('administer push subscription entities')));

    $rows = [];

    $vids = $push_subscription_storage->revisionIds($push_subscription);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\bluecadet_push_subscription\PushSubscriptionInterface $revision */
      $revision = $push_subscription_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $push_subscription->getRevisionId()) {
          $link = $this->l($date, new Url('entity.push_subscription.revision', ['push_subscription' => $push_subscription->id(), 'push_subscription_revision' => $vid]));
        }
        else {
          $link = $push_subscription->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => Url::fromRoute('entity.push_subscription.revision_revert', ['push_subscription' => $push_subscription->id(), 'push_subscription_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.push_subscription.revision_delete', ['push_subscription' => $push_subscription->id(), 'push_subscription_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['push_subscription_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
