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
 * @package    Base
 * @subpackage Model
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    $Id$
 * @author shopware AG
 */

/**
 * Shopware Model
 */
//{block name="backend/swag_import_export/model/session_list"}
Ext.define('Shopware.apps.SwagImportExport.model.SessionList', {
    /**
     * Extends the standard ExtJS 4
     * @string
     */
    extend : 'Ext.data.Model',
    /**
     * Configure the data communication
     * @object
     */
    fields: [
        // {block name="backend/swag_import_export/model/session_list/fields"}{/block}
        { name: 'id', type: 'string' },
        { name: 'profileId', type: 'string' },
        { name: 'profileName', type: 'string' },
        { name: 'type', type: 'string' },
        { name: 'position', type: 'string' },
        { name: 'totalCount', type: 'string' },
        { name: 'username', type: 'string' },
        { name: 'fileName', type: 'string' },
        { name: 'fileUrl', type: 'string' },
        { name: 'format', type: 'string' },
        { name: 'fileSize', type: 'string' },
        { name: 'state', type: 'string' },
        { name: 'createdAt', type: 'date' }
    ],
    
    proxy: {
        type: 'ajax',
        /**
         * Configure the url mapping for the different
         * store operations based on
         * @object
         */
        api: {
            read:'{url controller="SwagImportExport" action="getSessions"}',
            destroy:'{url controller="SwagImportExport" action="deleteSession"}'
        },
        /**
         * Configure the data reader
         * @object
         */
        reader: {
            type: 'json',
            root: 'data'
        },

        writer: {
            type: 'json',
            root: 'data'
        }
    }
});
//{/block}