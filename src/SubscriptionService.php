<?php

namespace Drupal\bc_ps_manager;

use Drupal\bluecadet_push_subscription\Entity\PushSubscription;
use Minishlink\WebPush\WebPush;

/**
 *
 */
class SubscriptionService {

  function upsertSubscription($raw_subscription) {
    // First check if endpoint exists...
    $query = \Drupal::entityQuery('push_subscription');
    $query->condition('endpoint', $raw_subscription->endpoint);
    $entity_ids = $query->execute();

    $subs = PushSubscription::loadMultiple($entity_ids);

    if (empty($subs)) {
      $subscription = PushSubscription::create([
        'uid' => 1,
        'endpoint' => $raw_subscription->endpoint,
        'subscription_data' => serialize((array)$raw_subscription),
        'status' => 1,
      ]);
      $subscription->save();
      return 'CREATED';
    }

    return 'UPDATED';
  }

  function retrieveAllSubscriptions() {
    $query = \Drupal::entityQuery('push_subscription');
    // $query->condition('status', 1);
    $entity_ids = $query->execute();
    $subs = PushSubscription::loadMultiple($entity_ids);

    return $subs;
  }

  function retrieveAllActiveSubscriptions() {
    $query = \Drupal::entityQuery('push_subscription');
    $query->condition('status', 1);
    $entity_ids = $query->execute();
    $subs = PushSubscription::loadMultiple($entity_ids);

    return $subs;
  }

  function pushToSubscriptions($data, Array $subscriptions) {
    $settings = \Drupal::state()->get('bc_ps_manager.push_api_keys', [
      'email' => '',
      'public_key' => '',
      'private_key' => ''
    ]);

    $auth = array(
      'VAPID' => array(
        'subject' => 'mailto:' . $settings['email'],
        'publicKey' => $settings['public_key'],
        'privateKey' => $settings['private_key'],
      ),
    );

    $defaultOptions = [
      'TTL' => 600, // defaults to 4 weeks
      // 'urgency' => 'normal', // protocol defaults to "normal"
      // 'topic' => 'new_event', // not defined by default,
      'batchSize' => 1000, // defaults to 1000
    ];

    $webPush = new WebPush($auth, $defaultOptions);

    foreach ($subscriptions as $sub) {
      $keys = $sub->getSubKeys();

      $webPush->sendNotification(
        $sub->getEndpoint(),
        json_encode($data),
        $keys->p256dh,
        $keys->auth
      );
    }

    $responses = $webPush->flush();
    // ksm($responses);
    if (is_bool($responses)) {
      $responses = [
        'success' => $responses,
      ];
    }

    // 'success' => boolFALSE
    // 'endpoint' => string(188) "https://fcm.googleapis.com/fcm/send/eimowD74ZnY:APA91bGzWLyfLcIKYTDq1gZsA9ExXl6b…"
    // 'message' => string(390) "Client error: `POST https://fcm.googleapis.com/fcm/send/eimowD74ZnY:APA91bGzWLyf…"
    // 'statusCode' => integer410
    // 'expired' => boolTRUE

    $this->handleResponses($subscriptions, $responses);
    return TRUE;
  }

  protected function handleResponses($subscriptions, $responses) {
    // ksm($subscriptions, $responses);
    $subs = array_values($subscriptions);

    foreach ($responses as $i => $response) {
      if ($response['success'] == FALSE) {

        if (isset($response['statusCode'])) {
          $subs[$i]->setNewRevision(TRUE);
          $subs[$i]->setRevisionLogMessage(json_encode($response));
          $subs[$i]->setPublished(FALSE);
          $subs[$i]->save();
        }
      }
    }
  }
}