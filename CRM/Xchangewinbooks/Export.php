<?php
/**
 * Class for Domus Medica Export to Winbooks
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 8 Dec 2017
 * @license AGPL-3.0
 */

class CRM_Xchangewinbooks_Export
{
  /**
   * Method to process the CiviCRM batchQuery hook to change the query
   *
   * @param $query
   */
  public static function batchQuery(&$query) {
    $query = "SELECT DISTINCT(d.invoice_id), d.creditnote_id, d.total_amount, d.net_amount, d.financial_type_id, d.receive_date, 
d.contact_id, g.name as description, i.accounting_code

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
  }

  /**
   * Method to process the CiviCRM batchItems hook to generate new items
   *
   * @param $results
   * @param $items
   */
  public static function batchItems(&$results, &$items) {
    $items = array();
    foreach ($results as $result) {
      $result['due_date'] = self::calculateDueDate($result['receive_date']);
      // decide if this is a verkoopfactuur or a creditnota
      if (!empty($result['creditnote_id'])) {
        self::generateGrootboekLines('credit', $items, $result);
        self::generateAnalytischLine('credit', $items, $result);
      } else {
        self::generateGrootboekLines('factuur', $items, $result);
        self::generateAnalytischLine('factuur', $items, $result);
      }
    }
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
    $data['formatted_amount'] = number_format($data['total_amount'], $settings->getDecimalPlaces() , ',', '.');
    if ($type == 'credit') {
      $pattern = $settings->getCreditGrootboek();
    } else {
      $pattern = $settings->getFactuurGrootboek();
    }
    $quotes = $settings->getQuotes();
    $eerste = array();
    $tweede = array();
    $derde = array();
    foreach ($pattern as $key => $values) {
      $eerste[$key] = self::writeGrootboekValue($values['eerste'], $data, $quotes);
      $tweede[$key] = self::writeGrootboekValue($values['tweede'],  $data, $quotes);
      $derde[$key] = self::writeGrootboekValue($values['derde'],  $data, $quotes);
    }
    $items[] = $eerste;
    $items[] = $tweede;
    $items[] = $derde;
  }

  /**
   * Method to determine what the grootboek line value is
   *
   * @param $value
   * @param array $data
   * @param bool $quotes
   * @return string
   */
  private static function writeGrootboekValue($value, $data, $quotes) {
    if (empty($value)) {
      $result = '';
    } elseif (substr($value, 0, 7) == 'column:') {
      $result = self::retrieveColumnValue($value, $data);
    } else {
      $result = $value;
    }
    if ($quotes) {
      return '"'.$result.'"';
    } else {
      return $result;
    }
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
          $result = date('Ymd', strtotime($data['receive_date']));
          break;
        case 'total_amount':
          $result = $data['formatted_amount'];
          break;
        case 'total_amount:negative':
          $result = '-'.$data['formatted_amount'];
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
          if (isset($data[$parts[1]])) {
            $result = $data[$parts[1]];
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
    $data['formatted_amount'] = number_format($data['total_amount'], $settings->getDecimalPlaces() , ',', '.');
    if ($type == 'credit') {
      $pattern = $settings->getCreditAnalytisch();
    } else {
      $pattern = $settings->getFactuurAnalytisch();
    }
    $quotes = $settings->getQuotes();
    $line = array();
    foreach ($pattern as $key => $values) {
      $line[$key] = self::writeGrootboekValue($values, $data, $quotes);
    }
    $items[] = $line;
  }

  /**
   * Method to calculate the due date for the invoice
   *
   * @param $invoiceDate
   * @return string
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