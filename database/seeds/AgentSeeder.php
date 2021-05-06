<?php

use App\Agent;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $agents = [
            [
                'name'      => 'Dinesh Adaji',
                'alias'     => 'DA',
                'contact_1' => '7894561230',
                'contact_2' => '23145670',
                'email'     => 'dineshadaji@gmail.com'
            ],
            [
                'name'      => 'Prabhuji Motiji',
                'alias'     => 'PM',
                'contact_1' => '8765431901',
                'contact_2' => '23145670',
                'email'     => 'prabhujimotiji@gmail.com'
            ],
            [
                'name'      => 'Shankarji Jepaji',
                'alias'     => 'SJ',
                'contact_1' => '8564793120',
                'contact_2' => '23145670',
                'email'     => 'shankarjijepaji@gmail.com'
            ],
            [
                'name'      => 'Umiya Brokers',
                'alias'     => 'UB',
                'contact_1' => '6549873210',
                'contact_2' => '23145670',
                'email'     => 'umiyabrokers@gmail.com'
            ],
            [
                'name'      => 'Ganesh Canvasing',
                'alias'     => 'GC',
                'contact_1' => '7894561230',
                'contact_2' => '23145670',
                'email'     => 'ganeshcanvasing@gmail.com'
            ],
        ];

        foreach ($agents as $agent) {
            Agent::create([
                'name'      => $agent['name'],
                'alias'     => $agent['alias'],
                'contact_1' => $agent['contact_1'],
                'contact_2' => $agent['contact_2'],
                'email'     => $agent['email'],
                'remarks'   => 'This is ' . $agent['name']
            ]);
        }
    }
}
