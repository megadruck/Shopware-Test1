//{block name="backend/order/view/detail/position" append}
Ext.override(Shopware.apps.Order.view.detail.Position, {
    getColumns: function() {
        var me = this;
        var columns = me.callOverridden(arguments);

        var columnUploadData= {
            header: 'Daten',
            dataIndex:'futiUploadData',
            flex: 3,
            renderer: function(value, metadata, record, rowIndex, colIndex, store, view){
                if (record && record.getAttributes() instanceof Ext.data.Store && record.getAttributes().getCount() > 0) {
                    var attr = record.getAttributes().first(),
                        dataReturn='';
                    
                    if(attr.get('futiUploadData')) {
                        var uploadData = Ext.JSON.decode(attr.get('futiUploadData'));
                        
                        for (var elem in uploadData) {
                            dataReturn+='<b>'+elem+'</b><br />';
                            for(var i=0;i<uploadData[elem]['name'].length;i++)
                            {
                                nr=i+1;
                                if(uploadData[elem]['linkType'][i]=='internal') {
                                    link = "http://"+window.location.host;
                                } else {
                                    link = '';
                                }
                                switch(uploadData[elem]['name'][i])
                                {
                                    case 'per_Post': dataReturn+='per Post'; break;
                                    case 'per_e_Mail': dataReturn+='per e-Mail'; break;
                                    default: dataReturn+=nr+". <a href='"+link+uploadData[elem]['link'][i]+"' target='_blank'>"+uploadData[elem]['name'][i]+"</a><br />"; break;
                                }
                            }
                        }
                    }
                    return dataReturn;
                } else {
                    return '';
                }
            }
        };
        return Ext.Array.insert(columns, 8, [columnUploadData]);
    }
});
//{/block}