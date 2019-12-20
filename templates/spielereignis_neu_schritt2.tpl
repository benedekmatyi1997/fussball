{include file="header.tpl" title="Neues Spielereignis Schritt 2"}
<form action="spielereignis_erstellen.php" method="POST">
<table>
<tr>
    <td>Spieler:</td>
    <td><select name="spieler">       

        {foreach $spieler_daten as $s_daten}
                {if $s_daten["disabled"] eq 1}<option disabled>{$s_daten["team_name"]}</option>{/if}
                <option value="{$s_daten["id"]}">{$s_daten["name"]}</option>
        {/foreach}
        </select>
    </td>
</tr>
<tr>
    <td>Minute:</td>
    <td><select name="minute">       
        <option disabled>1. Halbzeit</option>
        {for $minute=1 to 45}
                <option value="{$minute}">{$minute}. Minute</option>
        {/for}
        <option disabled>2. Halbzeit</option>
        {for $minute=46 to 90}
                <option value="{$minute}">{$minute}. Minute</option>
        {/for}
        <option disabled>1. Halbzeit Verl&auml;ngerung</option>
        {for $minute=91 to 105}
                <option value="{$minute}">{$minute}. Minute</option>
        {/for}
        <option disabled>2. Halbzeit Verl&auml;ngerung</option>
        {for $minute=106 to 120}
                <option value="{$minute}">{$minute}. Minute</option>
        {/for}
        </select>
    </td>
</tr>
<tr>
    <td>Nachspielzeit:</td>
    <td><select name="nachspielzeit">
        {for $nachspielzeit=0 to 10}
            <option value="{$nachspielzeit}">{$nachspielzeit}</option>
        {/for}
        </select>
    </td>
</tr>
<tr>
    <td>Typ:</td>
        <td><select name="typ">
                <option value="spielt">Spielt</option>
                <option value="einwechslung">Einwechslung</option>
                <option value="auswechslung">Auswechslung</option>
                <option value="tor">Tor</option>
                <option value="gelbe_karte">Gelbe Karte</option>
                <option value="rote_karte">Rote Karte</option>
        </select>
    </td>
</tr>
{include file="tr_input_submit.tpl"}
</table>
<input type="hidden" name="match" value="{$match}" />
</form>
{include file="footer.tpl"}
