//{namespace name=backend/order/main}
Ext.require( [
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.panel.*'
] );
Ext.define( 'Shopware.apps.SofortLogView.view.main.Window', {
    extend:          'Enlight.app.Window',
    title:           'Sofort AG Admin Log',
    alias:           'widget.sofort_log_view-main-window',
    border:          false,
    autoShow:        true,
    resizable:       true,
    layout:          {
        type: 'fit'
    },
    height:          520,
    width:           800,
    initComponent:   function ()
    {
        var me = this;
        me.store = me.listStore;
        me.items = [
            me.createMainGrid( me )
        ];
        me.callParent( arguments );
    },
    createMainGrid:  function ( me )
    {
        return Ext.create( 'Ext.grid.Panel', {
            store:       me.store,
            forceFit:    true,
            border:      false,
            height:      '100%',
            width:       '100%',
            columns:     [
                {
                    text:      '{s namespace=Sofort name=sofort_admin_view_lable_date}Date{/s}',
                    dataIndex: 'entry_date',
                    width:     50
                },
                {
                    text:      '{s namespace=Sofort name=sofort_admin_view_lable_version}Version{/s}',
                    dataIndex: 'version_module',
                    width:     50
                },
                {
                    text:      '{s namespace=Sofort name=sofort_admin_view_lable_source}Source{/s}',
                    dataIndex: 'source',
                    width:     350
                },
                {
                    text:      '{s namespace=Sofort name=sofort_admin_view_lable_entry}Entry{/s}',
                    dataIndex: 'message',
                    width:     350
                },
                {
                    xtype:  'actioncolumn',
                    header: '{s namespace=Sofort name=sofort_admin_view_lable_action}Actions{/s}',
                    width:  60,
                    items:  [
                        {
                            iconCls: 'sprite-pencil',
                            action:  'Details',
                            scope:   me,
                            handler: function ( grid, rowIndex, colIndex, item, eOpts, record )
                            {
                                me.openDetailPopup( record );
                            }
                        }
                    ]
                }
            ],
            dockedItems: [
                {
                    xtype:       'pagingtoolbar',
                    store:       me.store,
                    dock:        'bottom',
                    displayInfo: true
                }
            ]
        } );
    },
    openDetailPopup: function ( record )
    {
        Ext.create( 'Ext.window.Window', {
            title:     "Details",
            layout:    'fit',
            draggable: true,
            resizable: false,
            width:     '60%',
            items:     [
                Ext.create( 'Ext.panel.Panel', {
                    width:     '100%',
                    height:    '100%',
                    layout:    'fit',
                    bodyStyle: {
                        background: '#F0F2F4'
                    },
                    items:     [
                        {
                            xtype:       'fieldset',
                            collapsible: false,
                            items:       [
                                Ext.create( 'Ext.panel.Panel', {
                                    width:  '100%',
                                    layout: 'column',
                                    items:  [
                                        {
                                            xtype:       'fieldcontainer',
                                            defaultType: 'displayfield',
                                            items:       [
                                                {
                                                    fieldLabel: '{s namespace=Sofort name=sofort_admin_view_lable_entry}Entry{/s}',
                                                    value:      record.get( 'message' ),
                                                    fieldStyle: { margin: '0 0 0 50px'}
                                                }
                                            ]
                                        }
                                    ]} )
                            ]
                        }
                    ]} )
            ]
        } ).show();
    }
} );