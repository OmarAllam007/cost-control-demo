<?php

namespace App;

use App\Behaviors\HasChangeLog;
use App\Behaviors\RecordsUser;
use Illuminate\Database\Eloquent\Model;

class ProjectRole extends Model
{
    use HasChangeLog, RecordsUser;

    protected $fillable = ['role_id', 'project_id', 'name', 'email'];

    function role()
    {
        return $this->belongsTo(Role::class);
    }

    public static function updateRoles(Project $project, $input)
    {
        $roles = collect();
        foreach($input as $role) {
            foreach ($role['users'] as $user) {
                $project_role = null;

                if (!empty($user['id'])) {
                    $project_role = self::find($user['id']);
                    if ($project_role) {
                        $project_role->fill(['name' => $user['name'], 'email' => $user['email']]);
                    }
                }

                if (!$project_role) {
                    $project_role = new self([
                        'name' => $user['name'], 'email' => $user['email'],
                        'role_id' => $role['role_id'], 'project_id' => $project->id
                    ]);
                }

                $project_role->save();
                $roles->push($project_role);
            }
        }

        $ids = $roles->pluck('id');
        $project->roles()->whereNotIn('id', $ids)->delete();

        return $roles;
    }
}
