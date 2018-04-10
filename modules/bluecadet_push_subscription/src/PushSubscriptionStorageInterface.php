<?php

namespace Drupal\bluecadet_push_subscription;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface PushSubscriptionStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Push subscription revision IDs for a specific Push subscription.
   *
   * @param \Drupal\bluecadet_push_subscription\Entity\PushSubscriptionInterface $entity
   *   The Push subscription entity.
   *
   * @return int[]
   *   Push subscription revision IDs (in ascending order).
   */
  public function revisionIds(PushSubscriptionInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Push subscription author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Push subscription revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\bluecadet_push_subscription\Entity\PushSubscriptionInterface $entity
   *   The Push subscription entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(PushSubscriptionInterface $entity);

  /**
   * Unsets the language for all Push subscription with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
