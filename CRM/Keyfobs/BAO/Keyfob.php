<?php
use CRM_Keyfobs_ExtensionUtil as E;
use Aws\Sqs\SqsClient;

class CRM_Keyfobs_BAO_Keyfob extends CRM_Keyfobs_DAO_Keyfob {

  /**
   * Create a new Keyfob based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Keyfobs_DAO_Keyfob|NULL
   */
  public static function create($params) {
    $className = 'CRM_Keyfobs_BAO_Keyfob';
    $entityName = 'Keyfob';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    $instance->update_sqs();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

  public function update_sqs()
  {
    if (is_null($this->code)) {
      return;
    }
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
      'contact_id' => $contact_id,
    ]);

    $is_active = false;
    foreach ($result['values'] as $value) {
      if (in_array($value['status_id'], $status_ids)) {
        $is_active = true;
        break;
      }
    }

    if ($is_active) {
      $this->activate();
    } else {
      $this->deactivate();
    }

    // $message = array(
    //   'action' => 'deactivate',
    //   'code' => $this->code,
    //   'member' => $this->contact_id,
    //   'access_level' => 0,
    // );
    //
    // if ($is_active) {
    //   $message['action'] = 'activate';
    //   $message['access_level'] = $this->access_level;
    // }
    //
    // $message = json_encode($message);
    //
    // $client = $this->getSqsClient();
    // $client->sendMessage(array(
    //   'QueueUrl' => Civi::settings()->get('keyfobs_aws_sqs_queue_url'),
    //   'MessageBody' => $message,
    // ));
  }

  public function deactivate() {
    // You can't deactivate a code that doesn't exist
    if (! $this->code) {
      return;
    }

    $message = array(
      'action' => 'deactivate',
      'code' => $this->code,
      'member' => (int)$this->contact_id,
      'access_level' => 0,
    );

    $this->getSqsClient()->sendMessage(array(
      'QueueUrl' => Civi::settings()->get('keyfobs_aws_sqs_queue_url'),
      'MessageBody' => json_encode($message),
      'MessageGroupId' => 'DoorMessages',
      'MessageDepublicationId' => uniqid($more_entropy=true),
    ));
  }

  public function activate() {
    // Don't activate unless a code is present.
    if (! $this->code) {
      return;
    }

    $message = array(
      'action' => 'activate',
      'code' => $this->code,
      'member' => (int)$this->contact_id,
      'access_level' => (int)$this->access_level,
    );

    $this->getSqsClient()->sendMessage(array(
      'QueueUrl' => Civi::settings()->get('keyfobs_aws_sqs_queue_url'),
      'MessageBody' => json_encode($message),
      'MessageGroupId' => 'DoorMessages',
      'MessageDeduplicationId' => uniqid($more_entropy=true),
    ));
  }

  protected function getSqsClient() {
    return SqsClient::factory(array(
        'credentials' => array(
          'key' => Civi::settings()->get('keyfobs_aws_access_key_id'),
          'secret' => Civi::settings()->get('keyfobs_aws_secret_access_key'),
        ),
        'region'  => Civi::settings()->get('keyfobs_aws_region'),
        'version' => 'latest',
    ));
  }

  public function save($hook=True)
  {
    $oldValues = new CRM_Keyfobs_BAO_Keyfob();
    $oldValues->get('id', $this->id);

    if ($oldValues->code && ($oldValues->code != $this->code)) {
      // If we're changing a code, deactivate the old code first
      $oldValues->deactivate();
    }

    $ret = parent::save($hook);
    $this->update_sqs();
    return $ret;
  }
}
