<?php

namespace Drupal\greyfrut_module2\Form;

use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\greyfrut_module2\Entity\ReviewEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the form for adding reviews.
 */
class ReviewAddForm extends FormBase {

  /**
   * The entity interface.
   *
   * @var \Drupal\Component\Utility\EmailValidatorInterface
   */
  protected $emailValidator;

  /**
   * Constructor to set the review entity for deletion.
   *
   * @param \Drupal\Component\Utility\EmailValidatorInterface $emailValidator
   *   The email validator service.
   */
  public function __construct(EmailValidatorInterface $emailValidator) {
    $this->emailValidator = $emailValidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('email.validator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'review_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Set a prefix and suffix for the entire form.
    $form['#prefix'] = '<div id="review-form-wrapper">';
    $form['#suffix'] = '</div>';

    // Define the 'name' field.
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your name'),
      '#required' => TRUE,
      '#description' => $this->t('Min length: 2 characters. Max length: 32 characters'),
    ];

    // Define the 'email' field with AJAX validation.
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your email'),
      '#required' => TRUE,
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

    // Define the 'phone_number' field with AJAX validation.
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Your phone number'),
      '#required' => TRUE,
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

    // Define the 'review_text' field.
    $form['review_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Your review'),
      '#required' => TRUE,
      '#maxlength' => 500,
      '#description' => $this->t('Max length: 500 characters'),
    ];

    // Define the 'avatar' file upload field.
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

    // Define the 'image' file upload field.
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

    // Define the submit button with AJAX support.
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add review'),
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
  public function submitFormAjax(array &$form, FormStateInterface $form_state) {
    if ($form_state->hasAnyErrors()) {
      return $form;
    }

    // Retrieve values from the form.
    $name = $form_state->getValue('name');
    $email = $form_state->getValue('email');
    $review_text = $form_state->getValue('review_text');
    $phone_number = $form_state->getValue('phone_number');
    $avatar_id = $form_state->getValue('avatar')[0];
    $image_id = $form_state->getValue('image')[0];

    // Create a new ReviewEntity and set its values.
    $entity = ReviewEntity::create();
    $entity->set('name', $name);
    $entity->set('review_text', $review_text);
    $entity->set('email', $email);
    $entity->set('phone_number', $phone_number);
    $entity->set('avatar', $avatar_id);
    $entity->set('image', $image_id);

    // Set the creation time.
    $current_time = new DrupalDateTime('now');
    $entity->set('created', $current_time->getTimestamp());

    // Save the entity.
    $entity->save();
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Handle form submission if needed.
    // In this case, it returns the form itself.
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validate the form fields.
    $name = $form_state->getValue('name');
    $email = $form_state->getValue('email');
    $phone_number = $form_state->getValue('phone_number');

    // Check if 'name' meets length requirements.
    if (strlen($name) < 3 || strlen($name) > 32) {
      $form_state->setErrorByName('name', $this->t('Your name must be between 3 and 32 characters.'));
    }

    // Check if 'email' is a valid email address.
    if (!$this->emailValidator->isValid($email)) {
      $form_state->setErrorByName('email', $this->t('Email is not valid'));
    }

    // Check if 'phone_number' is a valid phone number format.
    if (!preg_match('/^(?:\+380|380|0)\d{9}$/', $phone_number)) {
      $form_state->setErrorByName('phone_number', $this->t('Phone is not valid'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateEmailAjax(array &$form, FormStateInterface $form_state) {
    $email = $form_state->getValue('email');

    // Check if 'email' is a valid email address.
    if (!$this->emailValidator->isValid($email)) {
      $form['email']['#attributes']['class'][] = 'error';
      $form['email_validate_message']['#markup'] = '<div class="email-validate-message">' . $this->t('Email is not valid.') . '</div>';
    }
    else {
      $form['email']['#attributes']['class'][] = '';
      $form['email_validate_message']['#markup'] = '';
    }
    return [$form['email'], $form['email_validate_message']];
  }

  /**
   * {@inheritdoc}
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
