<?php
/**
 * Class for Domus Medica Export to Winbooks general utilities
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 12 Dec 2017
 * @license AGPL-3.0
 */

class CRM_Xchangewinbooks_Utils {

  /**
   * Function to insert navigation menu
   *
   * @param $menu
   * @param $path
   * @param $item
   * @return bool
   */
  public static function insertNavigationMenu(&$menu, $path, $item) {
    // If we are done going down the path, insert menu
    if (empty($path)) {
      $menu[] = array(
        'attributes' => array_merge(array(
          'label'      => CRM_Utils_Array::value('name', $item),
          'active'     => 1,
        ), $item),
      );
      return TRUE;
    }
    else {
      // Find an recurse into the next level down
      $found = FALSE;
      $path = explode('/', $path);
      $first = array_shift($path);
      foreach ($menu as $key => &$entry) {
        if ($entry['attributes']['name'] == $first) {
          if (!$entry['child']) {
            $entry['child'] = array();
          }
          $newPath = implode('/', $path);
          $found = self::insertNavigationMenu($entry['child'], $newPath, $item);
        }
      }
      return $found;
    }
  }

}