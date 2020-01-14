{include file="header.tpl" title="Datensuche"}
{literal}
<script>
$(
function()
{
    $("form").submit(
        function(event)
        {
            alert($("form").serialize());
            $.get("get_data.php",$("form").serialize(),function(data){$("#result").text(data);});
            event.preventDefault();
        }
    );

}
);
</script>
{/literal}    
<h1>Eingabe</h1>
<form method="GET">

<table>
    <tr>
        <th>ID</th>
        <td>
            <input type="text" name="id" id="id">
        </td>
    </tr>
    <tr>
        <th>Class</th>
        <td>
            <select name="class" id="class">
                <option value="match">Match</option>
                <option value="spieler">Spieler</option>
                <option value="spieler2team">Spieler2team</option>
                <option value="spielereignis">Spielereignis</option>
                <option value="stadion">Stadion</option>
                <option value="team">Team</option>
                <option value="team2stadion">Team2stadion</option>
            </select>    
        </td>
    </tr>    
</table>
</form>      
<div id="result"></div>
{include file="footer.tpl"}
