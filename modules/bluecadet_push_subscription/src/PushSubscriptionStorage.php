<?php

namespace Drupal\bluecadet_push_subscription;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\bluecadet_push_subscription\Entity\PushSubscriptionInterface;

/**
 * Defines the storage handler class for Push subscription entities.
 *
 * This extends the base storage class, adding required special handling for
 * Push subscription entities.
 *
 * @ingroup bluecadet_push_subscription
 */
class PushSubscriptionStorage extends SqlContentEntityStorage implements PushSubscriptionStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(PushSubscriptionInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {push_subscription_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {push_subscription_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(PushSubscriptionInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {push_subscription_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('push_subscription_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
