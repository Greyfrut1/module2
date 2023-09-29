<?php

namespace Drupal\greyfrut_module2\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 *
 */
class ReviewsListController extends ControllerBase {

  /**
   *
   */
  public function buildList() {
    $list_builder = \Drupal::entityTypeManager()->getListBuilder('review');
    $build = $list_builder->render();
    return $build;
  }

}
