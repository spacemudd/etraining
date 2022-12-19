<?php
/*
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace App\Services;

use App\Models\Back\Trainee;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use TinCan\Activity;
use TinCan\Agent;
use TinCan\Context;
use TinCan\Extensions;
use TinCan\RemoteLRS;
use TinCan\Verb;

class NelcService
{
    /**
     * The HTTP client.
     *
     * @var RemoteLRS
     */
    protected $httpClient;

    /**
     * Custom headers.
     *
     * @var array
     */
    protected $customHeaders = [];

    protected $platform;

    /**
     * Instantiate client with dependencies.
     *
     * @return void
     */
    public function __construct()
    {
        $this->httpClient = $this->setupClient([
            'endpoint' => config('services.nelc.endpoint'),
            'username' => config('services.nelc.key'),
            'password' => config('services.nelc.secret'),
        ]);
        $this->platform = config('services.nelc.platform');
    }

    public function setupClient($options)
    {
        return new RemoteLRS($options['endpoint'], '1.0.3', 'Basic', [
            'username' => $options['username'],
            'password' => $options['password'],
        ]);
    }

    public function initializeTrainee(Trainee $trainee)
    {
        $actor = new Agent([
            'mbox' => 'mailto:'.$trainee->email,
            'name' => $trainee->identity_number,
        ]);

        $verb = new Verb(['id' => 'http://adlnet.gov/expapi/verbs/registered']);
        $verb->setDisplay('en-US');

        $activity = new Activity(['id' => 'https://ptc-ksa.com/1994']);
        $activity->setDefinition([
            'name' => [
                'en-US' => 'Java for Beginners',
                'ar-SA' => 'لغة الجافا للمبتدئين',
            ],
            'description' => [
                'en-US' => 'A detailed course on Java for beginners',
                'ar-SA' => 'دورة تعليمية مفصلة عن لغة الجافا للمبتدئين',
            ],
            'type' => 'http://adlnet.gov/expapi/activities/course',
        ]);

        $context = new Context();
        $context->setInstructor(
            new Agent([
            'mbox' => 'mailto:instructor@ptc-ksa.com',
            'name' => 'Shafiq al-Shaar',
            ])
        );
        $context->setPlatform($this->platform);
        $context->setLanguage('ar-SA');
        $context->setExtensions(
            new Extensions([
                'https://nelc.gov.sa/extensions/platform' => [
                    'name' => [
                        'en-US' => 'Professional Training Center',
                        'ar-SA' => 'مركز احترافية التدريب',
                    ],
                ]
            ])
        );

        $response = $this->httpClient->saveStatement([
            'actor' => $actor,
            'verb' => $verb,
            'object' => $activity,
            'context' => $context,
        ]);

        if ($response->success) {
            Log::info('Success! Statement saved with ID: '.$response->content);
        } else {
            Log::info('Failed to save statement: '.$response->content);
        }
    }
}
