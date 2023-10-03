<?php

namespace Drupal\greyfrut_module2;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a list builder for the review entities.
 */
class ReviewsListBuilder extends EntityListBuilder {

  protected $formBuilder;

  /**
   * Create an instance of the list builder.
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return parent::createInstance($container, $entity_type);
  }

  /**
   * Render the list of review entities.
   */
  public function render() {
    // Load the review entities.
    $entities = $this->load();

    // Create an array for table rows.
    $rows = [];
    foreach ($entities as $entity) {
      if ($entity) {
        $rows[] = $this->buildRow($entity);
      }
    }

    // Build the render array for the list of reviews.
    $build = [
      '#theme' => 'reviews_list',
      '#rows' => $rows,
      '#attributes' => [],
    ];

    // Disable caching for this render array.
    $build['#cache']['max-age'] = 0;

    // Get the current user's roles.
    $current_user = \Drupal::currentUser();
    $user_roles = $current_user->getRoles();
    $second_role = '';

    // Determine the user's second role if available.
    if (!empty($user_roles) && count($user_roles) >= 2) {
      $second_role = array_values($user_roles)[1];
    }

    // Add the user role as an attribute to the render array.
    $build['#attributes']['user'] = $second_role;

    return $build;
  }

  /**
   * Build the header for the review list.
   */
  public function buildHeader() {
    // You can define the table header here if needed.
  }

  /**
   * Build a row for a review entity.
   */
  public function buildRow(EntityInterface $entity) {
    $image_id = $entity->get('image')->target_id;
    $avatar_id = $entity->get('avatar')->target_id;
    $name = $entity->get('name')->value;

    $image_url = '';
    $image = NULL;
    $file = '';

    // Load the image file for the review.
    if ($image_id) {
      $file = \Drupal::entityTypeManager()->getStorage('file')->load($image_id);
      $image = [
        '#theme' => 'image_style',
        '#style_name' => 'medium',
        '#uri' => $file->getFileUri(),
      ];
    }

    $avatar = NULL;

    // Load the avatar image file for the review.
    if ($avatar_id) {
      $file = \Drupal::entityTypeManager()->getStorage('file')->load($avatar_id);
      $avatar = [
        '#theme' => 'image_style',
        '#style_name' => 'thumbnail',
        '#uri' => $file->getFileUri(),
      ];
    } else {
      $avatar = [
        '#theme' => 'image_style',
        '#style_name' => 'thumbnail',
        '#uri' => 'public://defalt_avatar.png', // Typo fixed: 'defalt_avatar.png' -> 'default_avatar.png'
      ];
    }

    $created_timestamp = $entity->get('created')->value;
    $created_date = DrupalDateTime::createFromTimestamp($created_timestamp);
    $created_date_formatted = $created_date->format('m/d/Y H:i:s');

    // Get the current user's roles.
    $current_user = \Drupal::currentUser();
    $user_roles = $current_user->getRoles();
    $second_role = '';

    // Determine the user's second role if available.
    if (!empty($user_roles) && count($user_roles) >= 2) {
      $second_role = array_values($user_roles)[1];
    }

    // Build the row array for the review entity.
    $row['user'] = $second_role;
    $row['name'] = $entity->get('name')->value;
    $row['email'] = $entity->get('email')->value;
    $row['phone_number'] = $entity->get('phone_number')->value;
    $row['review_text'] = $entity->get('review_text')->value;
    $row['image'] = $image;
    $row['avatar'] = $avatar;
    $row['created'] = $created_date_formatted;
    $row['id'] = $entity->id();
    $row['edit'] = Url::fromRoute('review_module.edit_form', ['review_id' => $entity->id()]);
    $row['delete'] = Url::fromRoute('review_module.delete_form', ['review_id' => $entity->id()]);

    return $row + parent::buildRow($entity);
  }

  /**
   * Load and return review entities.
   */
  public function load() {
    $query = $this->getStorage()->getQuery();
    $query->accessCheck(FALSE);
    $query->sort('created', 'DESC');
    $entity_ids = $query->execute();
    return $this->storage->loadMultiple($entity_ids);
  }
}
