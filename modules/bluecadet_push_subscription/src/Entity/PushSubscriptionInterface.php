<?php

namespace Drupal\bluecadet_push_subscription\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Push subscription entities.
 *
 * @ingroup bluecadet_push_subscription
 */
interface PushSubscriptionInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Push subscription name.
   *
   * @return string
   *   Name of the Push subscription.
   */
  public function getName();

  /**
   * Sets the Push subscription name.
   *
   * @param string $name
   *   The Push subscription name.
   *
   * @return \Drupal\bluecadet_push_subscription\Entity\PushSubscriptionInterface
   *   The called Push subscription entity.
   */
  public function setName($name);

  /**
   * Gets the Push subscription creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Push subscription.
   */
  public function getCreatedTime();

  /**
   * Sets the Push subscription creation timestamp.
   *
   * @param int $timestamp
   *   The Push subscription creation timestamp.
   *
   * @return \Drupal\bluecadet_push_subscription\Entity\PushSubscriptionInterface
   *   The called Push subscription entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Push subscription published status indicator.
   *
   * Unpublished Push subscription are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Push subscription is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Push subscription.
   *
   * @param bool $published
   *   TRUE to set this Push subscription to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\bluecadet_push_subscription\Entity\PushSubscriptionInterface
   *   The called Push subscription entity.
   */
  public function setPublished($published);

  /**
   * Gets the Push subscription revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Push subscription revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\bluecadet_push_subscription\Entity\PushSubscriptionInterface
   *   The called Push subscription entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Push subscription revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Push subscription revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\bluecadet_push_subscription\Entity\PushSubscriptionInterface
   *   The called Push subscription entity.
   */
  public function setRevisionUserId($uid);

}
