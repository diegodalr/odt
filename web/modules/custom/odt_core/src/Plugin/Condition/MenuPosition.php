<?php

declare(strict_types=1);

namespace Drupal\odt_core\Plugin\Condition;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Condition\Attribute\Condition;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Menu\MenuActiveTrailInterface;
use Drupal\Core\Menu\MenuLinkManagerInterface;
use Drupal\Core\Menu\MenuParentFormSelectorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Menu position' condition.
 */
#[Condition(
  id: 'odt_core_menu_position',
  label: new TranslatableMarkup('Menu position'),
)]
final class MenuPosition extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a new MenuPosition instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly MenuActiveTrailInterface $menuActiveTrail,
    private readonly MenuParentFormSelectorInterface $menuParentFormSelector,
    private readonly MenuLinkManagerInterface $pluginManagerMenuLink,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('menu.active_trail'),
      $container->get('menu.parent_form_selector'),
      $container->get('plugin.manager.menu.link'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    if (empty($this->configuration['menu_parent'])) {
      return TRUE;
    }

    list($menu_name, $link_plugin_id) = explode(':', $this->configuration['menu_parent'], 2);

    $active_trail_ids = $this->menuActiveTrail->getActiveTrailIds($menu_name);

    // The condition evaluates to TRUE if the given menu link is in the active
    // trail.
    if ($link_plugin_id) {
      return isset($active_trail_ids[$link_plugin_id]);
    }
    else {
      // Condition for when a whole menu was selected.
      return (bool) array_filter($active_trail_ids);
    }
  }

  /**
   * {@inheritdoc}
   * @throws PluginException
   */
  public function summary() {
    list($menu_name, $link_plugin_id) = explode(':', $this->configuration['menu_parent'], 2);
    if ($link_plugin_id) {
      $menu_link = $this->pluginManagerMenuLink->createInstance($link_plugin_id);
      return $this->t(
        'The menu item @link-title is either active or is in the active trail.', [
          '@link-title' => $menu_link->getTitle(),
        ]
      );
    }
    else {
      // Summary for when a whole menu was selected.
      return $this->t(
        'The active menu item is in the @menu-name menu.', [
          '@menu-name' => $menu_name,
        ]
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {

    // If we have existing selector element in configuration, use it.
    // Otherwise create a new element which will be set to the configuration
    // in submit.
    if (!empty($this->configuration['menu_parent'])) {
      $form['menu_parent'] = $this->menuParentFormSelector->parentSelectElement($this->configuration['menu_parent']);
    }
    else {
      $form['menu_parent'] = [
        '#type' => 'select',
        '#options' => $this->menuParentFormSelector->getParentSelectOptions(),
      ];
    }

    $form['menu_parent']['#options'] = ['' => $this->t("- None -")] + $form['menu_parent']['#options'];
    $form['menu_parent']['#title'] = $this->t("Menu parent");
    $form['menu_parent']['#description'] = $this->t("Show the block on this menu item and all its children.");

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $this->configuration['menu_parent'] = $form_state->getValue('menu_parent');
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return ['menu_parent' => ''] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $cache_contexts = parent::getCacheContexts();
    if ($this->configuration['menu_parent']) {
      list($menu_name, $link_plugin_id) = explode(':', $this->configuration['menu_parent'], 2);
      if ($menu_name) {
        $cache_contexts = Cache::mergeContexts($cache_contexts, ['route.menu_active_trails:' . $menu_name]);
      }
    }
    return $cache_contexts;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $tags = parent::getCacheTags();
    if ($this->configuration['menu_parent']) {
      list($menu_name, $link_plugin_id) = explode(':', $this->configuration['menu_parent'], 2);
      if ($menu_name) {
        $tags = Cache::mergeTags($tags, ['config:system.menu.' . $menu_name]);
      }
    }
    return $tags;
  }

}
