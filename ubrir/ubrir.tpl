<div class="control-group">
    <label class="control-label" for="twpg_id">{__("addons.ubrir.twpg_id")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][login]" id="twpg_id" value="{$processor_params.login}" size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="ubrir_pass">{__("addons.ubrir.twpg_pass")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][pass]" id="ubrir_pass" value="{$processor_params.pass}" size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="two">{__("addons.ubrir.two")}:</label>
    <div class="controls">
        <input type="checkbox" name="payment_data[processor_params][two]" id="logging" value="Y" {if $processor_params.logging == 'Y'} checked="checked"{/if}/>
    </div>
</div>

