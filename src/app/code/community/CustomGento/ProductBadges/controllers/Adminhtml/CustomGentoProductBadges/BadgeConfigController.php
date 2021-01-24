<?php

class CustomGento_ProductBadges_Adminhtml_CustomGentoProductBadges_BadgeConfigController
    extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/customgento_productbadges_config');

        return $this;
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id    = $this->getRequest()->getParam('badge_config_id');
        $model = $this->_getBadgeConfigModel();

        if ($id) {
            $model->load($id);
            if (!$model->getData('badge_config_id')) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('customgento_productbadges')->__('This badge no longer exists.')
                );
                $this->_redirect('*/*');

                return;
            }
        }

        $this->_title($model->getData('badge_config_id') ? $model->getName() : $this->__('New Badge'));

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $model->getConditions()->setJsFormObject('current_badge_conditions_fieldset');

        Mage::register('current_badge_config', $model);

        $this->_initAction()
            ->getLayout()
            ->getBlock('badge_config_edit')
            ->setData('form_action_url', $this->getUrl('*/*/save'));

        $this
            ->_addBreadcrumb(
                $id ? Mage::helper('customgento_productbadges')->__('Edit Badge')
                    : Mage::helper('customgento_productbadges')->__('New Badge'),
                $id ? Mage::helper('customgento_productbadges')->__('Edit Badge')
                    : Mage::helper('customgento_productbadges')->__('New Badge')
            )
            ->renderLayout();
    }

    /**
     * Badge Config save action
     */
    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $model = $this->_getBadgeConfigModel();
                Mage::dispatchEvent(
                    'adminhtml_controller_customgento_productbadges_badgeconfig_prepare_save',
                    array('request' => $this->getRequest())
                );
                $data = $this->getRequest()->getPost();
                $data = $this->_filterDates($data, array('from_date', 'to_date'));
                $id   = $this->getRequest()->getParam('badge_config_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getData('badge_config_id')) {
                        Mage::throwException(Mage::helper('customgento_productbadges')->__('Wrong badge specified.'));
                    }
                }

                $session = Mage::getSingleton('adminhtml/session');

                $validateResult = $model->validateData(new Varien_Object($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $session->addError($errorMessage);
                    }

                    $session->setPageData($data);
                    $this->_redirect('*/*/edit', array('badge_config_id' => $model->getId()));

                    return;
                }

                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }

                unset($data['rule']);

                $data = $this->_handleBadgeImageUpload($model, $data);
                $data = $this->_handleBadgeStoreAssignment($data);

                $model->loadPost($data);

                $session->setPageData($model->getData());

                $model->save();

                $session->addSuccess(
                    Mage::helper('customgento_productbadges')
                        ->__('The badge config for "%s" has been saved.', $model->getName())
                );
                $session->setPageData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('badge_config_id' => $model->getData('badge_config_id')));

                    return;
                }

                $this->_redirect('*/*/');

                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('badge_config_id');

                if (!empty($id)) {
                    $this->_redirect('*/*/edit', array('badge_config_id' => $id));
                } else {
                    $this->_redirect('*/*/new');
                }

                return;
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('catalogrule')
                        ->__('An error occurred while saving the rule data. Please review the log and try again.')
                );
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect(
                    '*/*/edit',
                    array('badge_config_id' => $this->getRequest()->getParam('badge_config_id'))
                );

                return;
            }
        }

        $this->_redirect('*/*/');
    }

    public function newConditionHtmlAction()
    {
        $id      = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type    = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule($this->_getBadgeConfigModel())
            ->setPrefix('conditions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }

        $this->getResponse()->setBody($html);
    }

    public function badgePreviewAction()
    {
        $badgeData = $this->getRequest()->getPost();

        $badgeData['internal_code'] = 'dummy-code-for-preview';

        if (empty($badgeData['badge_text'])) {
            $badgeData['badge_text'] = '---';
        }

        /** @var CustomGento_ProductBadges_Model_BadgeConfig $badgeConfig */
        $badgeConfig = Mage::getModel('customgento_productbadges/badgeConfig');
        $badgeConfig->setData($badgeData);

        /** @var CustomGento_ProductBadges_Block_Renderer_Badge $badgeRenderer */
        $badgeRenderer = Mage::getBlockSingleton('customgento_productbadges/renderer_badge');

        $badgeHtml = $badgeRenderer->renderBadge($badgeConfig);

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('preview' => $badgeHtml)));
    }

    /**
     * @param CustomGento_ProductBadges_Model_BadgeConfig $model
     * @param array                                       $postData
     *
     * @return array
     */
    protected function _handleBadgeImageUpload(CustomGento_ProductBadges_Model_BadgeConfig $model, $postData)
    {
        $badgeImageFieldName   = 'badge_image';
        $badgesUploadSubFolder = 'customgento_product_badges';

        try {
            if (!empty($postData[$badgeImageFieldName]['delete'])
                && (bool)$postData[$badgeImageFieldName]['delete'] == 1) {
                // Delete old image
                if ($model->getId() && $model->getData($badgeImageFieldName)) {
                    $io          = new Varien_Io_File();
                    $oldFilename = Mage::getBaseDir('media') . DS . implode(DS,
                            explode('/', $model->getData($badgeImageFieldName)));
                    $io->rm($oldFilename);
                }

                $postData[$badgeImageFieldName] = '';
            } else {
                unset($postData[$badgeImageFieldName]);
                if (isset($_FILES[$badgeImageFieldName]) && $_FILES[$badgeImageFieldName]['name']) {
                    $path     = Mage::getBaseDir('media') . DS . $badgesUploadSubFolder . DS;
                    $uploader = new Varien_File_Uploader($badgeImageFieldName);
                    $uploader->setAllowedExtensions(array('jpg', 'png', 'gif'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $destFile = $path . $_FILES[$badgeImageFieldName]['name'];
                    $filename = Varien_File_Uploader::getNewFileName($destFile);
                    $uploader->save($path, $filename);

                    // Delete old image
                    if ($model->getId() && $model->getData($badgeImageFieldName)) {
                        $io = new Varien_Io_File();
                        $io->rm(Mage::getBaseDir('media') . DS . implode(DS,
                                explode('/', $model->getData($badgeImageFieldName))));
                    }

                    // Assigning the uploaded image relative path in order to save it in DB
                    $postData[$badgeImageFieldName] = $badgesUploadSubFolder . '/' . $filename;
                }
            }
        } catch (Exception $e) {
            unset($postData[$badgeImageFieldName]);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        return $postData;
    }

    /**
     * @param $postData
     *
     * @return array
     */
    protected function _handleBadgeStoreAssignment($postData)
    {
        $stores = $postData['store_ids'];
        if (!is_array($stores) || count($stores) == 0) {
            Mage::throwException(
                Mage::helper('customgento_productbadges')
                    ->__('Please, select "Visible in Stores" for this badge configuration first.')
            );
        }

        if (is_array($stores)) {
            $postData['store_ids'] = implode(',', $stores);
        }

        return $postData;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/customgento_productbadges_config');
    }

    /**
     * @return CustomGento_ProductBadges_Model_BadgeConfig
     */
    protected function _getBadgeConfigModel()
    {
        return Mage::getModel('customgento_productbadges/badgeConfig');
    }
}
