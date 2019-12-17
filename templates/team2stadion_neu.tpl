{include file="header.tpl" title="Neu Team2Stadion Objekt"}
<h1>Eingabe</h1>
<form action="team2stadion_erstellen.php" method="POST">

<table>
    <tr>
        <td>Team</td>
        <td>
            <select name="team">       
            {foreach $teams as $team}
                <option value="{$team["id"]}">{$team["name"]}</option>
            {/foreach}
            </select>
        </td>
    </tr>
    <tr>
        <td>Spieler</td>
        <td>
            <select name="stadion">       
            {foreach $stadion as $stdn}
                <option value="{$stdn["id"]}">{$stdn["name"]}</option>
            {/foreach}
            </select>
        </td>
    </tr>
    <tr>
        <td>Von</td>
        <td><input type="date" name="von" /></td>
    </tr>
    <tr>
        <td>Bis</td>
        <td><input type="date" name="bis" /></td>
    </tr>
    <tr>
        <td colspan="2"><input type="submit"></td>
    </tr>
</table>
</form>        
{include file="footer.tpl"}


