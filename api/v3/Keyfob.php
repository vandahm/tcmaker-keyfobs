<?php

function civicrm_api3_keyfob_create($params) {
  return _civicrm_api3_basic_create('CRM_Keyfobs_BAO_Keyfob', $params);
}

function civicrm_api3_keyfob_get($params) {
  return _civicrm_api3_basic_get('CRM_Keyfobs_BAO_Keyfob', $params);
}

function civicrm_api3_keyfob_delete($params) {
  return _civicrm_api3_basic_delete('CRM_Keyfobs_BAO_Keyfob', $params);
}
