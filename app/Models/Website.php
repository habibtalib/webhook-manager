<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    protected $fillable = [
        'name',
        'domain',
        'root_path',
        'working_directory',
        'project_type',
        'php_version',
        'node_version',
        'php_settings',
        'php_pool_name',
        'port',
        'ssl_enabled',
        'is_active',
        'nginx_status',
        'ssl_status',
    ];

    protected $casts = [
        'ssl_enabled' => 'boolean',
        'is_active' => 'boolean',
        'php_settings' => 'array',
    ];

    /**
     * Get the badge color for project type
     */
    public function getProjectTypeBadgeAttribute(): string
    {
        return match($this->project_type) {
            'php' => 'primary',
            'node' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get the version display text
     */
    public function getVersionDisplayAttribute(): string
    {
        return $this->project_type === 'php' 
            ? ($this->php_version ?? 'Default')
            : ($this->node_version ?? 'Default');
    }

    /**
     * Get the status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    /**
     * Get the Nginx status badge color
     */
    public function getNginxStatusBadgeAttribute(): string
    {
        return match($this->nginx_status) {
            'active' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'inactive' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get the SSL status badge color
     */
    public function getSslStatusBadgeAttribute(): string
    {
        return match($this->ssl_status) {
            'active' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'none' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Scope to filter by project type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('project_type', $type);
    }

    /**
     * Scope to get only active websites
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
