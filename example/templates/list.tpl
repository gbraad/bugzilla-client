<html>
  <head>
    <title>{$title}</title>
    <style type="text/css" media="all">
@import url("styles/all.css");
    </style>
  </head>
  <body>

    <table>
    {foreach from=$bugs item=bug}
    {strip}
      <tr bgcolor="{cycle values="#e0e0e0,#f0f0f0"}">
        <td><a class='bz' href="{$bug.url}" target="_" /><a class='xc' href="./get.php?bugId={$bug.id}" target="_" /></td>
        <td>{$bug.id}</td>
        <td>{$bug.summary|truncate:120}</td>
        <td>{$bug.assigned_to}</td>
      </tr>
    {/strip}
    {/foreach}
    </table>

  </body>
</html>