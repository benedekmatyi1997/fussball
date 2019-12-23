{include file="header.tpl" title="Teamauswahl"}
<h1>Alle Teams</h1>

<table>
    <tr>
        <th>Teams</th>
    </tr>
    
    {foreach $alle_teams as $team}    
    <tr>
        <td><a href="team_anzeigen.php?id={$team["id"]}">{$team["name"]}</a></td>
    </tr>
    {/foreach}
</table>

{include file="footer.tpl"}