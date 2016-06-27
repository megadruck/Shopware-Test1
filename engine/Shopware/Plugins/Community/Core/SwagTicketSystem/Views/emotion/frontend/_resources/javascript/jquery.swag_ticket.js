/**
 * Shopware 4.0
 * Copyright © shopware AG
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
 * TODO@STP - Set invalidate number based on the required and maxlength
 * HTML attributes
 */
;(function($, window, document, undefined) {
        
    // Wait 'till the document is ready
    $(function () {
                
        function addUpload($upload, file) {
            var $field = $upload.find('.fileupload-input'),
                    $container = $('<p>', { 'class': 'thumbnail-wrapper' }).appendTo($upload);

            if (file.error) {
                var $response = $('<div class="error"/>').css('margin', '1em 0 0').text(file.error).appendTo($container);
            } else {
                var $response = $('<div class="success"/>').css('margin', '1em 0 0').text(file.message).appendTo($container);
                var $name = $('<div>', {'class': 'item-box'}).prependTo($container);
            }
            
            window.setTimeout(function () {
                $response.slideUp('slow', function () {
                    $container.remove();
                });
            }, 2000);
        }
        
        var $fileUploads = $('.option_values_upload_file, .option_values_upload_image');
        $fileUploads.each(function () {
            var $this = $(this),
                $dropZone = $this.find('.fileupload-dropzone'),
                $field = $this.find('.fileupload-input'),
                $answerId = $this.find('input[name=answer]').val(),
                $ticketId = $this.find('input[name=ticket]').val(),
                $progress = $('<div class="progress progress-striped active">').append('<div class="bar">').append('<div class="message">').hide().appendTo($this);

            if (!$.support.xhrFileUpload) {
                $dropZone.hide();
            }

            $field.fileupload({
                dataType: 'json',
                dropZone: $dropZone,
                url: uploadUrl + '?ticketId=' + $ticketId + '&answerId=' + $answerId,
                paramName: 'files',
                redirect: 0,
                //forceIframeTransport: true,
                formData: function (form) {
                    return [];
                },
                start: function (e, data) {
                    if ($.support.xhrFileUpload) {
                        $progress.children('.bar').css('width', 0);
                    } else {
                        $progress.children('.bar').css('width', '100%');
                    }
                    $progress.show();
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $progress.children('.bar').css('width', '' + progress + '%');
                },
                done: function (e, data) {
                    $progress.hide();
                    $.each(data.result, function (index, file) {
                        addUpload($this, file);
                    });
                }
            });
        });
    });

})(jQuery, window, document);