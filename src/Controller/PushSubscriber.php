<?php

namespace Drupal\bc_ps_manager\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\bluecadet_push_subscription\Entity\PushSubscription;
use Drupal\bc_ps_manager\SubscriptionService;

class PushSubscriber extends ControllerBase implements ContainerInjectionInterface {

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

  public function subscribe(Request $request) {
    $str = $request->getContent();
    $response_data = [
      'status' => 'OK',
      'response' => '',
      'msg' => ''
    ];

    if ($str !== NULL) {
      $data = json_decode($str);
      if (isset($data->subscription)) {
        $response_data['response'] = $this->subService->upsertSubscription($data->subscription);
      }
      else {
        $response_data = [
          'status' => 'ERROR',
          'response' => '',
          'msg' => 'No subscription sent',
        ];
      }
    }
    else {
      $response_data = [
        'status' => 'ERROR',
        'response' => '',
        'msg' => 'Nothing sent',
      ];
    }

    $response = new JsonResponse($response_data);
    return $response;
  }

  public function display() {

    $subscriptions = $this->subService->retrieveAllSubscriptions();

    ksm($subscriptions);

    return [];
  }
}