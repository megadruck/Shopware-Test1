{block name="frontend_index_header_css_screen" append}
    <link rel="stylesheet" href="{link file='frontend/_resources/styles/swag_ticket.css'}" />
{/block}

{block name="frontend_index_header_javascript_jquery" append}
    <script type="text/javascript" src="{link file='frontend/_resources/javascript/jquery.swag_ticket.js'}"></script>
    <script type="text/javascript" src="{link file='frontend/_resources/javascript/fileupload/jquery.ui.widget.js'}"></script>
    <script type="text/javascript" src="{link file='frontend/_resources/javascript/fileupload/jquery.fileupload.js'}"></script>
    <script type="text/javascript">
        var uploadUrl = '{url controller=ticket action=upload forceSecure}';
    </script>
{/block}