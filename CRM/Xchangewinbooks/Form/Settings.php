<?php

use CRM_Xchangewinbooks_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Xchangewinbooks_Form_Settings extends CRM_Core_Form {
  public function buildQuickForm() {
    $this->add('text', 'vf_gn_eerste_regelnummer', ts('Regelnummer'), array(),FALSE);
    $this->add('text', 'vf_gn_tweede_regelnummer', ts('Regelnummer'), array(),FALSE);
    $this->add('text', 'vf_gn_derde_regelnummer', ts('Regelnummer'), array(),FALSE);
    $this->add('text', 'vf_gn_eerste_dagboek', ts('Dagboek'), array(),FALSE);
    $this->add('text', 'vf_gn_tweede_dagboek', ts('Dagboek'), array(),FALSE);
    $this->add('text', 'vf_gn_derde_dagboek', ts('Dagboek'), array(),FALSE);
    $this->add('text', 'vf_gn_eerste_dagboek_code', ts('Dagboek Code'), array(),FALSE);
    $this->add('text', 'vf_gn_tweede_dagboek_code', ts('Dagboek Code'), array(),FALSE);
    $this->add('text', 'vf_gn_derde_dagboek_code', ts('Dagboek Code'), array(),FALSE);

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => true,),
      array('type' => 'cancel', 'name' => ts('Cancel'),),));

    parent::buildQuickForm();
  }

  public function preProcess() {
    CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url('civicrm/domusmedica/xchangewinbooks/page/settings', 'reset=1', true));
  }

  public function postProcess() {
    $values = $this->exportValues();
    $options = $this->getColorOptions();
    CRM_Core_Session::setStatus(E::ts('You picked color "%1"', array(
      1 => $options[$values['favorite_color']],
    )));
    parent::postProcess();
  }

  /**
   * Method to set the default values
   * @return array
   */
  public function setDefaultValues() {
    $defaults['vf_gn_eerste_regelnummer'] = '1';
    $defaults['vf_gn_tweede_regelnummer'] = '3';
    $defaults['vf_gn_derde_regelnummer'] = '4';
    $defaults['vf_gn_eerste_dagboek'] = 'VNCIVI';
    $defaults['vf_gn_tweede_dagboek'] = 'VNCIVI';
    $defaults['vf_gn_derde_dagboek'] = 'VNCIVI';
    $defaults['vf_gn_eerste_dagboek_code'] = '2';
    $defaults['vf_gn_tweede_dagboek_code'] = '2';
    $defaults['vf_gn_derde_dagboek_code'] = '2';
    return $defaults;
  }

}
