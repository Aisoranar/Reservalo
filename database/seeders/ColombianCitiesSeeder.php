<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Department;

class ColombianCitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $citiesByDepartment = [
            'Antioquia' => [
                'Medellín', 'Bello', 'Itagüí', 'Envigado', 'Sabaneta', 'Rionegro', 'La Ceja', 'Marinilla', 'Guarne', 'El Retiro'
            ],
            'Atlántico' => [
                'Barranquilla', 'Soledad', 'Malambo', 'Galapa', 'Baranoa', 'Sabanagrande', 'Palmar de Varela', 'Ciénaga de Oro', 'Puerto Colombia', 'Juan de Acosta'
            ],
            'Bolívar' => [
                'Cartagena', 'Magangué', 'Turbaco', 'Arjona', 'San Juan Nepomuceno', 'El Carmen de Bolívar', 'María La Baja', 'San Jacinto', 'Clemencia', 'Santa Rosa del Sur'
            ],
            'Boyacá' => [
                'Tunja', 'Duitama', 'Sogamoso', 'Chiquinquirá', 'Paipa', 'Villa de Leyva', 'Samacá', 'Nobsa', 'Moniquirá', 'Ráquira'
            ],
            'Caldas' => [
                'Manizales', 'La Dorada', 'Chinchiná', 'Villamaría', 'Riosucio', 'Salamina', 'Aguadas', 'Anserma', 'Pácora', 'Supía'
            ],
            'Cauca' => [
                'Popayán', 'Santander de Quilichao', 'El Tambo', 'Patía', 'Puerto Tejada', 'Piendamó', 'Sotará', 'Timbío', 'Cajibío', 'Miranda'
            ],
            'Córdoba' => [
                'Montería', 'Cereté', 'Sahagún', 'Lorica', 'Montelíbano', 'Planeta Rica', 'Tierralta', 'San Pelayo', 'Ciénaga de Oro', 'Puerto Libertador'
            ],
            'Cundinamarca' => [
                'Soacha', 'Facatativá', 'Zipaquirá', 'Chía', 'Girardot', 'Fusagasugá', 'Madrid', 'Funza', 'Mosquera', 'Cajicá'
            ],
            'Distrito Capital de Bogotá' => [
                'Bogotá D.C.'
            ],
            'Huila' => [
                'Neiva', 'Pitalito', 'Garzón', 'La Plata', 'Campoalegre', 'Palermo', 'Gigante', 'Aipe', 'Rivera', 'Timaná'
            ],
            'La Guajira' => [
                'Riohacha', 'Maicao', 'Uribia', 'Manaure', 'Fonseca', 'Barrancas', 'San Juan del Cesar', 'Villanueva', 'Hatonuevo', 'Albania'
            ],
            'Magdalena' => [
                'Santa Marta', 'Ciénaga', 'Fundación', 'El Banco', 'Plato', 'Aracataca', 'El Retén', 'Pivijay', 'Algarrobo', 'Concordia'
            ],
            'Meta' => [
                'Villavicencio', 'Acacías', 'Granada', 'Puerto López', 'Puerto Gaitán', 'Puerto Lleras', 'Puerto Concordia', 'Puerto Carreño', 'Puerto Inírida', 'Puerto Leguízamo'
            ],
            'Nariño' => [
                'Pasto', 'Tumaco', 'Ipiales', 'La Unión', 'Túquerres', 'El Charco', 'La Tola', 'Francisco Pizarro', 'Mosquera', 'Olaya Herrera'
            ],
            'Norte de Santander' => [
                'Cúcuta', 'Ocaña', 'Pamplona', 'Villa del Rosario', 'Los Patios', 'El Zulia', 'Chinácota', 'Tibú', 'Sardinata', 'Abrego'
            ],
            'Quindío' => [
                'Armenia', 'Calarcá', 'La Tebaida', 'Circasia', 'Montenegro', 'Salento', 'Quimbaya', 'Buenavista', 'Pijao', 'Córdoba'
            ],
            'Risaralda' => [
                'Pereira', 'Dosquebradas', 'La Virginia', 'Santa Rosa de Cabal', 'Belén de Umbría', 'Marsella', 'Pueblo Rico', 'Quinchía', 'Guática', 'Apía'
            ],
            'Santander' => [
                'Bucaramanga', 'Floridablanca', 'Girón', 'Piedecuesta', 'Barrancabermeja', 'San Gil', 'Málaga', 'Socorro', 'Puerto Wilches', 'Puerto Parra'
            ],
            'Sucre' => [
                'Sincelejo', 'Corozal', 'Sampués', 'San Marcos', 'San Onofre', 'Tolú', 'Galeras', 'Los Palmitos', 'Morroa', 'Ovejas'
            ],
            'Tolima' => [
                'Ibagué', 'Espinal', 'Honda', 'Mariquita', 'Líbano', 'Fresno', 'Melgar', 'Girardot', 'Ambalema', 'Armero'
            ],
            'Valle del Cauca' => [
                'Cali', 'Buenaventura', 'Palmira', 'Tuluá', 'Buga', 'Cartago', 'Jamundí', 'Yumbo', 'Florida', 'Pradera'
            ]
        ];

        foreach ($citiesByDepartment as $departmentName => $cities) {
            $department = Department::where('name', $departmentName)->first();
            
            if ($department) {
                foreach ($cities as $cityName) {
                    City::create([
                        'name' => $cityName,
                        'department_id' => $department->id,
                        'is_active' => true
                    ]);
                }
            }
        }

        $this->command->info('✅ Ciudades de Colombia creadas exitosamente');
    }
}
