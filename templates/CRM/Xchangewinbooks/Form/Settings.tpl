{* HEADER *}

<div class="crm-block crm-form-block">
  <div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="top"}
  </div>

  <h2>{ts}Verkoopfacturen - Grootboekniveau{/ts}</h2>
  {foreach from=$form.vf_gn key=line_number item=element}
    <h3>{$line_number} regel</h3>
    {foreach from=$element key=field_name item=field_value}
      {assign var{$var|substr:0:30}
    {/foreach}
  {/foreach}


  <h3>{ts}Eerste regel{/ts}</h3>
  <div class="crm-section">
    <div class="label">{$form.vf_gn_eerste_regelnummer.label}</div>
    <div class="content">{$form.vf_gn_eerste_regelnummer.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.vf_gn_eerste_dagboek.label}</div>
    <div class="content">{$form.vf_gn_eerste_dagboek.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.vf_gn_eerste_dagboek_code.label}</div>
    <div class="content">{$form.vf_gn_eerste_dagboek_code.html}</div>
    <div class="clear"></div>
  </div>


  <h3>{ts}Tweede regel{/ts}</h3>
  <div class="crm-section">
    <div class="label">{$form.vf_gn_tweede_regelnummer.label}</div>
    <div class="content">{$form.vf_gn_tweede_regelnummer.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.vf_gn_tweede_dagboek.label}</div>
    <div class="content">{$form.vf_gn_tweede_dagboek.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.vf_gn_tweede_dagboek_code.label}</div>
    <div class="content">{$form.vf_gn_tweede_dagboek_code.html}</div>
    <div class="clear"></div>
  </div>

  <h3>{ts}Derde regel{/ts}</h3>
  <table>
    <thead>
    <tr>
      <th>Regelnummer</th>
      <th>Dagboek</th>
      <th>Dagboek Code</th>
    </tr>
    </thead>
    <tbody>
      <tr>
        <td class="content">{$form.vf_gn_derde_regelnummer.html}</td>
        <td class="content">{$form.vf_gn_derde_dagboek.html}</td>
        <td class="content">{$form.vf_gn_derde_dagboek_code.html}</td>
      </tr>
    </tbody>
  </table>

  <h2>{ts}Verkoopfacturen - Analytisch niveau{/ts}</h2>
  <div class="crm-section">
    <div class="label">{$form.regelnummer.label}</div>
    <div class="content">{$form.regelnummer.html}</div>
    <div class="clear"></div>
  </div>

  <h2>{ts}Creditnota's - Grootboekniveau{/ts}</h2>
  <h3>{ts}Eerste regel{/ts}</h3>
  <div class="crm-section">
    <div class="label">{$form.regelnummer.label}</div>
    <div class="content">{$form.regelnummer.html}</div>
    <div class="clear"></div>
  </div>
  <h3>{ts}Tweede regel{/ts}</h3>
  <div class="crm-section">
    <div class="label">{$form.regelnummer.label}</div>
    <div class="content">{$form.regelnummer.html}</div>
    <div class="clear"></div>
  </div>
  <h3>{ts}Derde regel{/ts}</h3>
  <div class="crm-section">
    <div class="label">{$form.regelnummer.label}</div>
    <div class="content">{$form.regelnummer.html}</div>
    <div class="clear"></div>
  </div>
  <h2>{ts}Creditnota's - Analytisch niveau{/ts}</h2>
  <div class="crm-section">
    <div class="label">{$form.regelnummer.label}</div>
    <div class="content">{$form.regelnummer.html}</div>
    <div class="clear"></div>
  </div>


  {* FOOTER *}
  <div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
