{include file="header.tpl" title="Team Eingabe"}
<h1>Team Eingabe</h1>
<form action="team_erstellen.php" method="POST">

    <table>
        {include file="tr_input_text.tpl" beschriftung="ID(wenn leer, nächste Autoincrement-ID)" name="id"}
        {include file="tr_input_text.tpl" beschriftung="Name" name="name"}
        {include file="tr_input_submit.tpl"}
    </table>
</form>        
{include file="footer.tpl"}

