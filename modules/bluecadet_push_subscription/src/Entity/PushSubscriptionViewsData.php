<?php

namespace Drupal\bluecadet_push_subscription\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Push subscription entities.
 */
class PushSubscriptionViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
