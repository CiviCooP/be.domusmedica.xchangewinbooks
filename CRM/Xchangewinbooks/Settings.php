<?php

/**
 * Class for Domus Medica Export to Winbooks Settings
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 12 Dec 2017
 * @license AGPL-3.0
 */

class CRM_Xchangewinbooks_Settings {
  // singleton pattern
  static private $_singleton = NULL;

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
  private $_contributionDataCustomGroup = NULL;
  private $_invoiceExported = NULL;
  private $_invoiceBatch = NULL;
  private $_invoiceExportDate = NULL;
  private $_creditExported = NULL;
  private $_creditBatch = NULL;
  private $_creditExportDate = NULL;

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
    // check if required custom fields exist and create if not
    try {
      $this->_contributionDataCustomGroup = civicrm_api3('CustomGroup', 'getsingle', array(
        'name' => 'domus_contribution_data',
        'extends' => 'Contribution',
      ));
    } catch (CiviCRM_API3_Exception $ex) {
      CRM_Core_Error::createError('Could not find a single custom group with name domus_contribution_data extending Contribution, is required for extension be.domusmedica.xchangewinbooks. Contact your system administrator.');
    }
    $this->checkRequiredCustomFields();
  }

  /**
   * Getter for contribution data custom group
   *
   * @param $key
   * @return array|null
   */
  public function getContributionDataCustomGroup($key) {
    if (!empty($key) && isset($this->_contributionDataCustomGroup[$key])) {
      return $this->_contributionDataCustomGroup[$key];
    } else {
      return $this->_contributionDataCustomGroup;
    }
  }

  /**
   * Getter for invoice exported custom field
   *
   * @param $key
   * @return array|null
   */
  public function getInvoiceExportedCustomField($key) {
    if (!empty($key) && isset($this->_invoiceExported[$key])) {
      return $this->_invoiceExported[$key];
    } else {
      return $this->_invoiceExported;
    }
  }

  /**
   * Getter for invoice exported in batch custom field
   *
   * @param $key
   * @return array|null
   */
  public function getInvoiceBatchCustomField($key) {
    if (!empty($key) && isset($this->_invoiceBatch[$key])) {
      return $this->_invoiceBatch[$key];
    } else {
      return $this->_invoiceBatch;
    }
  }

  /**
   * Getter for invoice export date custom field
   *
   * @param $key
   * @return array|null
   */
  public function getInvoiceExportDateCustomField($key) {
    if (!empty($key) && isset($this->_invoiceExportDate[$key])) {
      return $this->_invoiceExportDate[$key];
    } else {
      return $this->_invoiceExportDate;
    }
  }

  /**
   * Getter for credit exported custom field
   *
   * @param $key
   * @return array|null
   */
  public function getCreditExportedCustomField($key) {
    if (!empty($key) && isset($this->_creditExported[$key])) {
      return $this->_creditExported[$key];
    } else {
      return $this->_creditExported;
    }
  }

  /**
   * Getter for credit exported in batch custom field
   *
   * @param $key
   * @return array|null
   */
  public function getCreditBatchCustomField($key) {
    if (!empty($key) && isset($this->_creditBatch[$key])) {
      return $this->_creditBatch[$key];
    } else {
      return $this->_creditBatch;
    }
  }

  /**
   * Getter for credit export date custom field
   *
   * @param $key
   * @return array|null
   */
  public function getCreditExportDateCustomField($key) {
    if (!empty($key) && isset($this->_creditExportDate[$key])) {
      return $this->_creditExportDate[$key];
    } else {
      return $this->_creditExportDate;
    }
  }

  /**
   * Method to check if required custom fields exist and create if not
   */
  private function checkRequiredCustomFields() {
    $customFields = array(
      '_invoiceExported' => array(
        'name' => 'domus_invoice_exported',
        'column_name' => 'domus_invoice_exported',
        'label' => 'Factuur geëxporteerd?',
        'data_type' => 'Boolean',
        'html_type' => 'Radio',
        'default' => 0,
        'is_searchable' => 1,
        'is_active' => 1,
        'is_view' => 1,
      ),
      '_invoiceBatch' => array(
        'name' => 'domus_invoice_batch',
        'column_name' => 'domus_invoice_batch',
        'label' => 'Factuur geëxporteerd in batch id',
        'data_type' => 'Int',
        'html_type' => 'Text',
        'is_searchable' => 1,
        'is_search_range' => 1,
        'is_active' => 1,
        'is_view' => 1,
      ),
      '_inoviceExportDate' => array(
        'name' => 'domus_invoice_export_date',
        'column_name' => 'domus_invoice_export_date',
        'label' => 'Datum factuur geëxporteerd',
        'data_type' => 'Date',
        'html_type' => 'Select Date',
        'is_searchable' => 1,
        'is_search_range' => 1,
        'is_active' => 1,
        'is_view' => 1,
        'date_format' => 'dd/mm/yy',
      ),
      '_creditExported' => array(
        'name' => 'domus_credit_exported',
        'column_name' => 'domus_credit_exported',
        'label' => 'Creditnota geëxporteerd?',
        'data_type' => 'Boolean',
        'html_type' => 'Radio',
        'default' => 0,
        'is_searchable' => 1,
        'is_active' => 1,
        'is_view' => 1,
      ),
      '_creditBatch' => array(
        'name' => 'domus_credit_batch',
        'column_name' => 'domus_credit_batch',
        'label' => 'Creditnota geëxporteerd in batch id',
        'data_type' => 'Int',
        'html_type' => 'Text',
        'is_searchable' => 1,
        'is_search_range' => 1,
        'is_active' => 1,
        'is_view' => 1,
      ),
      '_creditExportDate' => array(
        'name' => 'domus_credit_export_date',
        'column_name' => 'domus_credit_export_date',
        'label' => 'Datum creditnota geëxporteerd',
        'data_type' => 'Date',
        'html_type' => 'Select Date',
        'is_searchable' => 1,
        'is_search_range' => 1,
        'is_active' => 1,
        'is_view' => 1,
        'date_format' => 'dd/mm/yy',
      ),
    );
    foreach ($customFields as $property => $apiParams) {
      $apiParams['custom_group_id'] = $this->_contributionDataCustomGroup['id'];
      try {
        $this->$property = civicrm_api3('CustomField', 'getsingle', array(
          'custom_group_id' => $apiParams['custom_group_id'],
          'name' => $apiParams['name'],
        ));
      } catch (CiviCRM_API3_Exception $ex) {
        // create if not found
        try {
          $created = civicrm_api3('CustomField', 'create', $apiParams);
          $this->$property = $created['values'][$created['id']];
        }
        catch (CiviCRM_API3_Exception $ex) {
          CRM_Core_Error::createError('Could not find or create custom field with name '.$apiParams['name']
            .' in custom group '.$this->_contributionDataCustomGroup['name']
            .', required for extension be.domusmedica.xchangewinbooks. Contact your system administrator!');
        }

      }
    }
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

  /**
   * Function to return singleton object
   *
   * @return object $_singleton
   * @access public
   * @static
   */
  public static function &singleton() {
    if (self::$_singleton === NULL) {
      self::$_singleton = new CRM_Xchangewinbooks_Settings();
    }
    return self::$_singleton;
  }
}