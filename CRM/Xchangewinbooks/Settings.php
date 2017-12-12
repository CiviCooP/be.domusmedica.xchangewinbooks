<?php

/**
 * Class for Domus Medica Export to Winbooks Settings
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 12 Dec 2017
 * @license AGPL-3.0
 */

class CRM_Xchangewinbooks_Settings {
  private $_settingsPath = NULL;
  private $_generic = array();
  private $_factuurGrootboek = array();
  private $_factuurAnalytisch = array();
  private $_creditGrootboek = array();
  private $_creditAnalytisch = array();

  /**
   * CRM_Xchangewinbooks_Settings constructor.
   */
  function __construct() {
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $settingsPath = $container->getPath('be.domusmedica.xchangewinbooks') . '/settings/';
    if (!is_dir($settingsPath) || !file_exists($settingsPath)) {
      CRM_Core_Error::createError(ts('Could not find the folder ' . $settingsPath
        . ' which is required for extension be.domusmedica.xchangewinbooks in ' . __METHOD__
        . '.It does not exist or is not a folder, contact your system administrator'));
    }
    $this->_settingsPath = $settingsPath;
    $this->setSettingsFromJson();
  }

  /**
   * Getter for generic settings
   *
   * @return array
   */
  public function getGeneric() {
    return $this->_generic;
  }

  /**
   * Getter for verkoopfactuur grootboek niveau settings
   *
   * @return array
   */
  public function getFactuurGrootboek() {
    return $this->_factuurGrootboek;
  }

  /**
   * Getter for verkoopfactuur analytisch niveau settings
   *
   * @return array
   */
  public function getFactuurAnalytisch() {
    return $this->_factuurAnalytisch;
  }

  /**
   * Getter for creditnota grootboek niveau settings
   *
   * @return array
   */
  public function getCreditGrootboek() {
    return $this->_creditGrootboek;
  }

  /**
   * Getter for creditnota analytisch niveau settings
   *
   * @return array
   */
  public function getCreditAnalytisch() {
    return $this->_creditAnalytisch;
  }

  /**
   * Method to retrieve the settings from Json files and store them in the correct property
   */
  private function setSettingsFromJson() {
    $settings = array(
      '_generic' => 'generic',
      '_factuurGrootboek' => 'pattern_factuur_grootboek',
      '_factuurAnalytisch' => 'pattern_factuur_analytisch',
      '_creditGrootboek' => 'pattern_credit_grootboek',
      '_creditAnalytisch' => 'pattern_credit_analytisch',
    );
    foreach ($settings as $property => $fileName) {
      $jsonFile = $this->_settingsPath.$fileName.'.json';
      if (!file_exists($jsonFile)) {
        CRM_Core_Error::createError(ts('Could not load activity_types configuration file for extension,
      activity your system administrator!'));
      }
      $retrievedJson = file_get_contents($jsonFile);
      $this->$property = json_decode($retrievedJson, true);

    }
  }

  /**
   * Method to save the settings to Json files
   *
   * @param string $fileName
   * @param array $data
   * @throws Exception when not able to save
   */
  public function save($fileName, $data) {
    if (empty($fileName) || empty($data) || !is_array($data)) {
      CRM_Core_Error::createError('Parameters fileName and data can not be empty, data has to be an array in '
        .__METHOD__.' (extension be.domusmedica.xchangewinbooks)');
    }
    $jsonFile = $this->_settingsPath .$fileName.'.json';
    try {
      $fh = fopen($jsonFile, 'w');
      fwrite($fh, json_encode($data, JSON_PRETTY_PRINT));
      fclose($fh);
    } catch (Exception $ex) {
      throw new Exception('Could not open '.$fileName
        .'.json, contact your system administrator (extension be.domusmedica.changewinbooks). Error reported: '
        . $ex->getMessage());
    }
  }
}