{extends file="parent:frontend/index/index.tpl"}
{block name="frontend_index_header_javascript_jquery" append}
    <script type="text/javascript">
        var melissa_id = "{$melissaDataToken}";

        $(document).ready(function () {
            $('#registration form').on('submit', function () {
                var returnValue = false;
                var value = $('#street').val();
                if (value == '') {
                    return;
                }
                if (value.match(/([a-zA-Z\-\.]+\s[0-9]+).*/)) {
                    returnValue = true;
                }
                else {
                    $('#street').addClass('has--error');
                    returnValue = false;
                }
                value = $('#street2').val();
                if (value == '') {
                    return returnValue;
                }
                if (value.match(/([a-zA-Z]+\s[0-9]+).*/)) {
                    returnValue = true;
                }
                else {
                    $('#street').addClass('has--error');
                    returnValue = false;
                }
                return returnValue;

            });
        });
    </script>
{/block}