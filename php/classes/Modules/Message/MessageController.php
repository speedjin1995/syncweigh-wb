<?php
namespace App\Modules\Message;

use App\Core\BaseController;

class MessageController extends BaseController
{
    private MessageService $service;

    public function __construct(MessageService $service)
    {
        $this->service = $service;
    }

    /**
     * DataTables server-side list
     */
    public function list(): array
    {
        $p = $this->dataTableParams();
        $result = $this->service->getList($p['start'], $p['length'], $p['orderColumn'], $p['orderDir'], $p['search']);

        return $this->dataTableResponse($p['draw'], $result);
    }

    /**
     * Get single record by ID
     */
    public function get(): array
    {
        if (!isset($_POST['id'])) {
            return ['status' => 'failed', 'message' => 'Missing Attribute'];
        }

        return ['status' => 'success', 'message' => $this->service->getById($this->postId()) ?? []];
    }

    /**
     * Create or update record. The form posts the record ID as keyId.
     */
    public function save(): array
    {
        if (!isset($_POST['keyCode'], $_POST['englishDecs'], $_POST['chineseDecs'], $_POST['malayDecs'], $_POST['nepaliDecs'])) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        $data = [
            'message_key_code' => trim((string)$_POST['keyCode']),
            'en' => trim((string)$_POST['englishDecs']),
            'zh' => trim((string)$_POST['chineseDecs']),
            'my' => trim((string)$_POST['malayDecs']),
            'ne' => trim((string)$_POST['nepaliDecs'])
        ];

        if ($data['message_key_code'] === '' || $data['en'] === '') {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        return $this->service->save((int)($_POST['keyId'] ?? 0), $data);
    }

    /**
     * Hard delete single or multiple records
     */
    public function delete(): array
    {
        return $this->service->delete($this->postIds());
    }
}
