<?php

namespace Drupal\bc_ps_manager\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\bluecadet_push_subscription\Entity\PushSubscription;
use Drupal\bc_ps_manager\SubscriptionService;

class WebPushKeys extends FormBase {

  public function getFormId() {
    return 'pwa_web_push_keys';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $settings = \Drupal::state()->get('bc_ps_manager.push_api_keys', [
      'email' => '',
      'public_key' => '',
      'private_key' => ''
    ]);

    $form['email'] = [
      '#type' => 'email',
      '#title' => 'Email',
      '#description' => 'Email to legitimize Pushes',
      '#default_value' => $settings['email'],
    ];

    $form['public_key'] = [
      '#type' => 'textfield',
      '#title' => 'VAPID Public Key',
      '#default_value' => $settings['public_key'],
      '#size' => 100,
    ];

    $form['private_key'] = [
      '#type' => 'textfield',
      '#title' => 'VAPID Private Key',
      '#default_value' => $settings['private_key'],
      '#size' => 80,
    ];

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    );
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $to_save = [
      'email' => $values['email'],
      'public_key' => $values['public_key'],
      'private_key' => $values['private_key']
    ];

    \Drupal::state()->set('bc_ps_manager.push_api_keys', $to_save);
  }
}
