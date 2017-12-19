<?php
use CRM_Xchangewinbooks_ExtensionUtil as E;

class CRM_Xchangewinbooks_Page_Settings extends CRM_Core_Page {

  /**
   * Standard run function created when generating page with Civix
   *
   * @access public
   */
  function run() {
    $settings = new CRM_Xchangewinbooks_Settings();
    $this->setPageConfiguration();
    $this->assign('generic', $this->setOneLine('gen', $settings->getGeneric()));
    $this->assign('factuur_grootboek', $this->setGrootboek('fgn',$settings->getFactuurGrootboek()));
    $this->assign('credit_grootboek', $this->setGrootboek('cgn', $settings->getCreditGrootboek()));
    $this->assign('factuur_analytisch', $this->setOneLine('fan', $settings->getFactuurAnalytisch()));
    $this->assign('credit_analytisch', $this->setOneLine('can', $settings->getCreditAnalytisch()));
    $this->assign('done_url', '<a class="button done" title="Done" href="' . CRM_Core_Session::singleton()->readUserContext() . '"><span>'.ts("Done").'</span></a>');

    parent::run();
  }

  /**
   * Method to create page array with grootboek niveau settings
   *
   * @param string $type
   * @param array $data
   * @return array
   */
  private function setGrootboek($type, $data) {
    $lines = array();
    foreach ($data as $key => $values) {
      if (empty($values['eerste'])) {
        $lines['eerste'][] = $key . ": <strong>(leeg)</strong>";
      } else {
        $lines['eerste'][] = $key . ": <strong>" . $values['eerste']."</strong>";
      }
      if (empty($values['tweede'])) {
        $lines['tweede'][] = $key . ": <strong>(leeg)</strong>";
      } else {
        $lines['tweede'][] = $key . ": <strong>" . $values['tweede']."</strong>";
      }
      if (empty($values['derde'])) {
        $lines['derde'][] = $key . ": <strong>(leeg)</strong>";
      } else {
        $lines['derde'][] = $key . ": <strong>" . $values['derde']."</strong>";
      }
    }
    $editUrl = CRM_Utils_System::url('civicrm/domusmedica/xchangewinbooks/form/settings',
      'reset=1&action=update&type='.$type, true);
    $result = array(
      'eerste' => implode(" / ", $lines['eerste']),
      'tweede' => implode(" / ", $lines['tweede']),
      'derde' => implode(" / ", $lines['derde']),
      'edit' => '<a class="action-item" title="Edit" href="' . $editUrl . '">' . ts('Edit') . '</a>',
    );
    return $result;
  }

  /**
   * Method to create page array with one line settings
   *
   * @param string $type
   * @param array $data
   * @return array
   */
  private function setOneLine($type, $data) {
    $lines = array();
    foreach ($data as $key => $value) {
      $parts = explode('_', $key);
      if (isset($parts[1])) {
        $key = implode(' ', $parts);
      }
      $lines[] = $key . ": <strong>" . $value."</strong>";
    }
    $editUrl = CRM_Utils_System::url('civicrm/domusmedica/xchangewinbooks/form/settings',
      'reset=1&action=update&type='.$type, true);
    return array(
      'value' => implode(" / ", $lines),
      'edit' => '<a class="action-item" title="Edit" href="' . $editUrl . '">' . ts('Edit') . '</a>',
    );
  }

  /**
   * Function to set the page configuration
   *
   * @access protected
   */
  protected function setPageConfiguration() {
    CRM_Utils_System::setTitle(ts('Export to Winbooks Settings Domus Medica'));
    $this->assign('help_text', ts("Hieronder staan de instellingen die gebruikt worden om facturen, creditnota's en contacten te exporteren naar Winbooks"));
  }
}
