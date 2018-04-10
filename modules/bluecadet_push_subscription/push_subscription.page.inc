<?php

/**
 * @file
 * Contains push_subscription.page.inc.
 *
 * Page callback for Push subscription entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Push subscription templates.
 *
 * Default template: push_subscription.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_push_subscription(array &$variables) {
  // Fetch PushSubscription Entity Object.
  $push_subscription = $variables['elements']['#push_subscription'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
