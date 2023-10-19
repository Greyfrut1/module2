<?php

namespace Drupal\greyfrut_module2;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * {@inheritdoc}
 */
class ReviewsListBuilder extends EntityListBuilder {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The request stack service.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Constructs a new ReviewEntityListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack service.
   */
  public function __construct(
    EntityTypeInterface $entity_type,
    AccountProxyInterface $current_user,
    EntityTypeManagerInterface $entityTypeManager,
    RequestStack $requestStack
  ) {
    parent::__construct($entity_type, $entityTypeManager->getStorage('review'));
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entityTypeManager;
    $this->requestStack = $requestStack;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
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
    $current_user = $this->currentUser;
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
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $image_id = $entity->get('image')->target_id;
    $avatar_id = $entity->get('avatar')->target_id;
    $image = NULL;
    $file = '';

    // Load the image file for the review.
    if ($image_id) {
      $file = $this->entityTypeManager->getStorage('file')->load($image_id);
      $image = [
        '#theme' => 'image_style',
        '#style_name' => 'medium',
        '#uri' => $file->getFileUri(),
      ];
    }

    $avatar = NULL;

    // Load the avatar image file for the review.
    if ($avatar_id) {
      $file = $this->entityTypeManager->getStorage('file')->load($avatar_id);
      $avatar = [
        '#theme' => 'image_style',
        '#style_name' => 'thumbnail',
        '#uri' => $file->getFileUri(),
      ];
    }
    else {
      $avatar = [
        '#theme' => 'image_style',
        '#style_name' => 'thumbnail',
        '#uri' => 'public://defalt_avatar.png',
      ];
    }

    $created_timestamp = $entity->get('created')->value;
    $created_date = DrupalDateTime::createFromTimestamp($created_timestamp);
    $created_date_formatted = $created_date->format('m/d/Y H:i:s');

    // Get the current user's roles.
    $current_user = $this->currentUser;
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
   * {@inheritdoc}
   */
  public function load() {
    $entity_type_id = 'review';
    $query = $this->entityTypeManager->getStorage($entity_type_id)->getQuery();
    $query->accessCheck(FALSE);
    $query->sort('created', 'DESC');
    $entity_ids = $query->execute();
    return $this->storage->loadMultiple($entity_ids);
  }

}
