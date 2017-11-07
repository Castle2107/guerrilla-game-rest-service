<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        DB::table('guerrilla_types')->insert([
        	['name' => 'China',
        	'description' => 'ching chong hoo'],

        	['name' => 'USMC',
        	'description' => 'blrs rfdsh sdf fjod'],

        	['name' => 'MEC',
        	'description' => 'wtf is this sldf jdsfjd']
        ]);

        DB::table('battle_resources')->insert([
        	['name' => 'money',
        	'description' => 'sdijf osd jfosdijfoidis'],

        	['name' => 'people',
        	'description' => 'sdijf osd jfosdijfoidis'],

        	['name' => 'oil',
        	'description' => 'sdijf osd jfosdijfoidis'],
        ]);

        DB::table('battle_units')->insert([
        	['name' => 'assault',
        	'description' => 'unidades especializadas en 
        			infiltrarse en bases enemigas y acabar con la infantería',
			'type' => 'offense',
			'cost' => 100],

			['name' => 'engineer',
        	'description' => 'capaces no solo de construir estructuras de defensa sólidas, sino de
					destruir unidades mecánicas enemigas, pues siempre están 
					cargados de todo tipo de explosivos y herramientas.',
			'type' => 'defense',
			'cost' => 100],

			['name' => 'tank',
        	'description' => 'más costosos que la infantería, pero de mayor resistencia y poder
					de batalla. Es una unidad que no debe faltar en cualquier asalto.',
			'type' => 'offense',
			'cost' => 100],

			['name' => 'bunker',
        	'description' => 'indispensables para dar una buena bienvenida a la guerrilla enemiga que
					se ha invitado a dar un paseo por nuestras instalaciones.',
			'type' => 'offense',
			'cost' => 100],        	
        ]);

                
    }
}
