{include file="header.tpl" title="Neues Spielereignis Schritt 1"}
<form action="spielereignis_neu_schritt2.php" method="POST">
<table>
<tr>
    <td>Match:</td>
    <td><select name="match">       

        {foreach $match_daten as $match}
                <option value="{$match["id"]}">{$match["description"]}</option>
        {/foreach}
        </select>
    </td>
</tr>
{include file="tr_input_submit.tpl"}
</table>
</form>
{include file="footer.tpl"}