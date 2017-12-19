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
  private $_genJsonFileName = NULL;
  private $_fgnJsonFileName = NULL;
  private $_fanJsonFileName = NULL;
  private $_cgnJsonFileName = NULL;
  private $_canJsonFileName = NULL;

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
    $this->_fgnJsonFileName = 'pattern_factuur_grootboek';
    $this->_fanJsonFileName = 'pattern_factuur_analytisch';
    $this->_genJsonFileName = 'generic';
    $this->_canJsonFileName = 'pattern_credit_analytisch';
    $this->_cgnJsonFileName = 'pattern_credit_grootboek';
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
   * Method to get the json file name based on type
   *
   * @param $type
   * @return null
   */
  public function getJsonFileName($type) {
    $property = "_".$type.'JsonFileName';
    if (isset($this->$property)) {
      return $this->$property;
    } else {
      return NULL;
    }
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
      '_generic' => $this->_genJsonFileName,
      '_factuurGrootboek' => $this->_fgnJsonFileName,
      '_factuurAnalytisch' => $this->_fanJsonFileName,
      '_creditGrootboek' => $this->_cgnJsonFileName,
      '_creditAnalytisch' => $this->_canJsonFileName,
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

  /**
   * Method to get the between quotes generic setting
   *
   * @return int|mixed
   */
  public function getQuotes() {
    if (isset($this->_generic['Velden_tussen_quotes'])) {
      return $this->_generic['Velden_tussen_quotes'];
    } else {
      return 0;
    }
  }

  /**
   * Method to get the decimal places setting
   *
   * @return int|mixed
   */
  public function getDecimalPlaces() {
    if (isset($this->_generic['Cijfers_achter_de_komma'])) {
      return $this->_generic['Cijfers_achter_de_komma'];
    } else {
      return 0;
    }
  }

  /**
   * Method to get the credit analytic code setting
   *
   * @return int|mixed
   */
  public function getAnalyticCodeCredit() {
    if (isset($this->_generic['Analystische_code_Creditnota'])) {
      return $this->_generic['Analystische_code_Creditnota'];
    } else {
      return 0;
    }
  }

  /**
   * Method to get the factuu analytic code setting
   *
   * @return int|mixed
   */
  public function getAnalyticCodeFactuur() {
    if (isset($this->_generic['Analytische_code_Factuur'])) {
      return $this->_generic['Analytische_code_Factuur'];
    } else {
      return 0;
    }
  }
}