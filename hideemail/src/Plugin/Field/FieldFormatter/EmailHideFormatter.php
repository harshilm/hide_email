<?php

namespace Drupal\hideemail\Plugin\Field\FieldFormatter;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\MailToFormatter;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Field\FormatterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'email_mailto' formatter.
 *
 * @FieldFormatter(
 *   id = "hideemail",
 *   label = @Translation("Hide mail"),
 *   field_types = {
 *     "email"
 *   }
 * )
 */
class EmailHideFormatter extends FormatterBase {

const HIDE_MAILTO_ASCII = "&#109;&#97;&#105;&#108;&#116;&#111;&#58;";

  /*
   * {@inheritdoc}
   */
  function viewElements(FieldItemListInterface $items, $langcode)
  {
    $elements = [];
    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#markup' => $this->getEmailHtml($item->value),
      ];
    }

    return $elements;
  }
  /*
   * Helper function to fetch HTML for field view
   * @param string email
   * @return encoded  email string
   */
  public function getEmailHtml($string) {
    $output_as_link = $this->getSetting('link_html_email');
    $encode = $this->hide_encode_html($string);
    if ($output_as_link) {
      $encode = '<a href="' . self::HIDE_MAILTO_ASCII . "$encode\">$encode</a>";
    }
    return $encode;
  }
  /*
 * Helper function to convert each chracter's string into
 * non readable string
 * @param string email value
 * @return string encoded string
 */
  public function hide_encode_html($string) {

    $encode = '';
    for($i=0; $i<strlen($string); $i++) {

      $chr = substr($string, $i,1);
      $encode .= '&#'.ord($chr).';';
    }
    return $encode;
  }
  public function settingsSummary()
  {
    $settingSummary = parent::settingsSummary();
    $settingSummary[] = $this->t("Email addresses will be encoded using HTML Encode");
    return $settingSummary;
  }

  /*
 * {@inheritdoc}
 */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $settingsForm = parent::settingsForm($form, $form_state);
    $settingsForm['link_html_email'] = array(
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('link_html_email'),
      '#title' => $this->t('Automatically creates link from the email address'),
      '#description' => $this->t('Selecting "Automatically create links" will convert email addresses into a clickable "mailto:" link.'),
    );
    return $settingsForm;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'link_html_email' => FALSE,
      ] + parent::defaultSettings();
  }

}



