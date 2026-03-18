<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Nilai;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ProgressSeeder extends Seeder
{
    public function run(): void
    {
        $mapelList = [
            'Pemrograman Web',
            'Basis Data',
            'Pemrograman Berorientasi Objek',
            'Matematika',
            'Jaringan Komputer',
            'Bahasa Inggris',
        ];

        $tipeList = ['tugas', 'quiz', 'ulangan'];

        // ── Staff / Guru ──────────────────────────────────────────
        User::firstOrCreate(['email' => 'guru@lms.test'], [
            'name'     => 'Bu Sari Indah',
            'password' => Hash::make('password'),
            'role'     => 'staff',
            'status'   => 'active',
            'kelas'    => 'XII RPL 1',
        ]);

        // ── Siswa ─────────────────────────────────────────────────
        $siswaData = [
            ['name' => 'Nahel Ahya R',    'email' => 'nahel@lms.test',    'base' => 84, 'kelas' => 'XII RPL 1'],
            ['name' => 'Fadillah Putri',  'email' => 'fadillah@lms.test', 'base' => 87, 'kelas' => 'XII RPL 1'],
            ['name' => 'Farhan Rizaldi',  'email' => 'farhan@lms.test',   'base' => 85, 'kelas' => 'XII RPL 1'],
            ['name' => 'Reza Maulana',    'email' => 'reza@lms.test',     'base' => 83, 'kelas' => 'XII RPL 1'],
            ['name' => 'Nabila S',        'email' => 'nabila@lms.test',   'base' => 81, 'kelas' => 'XII RPL 1'],
            ['name' => 'Dian Puspita',    'email' => 'dian@lms.test',     'base' => 79, 'kelas' => 'XII RPL 1'],
            ['name' => 'Agung Prasetya',  'email' => 'agung@lms.test',    'base' => 76, 'kelas' => 'XII RPL 1'],
            ['name' => 'Dimas Prasetyo',  'email' => 'dimas@lms.test',    'base' => 72, 'kelas' => 'XII RPL 1'],
            ['name' => 'Siti Rahayu',     'email' => 'siti@lms.test',     'base' => 61, 'kelas' => 'XII RPL 1'],
            ['name' => 'Rizky F',         'email' => 'rizky@lms.test',    'base' => 58, 'kelas' => 'XII RPL 1'],
        ];

        foreach ($siswaData as $sd) {
            $siswa = User::firstOrCreate(['email' => $sd['email']], [
                'name'     => $sd['name'],
                'password' => Hash::make('password'),
                'role'     => 'student',
                'status'   => 'active',
                'kelas'    => $sd['kelas'],
            ]);

            // Hapus nilai lama (idempotent)
            Nilai::where('user_id', $siswa->id)->delete();

            // Buat nilai per mapel selama 28 hari terakhir
            foreach ($mapelList as $mapel) {
                $entri = rand(4, 7);
                for ($i = 0; $i < $entri; $i++) {
                    $nilaiVal = max(0, min(100, $sd['base'] + rand(-12, 10)));
                    $daysAgo  = rand(0, 27);
                    Nilai::create([
                        'user_id'        => $siswa->id,
                        'mata_pelajaran' => $mapel,
                        'judul'          => ucfirst($tipeList[array_rand($tipeList)]) . ' — ' . $mapel,
                        'tipe'           => $tipeList[array_rand($tipeList)],
                        'nilai'          => $nilaiVal,
                        'created_at'     => Carbon::now()->subDays($daysAgo)->subHours(rand(0, 8)),
                        'updated_at'     => Carbon::now()->subDays($daysAgo),
                    ]);
                }
            }
        }

        $this->command->info('✅ ProgressSeeder selesai!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Staff',   'guru@lms.test',  'password'],
                ['Student', 'nahel@lms.test', 'password'],
            ]
        );
    }
}
