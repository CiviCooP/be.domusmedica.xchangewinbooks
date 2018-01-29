<?php
/**
 * Class for Domus Medica Export to Winbooks
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 8 Dec 2017
 * @license AGPL-3.0
 */

class CRM_Xchangewinbooks_Export {
  /**
   * Method to get the alternative data for the batch
   *
   * @param int $batchId
   * @return object|bool
   */
  private static function alternativeBatchItems($batchId) {
    $query = "SELECT DISTINCT(d.invoice_id), d.creditnote_id, d.total_amount, d.net_amount, d.financial_type_id, 
d.receive_date, d.cancel_date, d.contact_id, g.name as description, i.accounting_code, d.id as contribution_id

FROM civicrm_entity_batch a
JOIN civicrm_financial_trxn b ON a.entity_id = b.id
JOIN civicrm_entity_financial_trxn c ON b.id = c.financial_trxn_id AND c.entity_table = 'civicrm_contribution'
LEFT JOIN civicrm_contribution d ON c.entity_id = d.id AND c.entity_table = 'civicrm_contribution'
LEFT JOIN civicrm_membership_payment e ON d.id = e.contribution_id
LEFT JOIN civicrm_membership f ON e.membership_id = f.id
LEFT JOIN civicrm_membership_type g ON f.membership_type_id = g.id
LEFT JOIN civicrm_entity_financial_account h ON h.entity_id = d.financial_type_id AND h.entity_table = 'civicrm_financial_type' 
  AND account_relationship = 1
LEFT JOIN civicrm_financial_account i ON h.financial_account_id = i.id

WHERE a.batch_id = %1 AND a.entity_table = 'civicrm_financial_trxn' AND d.invoice_id IS NOT NULL";
    try {
      $dao = CRM_Core_DAO::executeQuery($query, array(
        1 => array($batchId, 'Integer'),
      ));
      return $dao;
    }
    catch (Exception $ex) {
      CRM_Core_Error::debug_log_message('Could not fetch alternative batch data (extension be.domusmedica.xchangewinbooks)');
      return FALSE;
    }
  }

  /**
   * Method to retrieve batchId from results of batchItems hook
   *
   * @param $data
   * @return bool
   */
  private static function retrieveBatchId($data) {
    foreach ($data as $key => $values) {
      if ($values['batch_id']) {
        return $values['batch_id'];
      }
    }
    return FALSE;
  }
  /**
   * Method to process the CiviCRM batchItems hook to generate new items
   *
   * @param $results
   * @param $items
   */
  public static function batchItems(&$results, &$items) {
    // retrieve alternative data
    $batchId = self::retrieveBatchId($results);
    if ($batchId) {
      $alternative = self::alternativeBatchItems($batchId);
      if ($alternative) {
        $items = array();
        while ($alternative->fetch()) {
          // only if exportable
          if (self::isItemExportable($alternative)) {
            // decide if this is a verkoopfactuur or a creditnota
            if (!empty($alternative->creditnote_id)) {
              self::generateGrootboekLines('credit', $items, $alternative);
              self::generateAnalytischLine('credit', $items, $alternative);
            } else {
              self::generateGrootboekLines('factuur', $items, $alternative);
              self::generateAnalytischLine('factuur', $items, $alternative);
            }
          }
        }
      }
    }
  }

  /**
   * Method to determine if item can be exported. Can only happen if not exported
   *
   * @param $data
   * @return bool
   */
  public static function isItemExportable($data) {
    if (!empty($data->contribution_id)) {
      if (!empty($data->creditnote_id)) {
        $query = 'SELECT '.CRM_Xchangewinbooks_Settings::singleton()->getCreditExportedCustomField('column_name')
          .' FROM '.CRM_Xchangewinbooks_Settings::singleton()->getContributionDataCustomGroup('table_name')
          .' WHERE entity_id = %1';
      } else {
        $query = 'SELECT '.CRM_Xchangewinbooks_Settings::singleton()->getInvoiceExportedCustomField('column_name')
          .' FROM '.CRM_Xchangewinbooks_Settings::singleton()->getContributionDataCustomGroup('table_name')
          .' WHERE entity_id = %1';
      }
      $hasBeenExported = CRM_Core_DAO::singleValueQuery($query, array(
        1 => array($data->contribution_id, 'Integer'),
      ));
      if ($hasBeenExported == TRUE) {
        return FALSE;
      }
    }
    return TRUE;
  }

