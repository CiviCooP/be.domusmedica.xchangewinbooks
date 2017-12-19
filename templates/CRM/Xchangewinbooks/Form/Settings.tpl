{* HEADER *}

<div class="crm-block crm-form-block">
  {if $settings_type != "gen"}
    <div id="help">
      {ts}Je kunt hier alle instellingen wijzigen in vaste tekst.<br />
        LET OP! In sommige instellingen staat <strong>column:</strong> met daarachter de naam van een kolom. Dit betekent dat er een veld uit CiviCRM gebruikt wordt om de instelling te vullen. Als je wilt kun je dit wijzigen in vaste tekst, maar je moet wel zeker weten dat het klopt!
      {/ts}
    </div>
  {/if}
  <div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="top"}
  </div>

  {if $settings_type == "fgn" or $settings_type == "cgn"}
    <h3>{ts}Eerste regel{/ts}</h3>
    {foreach from=$elementNames item=elementName}
      {if $elementName|substr:0:7 eq 'eerste_'}
        <div class="crm-section">
          <div class="label">{$form.$elementName.label}</div>
          <div class="content">{$form.$elementName.html}</div>
          <div class="clear"></div>
        </div>
      {/if}
    {/foreach}
    <h3>{ts}Tweede regel{/ts}</h3>
    {foreach from=$elementNames item=elementName}
      {if $elementName|substr:0:7 eq 'tweede_'}
        <div class="crm-section">
          <div class="label">{$form.$elementName.label}</div>
          <div class="content">{$form.$elementName.html}</div>
          <div class="clear"></div>
        </div>
      {/if}
    {/foreach}
    <h3>{ts}Derde regel{/ts}</h3>
    {foreach from=$elementNames item=elementName}
      {if $elementName|substr:0:6 eq 'derde_'}
        <div class="crm-section">
          <div class="label">{$form.$elementName.label}</div>
          <div class="content">{$form.$elementName.html}</div>
          <div class="clear"></div>
        </div>
      {/if}
    {/foreach}

  {else}
    {foreach from=$elementNames item=elementName}
      <div class="crm-section">
        <div class="label">{$form.$elementName.label}</div>
        <div class="content">{$form.$elementName.html}</div>
        <div class="clear"></div>
      </div>
    {/foreach}
  {/if}

  {* FOOTER *}
  <div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>