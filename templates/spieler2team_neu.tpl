{include file="header.tpl" title="Neu Spieler2team Objekt"}
<h1>Eingabe</h1>
<form action="spieler2team_erstellen.php" method="POST">

<table>
    <tr>
        <td>Spieler</td>
        <td>
            <select name="spieler">       
            {foreach $spieler as $spler}
                <option value="{$spler["id"]}">{$spler["name"]}</option>
            {/foreach}
            </select>
        </td>
    </tr>
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

