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
d.contact_id, g.name as membership_type, i.accounting_code

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
      $result['formatted_amount'] = number_format($result['total_amount'], 3, ',', '.');
      $items[] = self::generateFirstLine($result);
      $items[] = self::generateSecondLine($result);
      $items[] = self::generateThirdLine($result);
    }
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
    return $dueDate->format('Y-m-d H:i:s');
  }


  /**
   * Method to generate first line
   *
   * @param $result
   * @return array
   */
  private static function generateFirstLine($result) {
    return array(
      '"1"',
      '"VECIVI"',
      '"2"',
      '"'.$result['invoice_id'].'"',
      '"001"',
      '""',
      '"400000"',
      '"'.$result['contact_id'].'"',
      '""',
      '""',
      '"'.$result['receive_date'].'"',
      '"'.$result['receive_date'].'"',
      '"'.$result['due_date'].'"',
      '"'.$result['membership_type'].'"',
      '""',
      '""',
      '"'.$result['formatted_amount'].'"',
      '"'.$result['formatted_amount'].'"',
      '""',
      '""',
      '""',
      '""',
      '"0"',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
    );
  }

  /**
   * Method to generate second line
   *
   * @param $result
   * @return array
   */
  private static function generateSecondLine($result) {
    return array(
      '"3"',
      '"VECIVI"',
      '"2"',
      '"'.$result['invoice_id'].'"',
      '"002"',
      '""',
      '"'.$result['accounting_code'].'"',
      '"'.$result['contact_id'].'"',
      '""',
      '""',
      '"'.$result['receive_date'].'"',
      '"'.$result['receive_date'].'"',
      '"'.$result['due_date'].'"',
      '"'.$result['membership_type'].'"',
      '""',
      '""',
      '"-'.$result['formatted_amount'].'"',
      '"0,000"',
      '""',
      '""',
      '""',
      '""',
      '"0"',
      '"244600"',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
    );
  }

  /**
   * Method to generate third line
   *
   * @param $result
   * @return array
   */
  private static function generateThirdLine($result) {
    return array(
      '"4"',
      '"VECIVI"',
      '"2"',
      '"'.$result['invoice_id'].'"',
      '"VAT"',
      '"FIXED"',
      '""',
      '"'.$result['contact_id'].'"',
      '""',
      '""',
      '"'.$result['receive_date'].'"',
      '"'.$result['receive_date'].'"',
      '"'.$result['due_date'].'"',
      '"'.$result['membership_type'].'"',
      '""',
      '""',
      '"0,000"',
      '"'.$result['formatted_amount'].'"',
      '"244600"',
      '""',
      '""',
      '""',
      '"0"',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
      '""',
    );
  }

}