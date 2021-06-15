<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\AdminActions;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy {
    use HandlesAuthorization, AdminActions;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\user  $model
     * @return mixed
     */
    public function view(User $authenticatedUser, User $model) {
      return $authenticatedUser->userId === $model->userId;

    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\user  $model
     * @return mixed
     */
    public function update(User $authenticatedUser, User $model) {
      return $authenticatedUser->userId === $model->userId;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\user  $model
     * @return mixed
     */
    public function delete(User $authenticatedUser, User $model) {

      return $authenticatedUser->userId === $model->userId/* && $authenticatedUser->token()->client()->personal_access_client*/;
    }

}
