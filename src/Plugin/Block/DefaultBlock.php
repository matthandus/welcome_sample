<?php

namespace Drupal\welcome_sample\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides a 'DefaultBlock' block.
 *
 * @Block(
 *  id = "default_block",
 *  admin_label = @Translation("Welcome"),
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
    // Get current user
    $user = \Drupal::currentUser();
    // Add default message for logged in users
    if ($user->isAuthenticated()) {
      // Get username
      $name = $user->getUsername();
      // Get last login time
      $logintime = $user->getLastAccessedTime();
      $login = \Drupal::service('date.formatter')->format(
        $logintime, 'custom', 'm/d/Y'
      );
      // Get user id
      $id = $user->id();
      // Generate profile url
      $url = Url::fromRoute('entity.user.canonical', array('user'  => $id));
      // Generate profile link
      $profile_link = Link::fromTextAndUrl('Visit your profile', $url);
      $profile = $profile_link->toString();
      $build['default_block_authenticated_message']['#markup'] = $this->t('<p>Hello ​ @name!<br>
      Your last log in was ​@login.<br>
      @profile</p>', array('@name' => $name, '@login' => $login, '@profile' => $profile));
    }
    $build['default_block_welcome_message']['#markup'] = '<p>' . $this->configuration['welcome_message']. '</p>';

    return $build;
  }

  /**
  * {@inheritdoc}
  */
  public function blockAccess(AccountInterface $account) {
    $user = \Drupal::currentUser();
    return AccessResult::allowedIf(($user->isAuthenticated() == TRUE) || ($this->configuration['hide_welcome_message_from_anonym'] == 1) && ($user->isAuthenticated() == FALSE));
  }

}
