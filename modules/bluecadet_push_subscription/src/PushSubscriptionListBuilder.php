<?php

namespace Drupal\bluecadet_push_subscription;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Push subscription entities.
 *
 * @ingroup bluecadet_push_subscription
 */
class PushSubscriptionListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Push subscription ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\bluecadet_push_subscription\Entity\PushSubscription */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.push_subscription.edit_form',
      ['push_subscription' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
