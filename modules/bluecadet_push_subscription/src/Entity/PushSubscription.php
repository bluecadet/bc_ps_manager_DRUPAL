<?php

namespace Drupal\bluecadet_push_subscription\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Push subscription entity.
 *
 * @ingroup bluecadet_push_subscription
 *
 * @ContentEntityType(
 *   id = "push_subscription",
 *   label = @Translation("Push subscription"),
 *   handlers = {
 *     "storage" = "Drupal\bluecadet_push_subscription\PushSubscriptionStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\bluecadet_push_subscription\PushSubscriptionListBuilder",
 *     "views_data" = "Drupal\bluecadet_push_subscription\Entity\PushSubscriptionViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\bluecadet_push_subscription\Form\PushSubscriptionForm",
 *       "add" = "Drupal\bluecadet_push_subscription\Form\PushSubscriptionForm",
 *       "edit" = "Drupal\bluecadet_push_subscription\Form\PushSubscriptionForm",
 *       "delete" = "Drupal\bluecadet_push_subscription\Form\PushSubscriptionDeleteForm",
 *     },
 *     "access" = "Drupal\bluecadet_push_subscription\PushSubscriptionAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\bluecadet_push_subscription\PushSubscriptionHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "push_subscription",
 *   revision_table = "push_subscription_revision",
 *   revision_data_table = "push_subscription_field_revision",
 *   admin_permission = "administer push subscription entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "endpoint",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/push_subscription/{push_subscription}",
 *     "add-form" = "/admin/structure/push_subscription/add",
 *     "edit-form" = "/admin/structure/push_subscription/{push_subscription}/edit",
 *     "delete-form" = "/admin/structure/push_subscription/{push_subscription}/delete",
 *     "version-history" = "/admin/structure/push_subscription/{push_subscription}/revisions",
 *     "revision" = "/admin/structure/push_subscription/{push_subscription}/revisions/{push_subscription_revision}/view",
 *     "revision_revert" = "/admin/structure/push_subscription/{push_subscription}/revisions/{push_subscription_revision}/revert",
 *     "revision_delete" = "/admin/structure/push_subscription/{push_subscription}/revisions/{push_subscription_revision}/delete",
 *     "collection" = "/admin/structure/push_subscription",
 *   },
 *   field_ui_base_route = "push_subscription.settings"
 * )
 */
class PushSubscription extends RevisionableContentEntityBase implements PushSubscriptionInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the push_subscription owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  public function getEndpoint() {
    return $this->get('endpoint')->value;
  }

  public function getSubKeys() {
    $data = current( $this->get('subscription_data')->getValue() );
    return $data['keys'];
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Push subscription entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['endpoint'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Endpoint'))
      ->setDescription(t('The endpoint of the Push subscription entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['subscription_data'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Subscription Data'))
      ->setDescription(t('The full subscription data.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue([])
      // ->setDisplayOptions('view', [
      //   'type' => 'web_page_archive_capture_utility_map_formatter',
      //   'weight' => -4,
      // ])
      // ->setDisplayOptions('form', [
      //   'type' => 'web_page_archive_capture_utility_map_widget',
      //   'weight' => -4,
      // ])
      ;

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Push subscription is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
