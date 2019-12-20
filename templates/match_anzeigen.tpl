{include file="header.tpl" title="Neu Spieler2team Objekt"}
<h1>Alle Matchdaten</h1><br />
{$match["description"]}

<table>
    <tr>
        <th>Minute</th>
        <th>Spieler</th>
        <th>Action</th>
    </tr>
    {foreach $spielereignisse as $spielereignis}
    <tr>
        <td>{$spielereignis["minute"]}{if $spielereignis["nachspielzeit"] neq 0}{literal}+{/literal}{$spielereignis["nachspielzeit"]}{/if}. Minute</td>
        <td>{$spielereignis["spieler"]}</td>
        <td>{$spielereignis["typ"]}</td>
    </tr>
    {/foreach}
</table>
{include file="footer.tpl"}