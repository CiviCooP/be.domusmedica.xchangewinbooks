<?php

use CRM_Xchangewinbooks_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Xchangewinbooks_Form_Settings extends CRM_Core_Form {

  private $_type = NULL;
  private $_data = array();

  /**
   * Overridden parent method to build form
   */
  public function buildQuickForm() {
    // todo help text with example of column:
    // todo validate scheidingsteken en achter de komma
    $this->assign('settings_type', $this->_type);
    $this->add('hidden', 'settings_type');
    switch ($this->_type) {
      case 'gen':
        $this->_data = CRM_Xchangewinbooks_Settings::singleton()->getGeneric();
        $this->buildGenericElements();
        break;
      case 'fgn':
        $this->_data = CRM_Xchangewinbooks_Settings::singleton()->getFactuurGrootboek();
        $this->buildGrootboekElements();
        break;
      case 'fan':
        $this->_data = CRM_Xchangewinbooks_Settings::singleton()->getFactuurAnalytisch();
        $this->buildAnalytischElements();
        break;
      case 'cgn':
        $this->_data = CRM_Xchangewinbooks_Settings::singleton()->getCreditGrootboek();
        $this->buildGrootboekElements();
        break;
      case 'can':
        $this->_data = CRM_Xchangewinbooks_Settings::singleton()->getCreditAnalytisch();
        $this->buildAnalytischElements();
        break;
    }

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => true,),
      array('type' => 'cancel', 'name' => ts('Cancel'),),));
    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  /**
   * Method to build the form elements for grootboek settings
   */
  private function buildGrootboekElements() {
    foreach ($this->_data as $key => $elements) {
      foreach ($elements as $line => $value)
      $this->add('text', $line.'_'.$key, ts($key), array(), false);
    }
  }

  /**
   * Method to build the form elements for generic settings
   */
  private function buildGenericElements() {
    foreach ($this->_data as $key => $value) {
      $parts = explode('_', $key);
      if (isset($parts[1])) {
        $label = implode(' ', $parts);
      } else {
        $label = $key;
      }
      if ($key != 'Velden_tussen_quotes') {
        $this->add('text', $key, ts($label),array(), true);
      } else {
        $this->addYesNo($key, ts($label), false, false);
      }
    }
  }

  /**
   * Method to build the form elements for analytisch settings
   */
  private function buildAnalytischElements() {
    foreach ($this->_data as $key => $value) {
      $this->add('text', $key, ts($key),array(), false);
    }
  }

  /**
   * Method to set default values based on type
   *
   * @return array|NULL|void
   */
  public function setDefaultValues() {
    $defaults = array();
    $defaults['settings_type'] = $this->_type;
    switch ($this->_type) {
      case 'gen':
        foreach ($this->_data as $key => $value) {
          $defaults[$key] = $value;
        }
        break;
      case 'fan':
        foreach ($this->_data as $key => $value) {
          $defaults[$key] = $value;
        }
        break;
      case 'can':
        foreach ($this->_data as $key => $value) {
          $defaults[$key] = $value;
        }
        break;
      case 'fgn':
        foreach ($this->_data as $key => $elements) {
          foreach ($elements as $line => $value) {
            $defaults[$line.'_'.$key] = $value;
          }
        }
        break;
      case 'cgn':
        foreach ($this->_data as $key => $elements) {
          foreach ($elements as $line => $value) {
            $defaults[$line.'_'.$key] = $value;
          }
        }
        break;
    }
    return $defaults;
  }

  /**
   * Overridden parent method with processing before form is built
   */
  public function preProcess() {
    $requestValues = CRM_Utils_Request::exportValues();
    if (isset($requestValues['type'])) {
      $this->_type = $requestValues['type'];
    } else {
      CRM_Core_Error::createError('No type in parameters to form in '.__METHOD__
        .', extension be.domusmedica.xchangewinbooks');
    }
    $firstTitle = 'Export to Winbooks - ';
    switch ($this->_type) {
      case 'gen':
        CRM_Utils_System::setTitle($firstTitle.ts('Algemene instellingen'));
        break;
      case 'fgn':
        CRM_Utils_System::setTitle($firstTitle.ts('Factuur grootboek niveau instellingen'));
        break;
      case 'fan':
        CRM_Utils_System::setTitle($firstTitle.ts('Factuur analytisch niveau instellingen'));
        break;
      case 'cgn':
        CRM_Utils_System::setTitle($firstTitle.ts('Creditnota grootboek niveau instellingen'));
        break;
      case 'cga':
        CRM_Utils_System::setTitle($firstTitle.ts('Creditnota analytisch niveau instellingen'));
        break;
      default:
        CRM_Core_Error::createError('Parameter '.$this->_type.' not valid in '.__METHOD__.'(extension be.domusmedica.xchangewinbooks');
    }
    CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url('civicrm/domusmedica/xchangewinbooks/page/settings', 'reset=1', true));
  }

  /**
   * Overridden parent method to process form after submission
   *
   * @throws Exception
   */
  public function postProcess() {
    if (!isset($this->_submitValues['settings_type'])) {
      CRM_Core_Error::createError(ts('Can not find required element settings_type in form submit values in '.__METHOD__.', (extension be.domusmedica.xchangewinbooks)'));
    }
    switch ($this->_submitValues['settings_type']) {
      case 'can':
        $this->saveSingleLine();
        break;
      case 'fan':
        $this->saveSingleLine();
        break;
      case 'gen':
        $this->saveSingleLine();
        break;
      case 'fgn':
        $this->saveTripleLine();
        break;
      case 'cgn':
        $this->saveTripleLine();
        break;
      default:
        CRM_Core_Error::createError('Invalid settings_type in form submit in '.__METHOD__.' (extension be.domusmedica.xchangewinbooks)');
        break;
    }
    parent::postProcess();
  }

  /**
   * Method to save triple line settings (factuur or credit grootboek)
   *
   * @throws Exception
   */
  private function saveTripleLine() {
    if ($this->_submitValues['settings_type'] == 'cgn') {
      $data = CRM_Xchangewinbooks_Settings::singleton()->getCreditGrootboek();
    } else {
      $data = CRM_Xchangewinbooks_Settings::singleton()->getFactuurGrootboek();
    }
    foreach ($data as $key => $value) {
      $parts = explode(' ', $key);
      $fieldName1 = 'eerste_'.implode('_', $parts);
      $fieldName2 = 'tweede_'.implode('_', $parts);
      $fieldName3 = 'derde_'.implode('_', $parts);
      if (isset($this->_submitValues[$fieldName1])) {
        if ($value['eerste'] != $this->_submitValues[$fieldName1]) {
          $data[$key]['eerste'] = $this->_submitValues[$fieldName1];
        }
      }
      if (isset($this->_submitValues[$fieldName2])) {
        if ($value['tweede'] != $this->_submitValues[$fieldName2]) {
          $data[$key]['tweede'] = $this->_submitValues[$fieldName2];
        }
      }
      if (isset($this->_submitValues[$fieldName3])) {
        if ($value['derde'] != $this->_submitValues[$fieldName3]) {
          $data[$key]['derde'] = $this->_submitValues[$fieldName3];
        }
      }
    }
    $fileName = CRM_Xchangewinbooks_Settings::singleton()->getJsonFileName($this->_submitValues['settings_type']);
    CRM_Xchangewinbooks_Settings::singleton()->save($fileName, $data);
  }

  /**
   * Method to save single line settings (generic + factuur or credit analytisch)
   *
   * @throws Exception
   */
  private function saveSingleLine() {
    $fileName = CRM_Xchangewinbooks_Settings::singleton()->getJsonFileName($this->_submitValues['settings_type']);
    CRM_Xchangewinbooks_Settings::singleton()->save($fileName, $this->getSaveList());
  }

  /**
   * Method to get list of form elements to export
   *
   * @return array
   */
  private function getSaveList() {
    $result = array();
    $ignores = array('qfKey', 'settings_type', 'entryURL');
    foreach ($this->_submitValues as $key => $value) {
      // do not export if first 4 pos = _qf_ or if key is in ignores
      if (substr($key,0,4) != '_qf_') {
        if (!in_array($key, $ignores)) {
          $result[$key] = $value;
        }
      }
    }
    return $result;
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
