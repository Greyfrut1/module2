<?php

namespace Drupal\greyfrut_module2\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the review entity.
 *
 * @ingroup review
 *
 * @ContentEntityType(
 *   id = "review",
 *   label = @Translation("Review"),
 *   base_table = "review",
 *   handlers = {
 *       "list_builder" = "Drupal\greyfrut_module2\ReviewsListBuilder",
 *       "route_provider" = {
 *         "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *       },
 *     },
 *   entity_keys = {
 *     "id" = "id",
 *     "created" = "created",
 *   },
 *   admin_permission = "administer my awesome entities",
 * )
 */
class ReviewEntity extends ContentEntityBase implements ContentEntityInterface {

  /**
   * Defines the base fields for the ReviewEntity.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    // Define the ID field for the entity.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('ID of the review entity'))
      ->setReadOnly(TRUE);

    // Define the created field for storing the entity's creation time.
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    // Define the 'name' field for the entity.
    $fields["name"] = BaseFieldDefinition::create("string")
      ->setLabel(t('Name'))
      ->setDescription(t("Min length: 2 characters. Max length: 100 characters"))
      ->setSettings(["max_length" => 100, "text_processing" => 0])
      ->setDefaultValue("")
      ->setDisplayOptions("view", ["label" => "above", "type" => "string", "weight" => -3])
      ->setDisplayOptions("form", ["type" => "string_textfield", "weight" => -3]);

    // Define the 'email' field for the entity.
    $fields["email"] = BaseFieldDefinition::create("email")
      ->setLabel(t("Email"))
      ->setDescription(t("Email can only contain Latin letters, underscore, or hyphen."))
      ->setDefaultValue("")
      ->setDisplayOptions("form", ["type" => "email_default", "weight" => -3]);

    // Define the 'phone_number' field for the entity.
    $fields["phone_number"] = BaseFieldDefinition::create("telephone")
      ->setLabel(t("Phone Number"))
      ->setDescription(t("Phone number field"))
      ->setDefaultValue("")
      ->setDisplayOptions("view", ["label" => "above", "type" => "string", "weight" => -2])
      ->setDisplayOptions("form", ["type" => "telephone_default", "weight" => -2]);

    // Define the 'review_text' field for the entity.
    $fields["review_text"] = BaseFieldDefinition::create("string")
      ->setLabel(t('Review text'))
      ->setDescription(t("Max length 500"))
      ->setSettings(["max_length" => 500, "text_processing" => 0])
      ->setDefaultValue("")
      ->setDisplayOptions("view", ["label" => "above", "type" => "textarea", "weight" => -3])
      ->setDisplayOptions("form", ["type" => "textarea_textfield", "weight" => -3]);

    // Define the 'avatar' field for the entity, which is an image field.
    $fields['avatar'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Avatar'))
      ->setDescription(t('An image associated with the custom entity.'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'image_image',
        'weight' => 0,
      ]);

    // Define the 'image' field for the entity, which is another image field.
    $fields['image'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Image'))
      ->setDescription(t('An image associated with the custom entity.'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'image_image',
        'weight' => 0,
      ]);

    return $fields;
  }

}
