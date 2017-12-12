<div class="crm-content-block crm-block">
  <div id="help">
    {$help_text}
  </div>
  <div id="dm_xchangewinbooks_page_wrapper" class="dataTables_wrapper">
    <table id="dm_xchangewinbooks-table" class="display">
      <thead>
      <tr>
        <th class="sorting-disabled" rowspan="1" colspan="1">{ts}Setting{/ts}</th>
        <th class="sorting-disabled" rowspan="1" colspan="1">{ts}Value{/ts}</th>
        <th class="sorting_disabled" rowspan="1" colspan="1"></th>
      </tr>
      </thead>
      <tbody>
      {assign var="row_class" value="odd-row"}
      {foreach from=$settings key=setting_name item=setting_value}
        <tr id="row_{$setting_name}" class={$row_class}>
          <td>{$setting_name}</td>
          <td>{$setting_value}</td>
          <td>
              <span>
                {foreach from=$rule.actions item=action_link}
                  {$action_link}
                {/foreach}
              </span>
          </td>
        </tr>
        {if $row_class eq "odd-row"}
          {assign var="row_class" value="even-row"}
        {else}
          {assign var="row_class" value="odd-row"}
        {/if}
      {/foreach}
      </tbody>
    </table>
  </div>
</div>
