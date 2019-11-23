<?php

use CRM_Keyfobs_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Keyfobs_Form_Keyfob extends CRM_Core_Form {
  public function buildQuickForm() {
    // add form elements
    $this->add(
      'text',
      'code',
      'Keyfob Code',
      null,
      TRUE
    );

    $this->add(
      'select', // field type
      'access_level', // field name
      'Access Level', // field label
      $this->getColorOptions(), // list of options
      TRUE // is required
    );

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function preProcess() {
    $this->contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $this->bao = new CRM_Keyfobs_BAO_Keyfob();
    $this->bao->get('contact_id', $this->contactId);
  }

  function setDefaultValues() {
    if (! $this->bao->id) {
      return array(
        'access_level' => '1',
      );
    }

    return array(
      'access_level' => $this->bao->access_level,
      'code' => $this->bao->code,
    );
  }

  public function postProcess() {
    $values = $this->exportValues();
    $this->bao->code = $values['code'];
    $this->bao->access_level = $values['access_level'];
    $this->bao->contact_id = $this->contactId;
    $this->bao->save();
    parent::postProcess();
    CRM_Utils_System::redirect('view?cid=' . $this->contactId);
  }

  public function getColorOptions() {
    $options = array(
      '' => E::ts('- select -'),
      '1' => E::ts('1'),
      '2' => E::ts('2'),
      '3' => E::ts('3'),
      '4' => E::ts('4'),
      '5' => E::ts('5'),
      '6' => E::ts('6'),
      '7' => E::ts('7'),
      '8' => E::ts('8'),
      '9' => E::ts('9'),
    );
    return $options;
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
