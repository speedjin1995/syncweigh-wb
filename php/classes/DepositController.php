<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/DepositService.php';

class DepositController extends BaseController {

    private $depositService;

    public function __construct($db) {
        parent::__construct($db);
        $this->depositService = new DepositService($db, $this->username);
    }

    /**
     * Weighing screen check for the selected party: balance, deposit control flag and unit price
     */
    public function check() {
        $partyType = trim($this->getPost('partyType', ''));
        $partyId = trim($this->getPost('partyId', ''));
        $itemCode = trim($this->getPost('itemCode', ''));

        try {
            $party = $this->depositService->getParty($partyType, $partyId);

            if (!$party) {
                $this->success('', ['enabled' => false, 'controlled' => false, 'balance' => 0, 'min' => 0, 'price' => 0]);
            }

            $price = $itemCode !== '' ? $this->depositService->price($partyType, $party['id'], $itemCode) : 0;

            $this->success('', [
                'enabled' => $this->depositService->controlEnabled() && $party['controlled'],
                'controlled' => $party['controlled'],
                'balance' => $party['balance'],
                'min' => $party['min'],
                'price' => $price
            ]);
        } catch (mysqli_sql_exception $e) {
            error_log('DepositController - ' . $e->getMessage());
            $this->failed('Something went wrong');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }
}
?>
