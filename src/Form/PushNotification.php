<?php

namespace Drupal\bc_ps_manager\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\bluecadet_push_subscription\Entity\PushSubscription;
use Drupal\bc_ps_manager\SubscriptionService;

class PushNotification extends FormBase implements ContainerInjectionInterface {

  protected $subService;

  /**
   * Class constructor.
   */
  public function __construct(SubscriptionService $subService) {
    $this->subService = $subService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load the service required to construct this class.
      $container->get('push_subscription')
    );
  }

  public function getFormId() {
    return 'pwa_push_notification_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form ['tag'] = [
      '#type' => 'textfield',
      '#title' => 'Tag'
    ];

    $form ['title'] = [
      '#type' => 'textfield',
      '#title' => 'Title'
    ];

    $form ['dir'] = [
      '#type' => 'select',
      '#title' => 'Direction',
      '#options' => [
        'auto',
        'ltr',
        'rtl'
      ]
    ];

    $form ['lang'] = [
      '#type' => 'textfield',
      '#title' => 'Title'
    ];

    $form['body'] = [
      '#type' => 'textarea',
      '#title' => 'Body'
    ];

    $form ['icon'] = [
      '#type' => 'textfield',
      '#title' => 'Icon'
    ];

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Send Notification'),
    );
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $data = [
      'title' => $values['title'],
      'tag' => $values['tag'],
      'dir' => $values['dir'],
      'lang' => $values['lang'],
      'body' => $values['body'],
      'icon' => $values['icon'],
    ];

    $subscriptions = $this->subService->retrieveAllActiveSubscriptions();
    $responses = $this->subService->pushToSubscriptions($data, $subscriptions);

  }
}
