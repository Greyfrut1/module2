<?php

namespace Drupal\greyfrut_module2\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for displaying a list of reviews.
 */
class ReviewsListController extends ControllerBase {

  /**
   * Builds and returns a list of reviews.
   *
   * @return array
   *   A renderable array representing the list of reviews.
   */
  public function buildList() {
    // Get the list builder for the 'review' entity type.
    $list_builder = \Drupal::entityTypeManager()->getListBuilder('review');

    // Render the list using the list builder.
    $build = $list_builder->render();

    return $build;
  }

}
