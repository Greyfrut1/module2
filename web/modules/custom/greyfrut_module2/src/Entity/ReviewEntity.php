<?php

namespace Drupal\greyfrut_module2\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the review entity.
 *
 * @ingroup reivew
 *
 * @ContentEntityType(
 *   id = "review",
 *   label = @Translation("review"),
 *   base_table = "review",
 *   entity_keys = {
 *     "id" = "id",
 *     "created" = "created",
 *   },
 *   admin_permission = "administer my awesome entities",
 * )
 */
class ReviewEntity extends ContentEntityBase implements ContentEntityInterface {

  /**
   *
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('Id of review entity'))
      ->setReadOnly(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields["name"] = BaseFieldDefinition::create("string")
      ->setLabel(t('Name'))
      ->setDescription(t("Min length: 2 characters. Max length: 100 characters"))
      ->setSettings(["max_length" => 100, "text_processing" => 0])
      ->setDefaultValue("")
      ->setDisplayOptions("view", ["label" => "above", "type" => "string", "wegith" => -3])
      ->setDisplayOptions("form", ["type" => "string_textfield", "wegith" => -3]);

    $fields["email"] = BaseFieldDefinition::create("email")
      ->setLabel(t("Email"))
      ->setDescription(t("Email can only contain Latin letters, underscore, or hyphen."))
      ->setDefaultValue("")
      ->setDisplayOptions("form", ["type" => "email_default", "wegith" => -3]);

    $fields["phone_number"] = BaseFieldDefinition::create("telephone")
      ->setLabel(t("Phone Number"))
      ->setDescription(t("Phone number field"))
      ->setDefaultValue("")
      ->setDisplayOptions("view", ["label" => "above", "type" => "string", "weight" => -2])
      ->setDisplayOptions("form", ["type" => "telephone_default", "weight" => -2]);


    return $fields;
  }

}
