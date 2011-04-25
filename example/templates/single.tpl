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
      <tr bgcolor="#e0e0e0">
        <td><a class='bz' href="{$bug.url}" /><a class='xc' href="./get.php?bugId={$bug.id}" /></td>
        <td>{$bug.id}</td>
        <td>{$bug.summary|truncate:120}</td>
        <td>{$bug.assigned_to}</td>
      </tr>
      <tr bgcolor="#f0f0f0">
        <td colspan="4"><pre>{$bug.description}</pre></td>
      </tr>
    {/strip}
    {/foreach}
    </table>

  </body>
</html>