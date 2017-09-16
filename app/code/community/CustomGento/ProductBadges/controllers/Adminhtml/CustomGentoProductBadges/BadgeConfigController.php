<?php
class CustomGento_ProductBadges_Adminhtml_CustomGentoProductBadges_BadgeConfigController
    extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();;
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
        $id = $this->getRequest()->getParam('badge_config_id');
        $model = $this->_getBadgeConfigModel();

        if ($id) {
            $model->load($id);
            if (!$model->getData('badge_config_id')) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('customgento_productbadges')->__('This badge no longer exists.'));
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

        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');

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
                    : Mage::helper('customgento_productbadges')->__('New Badge'))
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
                    array('request' => $this->getRequest()));
                $data = $this->getRequest()->getPost();
                $data = $this->_filterDates($data, array('from_date', 'to_date'));
                $id = $this->getRequest()->getParam('badge_config_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getData('badge_config_id')) {
                        Mage::throwException(Mage::helper('customgento_productbadges')->__('Wrong badge specified.'));
                    }
                }

                $session = Mage::getSingleton('adminhtml/session');

                $validateResult = $model->validateData(new Varien_Object($data));
                if ($validateResult !== true) {
                    foreach($validateResult as $errorMessage) {
                        $session->addError($errorMessage);
                    }
                    $session->setPageData($data);
                    $this->_redirect('*/*/edit', array('badge_config_id'=>$model->getId()));
                    return;
                }

                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }

                Mage::log($data);

                unset($data['rule']);
                $model->loadPost($data);

                $session->setPageData($model->getData());

                $model->save();

                $session->addSuccess(Mage::helper('customgento_productbadges')->__('The badge config for "%s" has been saved.', $model->getName()));
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
                    Mage::helper('catalogrule')->__('An error occurred while saving the rule data. Please review the log and try again.'));
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('badge_config_id' => $this->getRequest()->getParam('badge_config_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

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

    /**
     * Returns result of current user permission check on resource and privilege
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