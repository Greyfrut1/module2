<?php

namespace Drupal\greyfrut_module2\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\greyfrut_module2\Entity\ReviewEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for editing a review entity.
 */
class ReviewEditForm extends FormBase {

  protected $entity;

  /**
   * Constructor to set the review entity for editing.
   *
   * @param \Drupal\greyfrut_module2\Entity\ReviewEntity $entity
   *   The review entity to be edited.
   */
  public function __construct(ReviewEntity $entity) {
    $this->entity = $entity;
  }

  /**
   * Factory method to create an instance of the form.
   */
  public static function create(ContainerInterface $container) {
    // Load the review entity to be edited based on the route parameter 'review_id'.
    $route_parameters = \Drupal::routeMatch()->getParameters();
    $review_id = $route_parameters->get('review_id');
    $entity = \Drupal::entityTypeManager()->getStorage('review')->load($review_id);

    return new static($entity);
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
