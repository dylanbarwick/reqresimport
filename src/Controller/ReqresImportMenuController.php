<?php

namespace Drupal\reqresimport\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller reqresimport routes.
 */
class ReqresImportMenuController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'reqresimport';
  }

  /**
   * Page callback for the parent menu entry.
   *
   * This controller will build a render array from all child items of
   * this menu item, ie all views that nominate this menu entry as a parent.
   *
   * @throws \InvalidArgumentException
   */
  public function reqresSettingsParentMenu() {
    // Create a new menuTree object.
    $menu_tree = \Drupal::menuTree();
    $menu_name = 'admin';

    // Get parameters.
    $parameters = $menu_tree->getCurrentRouteMenuTreeParameters($menu_name);

    // Get current link ID, ie the ID of this menu item.
    $currentLinkId = reset($parameters->activeTrail);
    $parameters->setRoot($currentLinkId);

    // Load menu tree according to parameters.
    $tree = $menu_tree->load($menu_name, $parameters);

    // Transform the tree using manipulators.
    $manipulators = [
      // Only show links that are accessible for the current user.
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      // Use the default sorting of menu links.
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $tree = $menu_tree->transform($tree, $manipulators);

    // Take the first (only) element out of the resulting array.
    $first = array_values($tree)[0];

    // Build the subtree which will include all future reports dumped in here.
    $menu = $menu_tree->build($first->subtree);

    $build = [
      '#markup' => \Drupal::service('renderer')->render($menu),
    ];

    return $build;
  }

}
