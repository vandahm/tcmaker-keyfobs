<?php
use CRM_Keyfobs_ExtensionUtil as E;
use Aws\S3\SqsClient;
use Aws\Common\Credentials\Credentials;

class CRM_Keyfobs_BAO_Keyfob extends CRM_Keyfobs_DAO_Keyfob {

  /**
   * Create a new Keyfob based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Keyfobs_DAO_Keyfob|NULL
   *
  public static function create($params) {
    $className = 'CRM_Keyfobs_DAO_Keyfob';
    $entityName = 'Keyfob';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } */

  public function update_sqs($contact_id)
  {
    $contact_id = $this->contact_id;

    $status_results = civicrm_api3('MembershipStatus', 'getlist');
    $status_ids = array();
    foreach ($status_results['values'] as $status) {
      if ($status['label'] == 'Grace' || $status['label'] == 'Current') {
        $status_ids[] = $status['id'];
      }
    }

    $result = civicrm_api3('Membership', 'get', [
      'sequential' => 1,
      'contact_id' => 204,
    ]);

    $is_active = false;
    foreach ($result['values'] as $value) {
      if (in_array($value['status_id'], $status_ids)) {
        $is_active = true;
        break;
      }
    }

    $message = array(
      'action' => 'deactivate',
      'code' => $this->code,
      'member' => $this->contact_id,
      'access_level' => 0,
    );

    if ($is_active) {
      $message['action'] = 'activate';
      $message['access_level'] = $this->access_level;
      return;
    }

    $message = json_encode($message);

    $client = SqsClient::factory(array(
        'credentials' => array(
          'key' => Civi::settings()->get('keyfobs_aws_access_key_id'),
          'secret' => Civi::settings()->get('keyfobs_aws_secret_access_key'),
        ),
        'region'  => Civi::settings()->get('keyfobs_aws_region'),
    ));

    $client->sendMessage(array(
      'QueueUrl' => Civi::settings()->get('keyfobs_aws_sqs_queue_url'),
      'MessageBody' => $message,
    ));
  }


  public function save($hook=True)
  {
    $ret = parent::save($hook);
    $this->update_sqs();
    return $ret;
  }
}
