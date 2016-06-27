//{namespace name=backend/order/customizing/main}

//{block name="backend/order/view/customizing/grid"}
Ext.define('Shopware.apps.Customizing.view.customizing.Grid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.customizing-grid-panel',
    layout: 'fit',
    selType: 'rowmodel',
    /**
     * initComponent
     */
    initComponent: function () {
        var me = this;
        me.columns = me.getColumns();
        me.features = [me.createGroupingFeature()];
        me.callParent(arguments);
    },
    /**
     * Create the grouping feature for the grid
     *
     * @return Ext.grid.feature.GroupingSummary
     */
    createGroupingFeature: function () {
        var me = this;

        return Ext.create('Ext.grid.feature.GroupingSummary', {
            // {literal}
            groupHeaderTpl: '{[values.children[0].data.articleNumber]} &ndash;  {[values.children[0].data.articleName]} ',
            // {/literal}
            startCollapsed: false,
            collapsible: false
        });
    },
    /**
     * Overrides the getColumns function of the order position grid
     */
    getColumns: function () {
        var me = this;

        return [
            {
                header: '{s namespace="backend/customizing/view/main" name="list/columns/name"}Name{/s}',
                dataIndex: 'name',
                flex: 2
            },
            {
                header: '{s namespace="backend/customizing/view/main" name="value/columns/value"}Wert{/s}',
                dataIndex: 'selectedValue',
                renderer: me.customValueColumnRenderer,
                flex: 4
            },
            {
                header: '{s namespace="backend/customizing/view/main" name="option/fields/number"}Bestellnummer{/s}',
                dataIndex: 'number',
                flex: 2
            }
        ];
    },
    /**
     *  Special column renderer to display the option value depending on the typeId
     */
    customValueColumnRenderer: function (value, metaData, record, rowIdx, colIdx, store, view) {
        var me = this, gridCellValue;
        if (value.length === 0) {
            // show this text if there is no value data
            return '&lt;' + '{s namespace="backend/customizing/view/main" name="value/no_selection"}{/s}' + '&gt;';
        }

        switch (record.get('typeId')) {
            case 11: // typeTextField
            case 12: // typeTextArea
            case 13: // typeHTML
                // clean and trim - possibly contains html
                gridCellValue = value.replace(/(<([^>]+)>)/ig, "") // strip_tags
                    .substring(0, 40); // trim right
                return Ext.String.format('<div style="height: 2em">[0]</div>', gridCellValue);
            case 21: // typeDataUpload
            case 22: // typeImageUpload
                return me.renderGridCellImageDataValueHtml(value);
            case 41: // typeDate
                return Ext.util.Format.date(value);
            case 44: // typeDateTime
                return Ext.util.Format.date(value) + ' ' + Ext.util.Format.date(value, timeFormat);
            case 45: // typeTime
                return Ext.util.Format.date(value, timeFormat);
            case 51: // typeColorSelector
            case 52: // typeColorPicker
                return me.renderGridCellColorValue(record);
            case 35:  // typeImagePicker
                // Only the text description of the image is shown here. Actual image is shown in the popup detail window
                if (record.get('selectedValueDescription').length > 0) {
                    return record.get('selectedValueDescription');
                }
                return "(no description text)";
            default:
                return Ext.String.format('<div>[0]</div>', value);
        }
    },
    /**
     * Returns description text of the given typeId
     *
     */
    customTypeColumnRenderer: function (value, metaData, record, rowIdx, colIdx, store, view) {
        var typeId = record.get('typeId');
        var types = {
            '11': '{s namespace="backend/customizing/types" name="typeTextField"}Text Field{/s}',
            '12': '{s namespace="backend/customizing/types" name="typeTextArea"}Text Area{/s}',
            '13': '{s namespace="backend/customizing/types" name="typeHTML"}Html{/s}',
            '21': '{s namespace="backend/customizing/types" name="typeDataUpload"}Data Upload{/s}',
            '22': '{s namespace="backend/customizing/types" name="typeImageUpload"}Image Upload{/s}',
            '31': '{s namespace="backend/customizing/types" name="typeSelect"}Select box{/s}',
            '32': '{s namespace="backend/customizing/types" name="typeRadio"}Radio button{/s}',
            '33': '{s namespace="backend/customizing/types" name="typeCheckbox"}Checkbox{/s}',
            '34': '{s namespace="backend/customizing/types" name="typeMultipleSelect"}Multiple Select Box{/s}',
            '35': '{s namespace="backend/customizing/types" name="typeImagePicker"}Image Picker{/s}',
            '41': '{s namespace="backend/customizing/types" name="typeDate"}Date{/s}',
            '44': '{s namespace="backend/customizing/types" name="typeDateTime"}Date and Time{/s}',
            '45': '{s namespace="backend/customizing/types" name="typeTime"}Time{/s}',
            '51': '{s namespace="backend/customizing/types" name="typeColorSelector"}Color Selector{/s}',
            '52': '{s namespace="backend/customizing/types" name="typeColorPicker"}Color Picker{/s}'
        };
        return types[typeId];
    },
    /*
     * Helper methods to generate markup for special custom options in grid cells
     */
    renderGridCellImageDataValueHtml: function (value) {
        var urlArray = value.split(","), html = "";

        Ext.Array.each(urlArray, function (url, index) {
            html += Ext.String.format(
                '<div><a href="[0]" target="_blank">[1]</a></div>',
                encodeURI("{link file=''}" + urlArray[index]),
                urlArray[index].split("/").pop()
            );
        });
        return html;
    },
    renderGridCellColorValue: function (record) {
        return Ext.String.format(
            '<div style="width: 72px; background-color: [0];border: 1px solid #ccc">' +
            '<div style="color: [1]; text-transform: uppercase; margin: 0 auto; font-weight: bold; text-align: center;">[2]</div>' +
            '</div>',
            record.get('selectedValue'),
            record.get('selectedValueDescription'),
            record.get('selectedValue')
        );

    }
});

//{/block}