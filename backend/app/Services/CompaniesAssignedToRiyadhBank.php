<?php

namespace App\Services;

use App\Models\Back\Company;
use Illuminate\Support\Carbon;

class CompaniesAssignedToRiyadhBank
{
    public $list = [
        '8f07fb82-cddc-4dc6-a1ef-2a3db115da4c',
        'abb56ea1-f9c0-4d8f-80d6-eb146d5ebfda',
        '4f4d48fc-0a57-4f7a-9f68-d4eb5f06f153',
        '195bd3b1-21e9-486b-b09f-a797575f29aa',
        'caac04b7-b47d-4929-9491-efb519b949ad',
        '26e2e542-258c-4e6b-9420-93062ef52c32',
        '5ddac296-b17a-4df1-88e6-f00a97a7651d',
        '0a0676b3-7387-4936-bff9-d23fc9cc4d20',
        'ee2f994c-0c21-4f49-903c-9ef642ef6c1c',
        '905cbb9e-0753-4db6-933e-4c04ecae1c2c',
        '6fb69931-8fea-4b36-bef7-10a4a43ed489',
        '7b7483fc-c85f-4001-ad06-7ded60677cd6',
        '98ecbc18-5467-4b9d-bc21-bedfbae25c5d',
        '702bb589-11ee-42e3-9dd6-48b7d51f2a3b',
        '78c17349-6c35-4867-9761-476268f864a5',
        '0fedc3ac-5789-4966-8161-a8422395d68a',
        'e82aaf65-5160-40a5-9625-0ca180fb8019',
        '2d16f3ae-affa-4482-862d-1ae89d843493',
        '4d2e840d-c8f6-40f9-b256-fea958cf89aa',
        '65f572e6-0618-410d-90f2-3bf85f886263',
        '71982afb-52b5-4526-8606-1b621e78c390',
        '95f6786c-9f76-4726-933a-e755d7ee922e',
        'd0c26e57-3394-4e0a-bf25-3693ff62441f',
        'f1b04547-e6c0-43c3-a4f5-cfe5dda01096',
        'e8527e5c-f198-4b27-9d6f-395d0d770556',
        'bb99592e-532c-45f3-88c9-f7a00210d1ea',
        'f58003c1-9e71-40bf-bea5-5d74a7fddb1c',
        '795290c4-563c-490d-8473-3ae46a02a41c',
        '23e28423-c1ea-4d9f-8653-a902af96b88f',
        'ebe760f6-f9fe-42a5-a79d-4c4c6d3d044f',
        'd5992fb6-08c0-456f-895c-395dd02dd1ba',
        '4cd64200-5555-4d91-8054-e10043ec880c',
        '04081416-9960-4df7-a60f-b5d150d520a4',
        '21907815-5ad0-489b-a461-a9396579fb8f',
        'a6da3adc-28bf-4903-bb60-c890016718ec',
        '7d4fd84a-6652-4eb4-b797-73ebd80096a2',
        '87747f6b-4ec2-4c1a-a592-980837107f45',
        'd7549a4b-ca7a-4f88-be22-7c1d8ff9e59a',
        '0c976286-daa3-41ce-8335-470b912ab40b',
        '1ce236f6-7b25-4472-a57f-c4e0018d8c52',
        '8b08fcd7-38a3-4132-a83b-6d8167c82f3a',
        '8c34893a-6073-49c9-aaa0-d80941b8ebfd',
        'd531ba53-fd1b-4a81-82a0-763a4374fd62',
        'aa1b656b-26fb-45df-ae8b-de8b2c0faa3c',
        'c7ec7d2a-fc66-482c-afb8-5a52c2523f5a',
        '2a29573f-5764-4aa1-ad70-e8860502651b',
        '1a5728e8-a9f3-4151-ae3d-1fba8154a5f1',
        '47a53149-14a5-4f3e-897a-3234f97d3c93',
        '4049543c-6428-45fc-8345-adbdc1bfe976',
        '74e14719-0d51-43bd-a5e6-bdbd5601ea5d',
        'b7307f3e-f84f-41b9-926e-9b9caeffbabb',
        '6a5862a9-4e8b-4b0c-9e7d-8fa54ee57369',
        '3b11d8b8-ad62-4450-8a73-bbd92dab5d74',
        '92266806-90e7-4575-a6a9-645cec39245b',
        '9a2c2176-4e06-403e-8a0d-0e8007fda650',
        'f2b76708-0110-4e33-a2e1-19f23bd1b49e',
        'f19e92e1-6923-496a-9795-63cec24def4c',
        '0baa7c6c-9b4a-4866-9610-aebf6d383922',
        '690336f0-e61c-4183-b6fa-e7d54e336a7d',
        '6a65f1b1-e7e6-4f3f-8513-e2d55821aa34',
        'd600b564-d75e-4b23-87a1-85f9978442e9',
        '272797d1-c84e-439c-9b18-d6196f1c7778',
        '3c18455e-4b4e-4eb5-b9de-cf8b0286c109',
        '367415d7-2f57-4478-9004-66fc0f17b65f',
        'ffca13f3-ef0c-4503-8c63-87801f936563',
        '4c785115-cdc7-435e-804c-3ae291c06785',
        '6bfcfa9a-7fb5-44a7-acac-8ac395a5b637',
        '5227c6e2-4882-4dda-856a-4a56a2e77f4d',
        '545d02d7-a5d2-4b49-ad94-3ec3911deb39',
        '84b98e5a-a6cf-4f41-afce-fcbc1120c999',
        'f3584d0f-ad0c-4425-935b-05829ab152b0',
        'c6dc27d0-c3ea-4ef4-80bb-d143dd839e2f',
        'd3f295bc-df44-40d3-9d6b-4ce05ccd75f7',
        '7562503c-8a7e-498e-8e3c-7b0f674f55ed',
        'f1b5b9d8-2468-4854-b9c0-cb131dde3124',
    ];

    /**
     * @param string $company_id
     * @return void
     */
    public function setTapKey(string $company_id)
    {
        if (in_array($company_id, $this->list) || (Company::find($company_id)->created_at >= Carbon::parse('2023-02-09'))) {
            config(['tap.auth.api_key' => env('TAP_PAYMENT_API_KEY_SECONDARY')]);
        }
    }

    public function setSecondaryTap()
    {
        config(['tap.auth.api_key' => env('TAP_PAYMENT_API_KEY_SECONDARY')]);
    }
}