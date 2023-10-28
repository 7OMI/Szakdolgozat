<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class UserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:password {id} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Felhasználó jelszavának megváltoztatása';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id       = $this->argument('id');
        $password = $this->argument('password');

        $validator = Validator::make([
            'id'       => $id,
            'password' => $password,
        ], [
            'id' => ['required', 'integer'],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            $this->info('A felhasználó jelszava nem lett módosítva. Hibák:');
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return false;
        }

        $user = User::whereId($id)->update([
            'password' => Hash::make($password),
        ]);

        if ($user) {
            $this->info('A felhasználó jelszava sikeresen módosítva.');
        }
    }
}
