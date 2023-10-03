<?php

namespace Drupal\greyfrut_module2;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class ReviewsListBuilder extends EntityListBuilder {

  protected $formBuilder;

  /**
   *
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return parent::createInstance($container, $entity_type);
  }

  /**
   *
   */
  public function render() {
    $entities = $this->load();

    // Створюємо масив для рядків таблиці.
    $rows = [];
    foreach ($entities as $entity) {
      if ($entity) {
        $rows[] = $this->buildRow($entity);
      }
    }
    $build = [
      '#theme' => 'reviews_list',
      '#rows' => $rows,
      '#attributes' => [],
    ];

    $build['#cache']['max-age'] = 0;

    $current_user = \Drupal::currentUser();

    $user_roles = $current_user->getRoles();

    $second_role = '';
    if (!empty($user_roles) && count($user_roles) >= 2) {
      $second_role = array_values($user_roles)[1];
    }

    $build['#attributes']['user'] = $second_role;
    return $build;
  }

  /**
   *
   */
  public function buildHeader() {

  }

  /**
   *
   */
  public function buildRow(EntityInterface $entity) {
    $image_id = $entity->get('image')->target_id;
    $avatar_id = $entity->get('avatar')->target_id;
    $name = $entity->get('name')->value;

    $image_url = '';
    $image = null;
    $file = '';
    if ($image_id) {
      $file = \Drupal::entityTypeManager()->getStorage('file')->load($image_id);
      $image = [
        '#theme' => 'image_style',
        '#style_name' => 'thumbnail',
        '#uri' => $file->getFileUri(),
      ];
    }
    $avatar = null;
    if ($avatar_id) {
      $file = \Drupal::entityTypeManager()->getStorage('file')->load($avatar_id);
      $avatar = [
        '#theme' => 'image_style',
        '#style_name' => 'thumbnail',
        '#uri' => $file->getFileUri(),
      ];
    }else{
      $avatar = [
        '#theme' => 'image_style',
        '#style_name' => 'thumbnail',
        '#uri' => 'public://defalt_avatar.png',
      ];
    }


    $created_timestamp = $entity->get('created')->value;
    $created_date = DrupalDateTime::createFromTimestamp($created_timestamp);
    $created_date_formatted = $created_date->format('m/d/Y/ H:i:s');

    $current_user = \Drupal::currentUser();
    $user_roles = $current_user->getRoles();
    $second_role = '';
    if (!empty($user_roles) && count($user_roles) >= 2) {
      $second_role = array_values($user_roles)[1];
    }

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
   *
   */
  public function load() {
    $query = $this->getStorage()->getQuery();
    $query->accessCheck(FALSE);
    $query->sort('created', 'DESC');
    $entity_ids = $query->execute();
    return $this->storage->loadMultiple($entity_ids);
  }

}
