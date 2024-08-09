<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'super:admin {--email=} {--name=} {--password=} {--mobile_no=} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user Superadmin.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $result = User::select('id')->whereLevel(1)->first();
            $parent_id = 0;
            $email = !$this->option('email') ? 'rhmoon21@gmail.com' : $this->option('email');
            $name = !$this->option('name') ? 'Raqibul Hasan Moon' : $this->option('name');
            $password = !$this->option('password') ? '12345678' : $this->option('password');
            if ($result) {
                $this->components->error('Superadmin is already in the Database.');
            } elseif (!$result) {
                $user = User::create([
                    'parent_id' => $parent_id,
                    'user_type' => 'Internal',
                    'email' => $email,
                    'level' => 1,
                    'email_verified_at' => Carbon::now()->format('Y-m-d h:i:s'),
                    'created_by' => 1,
                    'updated_by' => 1,
                    'password' => bcrypt($password),
                ]);
                $user->user_details()->create([
                    'user_id' => $user->id,
                    'name' => $name,
                    'gender' => 'Male',
                ]);
                $user->assignRole("SuperAdmin");

                $this->components->info('Aready Created User ' . $name);

            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->components->error('An error occurred in Superadmin account setup.');
        } finally {
            DB::commit();
        }
    }
}
