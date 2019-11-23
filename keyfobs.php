<?php

require_once 'CRM/Keyfobs/Upgrader/Base.php';
require_once 'CRM/Keyfobs/Upgrader.php';
require_once 'keyfobs.civix.php';
require_once __DIR__ . '/vendor/autoload.php';


use CRM_Keyfobs_ExtensionUtil as E;

function keyfobs_civicrm_tabs(&$tabs, $contactID) {
  $session = CRM_Core_Session::singleton();

  $isAdmin = CRM_Core_Permission::check('administer CiviCRM') && CRM_Core_Permission::check('edit all contacts');
  if ($isAdmin) {
    $url = CRM_Utils_System::url( 'civicrm/contact/view/keyfob', "reset=1&cid={$contactID}&snippet=1" );
    $tabs[] = array(
      'id' => 'keyfob',
      'url' => $url,
      'title' => 'Keyfob',
      'weight' => 250,
    );
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function keyfobs_civicrm_config(&$config) {
  _keyfobs_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function keyfobs_civicrm_xmlMenu(&$files) {
  _keyfobs_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function keyfobs_civicrm_install() {
  _keyfobs_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function keyfobs_civicrm_postInstall() {
  _keyfobs_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function keyfobs_civicrm_uninstall() {
  _keyfobs_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function keyfobs_civicrm_enable() {
  $upgrader = CRM_Keyfobs_Upgrader::instance();
  $upgrader->onInstall();
  _keyfobs_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function keyfobs_civicrm_disable() {
  _keyfobs_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function keyfobs_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _keyfobs_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function keyfobs_civicrm_managed(&$entities) {
  _keyfobs_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function keyfobs_civicrm_caseTypes(&$caseTypes) {
  _keyfobs_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function keyfobs_civicrm_angularModules(&$angularModules) {
  _keyfobs_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function keyfobs_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _keyfobs_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function keyfobs_civicrm_entityTypes(&$entityTypes) {
  _keyfobs_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function keyfobs_civicrm_themes(&$themes) {
  _keyfobs_civix_civicrm_themes($themes);
}

function keyfobs_alterCalculatedMembershipStatus(&$membershipStatus, $arguments, $membership) {
  var_dump($membership);
}

function keyfobs_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == 'Membership') {
    $membership = $objectRef;
    $keyfob = new CRM_Keyfobs_BAO_Keyfob();
    $keyfob->get('contact_id', $membership->contact_id);
    $keyfob->update_sqs();
  }
}


// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function keyfobs_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
// function keyfobs_civicrm_navigationMenu(&$menu) {
//   _keyfobs_civix_insert_navigation_menu($menu, 'Keyfobs', array(
//     'label' =>
//   ));
// }


// function keyfobs_civicrm_navigationMenu(&$menu) {
//   _keyfobs_civix_insert_navigation_menu($menu, 'Mailings', array(
//     'label' => E::ts('New subliminal message'),
//     'name' => 'mailing_subliminal_message',
//     'url' => 'civicrm/mailing/subliminal',
//     'permission' => 'access CiviMail',
//     'operator' => 'OR',
//     'separator' => 0,
//   ));
//   _keyfobs_civix_navigationMenu($menu);
// }
