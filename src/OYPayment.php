<?php
namespace rymesaint\LaravelOY;

use Brick\Math\BigInteger;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

use function PHPUnit\Framework\throwException;

class OYPayment
{

    /**
     * Get default base url for using production or staging environment
     * @return string 
     * @throws BindingResolutionException 
     */
    protected static function getBaseUrl()
    {
        return config('oy-config.OY_PRODUCTION') == true ? 'https://partner.oyindonesia.com/api' : 'https://api-stg.oyindonesia.com/api';
    }

    /**
     * Get an array of default headers for accessing oyo payment
     * @return array 
     * @throws BindingResolutionException 
     */
    protected static function getDefaultHeaders()
    {
        return [
            'x-api-key' => config('oy-config.OY_API_KEY'),
            'x-oy-username' => config('oy-config.OY_USERNAME'),
            'content-type' => 'application/json',
            'accept' => 'application/json'
        ];
    }

    /**
     * Use this API to get partner balance.
     * 
     * @return Response 
     */
    public static function getBalance()
    {
       try {
        return Http::withHeaders(self::getDefaultHeaders())->get(self::getBaseUrl().'/balance');
       } catch(\Exception $e) {
        throwException($e);
       }
    }

    /**
     * Use this API to get beneficiary account details.
     * @param mixed $bankCode 
     * @param mixed $accountNumber 
     * @return Response 
     */
    public static function accountInquiry($bankCode, $accountNumber)
    {
       try {
        return Http::withHeaders(self::getDefaultHeaders())->post(self::getBaseUrl().'/account-inquiry', [
            'bank_code' => $bankCode,
            'account_number' => $accountNumber
        ]);
       } catch(\Exception $e) {
        throwException($e);
       }
    }

    /**
     * Use this API to get inquiry invoices.
     * @param int $offset 
     * @param int $limit 
     * @param string $status = PAID/UNPAID
     * @return Response
     */
    public static function getInvoices(int $offset = 0,int $limit = 10,String $status = '')
    {
       try {
           $queryParams = [
                'offset' => $offset,
                'limit' => $limit,
           ];
            if(!empty($status)) {
                $queryParams['status'] = $status;
            }
            return Http::withHeaders(self::getDefaultHeaders())->get(self::getBaseUrl().'/account-inquiry/invoices',$queryParams);
       } catch(\Exception $e) {
        throwException($e);
       }
    }

    /**
     * Use this API to get inquiry invoice by ID.
     * @param string $id 
     * @return Response|void 
     */
    public static function getInvoiceById(String $id)
    {
       try {
            return Http::withHeaders(self::getDefaultHeaders())->get(self::getBaseUrl()."/account-inquiry/invoices/$id");
       } catch(\Exception $e) {
        throwException($e);
       }
    }

    /**
     * Use this API to pay inquiry invoice.
     * @param string $id 
     * @return Response|void 
     */
    public static function payInvoice(String $id)
    {
       try {
            return Http::withHeaders(self::getDefaultHeaders())->post(self::getBaseUrl()."/account-inquiry/invoices/pay", [
                'invoice_id' => $id
            ]);
       } catch(\Exception $e) {
           throwException($e);
       }
    }

    /**
     * Use this API to start disbursing money to a specific beneficiary account.
     * 
     * @param string $recipientBank Required
     * @param string $recipientAccount Required
     * @param BigInteger $amount Accept non-decimal number, min amount 10.000
     * @param string $note 
     * @param string $partnerTrxId Required
     * @param string $email Optional
     * @return Response 
     */
    public static function disbursement(
        String $recipientBank, 
        String $recipientAccount, 
        BigInteger $amount, 
        String $partnerTrxId, 
        String $note, 
        String $email
    )
    {
       try {
            return Http::withHeaders(self::getDefaultHeaders())->post(self::getBaseUrl()."/remit", [
                'recipient_bank' => $recipientBank,
                'recipient_account' => $recipientAccount,
                'amount' => $amount,
                'note' => $note,
                'partner_trx_id' => $partnerTrxId,
                'email' => $email
            ]);
       } catch(\Exception $e) {
           throwException($e);
       }
    }

    /**
     * To get status of a disbursement request, you can call this API. You may need to call this API few times until getting a final status (success / failed).
     * This API offers an option to send you a callback status of the disbursement request to a specific URL. Please contact us and submit a callback URL if you need a callback status of a disbursement request.
     * 
     * @param string $partnerTrxId 
     * @param bool $send_callback default = false
     * @return Response 
     */
    public static function disbursementStatus(String $partnerTrxId, bool $send_callback = false)
    {
       try {
            return Http::withHeaders(self::getDefaultHeaders())->post(self::getBaseUrl()."/remit-status", [
                'partner_trx_id' => $partnerTrxId,
                'send_callback' => $send_callback
            ]);
       } catch(\Exception $e) {
           throwException($e);
       }
    }

