<?php
use CRM_Keyfobs_ExtensionUtil as E;

class CRM_Keyfobs_Page_Keyfob extends CRM_Core_Page {
  public function preProcess() {
    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $this->assign('contactId', $this->_contactId);
    CRM_Contact_Page_View::checkUserPermission($this);

    $this->keyfob = new CRM_Keyfobs_BAO_Keyfob();
    $this->keyfob->get('contact_id', $this->_contactId);

    $this->assign('keyfob', $this->keyfob);

    $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this, FALSE, 'browse');
    $this->assign('action', $this->_action);

    $urlParam = 'reset=1&action=add&cid=' . $this->_contactId;
    if ($this->keyfob->id) {
        $urlParam = 'reset=1&action=edit&cid=' . $this->_contactId;
    }
    $this->assign('keyfobFormUrl', CRM_Utils_System::url('civicrm/contact/keyfob', $urlParam));

  }

  public function run() {
    $this->preProcess();

    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(E::ts('Keyfob'));

    parent::run();
  }

}
