
/**
 * The address model contains all fields for a single address.
 */
//{block name="backend/customer/model/address"}
Ext.define('Shopware.apps.Customer.model.Address', {
        extend:'Shopware.data.Model',

        configure: function() {
                return {
                        controller: 'Address',
                        detail: 'Shopware.apps.Customer.view.address.detail.Address'
                }
        },

        /**
         * The fields used for this model
         * @array
         */
        fields:[
                { name:'defaultAddress', type: 'string', useNull: true }, // fake field
                { name:'setDefaultBillingAddress', type: 'boolean', useNull: true }, // fake field
                { name:'setDefaultShippingAddress', type: 'boolean', useNull: true }, // fake field
                { name:'setDefaultSenderAddress', type: 'boolean', useNull: true }, // fake field
                { name:'user_id', type: 'string', useNull: true }, // fake field
                { name:'company', type:'string', useNull: true },
                { name:'department', type:'string', useNull: true, },
                { name:'vatId', type:'string', useNull: true },
                { name:'salutation', type:'string' },
                { name:'title', type:'string' },
                { name:'firstname', type:'string' },
                { name:'lastname', type:'string' },
                { name:'street', type:'string' },
                { name:'zipcode', type:'string' },
                { name:'city', type:'string' },
                { name:'additionalAddressLine1', type:'string', useNull: true },
                { name:'additionalAddressLine2', type:'string', useNull: true },
                { name:'country_id', type:'int' },
                { name:'state_id', type:'int', useNull: true },
                { name:'phone', type:'string', useNull: true }
        ],

        associations: [
                { type:'hasMany', model:'Shopware.apps.Base.model.Customer', name:'getCustomer', associationKey:'customer' },
                { type:'hasMany', model:'Shopware.apps.Base.model.Country', name:'getCountry', associationKey:'country', relation: 'ManyToOne', field: 'country_id' },
                { type:'hasMany', model:'Shopware.apps.Base.model.CountryState', name:'getState', associationKey:'state', relation: 'ManyToOne', field: 'state_id' }
        ]

});
//{/block}


