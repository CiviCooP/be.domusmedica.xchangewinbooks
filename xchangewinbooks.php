<?php

require_once 'xchangewinbooks.civix.php';
use CRM_Xchangewinbooks_ExtensionUtil as E;

/**
 * Implements hook_civicrm_batchQuery().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_batchQuery/
 */
function xchangewinbooks_civicrm_batchQuery(&$query) {
  //CRM_Xchangewinbooks_Export::batchQuery($query);
}

/**
 * Implements hook_civicrm_batchItems().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_batchItems/
 */
function xchangewinbooks_civicrm_batchItems(&$results, &$items) {
  CRM_Xchangewinbooks_Export::batchItems($results, $items);
}

/**
 * Implements hook_civicrm_navigationMenu
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu/
 */
function xchangewinbooks_civicrm_navigationMenu(&$menu) {
  // Check that our item doesn't already exist
  $menuItemSearch = array('url' => 'civicrm/domusmedica/xchangewinbooks/page/settings');
  $menuItems = array();
  CRM_Core_BAO_Navigation::retrieve($menuItemSearch, $menuItems);

  if ( ! empty($menuItems) ) {
    return;
  }

  $navId = CRM_Core_DAO::singleValueQuery("SELECT max(id) FROM civicrm_navigation");
  if (is_integer($navId)) {
    $navId++;
  }
  // Find the Administer menu
  $administerId = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'Administer', 'id', 'name');
  $params[$administerId]['child'][$navId] = array(
    'attributes' => array (
      'label' => ts('Export to Winbooks Settings',array('domain' => 'be.domusmedica.xchangewinbooks')),
      'name' => 'Export to Winbooks Settings',
      'url' => 'civicrm/domusmedica/xchangewinbooks/page/settings',
      'permission' => 'access CiviCRM, administer CiviCRM',
      'operator' => 'AND',
      'separator' => 1,
      'parentID' => $administerId,
      'navID' => $navId,
      'active' => 1
    ),
  );
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function xchangewinbooks_civicrm_config(&$config) {
  _xchangewinbooks_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function xchangewinbooks_civicrm_xmlMenu(&$files) {
  _xchangewinbooks_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function xchangewinbooks_civicrm_install() {
  _xchangewinbooks_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function xchangewinbooks_civicrm_postInstall() {
  _xchangewinbooks_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function xchangewinbooks_civicrm_uninstall() {
  _xchangewinbooks_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function xchangewinbooks_civicrm_enable() {
  _xchangewinbooks_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function xchangewinbooks_civicrm_disable() {
  _xchangewinbooks_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function xchangewinbooks_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _xchangewinbooks_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function xchangewinbooks_civicrm_managed(&$entities) {
  _xchangewinbooks_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function xchangewinbooks_civicrm_caseTypes(&$caseTypes) {
  _xchangewinbooks_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function xchangewinbooks_civicrm_angularModules(&$angularModules) {
  _xchangewinbooks_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function xchangewinbooks_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _xchangewinbooks_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function xchangewinbooks_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function xchangewinbooks_civicrm_navigationMenu(&$menu) {
  _xchangewinbooks_civix_insert_navigation_menu($menu, NULL, array(
    'label' => E::ts('The Page'),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _xchangewinbooks_civix_navigationMenu($menu);
} // */