  /**
   * Method to generate the grootboek lines
   *
   * @param $type
   * @param $items
   * @param $data
   */
  private static function generateGrootboeklines($type, &$items, $data) {
    $settings = new CRM_Xchangewinbooks_Settings();
    $data->formatted_amount = number_format($data->total_amount, $settings->getDecimalPlaces() , ',', '.');
    if ($type == 'credit') {
      $pattern = $settings->getCreditGrootboek();
      $data->due_date = self::calculateDueDate($data->cancel_date);
    } else {
      $pattern = $settings->getFactuurGrootboek();
      $data->due_date = self::calculateDueDate($data->receive_date);
    }
    $eerste = array();
    $tweede = array();
    $derde = array();
    foreach ($pattern as $key => $values) {
      $eerste[$key] = self::writeValue($values['eerste'], $data);
      $tweede[$key] = self::writeValue($values['tweede'],  $data);
      $derde[$key] = self::writeValue($values['derde'],  $data);
    }
    $items[] = $eerste;
    $items[] = $tweede;
    $items[] = $derde;
  }

  /**
   * Method to determine what the line value is
   *
   * @param $value
   * @param object $data
   * @return string
   */
  private static function writeValue($value, $data) {
    if (empty($value)) {
      $result = '';
    } elseif (substr($value, 0, 7) == 'column:') {
      $result = self::retrieveColumnValue($value, $data);
    } else {
      $result = $value;
    }
    return $result;
  }

  /**
   * Method to retrieve the value from the data
   *
   * @param $value
   * @param $data
   * @return false|string
   */
  private static function retrieveColumnValue($value, $data) {
    $result = '';
    $parts = explode('column:', $value);
    if (isset($parts[1])) {
      switch ($parts[1]) {
        case 'invoice_date':
          $result = date('Ymd', strtotime($data->receive_date));
          break;
        case 'credit_date':
          $result = date('Ymd', strtotime($data->cancel_date));
          break;
        case 'total_amount':
          $result = $data->formatted_amount;
          break;
        case 'total_amount:negative':
          $result = '-'.$data->formatted_amount;
          break;
        case 'analytic_code_factuur':
          $settings = new CRM_Xchangewinbooks_Settings();
          $result = $settings->getAnalyticCodeFactuur();
          break;
        case 'analytic_code_credit':
          $settings = new CRM_Xchangewinbooks_Settings();
          $result = $settings->getAnalyticCodeCredit();
          break;
        default:
          $propertyName = $parts[1];
          if (isset($data->$propertyName)) {
            $result = $data->$propertyName;
          }
          break;
      }
    }
    return $result;
  }

  /**
   * Method to write the analytisch line
   *
   * @param $type
   * @param $items
   * @param $data
   */
  private static function generateAnalytischLine($type, &$items, $data) {
    $settings = new CRM_Xchangewinbooks_Settings();
    $data->formatted_amount = number_format($data->total_amount, $settings->getDecimalPlaces() , ',', '.');
    if ($type == 'credit') {
      $pattern = $settings->getCreditAnalytisch();
    } else {
      $pattern = $settings->getFactuurAnalytisch();
    }
    $line = array();
    foreach ($pattern as $key => $values) {
      $line[$key] = self::writeValue($values, $data);
    }
    $items[] = $line;
  }

  /**
   * Method to calculate the due date for the invoice
   *
   * @param $invoiceDate
   * @return string
   * @throws
   */
  private static function calculateDueDate($invoiceDate) {
    $dueDate = new DateTime($invoiceDate);
    try {
      $settings = civicrm_api3('Setting', 'getvalue', array(
        'name' => 'contribution_invoice_settings',
      ));
      switch ($settings['due_date_period']) {
        case 'days':
          $period = new DateInterval('P'.$settings['due_date'].'D');
          break;
        case 'months':
          $period = new DateInterval('P'.$settings['due_date'].'M');
          break;
        case 'years':
          $period = new DateInterval('P'.$settings['due_date'].'Y');
          break;
      }
      if ($period) {
        $dueDate->add($period);
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return $dueDate->format('Ymd');
  }

}