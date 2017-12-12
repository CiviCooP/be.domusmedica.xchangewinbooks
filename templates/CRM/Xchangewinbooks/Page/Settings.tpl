<div class="crm-content-block crm-block">
  <div id="help">
    {$help_text}
  </div>
  <div id="dm_xchangewinbooks_page_wrapper" class="dataTables_wrapper">
    <table id="dm_xchangewinbooks_-table" class="display">
      <thead>
        <tr>
          <th class="sorting-disabled" rowspan="1" colspan="1">{ts}Type{/ts}</th>
          <th class="sorting_disabled" rowspan="1" colspan="1"></th>
          <th class="sorting-disabled" rowspan="1" colspan="1">{ts}Regel{/ts}</th>
          <th class="sorting-disabled" rowspan="1" colspan="1">{ts}Settings{/ts}</th>
        </tr>
      </thead>
      <tbody>
        <tr id="row_generic" class="odd-row">
          <td>{ts}Algemeen{/ts}</td>
          <td><span>{$generic.edit}</span></td>
          <td>{ts}n.v.t.{/ts}</td>
          <td>{$generic.value}</td>
        </tr>

        <tr id="row_factuur_grootboek_eerste" class="even-row">
          <td>{ts}Factuur Grootboek niveau{/ts}</td>
          <td><span>{$factuur_grootboek.edit}</span></td>
          <td>{ts}eerste{/ts}</td>
          <td>{$factuur_grootboek.eerste}</td>
        </tr>
        <tr id="row_factuur_grootboek_tweede" class="odd-row">
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>{ts}tweede{/ts}</td>
          <td>{$factuur_grootboek.tweede}</td>
        </tr>
        <tr id="row_factuur_grootboek_derde" class="even-row">
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>{ts}derde{/ts}</td>
          <td>{$factuur_grootboek.derde}</td>
        </tr>

        <tr id="row_credit_grootboek_eerste" class="odd-row">
          <td>{ts}Credit Grootboek niveau{/ts}</td>
          <td><span>{$credit_grootboek.edit}</span></td>
          <td>{ts}eerste{/ts}</td>
          <td>{$credit_grootboek.eerste}</td>
        </tr>
        <tr id="row_credit_grootboek_tweede" class="even-row">
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>{ts}tweede{/ts}</td>
          <td>{$credit_grootboek.tweede}</td>
        </tr>
        <tr id="row_credit_grootboek_derde" class="odd-row">
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>{ts}derde{/ts}</td>
          <td>{$credit_grootboek.derde}</td>
        </tr>

        <tr id="row_factuur_analytisch" class="even-row">
          <td>{ts}Factuur Analytisch niveau{/ts}</td>
          <td><span>{$factuur_analytisch.edit}</span></td>
          <td>{ts}n.v.t.{/ts}</td>
          <td>{$factuur_analytisch.value}</td>
        </tr>

        <tr id="row_credit_analytisch" class="odd-row">
          <td>{ts}Credit Analytisch niveau{/ts}</td>
          <td><span>{$credit_analytisch.edit}</span></td>
          <td>{ts}n.v.t.{/ts}</td>
          <td>{$credit_analytisch.value}</td>
        </tr>
      </tbody>
    </table>
    <div class="action-link">{$done_url}</div>
  </div>
</div>
