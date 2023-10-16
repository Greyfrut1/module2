<?php

namespace Drupal\greyfrut_module2\Form;

use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\greyfrut_module2\Entity\ReviewEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * {@inheritdoc}
 */
class ReviewEditForm extends FormBase {

  /**
   * The entity interface.
   *
   * @var \Drupal\greyfrut_module2\Entity\ReviewEntity
   */
  protected $entity;

  /**
   * The email validator service.
   *
   * @var \Drupal\Component\Utility\EmailValidatorInterface
   */
  protected $emailValidator;

  /**
   * Constructs a ReviewsEntityEditForm object.
   *
   * @param \Drupal\greyfrut_module2\Entity\ReviewEntity $entity
   *   The entity.
   * @param \Drupal\Component\Utility\EmailValidatorInterface $emailValidator
   *   The email validator service.
   */
  public function __construct(ReviewEntity $entity, EmailValidatorInterface $emailValidator) {
    $this->entity = $entity;
    $this->emailValidator = $emailValidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $routeMatch = $container->get('current_route_match');
    $route_parameters = $routeMatch->getParameters();
    $review_id = $route_parameters->get('review_id');
    $entity = $container->get('entity_type.manager')->getStorage('review')->load($review_id);
    if (!$entity) {
      throw new NotFoundHttpException();
    }
    $emailValidator = $container->get('email.validator');
    return new static($entity, $emailValidator);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'review_id';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Retrieve the review entity being edited.
    $review_entity = $this->entity;

    // Add prefix and suffix div elements for the form.
    $form['#prefix'] = '<div id="review-form-wrapper">';
    $form['#suffix'] = '</div>';

    // Form fields for editing review information.
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
      '#prefix' => '<div id="email-validate-form-wrapper">',
      '#ajax' => [
        'callback' => '::validateEmailAjax',
        'wrapper' => 'email-validate-form-wrapper',
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
      '#prefix' => '<div id="phone-validate-form-wrapper">',
      '#ajax' => [
        'callback' => '::validatePhoneAjax',
        'wrapper' => 'phone-validate-form-wrapper',
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
    if ($this->entity->avatar->target_id) {
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
    }
    else {
      $form['avatar'] = [
        '#type' => 'managed_file',
        '#title' => $this->t('Your avatar'),
        '#description' => $this->t('Choose an image file to upload (jpeg, jpg, png formats only). Max size 2 mb.'),
        '#upload_location' => 'public://',
        '#upload_validators' => [
          'file_validate_extensions' => ['jpg jpeg png'],
          'file_validate_size' => [2100000],
        ],
      ];
    }

    if ($this->entity->image->target_id) {
      $form['image'] = [
        '#type' => 'managed_file',
        '#title' => $this->t('Image'),
        '#default_value' => [$this->entity->image->target_id],
        '#description' => $this->t('Choose an image file to upload (jpeg, jpg, png formats only). Max size 5 mb.'),
        '#upload_location' => 'public://',
        '#upload_validators' => [
          'file_validate_extensions' => ['jpg jpeg png'],
          'file_validate_size' => [5240000],
        ],
      ];
    }
    else {
      $form['image'] = [
        '#type' => 'managed_file',
        '#title' => $this->t('Image'),
        '#description' => $this->t('Choose an image file to upload (jpeg, jpg, png formats only). Max size 5 mb.'),
        '#upload_location' => 'public://',
        '#upload_validators' => [
          'file_validate_extensions' => ['jpg jpeg png'],
          'file_validate_size' => [5240000],
        ],
      ];
    }

    // Submit button with AJAX callback.
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Update the review entity with the edited information.
    $entity = $this->entity;
    $entity->set('name', $form_state->getValue('name'));
    $entity->set('email', $form_state->getValue('email'));
    $entity->set('image', $form_state->getValue('image'));
    $entity->set('avatar', $form_state->getValue('avatar'));

    // Save the updated entity.
    $entity->save();

    // Redirect to the entity listing page after saving.
    $form_state->setRedirect('greyfrut_module2.entities');
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $name = $form_state->getValue('name');

    if (strlen($name) < 3 || strlen($name) > 32) {
      $form_state->setErrorByName('name', $this->t('Your name must be between 3 and 32 characters.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateEmailAjax(array &$form, FormStateInterface $form_state) {
    $email = $form_state->getValue('email');

    if (!$this->emailValidator->isValid($email)) {
      $form['email']['#attributes']['class'][] = 'error';
      $form['email_validate_message']['#markup'] = '<div class="email-valudate-message">' . $this->t('Email is not valid.') . '</div>';
    }
    else {
      $form['email']['#attributes']['class'][] = '';
      $form['email_validate_message']['#markup'] = '';
    }

    return $form['email'];
  }

  /**
   * Validates the phone_number field using Ajax.
   */
  public function validatePhoneAjax(array &$form, FormStateInterface $form_state) {
    $phone_number = $form_state->getValue('phone_number');

    // Check if 'phone_number' is a valid phone number format.
    if ($this->isValidPhoneNumber($phone_number)) {
      $form['phone_number']['#attributes']['class'][] = '';
      $form['phone_validate_message']['#markup'] = '';
    }
    else {
      $form['phone_number']['#attributes']['class'][] = 'error';
      $form['phone_validate_message']['#markup'] = '<div class="phone-validate-message">' . $this->t('Phone number is not valid.') . '</div>';
    }

    return [$form['phone_number'], $form['phone_validate_message']];
  }

  /**
   * {@inheritdoc}
   */
  private function isValidPhoneNumber($phone_number) {
    $phone_number = preg_replace('/[\s\(\)-]/', '', $phone_number);

    // Check if 'phone_number' matches the required format.
    if (!preg_match('/^(?:\+380|380|0)\d{9}$/', $phone_number)) {
      return FALSE;
    }

    return TRUE;
  }

}
