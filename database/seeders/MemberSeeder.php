<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing members
        Member::truncate();

        $members = [
            // Main Officers - Ordered by hierarchy
            ['name' => 'Ln Eugene P. Balway', 'role' => 'President', 'photo_url' => null, 'order' => 1],
            ['name' => 'Ln Charles C. Castaneda', 'role' => 'First Vice President', 'photo_url' => null, 'order' => 2],
            ['name' => 'Ln Vladimir D. Cayabas', 'role' => 'Second Vice President', 'photo_url' => null, 'order' => 3],
            ['name' => 'Ln Fely D. Lingbaoan', 'role' => 'Secretary', 'photo_url' => null, 'order' => 4],
            ['name' => 'Ln Minnie Lorelie M. Lara', 'role' => 'Asst. Secretary', 'photo_url' => null, 'order' => 5],
            ['name' => 'Ln Gloria L. Ocampo', 'role' => 'Treasurer', 'photo_url' => null, 'order' => 6],
            ['name' => 'Ln Erca C. Rosendo', 'role' => 'Asst. Treasurer', 'photo_url' => null, 'order' => 7],
            ['name' => 'Ln Joeffrey A. Catbagan', 'role' => 'LCIF Coordinator', 'photo_url' => null, 'order' => 8],
            ['name' => 'Ln Greg V. Aquino', 'role' => 'Auditor', 'photo_url' => null, 'order' => 9],
            
            // Membership Committee
            ['name' => 'Ln Edgar C. Mananig', 'role' => 'Membership Committee Chairperson', 'photo_url' => null, 'order' => 10],
            
            // Marketing and Communication
            ['name' => 'Ln Fely O. Ocampo', 'role' => 'Marketing and Communication Chairperson', 'photo_url' => null, 'order' => 11],
            ['name' => 'Ln Milagros Aida M. Guanzo', 'role' => 'Marketing and Communication Co-Chair', 'photo_url' => null, 'order' => 12],
            
            // Service Committee
            ['name' => 'Ln Kristoffer G. Tabili', 'role' => 'Service Committee Chairperson', 'photo_url' => null, 'order' => 13],
            ['name' => 'Ln Ursula Pearl R. Mateo', 'role' => 'Service Committee Co-Chairperson', 'photo_url' => null, 'order' => 14],
            
            // Board of Directors - Two Years
            ['name' => 'CP Edith I. Imayaho', 'role' => 'Board of Directors (Two Years)', 'photo_url' => null, 'order' => 15],
            ['name' => 'PP Trinidad C. Trinidad', 'role' => 'Board of Directors (Two Years)', 'photo_url' => null, 'order' => 16],
            ['name' => 'PP Melba C. Guives', 'role' => 'Board of Directors (Two Years)', 'photo_url' => null, 'order' => 17],
            ['name' => 'PP Chito L. Tee', 'role' => 'Board of Directors (Two Years)', 'photo_url' => null, 'order' => 18],
            
            // Board of Directors - One Year
            ['name' => 'Ln Delfin C. Ringor', 'role' => 'Board of Directors (One Year)', 'photo_url' => null, 'order' => 19],
            ['name' => 'Ln David Joseph L. Bognadon', 'role' => 'Board of Directors (One Year)', 'photo_url' => null, 'order' => 20],
            ['name' => 'Ln Reynaldo S. Cabatuan', 'role' => 'Board of Directors (One Year)', 'photo_url' => null, 'order' => 21],
            ['name' => 'Ln Dexter O. Fag-ayan', 'role' => 'Board of Directors (One Year)', 'photo_url' => null, 'order' => 22],
            
            // Other Positions
            ['name' => 'Ln Alicia O. Cabatuan', 'role' => 'Lion Twister', 'photo_url' => null, 'order' => 23],
            ['name' => 'Ln Fe Mendoza', 'role' => 'Lion Tamer', 'photo_url' => null, 'order' => 24],
            ['name' => 'Ln Jason P. Doligas', 'role' => 'Club Branch Liason', 'photo_url' => null, 'order' => 25],
            
            // Fundraising Committee
            ['name' => 'Ln Antonio A. Anaban, Jr.', 'role' => 'Fundraising Committee Chairperson', 'photo_url' => null, 'order' => 26],
            
            // Hunger Service Committee
            ['name' => 'Ln Leofina Jane A. Paras', 'role' => 'Hunger Service Committee Chairperson', 'photo_url' => null, 'order' => 27],
            
            // Youth Service Committee
            ['name' => 'Ln Harland Gary Pawid', 'role' => 'Youth Service Committee Chairperson', 'photo_url' => null, 'order' => 28],
            ['name' => 'Zaide B. Laruan', 'role' => 'Healthcare Branch', 'photo_url' => null, 'order' => 29],
            
            // Environment Service Committee
            ['name' => 'Ln Linda Claire I. Pawid', 'role' => 'Environment Service Committee Chairperson', 'photo_url' => null, 'order' => 30],
        ];

        foreach ($members as $member) {
            Member::create($member);
        }
    }
}
