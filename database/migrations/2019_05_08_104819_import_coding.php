<?php

use App\Coding;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImportCoding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dcjs = collect([
            [
                'group' => 'trial',
                'name' => 'start_event',
                'codes' => [
                    1 => 'Detainment',
                    2 => 'Arrest',
                    3 => 'Indictment',
                    4 => 'Trial/ Hearing',
                    5 => 'Sentencing',
                    6 => 'Punishment/Release'
                ]
            ],
            [
                'group' => 'trial',
                'name' => 'end_event',
                'codes' => [
                    1 => 'Trial/Hearing',
                    2 => 'Sentencing',
                    3 => 'Punishment/Release'
                ]
            ],

            [
                'group' => 'truth',
                'name' => 'start_event',
                'codes' => [
                    1 => 'Initiate',
                    2 => 'Establishment',
                    3 => 'Functioning',
                    4 => 'Ending',
                    5 => 'Effectuate'
                ]
            ],
            [
                'group' => 'truth',
                'name' => 'end_event',
                'codes' => [
                    1 => 'Ending',
                    2 => 'Effectuate'
                ]
            ],

            [
                'group' => 'reparation',
                'name' => 'start_event',
                'codes' => [
                    1 => 'Initiate',
                    2 => 'Establishment',
                    3 => 'Functioning',
                    4 => 'Ending',
                    5 => 'Effectuate'
                ]
            ],
            [
                'group' => 'reparation',
                'name' => 'end_event',
                'codes' => [
                    1 => 'Ending',
                    2 => 'Effectuate'
                ]
            ],

            [
                'group' => 'amnesty',
                'name' => 'start_event',
                'codes' => [
                    1 => 'Promised',
                    2 => 'Initiated',
                    3 => 'Establishment',
                    4 => 'Functioning',
                    5 => 'Effectuate'
                ]
            ],
            [
                'group' => 'amnesty',
                'name' => 'end_event',
                'codes' => [
                    1 => 'Functioning'
                ]
            ],

            [
                'group' => 'purge',
                'name' => 'start_event',
                'codes' => [
                    1 => 'Promised/Threatened',
                    2 => 'Initiated',
                    3 => 'Functioning'
                ]
            ],
            [
                'group' => 'purge',
                'name' => 'end_event',
                'codes' => [
                    1 => 'Functioning'
                ]
            ],

            [
                'group' => 'exile',
                'name' => 'start_event',
                'codes' => [
                    1 => 'Promised/Threatened',
                    2 => 'Initiated',
                    3 => 'Functioning'
                ]
            ],
            [
                'group' => 'exile',
                'name' => 'end_event',
                'codes' => [
                    1 => 'Functioning'
                ]
            ]
        ]);

        $dcjs->each(function($dcj) {
            $coding = new Coding;
            $coding->group = $dcj['group'];
            $coding->name = $dcj['name'];
            $coding->codes = json_encode($dcj['codes']);
            $coding->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
