<?php

namespace Drupal\welcome_sample\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'DefaultBlock' block.
 *
 * @Block(
 *  id = "default_block",
 *  admin_label = @Translation("Default block"),
 * )
 */
class DefaultBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
                ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['welcome_message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Welcome Message'),
    '#description' => $this->t('Enter text to display a custom welcome message to users.'),
      '#default_value' => $this->configuration['welcome_message'],
      '#weight' => '0',
    ];
    $form['hide_welcome_message_from_anonym'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide Welcome Message from Anonymous Users'),
    '#description' => $this->t('Check the box to hide the welcome message from users that are not logged in.'),
      '#default_value' => $this->configuration['hide_welcome_message_from_anonym'],
      '#weight' => '1',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['welcome_message'] = $form_state->getValue('welcome_message');
    $this->configuration['hide_welcome_message_from_anonym'] = $form_state->getValue('hide_welcome_message_from_anonym');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['default_block_welcome_message']['#markup'] = '<p>' . $this->configuration['welcome_message']. '</p>';
    $build['default_block_hide_welcome_message_from_anonym']['#markup'] = '<p>' . $this->configuration['hide_welcome_message_from_anonym']. '</p>';

    return $build;
  }

}
