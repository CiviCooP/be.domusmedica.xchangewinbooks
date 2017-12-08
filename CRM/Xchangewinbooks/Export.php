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
   * Method to process the CiviCRM batchQuery hook
   *
   * @param $query
   */
  public static function batchQuery(&$query) {
    $query = "SELECT b.total_amount, b.net_amount, d.invoice_id, d.financial_type_id, d.receive_date, d.contact_id, 
g.name as membership_type, i.accounting_code

FROM civicrm_entity_batch a
JOIN civicrm_financial_trxn b ON a.entity_id = b.id
JOIN civicrm_entity_financial_trxn c ON b.id = c.financial_trxn_id AND c.entity_table = 'civicrm_contribution'
LEFT JOIN civicrm_contribution d ON c.entity_id = d.id AND c.entity_table = 'civicrm_contribution'
LEFT JOIN civicrm_membership_payment e ON d.id = e.contribution_id
LEFT JOIN civicrm_membership f ON e.membership_id = f.id
LEFT JOIN civicrm_membership_type g ON f.membership_type_id = g.id
LEFT JOIN civicrm_entity_financial_account h ON h.entity_id = d.financial_type_id AND h.entity_table = 'civicrm_financial_type' AND account_relationship = 1
LEFT JOIN civicrm_financial_account i ON h.financial_account_id = i.id

WHERE a.batch_id = %1 AND a.entity_table = 'civicrm_financial_trxn'";
  }

  /**
   * Method to process the CiviCRM batchItems hook
   *
   * @param $results
   * @param $items
   */
  public static function batchItems(&$results, &$items) {

  }

}