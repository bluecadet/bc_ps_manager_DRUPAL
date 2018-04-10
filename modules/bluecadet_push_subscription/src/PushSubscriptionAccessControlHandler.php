<?php

namespace Drupal\bluecadet_push_subscription;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Push subscription entity.
 *
 * @see \Drupal\bluecadet_push_subscription\Entity\PushSubscription.
 */
class PushSubscriptionAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\bluecadet_push_subscription\Entity\PushSubscriptionInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished push subscription entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published push subscription entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit push subscription entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete push subscription entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add push subscription entities');
  }

}
