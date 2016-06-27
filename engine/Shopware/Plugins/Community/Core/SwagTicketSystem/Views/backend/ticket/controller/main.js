/**
 * Shopware 4.0
 * Copyright © 2012 shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 *
 * @category   Shopware
 * @package    Ticket
 * @subpackage Controller
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    $Id$
 * @author     Stephan Pohl
 * @author     $Author$
 */
//{namespace name=backend/ticket/main}
//{block name="backend/ticket/controller/main"}
Ext.define('Shopware.apps.Ticket.controller.Main', {

    /**
     * The parent class that this class extends.
     * @string
     */
    extend:'Ext.app.Controller',

    /**
     * Class property which holds the main application if it is created
     *
     * @default null
     * @object
     */
    mainWindow: null,

    /**
     * A template method that is called when your application boots.
     * It is called before the Application's launch function is executed
     * so gives a hook point to run any code before your Viewport is created.
     *
     * @return void
     */
    init:function () {
        var me = this;

        me.subApplication.overviewStore = me.subApplication.getStore('List').load();
        me.subApplication.statusStore = me.subApplication.getStore('Status').load();
        me.subApplication.submissionStore = me.subApplication.getStore('Submission').load();
        me.subApplication.submissionDetailStore = me.subApplication.getStore('SubmissionDetail');
        me.subApplication.typesStore = me.subApplication.getStore('Types').load();
        me.subApplication.statusComboStore = me.subApplication.getStore('StatusCombo');
        me.subApplication.formsStore = me.subApplication.getStore('Forms').load();
        me.subApplication.localeStore = me.subApplication.getStore('Locale').load();
        me.subApplication.fileStore = me.subApplication.getStore('File').load();
        me.subApplication.employeeComboStore = me.subApplication.getStore('EmployeeCombo');

        me.subApplication.employeeStore = me.subApplication.getStore('Employee').load({
            callback: function() {
                me.mainWindow = me.getView('main.Window').create({
                    overviewStore: me.subApplication.overviewStore,
                    employeeStore: me.subApplication.employeeStore,
                    statusStore: me.subApplication.statusStore,
                    submissionStore: me.subApplication.submissionStore,
                    submissionDetailStore: me.subApplication.submissionDetailStore,
                    typesStore: me.subApplication.typesStore,
                    statusComboStore: me.subApplication.statusComboStore,
                    formsStore: me.subApplication.formsStore,
                    localeStore: me.subApplication.localeStore,
                    employeeComboStore: me.subApplication.employeeComboStore
                });
            }
        });

        if (me.subApplication.action && me.subApplication.action.toLowerCase() === 'detail') {
            if (me.subApplication.params && me.subApplication.params.ticketId) {
                //open the customer detail page with the passed customer id
                var me = this, historyStore, defaultSubmission;
                // Create a history store and add the ticket id to it
                historyStore = me.subApplication.getStore('History');
                historyStore.getProxy().extraParams = {
                    id: me.subApplication.params.ticketId,
                    record: me.subApplication.params.customerRecord
                };

                var submissionDetailStore = me.subApplication.submissionDetailStore;
                submissionDetailStore.getProxy().extraParams.onlyDefaultSubmission = true;

                submissionDetailStore.load({
                    scope: this,
                    callback: function(records, operation, success) {
                        defaultSubmission = records[0];
                        record = me.subApplication.params.customerRecord;
                        // Open the edit window
                        me.getView('ticket.EditWindow').create({
                            record: record,
                            defaultSubmission: defaultSubmission,
                            localeStore: me.subApplication.localeStore,
                            submissionStore: me.subApplication.submissionStore,
                            submissionDetailStore: me.subApplication.submissionDetailStore,
                            statusStore: me.subApplication.statusStore,
                            historyStore: historyStore.load(),
                            fileStore: me.subApplication.fileStore,
                            employeeStore: me.subApplication.employeeStore,
                            userID: record.get('userId'),
                            albumId: record.get('albumId'),
                            employeeId: me.subApplication.params.employeeId

                        });
                    }
                });
            } else {
                me.mainWindow = me.getView('main.Window').create({
                    listStore:me.subApplication.overviewStore.load()
                });
            }
        } else {

            return false;
        }

    }

});
//{/block}
