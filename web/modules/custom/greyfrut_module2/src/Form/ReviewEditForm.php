<?php

namespace Drupal\greyfrut_module2\Form;

use Drupal\greyfrut_module2\Entity\ReviewEntity;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class ReviewEditForm extends FormBase {

  protected $entity;

  /**
   *
   */
  public function __construct(ReviewEntity $entity) {
    $this->entity = $entity;
  }

  /**
   *
   */
  public static function create(ContainerInterface $container) {

    $route_parameters = \Drupal::routeMatch()->getParameters();
    $review_id = $route_parameters->get('review_id');

    $entity = \Drupal::entityTypeManager()->getStorage('review')->load($review_id);

    return new static($entity);
  }

  /**
   *
   */
  public function getFormId() {
    return 'review_id';
  }

  /**
   *
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $review_entity = $this->entity;
    $form['#prefix'] = '<div id="review-form-wrapper">';
    $form['#suffix'] = '</div>';
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your name'),
      '#required' => TRUE,
      '#default_value' => $review_entity->name->value,
      '#description' => $this->t('Min length: 2 characters. Max length: 32 characters'),
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your email'),
      '#required' => TRUE,
      '#default_value' => $review_entity->email->value,
      '#description' => 'Email can only contain Latin letters, underscore, or hyphen.',
      '#prefix' => '<div id="email-validate-form-wrapper">', // Використовуйте відмінний від phone_number wrapper та id для повідомлень
      '#ajax' => [
        'callback' => '::validateEmailAjax',
        'wrapper' => 'email-validate-form-wrapper', // Вказуємо той самий wrapper, що і для email
        'method' => 'replace',
        'event' => 'change',
      ],
    ];
    $form['email_validate_message'] = [
      '#markup' => '<div class="email-validate-message"></div>',
      '#suffix' => '</div>',
    ];


    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Your phone number'),
      '#required' => TRUE,
      '#default_value' => $review_entity->phone_number->value,
      '#prefix' => '<div id="phone-validate-form-wrapper">', // Використовуйте відмінний від email wrapper та id для повідомлень
      '#ajax' => [
        'callback' => '::validatePhoneAjax',
        'wrapper' => 'phone-validate-form-wrapper', // Вказуємо той самий wrapper, що і для phone_number
        'method' => 'replace',
        'event' => 'change',
      ],
    ];

    $form['phone_validate_message'] = [
      '#markup' => '<div class="phone-validate-message"></div>',
      '#suffix' => '</div>',
    ];

    $form['review_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Your review'),
      '#required' => TRUE,
      '#maxlength' => 500,
      '#default_value' => $review_entity->review_text->value,
      '#description' => $this->t('Max length: 500 characters'),
    ];
    $form['avatar'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Your avatar'),
      '#default_value' => [$this->entity->avatar->target_id],
      '#description' => $this->t('Choose an image file to upload (jpeg, jpg, png formats only). Max size 2 mb.'),
      '#upload_location' => 'public://',
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg jpeg png'],
        'file_validate_size' => [2100000],
      ],
    ];
    $form['image'] = [
      '#type' => 'managed_file',
      '#required' => TRUE,
      '#title' => $this->t('Image'),
      '#default_value' => [$this->entity->image->target_id],
      '#description' => $this->t('Choose an image file to upload (jpeg, jpg, png formats only). Max size 5 mb.'),
      '#upload_location' => 'public://',
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg jpeg png'],
        'file_validate_size' => [5240000],
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#ajax' => [
        'callback' => '::submitFormAjax',
        'wrapper' => 'review-form-wrapper',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $entity->set('name', $form_state->getValue('name'));
    $entity->set('email', $form_state->getValue('email'));
    $entity->set('image', $form_state->getValue('image'));
    $entity->set('avatar', $form_state->getValue('avatar'));

    $entity->save();

    $form_state->setRedirect('greyfrut_module2.entities');
  }

  /**
   *
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $cat_name = $form_state->getValue('name');

    if (strlen($cat_name) < 3 || strlen($cat_name) > 32) {
      $form_state->setErrorByName('name', $this->t('Your name must be between 3 and 32 characters.'));
    }
  }

  /**
   *
   */
  public function validateEmailAjax(array &$form, FormStateInterface $form_state) {
    $email = $form_state->getValue('email');

    if (!\Drupal::service('email.validator')->isValid($email)) {
      $form['email']['#attributes']['class'][] = 'error';
      $form['email_validate_message']['#markup'] = '<div class="email-valudate-message">' . $this->t('Email is not valid.') . '</div>';
    }
    else {
      $form['email']['#attributes']['class'][] = '';
      $form['email_validate_message']['#markup'] = '';
    }

    return $form['email'];
  }

}
