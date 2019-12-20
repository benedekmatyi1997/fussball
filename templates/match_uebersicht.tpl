{include file="header.tpl" title="Neu Spieler2team Objekt"}
<h1>Alle Matches</h1>

<table>
    
    <tr>
        <th>Spiele</th>
    </tr>
    
    {foreach $alle_matches as $match}    
    <tr>
        <td><a href="match_anzeigen.php?id={$match["id"]}">{$match["description"]}</a></td>
    </tr>
    {/foreach}
    
</table>
{include file="footer.tpl"}