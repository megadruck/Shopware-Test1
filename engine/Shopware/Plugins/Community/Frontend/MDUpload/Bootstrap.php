<?php
/**
 * Copyright (c) 2016
 * Megadruck.de Produktions- und Vertriebs GmbH
 * Joerg Frintrop
 * j.frintrop@megadruck.de
 *
 * Eichendorffstrasse 34b
 * 26655 Westerstede
 * Tel. 04488 52540-25
 * Fax. 04488 52540-14
 *
 * www.megadruck.de
 */


class Shopware_Plugins_Frontend_MDUpload_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    protected function getEntityManager()
    {
        return Shopware()->Models();
    }


    public function getVersion()
    {
        return '1.0.1';
    }

    public function getInfo() {
        return array(
            'version' => $this->getVersion(),
            'copyright' => 'Copyright (c) 2016, Megadruck.de',
            'supplier' => 'Megadruck.de',
            'author' => 'Megadruck.de',
            'label' => $this->getLabel(),
            'description' => file_get_contents(__DIR__ . '/info.txt')
        );
    }


    public function getLabel()
    {
        return 'Megadruck Printfile Upload';
    }

    public function install()
    {
        try {
            $this->createConfigForm();
            $this->subscribeEvents();
            $this->createAttributes();
        }
        catch( Exception $e )
        {
            return array( "success" => false, "message" => $e->getMessage() );
        }
        return array( "success" => true );
    }

    public function enable()
    {
        return array('success'  => true,'invalidateCache' => array('frontend', 'template', 'config', 'backend')
        );
    }

    public function disable()
    {
        return array('success'  => true,'invalidateCache' => array('frontend', 'template', 'config', 'backend')
        );
    }

    public function uninstall()
    {
        return array('success' => true);
    }



    protected function createConfigForm()
    {
        $form = $this->Form();


        $form->setElement(
            'checkbox',
            'vorauswahl_enable',
            array('label' => 'Auswahl aktivieren?', 'value' => true, 'scope' => Shopware\Models\Config\Element::SCOPE_SHOP)
        );

        $form->setElement(
            'checkbox',
            'email_enable',
            array('label' => 'per e-Mail aktivieren?', 'value' => true, 'scope' => Shopware\Models\Config\Element::SCOPE_SHOP)
        );

        $form->setElement(
            'text',
            'file_type',
            array('label' => 'Endungen Printfiles', 'value' => '\'pdf\',\'jpg\',\'jpeg\',\'ids\'', 'description' => '', 'required' => true, 'scope' => Shopware\Models\Config\Element::SCOPE_SHOP)
        );
        $form->setElement(
            'numberfield',
            'file_size',
            array('label' => 'Maximale Dateigr&ouml;&szlig;e (MegaByte)', 'value' => '20', 'required' => true, 'scope' => Shopware\Models\Config\Element::SCOPE_SHOP));

        /**
         * path without / at the beginning
         */
        $form->setElement(
            'text',
            'upload_path',
            array('label' => 'Pfad zum Upload Ordner', 'value' => 'media/DruckDaten', 'required' => true, 'scope' => Shopware\Models\Config\Element::SCOPE_SHOP)
        );

        $form->setElement(
            'checkbox',
            'drag_and_drop_enable',
            array('label' => 'Drag&Drop aktivieren?', 'value' => true, 'scope' => Shopware\Models\Config\Element::SCOPE_SHOP)
        );
        $form->setElement(
            'checkbox',
            'file_attachment',
            array('label' => 'Datenanhang E-Mail aktivieren?', 'value' => false, 'description' => 'Die Printfiles werden in der Bestellbestätigungs-eMail angehängt.', 'scope' => Shopware\Models\Config\Element::SCOPE_SHOP)
        );

        $form->setElement(
            'checkbox',
            'debug_enable',
            array('label' => 'Debug aktivieren?', 'value' => false, 'scope' => Shopware\Models\Config\Element::SCOPE_SHOP)
        );

        $form->setElement(
            'button',
            "titel_chunking",
            array( "label" => "Pausefunktion & Datei-Partitionierung" )
        );

        $form->setElement(
            'checkbox',
            "chunking_enable",
            array( "label" => "Aktivieren?", "value" => true, "scope" => Shopware\Models\Config\Element::SCOPE_SHOP )
        );

        $form->setElement(
            'numberfield',
            "chunking_partsize",
            array( "label" => "Paketgröße in bytes", "value" => "2000000", "required" => true, "description" => "Standard: 2000000 byts = 2 megabytes)", "scope" => Shopware\Models\Config\Element::SCOPE_SHOP )
        );


        $form = $this->Form();
    }

    private function subscribeEvents()
    {

        /**
         * add controller Upload
         * /Upload/{action}/
         */
        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_Fileupload',
            'onGetControllerPathFrontend'
         );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail',
            'onActionFrontendDetail'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout',
            'onActionFrontendCheckout'
        );

        $this->subscribeEvent(
            'Shopware_Modules_Order_SaveOrder_ProcessDetails',
             'onOrder_SaveOrderProcessDetails'
         );

         $this->subscribeEvent(
            'Shopware_Modules_Order_SendMail_FilterVariables',
            'onSendMailFilterVariables'
        );

        $this->subscribeEvent(
             'Shopware_Modules_Order_SendMail_BeforeSend',
             'onSendMail'
         );

        /**
         * add javascript files
         */
        $this->subscribeEvent(
            'Theme_Compiler_Collect_Plugin_Javascript',
            'addJsFiles'
        );
        /**
         * add less files
         */
        $this->subscribeEvent(
            'Theme_Compiler_Collect_Plugin_Less',
            'addLessFiles'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Backend_Order',
            'postDispatchOrder'
        );
    }


    private function createAttributes()
    {
        $this->Application()->Models()->addAttribute(
            's_order_details_attributes',
            'md',
            'upload',
            'longtext', true, NULL);

        $this->Application()->Models()->addAttribute(
            's_order_basket_attributes',
            'md',
            'upload',
            'longtext', true, NULL);

        $this->getEntityManager()->generateAttributeModels(array('s_order_details_attributes', 's_order_basket_attributes'));
    }

    /**
     * @param Enlight_Event_EventArgs $args
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function addJsFiles(Enlight_Event_EventArgs $args)
    {
        $jsFiles = array(__DIR__ . '/Views/frontend/_public/src/js/jquery.fine-uploader.min.js');
        return new Doctrine\Common\Collections\ArrayCollection($jsFiles);
    }

    /**
     * @param Enlight_Event_EventArgs $args
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function addLessFiles(Enlight_Event_EventArgs $args)
    {
        $less = new Shopware\Components\Theme\LessDefinition(
            array(),
            //less files to compile
            array(
                __DIR__ . "/Views/frontend/_public/src/less/fine-uploader.less",
                __DIR__ . "/Views/frontend/_public/src/less/mdupload.less"
            ),
            //import directory
            __DIR__);
        return new Doctrine\Common\Collections\ArrayCollection(array( $less ));
    }


    /**
     * @param Enlight_Event_EventArgs $args
     * @return null
     */
    public static function onActionFrontendDetail(Enlight_Event_EventArgs $args)
    {
        $controller = $args->getSubject();
        $view = $controller->View();
        $article = $view->getAssign("sArticle");
        //$this->write_log($article['attr20']);

        $datei = @fopen($_SERVER["DOCUMENT_ROOT"]."var/log/debug.txt","a");
        @fwrite($datei, $article['attr20']."\n");
        @fclose($datei);

        if( !$article["articleID"] || $article['attr20'] == "1" )
        {
            return NULL;
        }

        $view->addTemplateDir(__DIR__ . "/Views/");
        $view->assign("uploadStatus", $article['attr20']);
    }

    /**
     * @param Enlight_Event_EventArgs $args
     */
    public static function onActionFrontendCheckout(Enlight_Event_EventArgs $args)
    {
        $controller = $args->getSubject();
        $view = $controller->View();
        $plugin = Shopware()->Plugins()->Frontend()->MDUpload();
        $view->addTemplateDir(__DIR__ . "/Views/");
        if( $controller->Request()->getActionName() == "cart" || $controller->Request()->getActionName() == "confirm" )
        {
            $plugin->getCartUpload($plugin, $view);
        }
        else
        {
            if( $args->getSubject()->Request()->getActionName() == "finish" )
            {
                $plugin->getFinishList($plugin, $view);
            }

        }

    }

    /**
     * @param $plugin
     * @param $view
     */
    private static function getCartUpload($plugin, $view)
    {

        if( !Shopware()->Session()->uploadStatus )
        {
            Shopware()->Session()->uploadStatus = 0;
        }

        $dataCounter = 0;
        $minUpload = array(  );
        $uploadMD = array(  );
        $config = $plugin->Config();
        $uploadMD["file_type"] = $config->file_type;
        $uploadMD["file_size"] = $config->file_size * 1000000;
        $uploadMD["drag_and_drop_enable"] = $config->drag_and_drop_enable;
        $uploadMD["debug_enable"] = $config->debug_enable;
        $uploadMD["chunking_enable"] = $config->chunking_enable;
        $uploadMD["chunking_partsize"] = $config->chunking_partsize;
        $uploadMD["resume_enable"] = $config->resume_enable;
        $uploadMD["uploadPath"] = $config->upload_path;
        $uploadMD["vorauswahl_enable"] = $config->vorauswahl_enable;
        $uploadMD["select1"] = "Sofort";
        if( $config->email_enable == true )
        {
            $uploadMD["select2"] = "per e-Mail";
        }

        $sBasket = $view->sBasket;
        foreach( $sBasket["content"] as &$basketItem )
        {

            //Check if upload is deactivated
            if( $basketItem["modus"] == 0 && $basketItem["additional_details"]['attr20'] != "1" )
            {

                $uploadFieldsData = "";
                $tempArticle = Shopware()->Modules()->Articles();
                $getArticle = $tempArticle->sGetArticleById($basketItem["articleID"]);

                //name,min,max

                //$uploadFields = $getArticle["attr" . $config->upload_fields];
                $uploadFields ='Druckdatei,0,3';
                $uploadFieldsSplit = preg_split("/;/", $uploadFields);
                for( $i = 0; $i < count($uploadFieldsSplit); $i++ )
                {
                    $uploadFieldsSplit2 = preg_split("/,/", $uploadFieldsSplit[$i]);
                    if( $uploadFieldsSplit2[1] != 0 )
                    {
                        $minUpload[$basketItem["id"]][$uploadFieldsSplit2[0]] = $uploadFieldsSplit2[1];
                    }

                    $uploadFieldsData .= $uploadFieldsSplit2[0];
                    $uploadFieldsData .= "," . $uploadFieldsSplit2[1];
                    $uploadFieldsData .= "," . $uploadFieldsSplit2[2];
                    $uploadFieldsData .= "," . $plugin->getUploadPageStatus($basketItem["id"], $uploadFieldsSplit2[0]);
                    if( $i < count($uploadFieldsSplit) - 1 )
                    {
                        $uploadFieldsData .= ";";
                    }

                }
                $tempfieldArray = explode(";", $uploadFieldsData);
                $fieldArray = array(  );
                foreach( $tempfieldArray as $value )
                {
                    $fieldArray[] = explode(",", $value);
                }
                $basketItem["uploadFields"][] = $fieldArray;
                $selectSession = "uploadStatus_" . $basketItem["id"];
                $uploadMD["selection_status_" . $basketItem["id"]] = Shopware()->Session()->$selectSession;
                $sql = "SELECT md_upload FROM s_order_basket_attributes WHERE basketID='" . $basketItem["id"] . "'";
                $uploadFiles = json_decode(Shopware()->Db()->fetchOne($sql), true);
                foreach( $uploadFiles as $key => $files )
                {
                    foreach( $files["link"] as $key2 => $file )
                    {
                        if( $files["linkType"][$key2] == "internal" )
                        {
                            $uploadFiles[$key]["type"][$key2] = mime_content_type($_SERVER["DOCUMENT_ROOT"] . $file);
                            $uploadFiles[$key]["size"][$key2] = $plugin->formFileSize(filesize($_SERVER["DOCUMENT_ROOT"] . $file));
                        }

                    }
                }
                $uploadFilesCheck[$basketItem["id"]] = $uploadFiles;
                $basketItem["uploadedFiles"] = $uploadFiles;
                $min = 0;
                $count = 0;
                foreach( $minUpload as $key => $minData )
                {
                    foreach( $minData as $key2 => $data )
                    {
                        $min += $minData[$key2];
                        if( $minData[$key2] <= count($uploadFilesCheck[$key][$key2]["name"]) )
                        {
                            $count += count($uploadFilesCheck[$key][$key2]["name"]);
                        }

                        if( $uploadFilesCheck[$key][$key2]["linkType"][0] == "perMail" )
                        {
                            $count--;
                            $count += count($minUpload[$key]);
                        }

                    }
                }
            }

        }
        if( $min <= $count )
        {
            $dataCounter = 1;
        }

        $view->checkSubmitButton = $dataCounter;
        $view->timestamp = time();
        $view->timestamp_md5 = md5("unique_salt" . time());
        $view->uploadMD = $uploadMD;
        $view->assign("sBasket", $sBasket);
        $view->assign("fileExtension", $uploadMD["file_type"]);

        $view->addTemplateDir(__DIR__ . "/Views/");


    }

    /**
     * @param $byte
     * @return string
     */
    private static function formFileSize($byte)
    {
        if( $byte < 1024 )
        {
            $result = round($byte, 2) . "Byte";
        }
        else
        {
            if( 1024 <= $byte && $byte < pow(1024, 2) )
            {
                $result = round($byte / 1024, 2) . "kB";
            }
            else
            {
                if( pow(1024, 2) <= $byte && $byte < pow(1024, 3) )
                {
                    $result = round($byte / pow(1024, 2), 2) . "MB";
                }
                else
                {
                    if( pow(1024, 3) <= $byte && $byte < pow(1024, 4) )
                    {
                        $result = round($byte / pow(1024, 3), 2) . "GB";
                    }
                    else
                    {
                        if( pow(1024, 4) <= $byte && $byte < pow(1024, 5) )
                        {
                            $result = round($byte / pow(1024, 4), 2) . "TB";
                        }
                        else
                        {
                            if( pow(1024, 5) <= $byte && $byte < pow(1024, 6) )
                            {
                                $result = round($byte / pow(1024, 5), 2) . "PB";
                            }
                            else
                            {
                                if( pow(1024, 6) <= $byte && $byte < pow(1024, 7) )
                                {
                                    $result = round($byte / pow(1024, 6), 2) . "EB";
                                }

                            }

                        }

                    }

                }

            }

        }

        return $result;
    }

    /**
     * @param $basketID
     * @param $page
     * @return int
     */
    public static function getUploadPageStatus($basketID, $page)
    {
        $sql = "SELECT md_upload FROM s_order_basket_attributes WHERE basketID=" . $basketID;
        $data = json_decode(Shopware()->Db()->fetchOne($sql), true);
        $result = 0;
        foreach( $data[$page] as $dataItem )
        {
            return count($dataItem);
        }
    }
    static public function onGetControllerPathFrontend(Enlight_Event_EventArgs $args)
    {
        return __DIR__ . '/Controllers/Frontend/Fileupload.php';
    }

    /**
     * @param $args
     */
    public static function onOrder_SaveOrderProcessDetails($args)
    {
        $plugin = Shopware()->Plugins()->Frontend()->MDUpload();
        $order = $args->getSubject();
        $orderNumber = $order->sOrderNumber;
        $orderID = Shopware()->Db()->fetchOne("SELECT id FROM s_order WHERE ordernumber=?", array( $orderNumber ));
        $orderDetailsIDs = Shopware()->Db()->fetchAll("SELECT id FROM s_order_details WHERE modus='0' AND ordernumber=?", array( $orderNumber ));
        $basketContent = $args->getDetails();
        $basketCounter = 0;
        foreach( $basketContent as &$basketItem )
        {
            if( $basketItem["modus"] == 0 )
            {
                $sql = "SELECT md_upload FROM s_order_basket_attributes WHERE basketID='" . $basketItem["id"] . "'";
                $data = Shopware()->Db()->fetchOne($sql);
                $sql = "UPDATE s_order_details_attributes SET md_upload='" . $data . "' WHERE detailID='" . $orderDetailsIDs[$basketCounter]["id"] . "'";
                Shopware()->Db()->exec($sql);
                $basketCounter++;
            }

        }


    }

    /**
     * @param $plugin
     * @param $view
     */
    private static function getFinishList($plugin, $view)
    {
        $orderNumber = $view->sOrderNumber;
        $sql = "SELECT id FROM s_order WHERE ordernumber=?";
        $orderID = Shopware()->Db()->fetchOne($sql, array( $orderNumber ));
        $sql = "SELECT id FROM s_order_details WHERE orderID=?";
        $detailID = Shopware()->Db()->fetchAll($sql, array( $orderID ));
        $uploadData = array(  );
        foreach( $detailID as $detail )
        {
            $sql = "SELECT md_upload FROM s_order_details_attributes WHERE detailID=?";
            $uploadData[] = json_decode(Shopware()->Db()->fetchOne($sql, array( $detail["id"] )), true);
        }
        $view->sFinishUploads = $uploadData;
        $view->addTemplateDir(__DIR__ . "/Views/");

    }


    public static function onSendMailFilterVariables($args)
    {
        global $orderNumber;
        $details = $args->getReturn();
        $orderNumber = $details["ordernumber"];
        foreach( $details["sOrderDetails"] as $k => $detail )
        {
            if( !empty($detail["articleID"]) && $detail["modus"] == 0 )
            {
                $sql = "SELECT md_upload FROM s_order_details_attributes WHERE detailID='" . $detail["orderDetailId"] . "'";
                $uploadData = json_decode(Shopware()->Db()->fetchOne($sql), true);
                $details["sOrderDetails"][$k]["attributes"]["upload"] = $uploadData;
            }

        }
        return $details;
    }


    /**
     * @param $args
     * @return null
     * @throws Enlight_Exception
     */
    public static function onSendMail($args)
    {
        global $orderNumber;
        $plugin = Shopware()->Plugins()->Frontend()->MDUpload();
        $config = $plugin->Config();
        if( $config->file_attachment == false )
        {
            return NULL;
        }

        $date = date("d.m.Y");
        $orderID = Shopware()->Db()->fetchOne("SELECT id FROM s_order WHERE ordernumber=?", array( $orderNumber ));
        $orderAttributes = $plugin->getOrderAttributes($orderID);
        $uploadNumber = $orderAttributes[0]["attribute6"];
        $path = Shopware()->DocPath() . $config->upload_path . "/" . $date . "/" . $uploadNumber . "/";
        $datas = scandir($path);
        foreach( $datas as $data )
        {
            if( $data != "." && $data != ".." )
            {
                if( false === ($fileHandle = fopen($path . $data, "r")) )
                {
                    throw new Enlight_Exception("File could not been loaded: " . $data);
                }

                $fileAttachment = $args->mail->createAttachment($fileHandle);
                $fileAttachment->filename = basename($path . $data);
            }

        }
    }

    /**
     * @param $orderID
     * @return array
     */
    public function getOrderAttributes($orderID)
    {
        return Shopware()->Db()->fetchAll("SELECT * FROM s_order_attributes WHERE orderID=?", array( $orderID ));
    }

    /**
     * @param $args
     */
    public function postDispatchOrder($args)
    {
        $view = $args->getSubject()->View();
        $args->getSubject()->View()->addTemplateDir($this->Path() . "/Views/");
        if( $args->getRequest()->getActionName() === "load" )
        {
            $view->extendsTemplate("backend/mdupload/order/model/attribute.js");
            $view->extendsTemplate("backend/mdupload/order/view/list/position.js");
            $view->extendsTemplate("backend/mdupload/order/view/detail/position.js");
        }

    }


    public function write_log($str)
    {

        $datei = @fopen($_SERVER["DOCUMENT_ROOT"]."var/log/debug.txt","a");
        @fwrite($datei, $str."\n");
        @fclose($datei);
    }
}


?>
