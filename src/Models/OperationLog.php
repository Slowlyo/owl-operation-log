<?php

namespace Slowlyo\OwlOperationLog\Models;

use Slowlyo\OwlAdmin\Admin;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Slowlyo\OwlOperationLog\OwlOperationLogServiceProvider;

class OperationLog extends Model
{
    protected $table = 'admin_operation_log';

    protected $appends = ['method_color'];

    const METHODS = ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'];

    public function user()
    {
        return $this->belongsTo(Admin::adminUserModel());
    }

    public function methodColor(): Attribute
    {
        $color = [
            'GET'    => 'processing',
            'POST'   => 'success',
            'PUT'    => 'warning',
            'DELETE' => 'error',
        ];

        return Attribute::get(fn() => $color[$this->method] ?? 'gray');
    }

    public function path():Attribute
    {
        $settings = OwlOperationLogServiceProvider::setting('path_map');
        return Attribute::get(function ($value) use ($settings) {
            if(!$settings){
                return $value;
            }

            foreach ($settings as $item) {
                if (Str::is($item['path'], $value)) {
                    return "[{$item['name']}] {$value} ";
                }
            }

            return $value;
        });
    }
}