    /**
     * There are two types of scheduled disbursement:
     * Non-trigger-based scheduled disbursement: OY! scheduler will run and execute the disbursement automatically on date specified in schedule_date field.
     * Trigger-based scheduled disbursement: We will send a fund acceptance email to beneficiary email provided in trigger_email field. Beneficiary will be able to execute disbursement by clicking URL provided in the email on or after the date specified in trigger_date field.
     * 
     * @param string $recipientBank Required
     * @param string $recipientAccount Required
     * @param BigInteger $amount Required
     * @param string $partnerTrxId Required
     * @param string $scheduleDate Required if If is_trigger_based = FALSE or not set
     * @param string $triggerDate Required if is_trigger_based = TRUE
     * @param string $csPhoneNumber Required if is_trigger_based = TRUE
     * @param string $csEmail Required if is_trigger_based = TRUE
     * @param string $triggerEmail Required if is_trigger_based = TRUE
     * @param string $note 
     * @param string $email 
     * @param bool $isTriggerBased Whether scheduled transfer is trigger-based. Default value is false, if set to true, trigger_date and trigger_email are required
     * @return Response|void 
     */
    public static function scheduledDisbursement(
        String $recipientBank, 
        String $recipientAccount, 
        BigInteger $amount, 
        String $partnerTrxId, 
        String $scheduleDate,
        String $triggerDate,
        String $csPhoneNumber,
        String $csEmail,
        String $triggerEmail,
        String $note, 
        String $email,
        bool $isTriggerBased = false,
        )
    {
       try {
            return Http::withHeaders(self::getDefaultHeaders())->post(self::getBaseUrl()."/scheduled-remit", [
                'recipient_bank' => $recipientBank,
                'recipient_account' => $recipientAccount,
                'amount' => $amount,
                'note' => $note,
                'partner_trx_id' => $partnerTrxId,
                'email' => $email,
                'schedule_date' => $scheduleDate,
                'is_trigger_based' => $isTriggerBased,
                'trigger_date' => $triggerDate,
                'trigger_email' => $triggerEmail,
                'cs_phone_number' => $csPhoneNumber,
                'cs_email' => $csEmail
            ]);
       } catch(\Exception $e) {
           throwException($e);
       }
    }

    /**
     * This API allows you to get detail of a created scheduled disbursement.
     * @param string $partnerTrxId 
     * @return Response|void 
     */
    public static function getDetailScheduledDisbursement(String $partnerTrxId)
    {
       try {
            return Http::withHeaders(self::getDefaultHeaders())->get(self::getBaseUrl()."/scheduled-remit", [
                'partner_trx_id' => $partnerTrxId
            ]);
       } catch(\Exception $e) {
           throwException($e);
       }
    }

    /**
     * This API allows you get list of scheduled disbursement with or without applying filter on status and processing date (trigger_date/schedule_date)
     * 
     * @param string|null $startDate 
     * @param string|null $endDate 
     * @param string|null $status 
     * @param int $offset 
     * @param int $limit 
     * @return Response|void 
     */
    public static function getListScheduledDisbursement(
        String $startDate = null,
        String $endDate = null,
        String $status = null,
        int $offset = 0,
        int $limit = 100
    )
    {
       try {
           $queryParams = [
               'offset' => $offset,
               'limit' => $limit
           ];

           if(!is_null($startDate)) {
               $queryParams['start_date'] = $startDate;
           }

            if(!is_null($endDate)) {
                $queryParams['end_date'] = $endDate;
            }

            if(!is_null($status)) {
                $queryParams['scheduled_trx_status'] = $status;
            }

            return Http::withHeaders(self::getDefaultHeaders())->post(self::getBaseUrl()."/scheduled-remit/list", $queryParams);
       } catch(\Exception $e) {
           throwException($e);
       }
    }

    /**
     * This API allows you to update created scheduled disbursement up to a day before the schedule_date/trigger_date. For non-trigger based scheduled disburse, only update to schedule_date is allowed. For trigger-based scheduled disburse, only update to trigger_date is allowed.
     * 
     * @param string $partnerTrxId 
     * @param string $scheduleDate Required if scheduled disbursement is non-trigger-based
     * @param string $triggerDate Required if scheduled disbursement is trigger-based.
     * @return Response|void 
     */
    public static function updateScheduledDisbursement(
        String $partnerTrxId,
        String $scheduleDate,
        String $triggerDate
    )
    {
       try {
            return Http::withHeaders(self::getDefaultHeaders())->put(self::getBaseUrl()."/scheduled-remit", [
                'partner_trx_id' => $partnerTrxId,
                'schedule_date' => $scheduleDate,
                'trigger_date' => $triggerDate,
            ]);
       } catch(\Exception $e) {
           throwException($e);
       }
    }

    /**
     * This API allows you to cancel created scheduled disbursement up to a day before the schedule_date/trigger_date.
     * 
     * @param string $partnerTrxId 
     * @return Response|void 
     */
    public static function cancelScheduledDisbursement(
        String $partnerTrxId
    )
    {
       try {
            return Http::withHeaders(self::getDefaultHeaders())->delete(self::getBaseUrl()."/scheduled-remit", [
                'partner_trx_id' => $partnerTrxId,
            ]);
       } catch(\Exception $e) {
           throwException($e);
       }
    }
}