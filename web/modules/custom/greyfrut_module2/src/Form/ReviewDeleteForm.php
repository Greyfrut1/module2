<?php

namespace Drupal\greyfrut_module2\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\greyfrut_module2\Entity\ReviewEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a confirmation form for deleting a review entity.
 */
class ReviewDeleteForm extends FormBase {

  protected $reviewEntity;

  /**
   * Constructor to set the review entity for deletion.
   *
   * @param \Drupal\greyfrut_module2\Entity\ReviewEntity $review_entity
   *   The review entity to be deleted.
   */
  public function __construct(ReviewEntity $review_entity) {
    $this->reviewEntity = $review_entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'review_module_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Display a confirmation message with the review's name.
    $form['message'] = [
      '#markup' => $this->t('Are you sure you want to delete the review: %name?', ['%name' => $this->reviewEntity->label()]),
    ];

    // Create form actions.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add 'Yes' button to confirm deletion.
    $form['actions']['yes'] = [
      '#type' => 'submit',
      '#value' => $this->t('Yes'),
    ];

    // Add 'No' button to cancel and close the modal dialog.
    $form['actions']['no'] = [
      '#type' => 'submit',
      '#value' => $this->t('No'),
      '#ajax' => [
        'callback' => [$this, 'closeModal'],
      ],
      '#limit_validation_errors' => [],
    ];

    return $form;
  }

  /**
   * Callback function to close the modal dialog when 'No' button is clicked.
   */
  public function closeModal(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $response->addCommand(new CloseModalDialogCommand());
    return $response;
  }

  /**
   * Factory method to create an instance of the form.
   */
  public static function create(ContainerInterface $container) {
    // Load the review entity to be deleted based on the route parameter 'review_id'.
    $route_match = \Drupal::routeMatch();
    $review_id = $route_match->getParameter('review_id');
    $review_entity = \Drupal::entityTypeManager()->getStorage('review')->load($review_id);

    return new static($review_entity);
  }

  /**
   * Callback function to delete the review entity via AJAX.
   */
  public function submitFormAjax(array &$form, FormStateInterface $form_state) {
    // Delete the review entity.
    $this->reviewEntity->delete();

    // Close the modal dialog.
    $response = new AjaxResponse();
    $response->addCommand(new CloseModalDialogCommand());
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Delete the review entity.
    $this->reviewEntity->delete();

    // Redirect to the entity listing page after deletion.
    $form_state->setRedirect('greyfrut_module2.entities');
  }

}
