<?php

namespace Drupal\greyfrut_module2\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for displaying a list of reviews.
 */
class ReviewsListController extends ControllerBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new ReviewsListController instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Builds and returns a list of reviews.
   *
   * @return array
   *   A renderable array representing the list of reviews.
   */
  public function buildList() {
    // Get the list builder for the 'review' entity type.
    $list_builder = $this->entityTypeManager->getListBuilder('review');

    // Render the list using the list builder.
    $build = $list_builder->render();

    return $build;
  }

}
