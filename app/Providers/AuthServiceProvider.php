<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
use App\Models\Topic;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
     /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('create-update-delete-users', function (User $user){
            if($user->role_id===1){
                return true;
            }
        });

        Gate::define('delete-topics', function (User $user){
            if($user->role_id===1 || $user->role_id===2){
                return true;
            }
        });

        Gate::define('delete-posts', function (User $user){
            if($user->role_id===1 || $user->role_id===2){
                return true;
            }
        });

        Gate::define('edit-topics', function (User $user){
            if($user->role_id===1 || $user->role_id===2){
                return true;
            }

            if($user->role_id===4){
                return true;
            }
        });

        Gate::define('edit-user', function (User $user){
            if($user->role_id===1 || $user->role_id===4){
                return true;
            }
        });

        Gate::define('edit-posts', function (User $user){
            if($user->role_id===1 || $user->role_id===2){
                return true;
            }

            if($user->role_id===4){
                return true;
            }
        });

        Gate::define('create-update-delete-worlds', function (User $user){
            if($user->role_id===1 || $user->role_id===3){
                return true;
            }
        });

        Gate::define('create-delete-islands', function (User $user){
            if($user->role_id===1 || $user->role_id===3){
                return true;
            }
        });

        Gate::define('create-delete-queries', function (User $user){
            if($user->role_id===1 || $user->role_id===3 || $user->role_id===4){
                return true;
            }
        });

        Gate::define('create-update-delete-fleets', function (User $user){
            if($user->role_id===1 || $user->role_id===3 || $user->role_id===4){
                return true;
            }
        });

        Gate::define('update-delete-items', function (User $user){
            if($user->role_id===1 || $user->role_id===3 || $user->role_id===4){
                return true;
            }
        });

        Gate::define('create-update-delete-battles', function (User $user){
            if($user->role_id===1 || $user->role_id===3 || $user->role_id===4){
                return true;
            }
        });

        Gate::define('create-update-delete-equipments', function (User $user){
            if($user->role_id===1 || $user->role_id===3 || $user->role_id===4){
                return true;
            }
        });

        Gate::define('create-update-delete-ships', function (User $user){
            if($user->role_id===1 || $user->role_id===3 || $user->role_id===4){
                return true;
            }
        });
    }
}
