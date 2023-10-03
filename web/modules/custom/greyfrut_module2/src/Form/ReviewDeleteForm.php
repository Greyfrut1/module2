<?php

namespace Drupal\greyfrut_module2\Form;

use Drupal\greyfrut_module2\Entity\ReviewEntity;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a confirmation form for deleting a cat entity.
 */
class ReviewDeleteForm extends FormBase {

  protected $reviewEntity;

  /**
   * {@inheritdoc}
   */
  public function __construct(ReviewEntity $review_entity) {
    $this->reviewEntity = $review_entity;
  }

  /**
   * {@inheritdoc}
   */

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
    $form['message'] = [
      '#markup' => $this->t('Are you sure you want to delete the cat entity: %name?', ['%name' => $this->reviewEntity->label()]),
    ];
    $form['actions'] =[
      '#type' => 'actions',
    ];
    $form['actions']['yes'] = [
      '#type' => 'submit',
      '#value' => $this->t('Yes'),

    ];

    $form['actions']['no'] = [
      '#type' => 'submit',
      '#value' => $this->t('No'),
      '#ajax' => [
        'callback' => [$this, 'closeModal'],
      ],
      '#limit_validation_errors' => [],
    ];
    $form['#attached']['library'][] = 'cats_module/cats_module_js';

    return $form;
  }

  /**
   *
   */
  public function closeModal(array &$form, FormStateInterface $form_state) {
    // Create an AjaxResponse to close the modal.
    $response = new AjaxResponse();
    $response->addCommand(new CloseModalDialogCommand());
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $route_match = \Drupal::routeMatch();
    $review_id = $route_match->getParameter('review_id');
    $review_entity = \Drupal::entityTypeManager()->getStorage('review')->load($review_id);

    return new static($review_entity);
  }

  /**
   *
   */
  public function submitFormAjax(array &$form, FormStateInterface $form_state) {
    $this->reviewEntity->delete();
    $response = new AjaxResponse();
    $response->addCommand(new CloseModalDialogCommand());
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->reviewEntity->delete();

    $form_state->setRedirect('greyfrut_module2.entities');
  }

}
